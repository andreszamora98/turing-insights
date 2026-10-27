<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:5', 'max:255'],
            'excerpt' => ['sometimes', 'string', 'max:500'],
            'content' => ['sometimes', 'string'],
            'cover_url' => ['sometimes', 'nullable', 'url', 'max:2048'],
            'topic_id' => ['sometimes', 'integer', 'exists:topics,id'],
            'published_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}