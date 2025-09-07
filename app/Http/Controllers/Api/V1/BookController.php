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
        $books = Book::query();

        if ($request->has('search')) {
            $books->where('title', 'like', '%' . $request->search . '%')
            ->orWhere('author', 'like', '%', $request->search . '%' );
        }

        return response()->json($books->paginate(10));
    }

    public function show(Book $book)
    {
        return response()->json($book); 
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
            'cover' => 'nullable|image|mime:jpeg,png,jpg,gif|max:2048',
        ]);

        $book = Book::create($request->only([
            'title', 'author', 'publisher', 'publish_year', 'stock', 'description'
        ]));
        
        if ($request->hasFile('cover')) {
            $book->addMedia($request->file('cover'))->toMediaCollection('cover');
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
