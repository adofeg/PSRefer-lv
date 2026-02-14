<?php

namespace App\Http\Requests\Admin;

use App\Data\Categories\CategoryUpsertData;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        if ($this->routeIs('admin.categories.index')) {
            return true;
        }

        $category = $this->route('category');

        if ($category) {
            return $user->can('update', $category);
        }

        return $user->can('create', Category::class);
    }

    public function rules(): array
    {
        if ($this->routeIs('admin.categories.index')) {
            return [
                'search' => ['nullable', 'string', 'max:120'],
                'status' => ['nullable', Rule::in(['true', 'false'])],
            ];
        }

        if ($this->routeIs('admin.categories.toggle-status')) {
            return [
                'is_active' => ['required', 'boolean'],
            ];
        }

        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,'.$this->route('category')?->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['sometimes', 'boolean'],
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
