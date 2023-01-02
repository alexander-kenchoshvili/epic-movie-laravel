<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
	use HasFactory;

	protected $with = ['from'];

	protected $guarded = [];

	public function to(): BelongsTo
	{
		return $this->belongsTo(User::class);
	}

	public function from(): BelongsTo
	{
		return $this->belongsTo(User::class, 'from');
	}
}
