    <?php

    use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeIngredientController;
use App\Http\Controllers\RecipeStepController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/step/{recipe_id}', [RecipeStepController::class, 'index']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/{id}', [RecipeController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/recipe-ingredients/{recipe_id}', [RecipeIngredientController::class, 'getByRecipe']);
Route::get('/ingredient', [IngredientController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [UserController::class, 'user']);

    Route::post('/recipe', [RecipeController::class, 'store']);
    Route::post('/recipe/{recipe}', [RecipeController::class, 'update']);
    Route::delete('/recipe/{recipe}', [RecipeController::class, 'destroy']);

    Route::get('/steps/{recipe_id}', [RecipeStepController::class, 'index']);
    Route::post('/steps/{recipe_id}', [RecipeStepController::class, 'store']);
    Route::post('/step-update/{id}', [RecipeStepController::class, 'update']);
    Route::delete('/step/{id}', [RecipeStepController::class, 'destroy']);

    Route::post('/save-ingredients/{recipe_id}', [RecipeIngredientController::class, 'saveAll']);

    Route::post('/step-user/{recipe_id}', [RecipeStepController::class, 'saveUserStep']);
    Route::get('/step-user/{recipe_id}', [RecipeStepController::class, 'getUserStep']);

    Route::post('/favorite/{id}', [FavoriteController::class, 'toggle']);
    Route::get('/favorites', [FavoriteController::class, 'getFavorites']);
    Route::get('/favorite-ids', [FavoriteController::class, 'getFavoriteIds']);

    Route::post('/category', [CategoryController::class, 'store']);
    Route::post('/category/{category}', [CategoryController::class, 'update']);
    Route::delete('/category/{category}', [CategoryController::class, 'destroy']);

    Route::post('/ingredient', [IngredientController::class, 'store']);
    Route::post('/ingredient/{ingredient}', [IngredientController::class, 'update']);
    Route::delete('/ingredient/{ingredient}', [IngredientController::class, 'destroy']);
});
