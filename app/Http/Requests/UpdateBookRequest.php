<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'genre_id'       => 'sometimes|exists:genres,id',
            'title'          => 'sometimes|string|max:255',
            'isbn'           => 'nullable|string|unique:books,isbn,' . $this->book->id,
            'author'         => 'sometimes|string|max:255',
            'publisher'      => 'nullable|string|max:255',
            'lang'           => 'sometimes|string|max:5',
            'published_date' => 'nullable|date',
            'format'         => 'sometimes|in:pdf,epub,mobi,djvu',
            'page_count'     => 'nullable|integer|min:1',
            'price'          => 'sometimes|integer|min:0',
            'cover'          => 'nullable|image|max:2048',
            'file'           => 'nullable|file|mimes:pdf,epub|max:102400',
        ];
    }
}
