<?php

namespace App\Http\Controllers\Broadcast;

use App\Events\CommentEvent;
use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Notification;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
	public function store(CommentRequest $request, Quote $quote): JsonResponse
	{
		event(new CommentEvent($request->all()));

		Comment::create(
			[
				'user_id'  => jwtUser()->id,
				'quote_id' => $quote->id,
				'body'     => $request->body,
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
		return response()->json('add comment', 200);
	}
}
