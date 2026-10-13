<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

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
