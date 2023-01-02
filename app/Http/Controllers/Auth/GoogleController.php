<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
	public function login(): RedirectResponse
	{
		return Socialite::driver('google')->stateless()->redirect();
	}

	public function callback(): RedirectResponse
	{
		try
		{
			$user = Socialite::driver('google')->stateless()->user();

			$isUser = User::where('email', $user->getEmail())->first();

			if (!$isUser)
			{
				$newUser = User::Create([
					'username'     => $user->getName(),
					'email'        => $user->getEmail(),
					'password'     => '',
				]);
				$payload = [
					'exp' => Carbon::now()->addMinutes(60)->timestamp,
					'uid' => User::where('email', $newUser->email)->first()->id,
				];

				$jwt = JWT::encode($payload, config('auth.jwt_secret'), config('auth.jwt_algo'), );

				$cookie = cookie('access_token', $jwt, 30, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

				return redirect(config('verification.vue_url') . 'news-feed')->withCookie($cookie);
			}
			else
			{
				$payload = [
					'exp' => Carbon::now()->addMinutes(60)->timestamp,
					'uid' => User::where('email', $isUser->email)->first()->id,
				];

				$jwt = JWT::encode($payload, config('auth.jwt_secret'), config('auth.jwt_algo'), );

				$cookie = cookie('access_token', $jwt, 30, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');

				return redirect(config('verification.vue_url') . 'news-feed')->withCookie($cookie);
			}
		}
		catch (\Throwable $th)
		{
			throw $th;
		}
	}
}
