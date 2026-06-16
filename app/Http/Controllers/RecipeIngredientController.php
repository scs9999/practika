<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveIngredientsRequest;
use App\Models\RecipeIngredient;

class RecipeIngredientController extends Controller
{
    public function getByRecipe($recipe_id)
    {
        return RecipeIngredient::where('recipe_id', $recipe_id)->with('ingredient')->get();
    }

    public function saveAll(SaveIngredientsRequest $request, $recipe_id)
    {
        $ingredients = json_decode($request->all_ingredients);

        RecipeIngredient::where('recipe_id', $recipe_id)->delete();

        foreach ($ingredients as $item) {
            $recipeIngredient = new RecipeIngredient;
            $recipeIngredient->recipe_id = $recipe_id;
            $recipeIngredient->ingredient_id = $item->ingredient_id;
            $recipeIngredient->quantity = $item->quantity;
            $recipeIngredient->save();
        }

        return response()->json(['message' => 'ok']);
    }
}
