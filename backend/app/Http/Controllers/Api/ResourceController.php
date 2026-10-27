<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Models\Resource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'topic' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'in:recent,old'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Resource::query()
            ->published()
            ->select(['id', 'title', 'slug', 'excerpt', 'cover_url', 'topic_id', 'author_id', 'published_at'])
            ->with(['topic:id,name,slug', 'author:id,name']);

        if (!empty($validated['topic'])) {
            $query->whereHas('topic', fn ($q) => $q->where('slug', $validated['topic']));
        }

        if (!empty($validated['search'])) {
            $term = $validated['search'];
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('excerpt', 'like', "%{$term}%");
            });
        }

        $direction = ($validated['sort'] ?? 'recent') === 'old' ? 'asc' : 'desc';
        $query->orderBy('published_at', $direction);

        $resources = $query
            ->paginate($validated['per_page'] ?? 6)
            ->withQueryString();

        return response()->json($resources);
    }

    public function show(string $slug): JsonResponse
    {
        $resource = Resource::query()
            ->published()
            ->where('slug', $slug)
            ->with(['topic:id,name,slug', 'author:id,name'])
            ->firstOrFail();

        return response()->json(['data' => $resource]);
    }

    public function store(StoreResourceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['author_id'] = $request->user()->id;
        $data['published_at'] = $data['published_at'] ?? now();

        $resource = Resource::create($data);

        return response()->json(['data' => $resource->load(['topic:id,name,slug', 'author:id,name'])], 201);
    }

    public function update(UpdateResourceRequest $request, Resource $resource): JsonResponse
    {
        $resource->update($request->validated());

        return response()->json(['data' => $resource->load(['topic:id,name,slug', 'author:id,name'])]);
    }

    public function destroy(Resource $resource): JsonResponse
    {
        $resource->delete();

        return response()->json(null, 204);
    }
}