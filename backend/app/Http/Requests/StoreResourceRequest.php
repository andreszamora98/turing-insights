<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'cover_url' => ['nullable', 'url', 'max:2048'],
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
            'published_at' => ['nullable', 'date'],
        ];
    }
}