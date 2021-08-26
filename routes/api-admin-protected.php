<?php

use Ikoncept\Fabriq\Http\Controllers\Api\ArticlesController;
use Illuminate\Support\Facades\Route;
/**
 * Articles
 */
Route::get('articles', [ArticlesController::class, 'index']);
Route::post('articles', [ArticlesController::class, 'store']);
Route::get('articles/{id}', [ArticlesController::class, 'show']);
Route::patch('articles/{id}', [ArticlesController::class, 'update']);
Route::delete('articles/{id}', [ArticlesController::class, 'destroy']);