<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/articles",
     *     summary="Get list of articles",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        // We always include 'reviews.reviewer' so the dashboard knows who is assigned
        $query = Article::with(['author', 'reviews.reviewer']);

        if ($user->role === 'author') {
            $articles = $query->where('author_id', $user->id)->get();
        } elseif ($user->role === 'reader') {
            $articles = $query->whereIn('status', ['accepted', 'published'])->get();
        } else {
            // Admins/Editors see everything + reviewers
            $articles = $query->get();
        }

        return response()->json($articles);
    }

    /**
     * @OA\Get(
     *     path="/articles/accepted",
     *     summary="Get list of accepted articles",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getAccepted(Request $request)
    {
        $articles = Article::whereIn('status', ['accepted', 'published'])->with('author')->get();
        return response()->json($articles);
    }

    /**
     * @OA\Get(
     *     path="/articles/{id}",
     *     summary="Get article details",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of article to return",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Article not found")
     * )
     */
    public function show($id)
    {
        $article = Article::with(['author', 'reviews', 'comments.user'])->findOrFail($id);
        return response()->json($article);
    }

    /**
     * @OA\Post(
     *     path="/articles",
     *     summary="Submit a new article",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","abstract"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="abstract", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article submitted successfully"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'abstract' => 'required|string',
            'file_path' => 'nullable|string', // 1. Add this to validation
        ]);

        $article = Article::create([
            'title' => $validated['title'],
            'abstract' => $validated['abstract'],
            'file_path' => $request->file_path, // 2. Add this to the creation array
            'author_id' => $request->user()->id,
            'status' => 'submitted',
        ]);

        return response()->json([
            'message' => 'Article submitted successfully',
            'article' => $article
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/articles/{id}/status",
     *     summary="Update article status (Admin/Editor)",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of article",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="string", enum={"submitted","under_review","accepted","rejected","published"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article status updated"
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Article not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,under_review,accepted,rejected,published',
        ]);

        $article = Article::findOrFail($id);
        $article->status = $validated['status'];
        
        // If status changes to published and not previously set, assign current timestamp
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
