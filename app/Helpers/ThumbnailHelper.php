<?php

namespace App\Helpers;

class ThumbnailHelper
{
	public static function checkImage($request, $model = null)
	{
		$file_path = '';
		if ($request->file('thumbnail'))
		{
			$file_name = time() . '_' . request()->file('thumbnail')->getClientOriginalName();
			$file_path = request()->file('thumbnail')->storeAs('images', str_replace(' ', '_', $file_name), 'public');

			if ($model)
			{
				$model->thumbnail = '/storage/' . $file_path;
			}
		}

		return $file_path;
	}
}
