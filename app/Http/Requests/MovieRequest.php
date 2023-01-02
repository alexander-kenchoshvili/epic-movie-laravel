<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, mixed>
	 */
	public function rules()
	{
		return [
			'title_en'       => 'required|min:3',
			'title_ka'       => 'required|min:3',
			'director_en'    => 'required',
			'director_ka'    => 'required',
			'genre'          => 'required',
			'description_en' => 'required|min:5',
			'description_ka' => 'required|min:5',
			'thumbnail'      => 'image',
		];
	}
}
