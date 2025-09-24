<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;
use App\Models\Loan;


class LandingPageController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $activeMembers = User::count();
        $borrowedBooks = Loan::whereIn('status_peminjaman', ['dipinjam', 'terlambat'])->count();

        $popularBooks = Book::with(['categories','loans'])
            ->withCount('loans')
            ->orderByDesc('loans_count')
            ->take(6)
            ->get()
            ->map(function($book){
                return [
                    'id' => $book->id_book,
                    'title' => $book->title,
                    'author' => $book->author,
                    'cover' => $book->getFirstMediaUrl('cover') ?? null,
                    'genres' => $book->categories->pluck('category_name'),
                    'available' => $book->stock > 0,
                    'description' => $book->description,
                ];
            });
        return response()->json([
            'stats' => [
                'total_books' => $totalBooks,
                'active_members' => $activeMembers,
                'borrowed_books' => $borrowedBooks,
            ],
            'popular_books' => $popularBooks,
        ]);
    }
}
