<?php

namespace App\Http\Requests;

use App\Domain\Attribute\Attribute;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read array $filters
 */
class FilterRequest extends FormRequest
{
    public function rules(): array
    {
        return $this->getAttributeRules();
    }

    private function getAttributeRules(): array
    {
        if (!$this->filters) {
            return [];
        }

        $attributes = Attribute::whereIn('slug', array_keys($this->filters))->get();

        $rule = [];

        $attributes->each(function (Attribute $attribute) use (&$rule) {
            $rule = [];

            if ($attribute->is_multiple) {
                $rule['filters.' . $attribute->slug] = 'required|array';
                $rule['filters.' . $attribute->slug . '.*'] = 'numeric|exists:attribute_values,id';
            } else {
                $rule['filters.' . $attribute->slug] = 'required|numeric|exists:attribute_values,id';
            }
        });

        return $rule;
    }
}
