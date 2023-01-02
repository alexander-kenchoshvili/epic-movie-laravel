<?php

namespace App\Http\Controllers\Broadcast;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
	public function index(): JsonResponse
	{
		$notifications = Notification::where('to', JwtUser()->id)->orderBy('id', 'desc')->get();
		return response()->json($notifications);
	}

	public function update(): JsonResponse
	{
		$notifications = Notification::where('read', '=', 0)->where('to', JwtUser()->id);
		$notifications->update(['read' => 1]);
		return response()->json('marked', 200);
	}
}
