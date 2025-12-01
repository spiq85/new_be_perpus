<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    // 📘 Untuk sisi User & Admin (list semua buku)
    public function index(Request $request)
    {
        $query = Book::with('categories')->orderByDesc("id_book");

        if($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('author', 'like', '%' . $request->search . '%')
                    ->orWhere('publisher', 'like', '%' . $request->search . '%');
            });
        }

        $books = $query->get()->map(function($book){
            return [
                'id_book'     => $book->id_book,
                'title'       => $book->title,
                'author'      => $book->author,
                'publisher'   => $book->publisher,
                'publish_year'=> $book->publish_year,
                'stock'       => $book->stock,
                'description' => $book->description,
                'categories'  => $book->categories->map(function ($c) {
                    return [
                        'id' => $c->id_category,
                        'category_name' => $c->category_name,
                    ];
                }),
                'cover' => $book->getFirstMediaUrl('cover'),
            ];
        });
        return response()->json($books);
    }

    // 📘 Tampilkan detail buku spesifik
    public function show(Book $book)
    {
        $book->load('categories');

        return response()->json([
            'id_book'     => $book->id_book,
            'title'       => $book->title,
            'author'      => $book->author,
            'publisher'   => $book->publisher,
            'publish_year'=> $book->publish_year,
            'stock'       => $book->stock,
            'description' => $book->description,
            'categories'  => $book->categories->map(function ($c) {
                return [
                    'id' => $c->id_category,
                    'category_name' => $c->category_name,
                ];
            }),
            'cover' => $book->getFirstMediaUrl('cover'),
        ]);
    }

    // 🧱 Tambah buku (Admin)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string',
            'author'       => 'required|string',
            'publisher'    => 'required|string',
            'publish_year' => 'required|integer|min:1901|max:2155',
            'stock'        => 'required|integer',
            'description'  => 'required|string',
            'categories'   => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id_category',
            'cover'        => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:4096',
        ]);

        $categoryIds = collect($request->input('categories', []))
            ->map(fn ($v) => (int) $v)->filter()->values()->all();

        $book = DB::transaction(function () use ($request, $validated, $categoryIds) {
            $book = Book::create([
                'title'        => $validated['title'],
                'author'       => $validated['author'],
                'publisher'    => $validated['publisher'],
                'publish_year' => $validated['publish_year'],
                'stock'        => $validated['stock'],
                'description'  => $validated['description'],
            ]);

            if ($request->hasFile('cover')) {
                $book->addMediaFromRequest('cover')->toMediaCollection('cover');
            }

            if (!empty($categoryIds)) {
                $book->categories()->sync($categoryIds);
            }

            return $book;
        });

        return response()->json([
            'message' => 'Book Created Successfully',
            'data'    => [
                'id_book' => $book->id_book,
                'title'   => $book->title,
                'author' => $book->author,
                'publisher' => $book->publisher,
                'publish_year' => $book->publish_year,
                'stock' => $book->stock,
                'description' => $book->description,
                'categories' => $book->categories->map(fn($c) => [
                   'id' => $c->id_category,
                   'category_name' => $c->category_name, 
                ]),
                'cover' => $book->getFirstMediaUrl('cover'),
            ],
        ], 201);
    }

    // 🧱 Update buku (Admin)
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'        => 'sometimes|string',
            'author'       => 'sometimes|string',
            'publisher'    => 'sometimes|string',
            'publish_year' => 'sometimes|digits:4',
            'stock'        => 'sometimes|integer',
            'description'  => 'sometimes|string',
            'categories'   => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id_category',
            'cover'        => 'nullable|image|mimes:jpg,png,jpeg,gif,webp|max:4096', // samain dgn store
        ]);

        $book->update($request->only(['title','author','publisher','publish_year','stock','description']));

        if ($request->hasFile('cover')) {
            $book->clearMediaCollection('cover');
            $book->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        if ($request->has('categories')) {
            $categoryIds = collect($request->input('categories', []))
                ->map(fn($v) => (int) $v)->filter()->values()->all();
            $book->categories()->sync($categoryIds); // kirim [] untuk kosongkan
        }

        return response()->json([
            'message' => 'Book Updated Successfully',
            'data'    => [
                'id_book' => $book->id_book,
                'title'   => $book->title,
                'cover'   => $book->getFirstMediaUrl('cover'),
            ],
        ]);
    }
    // 🗑️ Hapus buku
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json([
            'message' => 'Book Deleted Successfully',
        ]);
    }
}
