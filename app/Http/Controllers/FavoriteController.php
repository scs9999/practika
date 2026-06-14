<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle($id)
    {
        $favorite = Favorite::where('recipe_id', $id)->where('user_id', Auth::id())->first();

        if ($favorite) {
            $favorite->delete();
        } else {
            $favorite = new Favorite();
            $favorite->recipe_id = $id;
            $favorite->user_id = Auth::id();
            $favorite->save();
        }

        return response()->json(['message' => 'ok']);
    }

    public function getFavorites()
    {
        return Favorite::where('user_id', Auth::id())->with('recipe.category')->get();
    }

    public function getFavoriteIds()
    {
        return Favorite::where('user_id', Auth::id())->get();
    }
}
