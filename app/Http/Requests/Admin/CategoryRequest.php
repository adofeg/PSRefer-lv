<?php

namespace App\Http\Requests\Admin;

use App\Data\Categories\CategoryUpsertData;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Category;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $category = $this->route('category');

        if ($category) {
            return $this->user()?->can('update', $category) ?? false;
        }

        return $this->user()?->can('create', Category::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:categories,name,' . $this->route('category')?->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ];
    }

    public function toData(): CategoryUpsertData
    {
        return new CategoryUpsertData(
            name: $this->validated('name'),
            description: $this->validated('description'),
            is_active: (bool) ($this->validated('is_active') ?? true)
        );
    }
}
