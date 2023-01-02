<?php

namespace App\Http\Controllers\MovieQuote;

use App\Helpers\ThumbnailHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\MovieRequest;
use App\Models\Movie;
use App\Models\Quote;
use Illuminate\Http\JsonResponse;

class MovieController extends Controller
{
	public function store(MovieRequest $request): JsonResponse
	{
		$file_path = ThumbnailHelper::checkImage($request);

		$movie = Movie::create([
			'thumbnail'      => '/storage/' . $file_path,
			'title'          => [
				'en'       => $request->title_en,
				'ka'       => $request->title_ka,
			],
			'director' => [
				'en' => $request->director_en,
				'ka' => $request->director_ka,
			],
			'description' => [
				'en' => $request->description_en,
				'ka' => $request->description_ka,
			],
			'genre'          => $request->genre,
			'user_id'        => jwtUser()->id,
		]);
		return response()->json('added movie', 200);
	}

	public function allMovies(): JsonResponse
	{
		$movies = Movie::where('user_id', jwtUser()->id)->with('quotes')->orderBy('id', 'desc')->get();
		return response()->json($movies);
	}

	public function singleMovie(Movie $movie, Quote $quote): JsonResponse
	{
		$movie->genre = json_decode($movie->genre, true);
		return response()->json([$movie, 'quotes'=> Quote::where('movie_id', $movie->id)->orderBy('id', 'desc')->get()], 200);
	}

	public function update(MovieRequest $request, Movie $movie): JsonResponse
	{
		ThumbnailHelper::checkImage($request, $movie);
		$movie->setTranslation('title', 'en', $request->title_en);
		$movie->setTranslation('title', 'ka', $request->title_ka);
		$movie->setTranslation('description', 'en', $request->description_en);
		$movie->setTranslation('description', 'ka', $request->description_ka);
		$movie->setTranslation('director', 'en', $request->director_en);
		$movie->setTranslation('director', 'ka', $request->director_ka);
		$movie->genre = $request->genre;

		$movie->update();

		return response()->json('edited', 200);
	}

	public function destroy(Movie $movie): JsonResponse
	{
		$movie->delete();
		return response()->json('deleted', 200);
	}
}
