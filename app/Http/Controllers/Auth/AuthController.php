<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\ThumbnailHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Mail\MailNotify;
use App\Models\Email;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
	public function register(RegisterRequest $request): JsonResponse
	{
		$user = User::create([
			'username'       => $request->username,
			'email'          => $request->email,
			'password'       => $request->password,
			'token'          => str::random(60),
		]);

		Mail::to($user->email)->send(new MailNotify($user));

		return response()->json('User successfuly registered!', 200);
	}

	public function updateCrudentials(UpdateUserRequest $request): JsonResponse
	{
		$file_path = ThumbnailHelper::checkImage($request);

		$user = jwtUser();
		if ($request->username)
		{
			$user->username = $request->username;
		}
		if ($request->password)
		{
			$user->password = $request->password;
		}
		if ($request->file('thumbnail'))
		{
			$user->thumbnail = config('app.url') . 'storage/' . $file_path;
		}
		$user->save();
		return response()->json('crudentials updated', 200);
	}

	public function emailVerify(Request $request): JsonResponse
	{
		$user = User::where('token', $request->token)->first();
		if ($user)
		{
			$user->markEmailAsVerified();
			$user->save();
			auth()->login($user);
			$payload = [
				'exp' => Carbon::now()->addMinutes(60)->timestamp,
			];
			$jwt = JWT::encode($payload, config('auth.jwt_secret'), config('auth.jwt_algo'), );
			$cookie = cookie('access_token', $jwt, 30, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');
			return response()->json('success', 200)->withCookie($cookie);
		}
		else
		{
			return response()->json('error', 500);
		}
	}

	public function login(LoginRequest $request): JsonResponse
	{
		$user = $request->validated();
		$loginInput = filter_var($request->username, FILTER_VALIDATE_EMAIL) ?
		'email' : 'username';
		$authenticated = auth()->attempt([$loginInput => $user['username'], 'password' => $user['password']]);
		$user = auth()->user();
		if (!$authenticated)
		{
			$email = Email::where('email', '=', $request->username)->first();
			if ($email)
			{
				$login = $email->user->email;
				$authenticated = auth()->attempt([
					'email'    => $login,
					'password' => $request->password,
				]);
				$user = auth()->user();
				if ($email->email_verified_at == null)
				{
					return response()->json(['error' => 'email is not verified'], 404);
				}
			}
		}

		if (!$authenticated)
		{
			return response()->json(['errors' =>  ['password' => 'Password Does not exist!']], 404);
		}
		if (!$user->hasVerifiedEmail())
		{
			return response()->json(['error' => 'email is not verified'], 404);
		}
		$payload = [
			'exp' => Carbon::now()->addMinutes(60)->timestamp,
			'uid' => auth()->user()->id,
		];
		$remember = 5000;
		if ($request->remember === 'yes')
		{
			$remember = 10000;
		}

		$jwt = JWT::encode($payload, config('auth.jwt_secret'), config('auth.jwt_algo'), );

		$cookie = cookie('access_token', $jwt, $remember, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

		return response()->json('success', 200)->withCookie($cookie);
	}

	public function logout(): JsonResponse
	{
		$cookie = cookie('access_token', '', 0, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

		return response()->json('success', 200)->withCookie($cookie);
	}

	public function me(): JsonResponse
	{
		return response()->json(
			[
				'message' => 'authenticated successfully',
				'user'    => jwtUser()->load('emails'),
			],
			200
		);
	}
}
