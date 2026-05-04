<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function assign(Request $request, $id)
    {
        $validated = $request->validate([
            'reviewer_id' => 'required|exists:users,id',
        ]);

        $reviewer = User::findOrFail($validated['reviewer_id']);
        if ($reviewer->role !== 'reviewer') {
            return response()->json(['message' => 'User is not a reviewer'], 400);
        }

        $article = Article::findOrFail($id);
        
        $review = Review::create([
            'article_id' => $article->id,
            'reviewer_id' => $reviewer->id,
            'status' => 'pending',
        ]);
        
        if ($article->status === 'submitted') {
            $article->status = 'under_review';
            $article->save();
        }

        return response()->json([
            'message' => 'Reviewer assigned successfully',
            'review' => $review
        ], 201);
    }

    public function index($id)
    {
        $reviews = Review::where('article_id', $id)->with('reviewer')->orderBy('created_at', 'desc')->get();
        return response()->json($reviews);
    }

    public function submit(Request $request, $id)
    {
        $validated = $request->validate([
            'decision' => 'required|string',
            'comments' => 'nullable|string',
        ]);

        $review = Review::findOrFail($id);

        if ($review->reviewer_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $review->decision = $validated['decision'];
        $review->comments = $validated['comments'] ?? null;
        $review->status = 'completed';
        $review->save();

        $article = Article::findOrFail($review->article_id);
        if ($validated['decision'] === 'accepted') {
            $article->status = 'accepted';
            $article->save();
        } elseif ($validated['decision'] === 'rejected') {
            $article->status = 'rejected';
            $article->save();
        }

        return response()->json([
            'message' => 'Review submitted successfully',
            'review' => $review,
            'article_status' => $article->status
        ]);
    }
}
