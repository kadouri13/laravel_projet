<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CommentController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/articles/accepted', [ArticleController::class, 'getAccepted']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Users
    Route::get('/users/search', [\App\Http\Controllers\UserController::class, 'search']);
    Route::put('/users/{id}/make-reviewer', [\App\Http\Controllers\UserController::class, 'makeReviewer'])->middleware('role:admin');
    Route::put('/users/{id}/make-editor', [\App\Http\Controllers\UserController::class, 'makeEditor'])->middleware('role:admin');
    Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);

    // Articles
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{id}', [ArticleController::class, 'show']);
    Route::post('/articles', [ArticleController::class, 'store'])->middleware('role:author,reviewer,admin');
    Route::put('/articles/{id}/status', [ArticleController::class, 'updateStatus'])->middleware('role:admin,reviewer,editor');

    // AI Detector
    Route::post('/articles/{id}/ai-detect', [\App\Http\Controllers\AiDetectorController::class, 'detect']);

    // Reviews
    Route::post('/articles/{id}/assign-reviewer', [ReviewController::class, 'assign'])->middleware('role:editor,admin');
    Route::get('/articles/{id}/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews/{id}', [ReviewController::class, 'submit'])->middleware('role:reviewer');

    // Comments
    Route::get('/articles/{id}/comments', [CommentController::class, 'index']);
    Route::post('/articles/{id}/comments', [CommentController::class, 'store']);
});
