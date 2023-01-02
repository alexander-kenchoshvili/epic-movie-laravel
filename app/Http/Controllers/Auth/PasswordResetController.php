<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\SendResetLinkRequest;
use App\Models\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
	public function reset(SendResetLinkRequest $request): JsonResponse
	{
		$validEmail = $request->validated();
		$status = Password::sendResetLink(
			$request->only('email')
		);

		return $status === Password::RESET_LINK_SENT
				? response()->json($status, 200)
				: back()->withErrors(['email' => 'Invalid email address']);
	}

	public function newPasswordForm($token): RedirectResponse
	{
		$email = request('email');
		return redirect(config('verification.vue_url') . 'new-password' . "?token={$token}&email={$email}");
	}

	public function resetPassword(PasswordRequest $request): JsonResponse
	{
		$attributes = [
			'token'                 => $request->token,
			'email'                 => $request->email,
			'password'              => $request->password,
			'password_confirmation' => $request->password_confirmation,
		];
		$payload = [
			'exp' => Carbon::now()->addMinutes(60)->timestamp,
			'uid' => User::where('email', $request->email)->first()->id,
		];

		$jwt = JWT::encode($payload, config('auth.jwt_secret'), config('auth.jwt_algo'), );

		$cookie = cookie('access_token', $jwt, 30, '/', config('auth.front_end_top_level_domain'), true, true, false, 'Strict');
		$status = Password::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function ($user, $password) {
				$user->forceFill([
					'password' => $password,
				])->setRememberToken(Str::random(60));
				$user->save();
				event(new PasswordReset($user));
			}
		);
		return $status = Password::PASSWORD_RESET ? response()->json($status, 200)->withCookie($cookie) : back()->withErrors(['email' =>'password changed succesfully']);
	}
}
