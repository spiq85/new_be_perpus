<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DashboardController;
use App\Http\Controllers\Api\V1\BookController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ReportController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\CollectionController;
use App\Http\Controllers\Api\V1\ReviewReportController;
use App\Http\Controllers\Api\V1\ForgotPasswordController;
use App\Http\Controllers\LandingPageController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/landing', [LandingPageController::class, 'index']);

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{book}', [BookController::class, 'show']);

// Forgot Password
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [ForgotPasswordController::class, 'reset']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});

// User Routes
Route::middleware('auth:sanctum',)->group(function(){
    Route::post('/logout', [AuthController::class, 'logout']);

    // Books Routes
    Route::get('books',[BookController::class, 'index']);
    Route::get('books/{book}', [BookController::class, 'show']);

    // Loans Routes
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/my-loans', [LoanController::class, 'myLoans']);

    // Routes Review
    Route::post('/reviews/{book}', [ReviewController::class, 'store']);
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy']);

    // Routes Collection
    Route::post('/collections/{book}', [CollectionController::class, 'toggle']);
    Route::get('/collections', [CollectionController::class, 'index']);

    // Routes Report
    Route::post('/reviews/{review}/report', [ReviewReportController::class, 'store']);

    // Routes Dashboard Dibedakan Rolenya Melalui Login
    Route::get('/dashboard', [DashboardController::class, 'index']);
});

// Officer Routes
Route::middleware('auth:sanctum', 'role:petugas')->prefix('petugas')->group(function (){

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']); 

    // Loans Routes
    Route::get('/loans', [LoanController::class, 'index']);
    Route::put('/loans/{loan}/validate', [LoanController::class, 'validateLoan']);
    Route::post('/loan/{loan}/reject', [LoanController::class, 'rejectionLoan']);
    Route::put('/loans/{loan}/pickup', [LoanController::class, 'pickupConfirmation']);
    Route::put('/loans/{loan}/return', [LoanController::class, 'returnBook']);
    Route::get('/reports/loans', [ReportController::class, 'generateLoanReport']);
    Route::get('/reports/fines', [ReportController::class, 'generateFineReport']);
});

// Admin Routes 
Route::middleware('auth:sanctum', 'role:admin')->prefix('admin')->group(function (){

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Chart Endpoints
    Route::get("/dashboard/user-growth", [DashboardController::class, 'userGrowth']);
    Route::get('/dashboard/top-books', [DashboardController::class, 'topBooks']);
    Route::get('/dashboard/top-users', [DashboardController::class, 'topUsers']);

    // Laons Routes
    Route::get('/loans', [LoanController::class, 'index']);

    // CRUD BUKU
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);

    // CRUD CATEGORY
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // Manage User
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);
    Route::put('/users/{user}/ban', [UserController::class, 'ban']);
    Route::put('/users/{user}/unban', [UserController::class, 'unban']);

    // Generate Laporan Buku
    Route::get('/reports/books', [ReportController::class, 'generateBookReport']);
    Route::get('/reports/books/popular', [ReportController::class, 'generatePopularBooksReport']);
    Route::get('/reports/books/category-stats', [ReportController::class, 'generateCategoryStatsReport']);
});