<?php

use App\Http\Controllers\CsvUploadController;
use App\Http\Controllers\VideoUploadController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::get('/', function () {
    return view('welcome');
});
Route::get('/upload', [CsvUploadController::class, 'index'])->name('upload');
Route::get('/video-upload', function () {
    return view('video_upload');
})->name('progress');
Route::get('/progress', [CsvUploadController::class, 'progress'])->name('progress');
Route::post('/upload', [CsvUploadController::class, 'uploadFileAndStoreDatabase'])->name('processFile');
Route::post('/video-upload', [VideoUploadController::class, 'uploadVideo'])->name('uploadVideo');

Route::get('/progress/data', [CsvUploadController::class, 'progressForCsvStoreProcess'])->name('csvStoreProcess');
