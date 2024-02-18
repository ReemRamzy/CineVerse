<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function getMovieByArtist(Request $request)
    {
        $artistName = $request->input('artist_name');
        $apiKey = env('TMDB_API_KEY');
        $searchUrl = "https://api.themoviedb.org/3/search/person?api_key={$apiKey}&query={$artistName}";

        $client = new Client();
        $searchResponse = $client->get($searchUrl);
        $searchResults = json_decode($searchResponse->getBody(), true);

        if (isset($searchResults['results']) && count($searchResults['results']) > 0) {
            $artistId = $searchResults['results'][0]['id'];

            $moviesUrl = "https://api.themoviedb.org/3/person/{$artistId}/movie_credits?api_key={$apiKey}";
            $movieResponse = $client->get($moviesUrl);
            $movieData = json_decode($movieResponse->getBody(), true);
            $movie = array_column($movieData['cast'], 'original_title');
            return response()->json($movie);
        } else {
            return response()->json(['message' => 'No result found']);
        }
    }
}