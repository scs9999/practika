<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIngredientRequest;
use App\Http\Requests\UpdateIngredientRequest;
use App\Models\Ingredient;

class IngredientController extends Controller
{
    public function index()
    {
        return Ingredient::all();
    }

    public function store(StoreIngredientRequest $request)
    {
        $ingredient = new Ingredient;
        $ingredient->name = $request->name;
        $ingredient->unit = $request->unit;
        $ingredient->save();

        return response()->json(['message' => 'ok']);
    }

    public function show(Ingredient $ingredient)
    {
        return $ingredient;
    }

    public function update(UpdateIngredientRequest $request, Ingredient $ingredient)
    {
        $ingredient->name = $request->name;
        $ingredient->unit = $request->unit;
        $ingredient->save();

        return response()->json(['message' => 'ok']);
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return response()->json(['message' => 'ok']);
    }
}
