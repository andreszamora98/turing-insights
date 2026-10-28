<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['required', Rule::in(['consulta', 'contenido', 'error', 'otro'])],
            'message' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Ingresa tu nombre.',
            'name.max' => 'El nombre no puede superar los 100 caracteres.',
            'email.required' => 'Ingresa tu correo.',
            'email.email' => 'Ingresa un correo válido.',
            'subject.required' => 'Selecciona un asunto.',
            'subject.in' => 'El asunto seleccionado no es válido.',
            'message.required' => 'Escribe un mensaje.',
            'message.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'message.max' => 'El mensaje no puede superar los 1000 caracteres.',
        ];
    }
}