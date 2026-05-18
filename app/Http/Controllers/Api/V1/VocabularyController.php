<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Word\ReviewWordRequest;
use App\Models\Word;
use App\Services\Vocabulary\VocabularyService;
use Illuminate\Http\JsonResponse;

class VocabularyController extends Controller
{
    public function __construct(
        protected VocabularyService $vocabularyService
    ) {
    }

    public function wordsForReview(): JsonResponse
    {
        $userWords = $this->vocabularyService->getWordsForReview(request()->user());

        return response()->json([
            'words' => $userWords->map(fn ($userWord) => [
                'id' => $userWord->id,
                'word' => $userWord->word->word,
                'meaning' => $userWord->word->meaning,
                'example_sentence' => $userWord->word->example_sentence,
                'familiarity' => $userWord->familiarity,
                'next_review_at' => $userWord->next_review_at,
            ]),
        ]);
    }

    public function reviewWord(ReviewWordRequest $request, int $userWordId): JsonResponse
    {
        $userWord = request()->user()->userWords()->findOrFail($userWordId);

        $updatedWord = $this->vocabularyService->reviewWord(
            $userWord,
            $request->was_correct
        );

        return response()->json([
            'message' => 'Word reviewed',
            'word' => [
                'id' => $updatedWord->id,
                'word' => $updatedWord->word->word,
                'familiarity' => $updatedWord->familiarity,
                'next_review_at' => $updatedWord->next_review_at,
            ],
        ]);
    }

    public function addWord(int $wordId): JsonResponse
    {
        $word = Word::findOrFail($wordId);

        $userWord = $this->vocabularyService->addWordToReview(
            request()->user(),
            $word
        );

        return response()->json([
            'message' => 'Word added to review list',
            'word' => [
                'id' => $userWord->id,
                'word' => $userWord->word->word,
                'familiarity' => $userWord->familiarity,
            ],
        ], 201);
    }

    public function masteredWords(): JsonResponse
    {
        $words = $this->vocabularyService->getMasteredWords(request()->user());

        return response()->json([
            'words' => $words->map(fn ($userWord) => [
                'id' => $userWord->id,
                'word' => $userWord->word->word,
                'meaning' => $userWord->word->meaning,
                'example_sentence' => $userWord->word->example_sentence,
            ]),
        ]);
    }

    public function progress(): JsonResponse
    {
        $progress = $this->vocabularyService->getWordProgress(request()->user());

        return response()->json([
            'progress' => $progress,
        ]);
    }
}
