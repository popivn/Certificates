<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\PdfController;

Route::get('/test-pdf', function () {
    return \Barryvdh\Snappy\Facades\SnappyPdf::loadHTML('<h1>Hello PDF</h1>')
        ->inline('test.pdf');
});

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::prefix('certificate')->name('certificate.')->group(function () {
    Route::get('/', [RecipientController::class, 'create'])->name('create');
    Route::get('/bulk', [RecipientController::class, 'bulk'])->name('bulk');
    Route::post('/preview', [RecipientController::class, 'preview'])->name('preview');
    Route::post('/preview-pdf', [RecipientController::class, 'previewPdf'])->name('preview.pdf');
    Route::post('/generate-pdf', [RecipientController::class, 'generatePdf'])->name('generate.pdf');
    Route::post('/preview-ajax', [RecipientController::class, 'previewAjax'])->name('preview.ajax');
});

Route::get('/create-certificate', [RecipientController::class, 'create'])->name('create.certificate');
Route::post('/create-certificate-bulk', [RecipientController::class, 'generateBulkPdf'])->name('certificate.generate.bulk');
Route::get('/preview-certificate', [RecipientController::class, 'previewPdf'])->name('preview.certificate');
Route::post('/pdf/add-background', [PdfController::class, 'addBackground'])->name('pdf.add-background');
Route::get('/pdf/upload', [PdfController::class, 'showUploadForm'])->name('pdf.upload.form');
