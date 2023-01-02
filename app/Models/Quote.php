<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Quote extends Model
{
	use HasFactory,HasTranslations;

	public $translatable = ['quote'];

	protected $with = ['comments', 'likes', 'movie', 'user'];

	protected $fillable = [
		'quote',
		'thumbnail',
		'user_id',
		'movie_id',
	];

	public function comments(): HasMany
	{
		return $this->hasMany(Comment::class);
	}

	public function likes(): hasMany
	{
		return $this->hasMany(Like::class);
	}

	public function movie(): BelongsTo
	{
		return $this->belongsTo(Movie::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
