<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index(): JsonResponse
    {
        $topics = Topic::query()
            ->select(['id', 'name', 'slug', 'description'])
            ->orderBy('name')
            ->get();

        return response()->json(['data' => $topics]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'unique:topics,name'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $topic = Topic::create($data);

        return response()->json(['data' => $topic], 201);
    }

    public function update(Request $request, Topic $topic): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'min:3', 'max:255', 'unique:topics,name,' . $topic->id],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
        ]);

        $topic->update($data);

        return response()->json(['data' => $topic]);
    }

    public function destroy(Topic $topic): JsonResponse
    {
        if ($topic->resources()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar un tema con recursos asociados.',
            ], 409);
        }

        $topic->delete();

        return response()->json(null, 204);
    }
}