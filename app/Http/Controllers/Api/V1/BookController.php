<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    // LIST SEMUA BUKU (User & Admin) + RATING + JUMLAH ULASAN
    public function index(Request $request)
    {
        $query = Book::with('categories')
            ->withAvg('reviews', 'rating')      // Tambah rata-rata rating
            ->withCount('reviews')              // Tambah jumlah ulasan
            ->orderByDesc('id_book');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('publisher', 'like', "%{$search}%");
            });
        }

        $books = $query->get();

        $formatted = $books->map(function ($book) {
            return [
                'id_book'            => $book->id_book,
                'title'              => $book->title,
                'author'             => $book->author,
                'publisher'          => $book->publisher,
                'publish_year'       => $book->publish_year,
                'stock'              => $book->stock,
                'description'        => $book->description,
                'cover'              => $book->getFirstMediaUrl('cover') ?: null,
                'categories'         => $book->categories->map(fn($c) => [
                    'id'             => $c->id_category,
                    'category_name'  => $c->category_name,
                ]),
                // INI YANG BARU: rating & jumlah ulasan
                'reviews_avg_rating' => $book->reviews_avg_rating
                    ? round((float)$book->reviews_avg_rating, 1)
                    : 0.0,
                'reviews_count'      => $book->reviews_count ?? 0,
            ];
        });

        return response()->json($formatted);
    }

    // DETAIL BUKU SPESIFIK (juga include rating)
    public function show(Book $book)
    {
        $book->load([
            'categories',
            'media',
            'reviews.user:id_user,username'
        ])
            ->loadAvg('reviews', 'rating')
            ->loadCount('reviews');

        return response()->json([
            'id_book'            => $book->id_book,
            'title'              => $book->title,
            'author'             => $book->author,
            'publisher'          => $book->publisher,
            'publish_year'       => $book->publish_year,
            'stock'              => $book->stock,
            'description'        => $book->description,
            'cover'              => $book->getFirstMediaUrl('cover') ?: null,
            'categories'         => $book->categories->map(fn($c) => [
                'id'             => $c->id_category,
                'category_name'  => $c->category_name,
            ]),
            'reviews_avg_rating' => $book->reviews_avg_rating
                ? round((float)$book->reviews_avg_rating, 1)
                : 0.0,
            'reviews_count'      => $book->reviews_count ?? 0,

            // 👇 INI YANG PALING PENTING
            'reviews'            => $book->reviews->map(fn($r) => [
                'id_review' => $r->id_review,
                'rating'    => $r->rating,
                'review'    => $r->review,
                'user'      => [
                    'id_user'  => $r->user->id_user ?? null,
                    'name'     => $r->user->name ?? null,
                    'username' => $r->user->username ?? null,
                ]
            ]),
        ]);
    }

    // UPDATE BUKU (Admin)
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'author'       => 'sometimes|required|string|max:255',
            'publisher'    => 'sometimes|required|string|max:255',
            'publish_year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 5),
            'stock'        => 'sometimes|required|integer|min:0',
            'description'  => 'sometimes|required|string',
            'categories'   => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id_category',
            'cover'        => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:4096',
        ]);

        $book->update($request->only([
            'title',
            'author',
            'publisher',
            'publish_year',
            'stock',
            'description'
        ]));

        if ($request->hasFile('cover')) {
            $book->clearMediaCollection('cover');
            $book->addMediaFromRequest('cover')->toMediaCollection('cover');
        }

        if ($request->has('categories')) {
            $categoryIds = collect($request->categories ?? [])->map(fn($v) => (int)$v)->filter()->values()->all();
            $book->categories()->sync($categoryIds);
        }

        $book->load('categories');

        return response()->json([
            'message' => 'Buku berhasil diperbarui',
            'data'    => $this->formatBookResponse($book->fresh(['reviews']))
        ]);
    }

    // HAPUS BUKU (Admin)
    public function destroy(Book $book)
    {
        $book->clearMediaCollection('cover');
        $book->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus'
        ]);
    }

    // Helper biar ga duplikat kode
    private function formatBookResponse($book)
    {
        $book->loadAvg('reviews', 'rating')->loadCount('reviews');

        return [
            'id_book'            => $book->id_book,
            'title'              => $book->title,
            'author'             => $book->author,
            'publisher'          => $book->publisher,
            'publish_year'       => $book->publish_year,
            'stock'              => $book->stock,
            'description'        => $book->description,
            'cover'              => $book->getFirstMediaUrl('cover') ?: null,
            'categories'         => $book->categories->map(fn($c) => [
                'id'             => $c->id_category,
                'category_name'  => $c->category_name,
            ]),
            'reviews_avg_rating' => $book->reviews_avg_rating
                ? round((float)$book->reviews_avg_rating, 1)
                : 0.0,
            'reviews_count'      => $book->reviews_count ?? 0,
        ];
    }
}
