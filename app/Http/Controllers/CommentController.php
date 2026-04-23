<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/articles/{id}/comments",
     *     summary="Get list of comments for an article",
     *     tags={"Comments"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Article ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index($article_id)
    {
        $comments = Comment::where('article_id', $article_id)->with('user')->get();
        return response()->json($comments);
    }

    /**
     * @OA\Post(
     *     path="/articles/{id}/comments",
     *     summary="Add a comment to an article",
     *     tags={"Comments"},
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
     *             required={"content"},
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment added successfully"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Article not found")
     * )
     */
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
