<?php

namespace App\Http\Controllers;

use App\Models\RecipeStep;
use App\Models\UserStep;
use App\Http\Requests\StoreRecipeStepRequest;
use App\Http\Requests\UpdateRecipeStepRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RecipeStepController extends Controller
{
    public function index($recipe_id)
    {
        return RecipeStep::where('recipe_id', $recipe_id)->orderBy('step_number')->get();
    }

    public function store(StoreRecipeStepRequest $request, int $recipe_id)
    {
        $step = new RecipeStep();
        $step->recipe_id = $recipe_id;
        $step->step_number = $request->step_number;
        $step->description = $request->description;
        if ($request->hasFile('image_url')) {
            $step->image_url = Storage::disk('public')->putFile('steps', $request->image_url);
        }
        $step->save();
        return response()->json(['message' => 'ok']);
    }

    public function update(UpdateRecipeStepRequest $request, $id)
    {
        $step = RecipeStep::findOrFail($id);
        $step->step_number = $request->step_number;
        $step->description = $request->description;
        if ($request->hasFile('image_url')) {
            $step->image_url = Storage::disk('public')->putFile('steps', $request->image_url);
        }
        $step->save();
        return response()->json(['message' => 'ok']);
    }

    public function destroy($id)
    {
        RecipeStep::findOrFail($id)->delete();
        return response()->json(['message' => 'ok']);
    }

    public function saveUserStep(Request $request, $recipe_id)
    {
        $step = UserStep::firstOrNew([
            'user_id' => Auth::id(),
            'recipe_id' => $recipe_id,
        ]);
        $step->step_number = $request->step_number;
        $step->save();
        return response()->json(['message' => 'ok']);
    }

    public function getUserStep($recipe_id)
    {
        return UserStep::where('user_id', Auth::id())->where('recipe_id', $recipe_id)->first();
    }
}
