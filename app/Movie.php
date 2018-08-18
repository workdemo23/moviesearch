<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Imdb\TitleSearch;

class Movie extends Model
{
    protected $fillable = ['title', 'release_year', 'ratings', 'genres'];


	protected $casts = [
        'genres' => 'array',
    ];
	
    public static function getMovieByTitle($title) {
        $movie = self::where('title','like', $title)->first();
        if(!$movie instanceof Movie) {
            $imdbQuery = new TitleSearch();
            $results = $imdbQuery->search($title, array(TitleSearch::MOVIE));
            foreach ($results as $result) {
                $movieTitle = $result->title();

                // condition to make sure title matches exactly
                if(strtolower($movieTitle) == strtolower($title)) {
                    $movie = self::create([
                        'title' => $result->title(),
                        'release_year' => $result->year(),
                        'ratings' => $result->rating(),
                        'genres' => $result->genres(),
                    ]);
                }
            }
        }

        return $movie;
    }
}
