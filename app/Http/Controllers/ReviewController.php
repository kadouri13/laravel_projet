<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * @OA\Post(
     *     path="/articles/{id}/assign-reviewer",
     *     summary="Assign a reviewer to an article",
     *     tags={"Reviews"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Article ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reviewer_id"},
     *             @OA\Property(property="reviewer_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Reviewer assigned"),
     *     @OA\Response(response=400, description="User is not a reviewer"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Article or User not found")
     * )
     */
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

    /**
     * @OA\Get(
     *      path="/articles/{id}/reviews",
     *      tags={"Reviews"},
     *      summary="Get list of reviews for an article",
     *      security={{"sanctum":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Article ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function index($id)
    {
        $reviews = Review::where('article_id', $id)->with('reviewer')->get();
        return response()->json($reviews);
    }

    /**
     * @OA\Post(
     *     path="/reviews/{id}",
     *     summary="Submit a review decision",
     *     tags={"Reviews"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Review ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"decision"},
     *             @OA\Property(property="decision", type="string"),
     *             @OA\Property(property="comments", type="string", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Review submitted successfully"),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Unauthorized"),
     *     @OA\Response(response=404, description="Review not found")
     * )
     */
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

        // Automatically update the article status based on the reviewer's decision
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
