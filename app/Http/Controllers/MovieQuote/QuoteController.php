<?php

namespace App\Http\Controllers\MovieQuote;

use App\Helpers\ThumbnailHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuoteRequest;
use App\Models\Movie;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
	public function store(QuoteRequest $request, Movie $movie): JsonResponse
	{
		$file_path = ThumbnailHelper::checkImage($request);

		$quote = Quote::create([
			'thumbnail'      => '/storage/' . $file_path,
			'quote'          => [
				'en' => $request->quote_en,
				'ka' => $request->quote_ka,
			],
			'movie_id'       => $movie->id,
			'user_id'        => jwtUser()->id,
		]);
		return response()->json('added quote', 200);
	}

	public function allQuotes(): JsonResponse
	{
		$quotes = Quote::orderBy('id', 'desc')->paginate(1);

		return response()->json($quotes);
	}

	public function singleQuote(Quote $quote)
	{
		return response()->json($quote);
	}

	public function update(QuoteRequest $request, Quote $quote): JsonResponse
	{
		ThumbnailHelper::checkImage($request, $quote);

		$quote->setTranslation('quote', 'en', $request->quote_en);
		$quote->setTranslation('quote', 'ka', $request->quote_ka);
		$quote->update();

		return response()->json('edited', 200);
	}

	public function destroy(Quote $quote): JsonResponse
	{
		$quote->delete();
		return response()->json('deleted', 200);
	}

	public function getQuotes(Request $request)
	{
		if ($request->search)
		{
			$search = $request->search;
			$quotes = null;
			if ($search[0] == '@')
			{
				$search = ltrim($search, '@');
				$quotes = Quote::take($request->number)->whereHas('movie', function ($query) use ($search) {
					$query
						->where('title->en', 'like', $search . '%')->orWhere('title->ka', 'like', $search . '%');
				})->orderBy('created_at', 'desc')->get();
			}
			elseif ($search[0] == '#')
			{
				$search = ltrim($search, '#');

				$quotes = Quote::take($request->number)->where('quote->en', 'like', '%' . $search . '%')
				->orwhere('quote->ka', 'like', '%' . $search . '%')->orderBy('created_at', 'desc')->get();
			}
			else
			{
				$quotes = Quote::take($request->number)->whereHas('movie', function ($query) use ($search) {
					$query
						->where('title->en', 'like', $search . '%')->orWhere('title->ka', 'like', $search . '%');
				})->orwhere('quote->en', 'like', '%' . $search . '%')
				->orwhere('quote->ka', 'like', '%' . $search . '%')->orderBy('created_at', 'desc')->get();
			}
			if ($quotes)
			{
				return $quotes;
			}
		}

		return Quote::take($request->number)->orderBy('created_at', 'desc')->get();
	}

	public function search(Request $request): JsonResponse
	{
		$quotes = [];

		$search = $request->search;
		if ($search[0] == '@')
		{
			$search = ltrim($search, '@');
			$quotes = Quote::whereHas('movie', function ($query) use ($search) {
				$query
					->where('title->en', 'like', $search . '%')->orWhere('title->ka', 'like', $search . '%');
			})->orderBy('created_at', 'desc')->get();
		}
		elseif ($search[0] == '#')
		{
			$search = ltrim($search, '#');

			$quotes = Quote::where('quote->en', 'like', '%' . $search . '%')
			->orwhere('quote->ka', 'like', '%' . $search . '%')->orderBy('created_at', 'desc')->get();
		}
		else
		{
			$quotes = Quote::whereHas('movie', function ($query) use ($search) {
				$query
					->where('title->en', 'like', $search . '%')->orWhere('title->ka', 'like', $search . '%');
			})->orwhere('quote->en', 'like', '%' . $search . '%')
			->orwhere('quote->ka', 'like', '%' . $search . '%')->orderBy('created_at', 'desc')->get();
		}
		return response()->json($quotes);
	}
}
