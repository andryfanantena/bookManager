<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

// Redirection d'accueil
Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Routes Visiteurs (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Routes Authentifiées (Auth)
Route::middleware('auth')->group(function () {
    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profil & photo de profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');

    // Tableau de bord
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Recherche par ISBN + analyse IA
    Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
    Route::post('/scan/analyze', [ScanController::class, 'analyze'])->name('scan.analyze');

    // Catalogue
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
    Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
    Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');

    // Export
    Route::get('/books/export/pdf', [BookController::class, 'exportPdf'])->name('books.export.pdf');
    Route::get('/books/export', [BookController::class, 'exportPreview'])->name('books.export.index');
    Route::get('/books/export/excel', [BookController::class, 'exportExcel'])->name('books.export.excel');

    // Rubriques / catégories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});
