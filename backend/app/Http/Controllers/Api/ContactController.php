<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function store(ContactRequest $request): JsonResponse
    {
        $contact = ContactMessage::create([
            ...$request->validated(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'message' => 'Mensaje recibido correctamente.',
            'data' => [
                'id' => $contact->id,
            ],
        ], 201);
    }
}