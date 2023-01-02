<?php

namespace App\Http\Controllers\Broadcast;

use App\Events\LikeEvent;
use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Notification;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LikeController extends Controller
{
	public function store(Quote $quote, Request $request): JsonResponse
	{
		event(new LikeEvent($request->all()));

		$like = Like::where('user_id', JwtUser()->id)->where('quote_id', $quote->id)->first();
		if (!$like)
		{
			Like::create(
				[
					'user_id'  => jwtUser()->id,
					'quote_id' => $quote->id,
				]
			);

			if (jwtUser()->id != $request->to)
			{
				event(new NotificationEvent($request->all()));

				Notification::create(
					[
						'from'     => $request->from,
						'type'     => $request->type,
						'to'       => $request->to,
					]
				);
			}

			return response()->json('liked', 200);
		}
		else
		{
			$like->delete();
			return response()->json('delete', 200);
		}
	}
}
