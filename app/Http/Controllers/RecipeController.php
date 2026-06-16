<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $sort = json_decode($request->sort ?? '{"field":"created_at","by":"desc"}');

        return Recipe::with('category')->where(function ($query) use ($request) {
            if ($request->categories) {
                $query->whereIn('category_id', json_decode($request->categories));
            }
            if ($request->difficulty) {
                $query->where('difficulty', $request->difficulty);
            }
            if ($request->checkedTime) {
                $query->whereBetween('cook_time', json_decode($request->checkedTime));
            }
            if ($request->search) {
                $query->where('title', 'like', '%'.$request->search.'%');
            }
        })
            ->orderBy($sort->field, $sort->by)->paginate(8);
    }

    public function show($id)
    {
        $recipe = Recipe::with('steps')->findOrFail($id);

        return response()->json(['recipe' => $recipe]);
    }

    public function store(StoreRecipeRequest $request)
    {
        $recipe = new Recipe;
        $recipe->title = $request->title;
        $recipe->description = $request->description;
        $recipe->cook_time = $request->cook_time;
        $recipe->difficulty = $request->difficulty;
        $recipe->category_id = $request->category_id;
        $recipe->image_url = Storage::disk('public')->putFile('images', $request->image_url);
        $recipe->save();

        return response()->json($recipe->id);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe)
    {
        $recipe->title = $request->title;
        $recipe->description = $request->description;
        $recipe->cook_time = $request->cook_time;
        $recipe->difficulty = $request->difficulty;
        $recipe->category_id = $request->category_id;
        if ($request->hasFile('image_url')) {
            $recipe->image_url = Storage::disk('public')->putFile('images', $request->image_url);
        }
        $recipe->save();

        return response()->json(['message' => 'ok']);
    }

    public function destroy(Recipe $recipe)
    {
        $recipe->delete();

        return response()->json(['message' => 'ok']);
    }
}
