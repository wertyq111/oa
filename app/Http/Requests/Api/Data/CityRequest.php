<?php

namespace App\Http\Requests\Api\Data;

use App\Http\Requests\Api\FormRequest;
use App\Rules\AvatarUrl;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CityRequest extends FormRequest
{

    public function rules(): array
    {
        switch ($this->method()) {
            case 'POST':
                $rules = [
                    'name' => 'string:1, 100'
                ];
                return $rules;
            default:
                return [];
        }
    }

    public function attributes()
    {
        return [
            'name' => '城市名称'
        ];
    }
}
