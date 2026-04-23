<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class AiDetectorController extends Controller
{
    /**
     * @OA\Post(
     *     path="/articles/{id}/ai-detect",
     *     summary="Run a heuristic AI detection pre-consultation on an article",
     *     tags={"Articles"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="AI detection result",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="article_id", type="integer"),
     *             @OA\Property(property="ai_probability", type="integer"),
     *             @OA\Property(property="human_probability", type="integer"),
     *             @OA\Property(property="decision", type="string", example="AI Generated"),
     *             @OA\Property(property="disclaimer", type="string")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=404, description="Article not found")
     * )
     */
    public function detect(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        
        // Ensure abstract is present
        $text = strtolower($article->abstract ?? '');
        $totalWords = str_word_count($text);
        
        // Defensive check if abstract is completely empty
        if ($totalWords === 0) {
            return response()->json(['message' => 'Article has no abstract to analyze.'], 400);
        }

        // Heuristic AI terminology markers
        $aiMarkers = [
            'delve', 'tapestry', 'testament', 'furthermore', 'realm', 
            'landscape', 'multifaceted', 'beacon', 'moreover', 'crucial',
            'in conclusion', 'it is important to note', 'foster', 'underscore'
        ];

        $markerCount = 0;
        foreach ($aiMarkers as $marker) {
            $markerCount += substr_count($text, $marker);
        }

        // Extremely simple algorithm: Each marker hit adds approximately 15% probability
        // And we map total word count constraints so it doesn't instantly become 100 on long texts
        $aiProbability = min(100, intval(($markerCount * 15) * (100 / max($totalWords, 100))));
        
        // Smooth out very low probabilities into a "human" baseline jitter (0-5%)
        if ($aiProbability === 0) {
            $aiProbability = rand(0, 5);
        }

        $humanProbability = 100 - $aiProbability;

        // Formal Decision Metric
        if ($aiProbability >= 50) {
            $decision = 'AI Generated';
        } else {
            $decision = 'Human Authored';
        }

        // Database commit
        $article->ai_decision = $decision;
        $article->save();

        return response()->json([
            'message' => 'AI detection completed successfully.',
            'article_id' => $article->id,
            'ai_probability' => $aiProbability,
            'human_probability' => $humanProbability,
            'decision' => $decision,
            'disclaimer' => 'This is a pre-consultation heuristic result. The real decision will be made by a verified human reviewer.',
        ]);
    }
}
