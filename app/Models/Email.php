<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Email extends Model
{
	use HasFactory;

	public $fillable = ['user_id', 'email', 'email_verified_at', 'token'];

	public function user(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}
}
