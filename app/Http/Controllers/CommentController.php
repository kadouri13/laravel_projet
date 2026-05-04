<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($article_id)
    {
        $comments = Comment::where('article_id', $article_id)->with('user')->orderBy('created_at', 'desc')->get();
        return response()->json($comments);
    }

    public function store(Request $request, $article_id)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $article = Article::findOrFail($article_id);

        $comment = Comment::create([
            'article_id' => $article->id,
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return response()->json([
            'message' => 'Comment added successfully',
            'comment' => $comment->load('user')
        ], 201);
    }
}
