<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Article::with(['author', 'reviews.reviewer'])->orderBy('created_at', 'desc');

        if ($user->role === 'author') {
            $articles = $query->where('author_id', $user->id)->get();
        } elseif ($user->role === 'reader') {
            $articles = $query->whereIn('status', ['accepted', 'published'])->get();
        } else {
            $articles = $query->get();
        }

        return response()->json($articles);
    }

    public function getAccepted(Request $request)
    {
        $articles = Article::whereIn('status', ['accepted', 'published'])->with('author')->orderBy('created_at', 'desc')->get();
        return response()->json($articles);
    }

    public function show($id)
    {
        $article = Article::with(['author', 'reviews', 'comments.user'])->orderBy('created_at', 'desc')->findOrFail($id);
        return response()->json($article);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'file_path' => 'nullable|string',
        ]);

        $article = Article::create([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'file_path' => $request->file_path,
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
        
        if ($validated['status'] === 'published' && is_null($article->published_at)) {
            $article->published_at = now();
        }

        $article->save();

        return response()->json([
            'message' => 'Article status updated',
            'article' => $article
        ]);
    }
}
