<?php

namespace App\Http\Controllers;

use App\Http\Resources\MovieResource;
use App\Http\Resources\MoviesResource;
use App\Movie;
use Illuminate\Http\Request;

class MoviesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MoviesResource::collection(Movie::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            return MovieResource::make(Movie::findOrFail($id));
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
			$ratings = $request->get('ratings', false);
			$genres = $request->get('genres', false);
			$movie = Movie::findOrFail($id);
			if($ratings !== false) {
				$movie->ratings = $ratings;
			}
			if($genres !== false) {
				$movie->genres = explode(",", $genres);
			}
			$movie->save();
            return MovieResource::make($movie);
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function search($title) {
        try {
			$movie = Movie::getMovieByTitle($title);
			if($movie instanceof Movie) {
				return MovieResource::make($movie);
			} else {
				throw new \Exception("No movie found for the title");
			}            
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function searchByReleased($from, $to = null) {
        try {
            if($to !== null) {
                return MovieResource::collection(Movie::whereBetween('release_year', [$from, $to])->get());
            } else {
                return MovieResource::collection(Movie::whereReleaseYear($from)->get());
            }
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function searchMaximumRating($rating) {
        try {
            return MovieResource::make(Movie::where('ratings', '<', $rating)->get());
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function searchMinimumRating($rating) {
        try {
            return MovieResource::make(Movie::where('ratings', '>', $rating)->get());
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }

    public function searchByGenres($genres) {
        try {

            $movies = Movie::where(function($query) use ($genres) {
                $arrGenres = explode(",", $genres);
                foreach ($arrGenres as $index => $genre) {
                    if($index == 0) {
                        $query->whereJsonContains('genres', $genre);
                    } else {
                        $query->orWhereJsonContains('genres', $genre);
                    }
                }
            })->get();

            return MovieResource::make($movies);
        } catch (\Exception $ex) {
            return response()->json(["error" => $ex->getMessage()], 500);
        }
    }
}
