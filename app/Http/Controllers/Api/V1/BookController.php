<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;

class BookController extends Controller
{
    // Sisi User
    public function index(Request $request)
    {
        $books = Book::with('categories');

        if ($request->has('search')) {
            $books->where(function($q) use ($request){
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json(
            $books->get()->map(function($book){
                return [
                    'id' => $book->id_book,
                    'title' => $book->title,
                    'author' => $book->author,
                    'publisher' => $book->publisher,
                    'publish_year' => $book->publish_year,
                    'description' => $book->description,
                    'stock' => $book->stock,
                    'category' => $book->categories->first()
                        ? [
                            'id' => $book->categories->first()->id_category,
                            'category_name' => $book->categories->first()->category_name,
                        ]
                        : null,
                    'cover' => $book->getFirstMediaUrl('cover'),
                ];
            })
        );
    }

    public function show(Book $book)
    {
        $book->load('categories');

        return response()->json([
            'id' => $book->id_book,
            'title' => $book->title,
            'author' => $book->author,
            'publisher' => $book->publisher,
            'publish_year' => $book->publish_year,
            'stock' => $book->stock,
            'description' => $book->description,
            'stock' => $book->stock,
            'category' => $book->categories->first()
            ? [
                'id' => $book->categories->first()->id_category,
                'category_name' => $book->categories->first()->category_name,
            ]
            : null,
            'cover' => $book->getFirstMediaUrl('cover'),
        ]);
    }

    // Sisi Admin
    public function store (Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string',
            'publisher' => 'required|string',
            'publish_year' => 'required|digits:4',
            'stock' => 'required|integer',
            'description' => 'required|string',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $book = Book::create($request->only([
            'title', 'author', 'publisher', 'publish_year', 'stock', 'description'
        ]));
        
        if ($request->hasFile('image')) {
            $book->addMedia($request->file('image'))->toMediaCollection('cover');
        }

        return response()->json([
            'message' => 'Book Created Successfully',
            'data' => [
                'id' => $book->id_book,
                'title' => $book->title,
                'cover' => $book->getFirstMediaUrl('cover'),
            ]
        ], 201);
    }

    public function update(Request $request, Book $book)
    {
        $book->update($request->only([
            'title', 'author', 'publisher', 'publish_year', 'stock', 'description'
        ]));

        if ($request->hasFile('cover')) {
            $book->clearMediaCollection('cover');
            $book->addMedia($request->file('cover'))->toMediaCollection('cover');
        }

        return response()->json([
            'message' => 'Book Updated Successfully',
            'data' => [
                'id' => $book->id_book,
                'title' => $book->title,
                'cover' => $book->getFirstMediaUrl('cover'),
            ]
        ]);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json([
            'message' => 'Book Deleted Successfully'
        ]);
    }
}
