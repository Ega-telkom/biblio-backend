<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        'genre_id'       => 'required|exists:genres,id',
        'title'          => 'required|string|max:255',
        'isbn'           => 'nullable|string|unique:books,isbn',
        'description'    => 'nullable|string',
        'author'         => 'required|string|max:255',
        'publisher'      => 'nullable|string|max:255',
        'lang'           => 'required|string|max:5',
        'published_date' => 'nullable|date',
        'format'         => 'required|in:pdf,epub,mobi,djvu',
        'page_count'     => 'nullable|integer|min:1',
        'price'          => 'required|integer|min:0',
        'cover'          => 'nullable|image|max:2048',        // file cover
        // 'file'           => 'required|file|mimes:pdf,epub|max:102400', // file buku 
        'file' => 'required', 
        ];
    }
}
