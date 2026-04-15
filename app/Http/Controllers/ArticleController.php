<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->role === 'author') {
            $articles = Article::where('author_id', $user->id)->with('author')->get();
        } elseif ($user->role === 'reader') {
            $articles = Article::where('status', 'published')->with('author')->get();
        } else {
            $articles = Article::with('author')->get();
        }

        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Article::with(['author', 'reviews', 'comments.user'])->findOrFail($id);
        return response()->json($article);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
        ]);

        // File upload handling could go here

        $article = Article::create([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'author_id' => $request->user()->id,
            'status' => 'submitted',
        ]);

        return response()->json([
            'message' => 'Article submitted successfully',
            'article' => $article
        ], 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,under_review,accepted,rejected,published',
        ]);

        $article = Article::findOrFail($id);
        $article->status = $validated['status'];
        $article->save();

        return response()->json([
            'message' => 'Article status updated',
            'article' => $article
        ]);
    }
}
