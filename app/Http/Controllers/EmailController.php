<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmailRequest;
use App\Mail\SecondaryMail;
use App\Models\Email;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;

class EmailController extends Controller
{
	public function store(EmailRequest $request): JsonResponse
	{
		$email = Email::create([
			'user_id'          => jwtUser()->id,
			'email'            => $request->email,
			'token'            => str::random(60),
		]);
		Mail::to(jwtUser()->email)->send(new SecondaryMail(jwtUser(), $email));

		return response()->json('success', 200);
	}

	public function verify(Request $request): JsonResponse
	{
		$email = Email::where('token', $request->token)->first();
		if ($email)
		{
			$email->email_verified_at = Carbon::now();
			$email->save();
			return response()->json('email is verified');
		}
		return response()->json('email is not verified');
	}

	public function destroy(Email $email): JsonResponse
	{
		$email->delete();
		return response()->json('email deleted', 200);
	}

	public function makePrimaryEmail(Request $request): JsonResponse
	{
		$email = Email::where('id', $request->email_id)->first();
		$user = User::where('id', jwtUser()->id)->first();
		$newEmail = $user->email;
		$user->email = $email->email;
		$user->save();
		$email->email = $newEmail;
		$email->save();

		return response()->json('email has made primary', 200);
	}
}
