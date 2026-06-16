<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRecipeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // public function authorize(): bool
    // {
    //     return false;
    // }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
   public function rules(): array
{
    return [
        'title' => 'required',
        'description' => 'required',
        'cook_time' => 'required|integer|min:1',
        'difficulty' => 'required|in:easy,medium,difficult',
        'category_id' => 'required|exists:categories,id',
        'image_url' => 'required|mimes:jpeg,jpg,png',
    ];
}
}
