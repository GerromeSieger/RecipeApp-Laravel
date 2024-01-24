<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;

class RecipeController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/recipes/userrecipes",
     *     tags={"Recipe"},
     *     security={{"bearerAuth": {}}},  
     *     @OA\Response(response="200", description="OK")
     * )
     */
    public function allUserRecipes(Recipe $recipe)
    {
        $user = Auth::user();

        $recipes = Recipe::where('user_id', $user->id)->get();
        $recipes->load('user');
        return response()->json(['data' => $recipes]);
    }

    /**
     * @OA\Get(
     *     path="/api/recipes/onerecipe/{recipeId}",
     *     tags={"Recipe"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="recipeId",
     *         in="path",
     *         description="ID of the recipe",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),  
     *     @OA\Response(response="200", description="OK")
     * )
     */
    public function userRecipe(Recipe $recipe, $recipeId)
    {
        $user = Auth::user();

        $rec = Recipe::where('id', $recipeId)->where('user_id', $user->id)->firstOrFail();
        if ($rec) {
            return response()->json(['data' => $rec]);
        } else {
            return response()->json(['message' => 'No such recipe'], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/recipes/create",
     *     tags={"Recipe"}, 
     *     security={{"bearerAuth": {}}}, 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "description", "ingredients"},
     *             @OA\Property(property="name", type="string", example="Pasta"),
     *             @OA\Property(property="description", type="string", example="This is my pasta recipe"),
     *             @OA\Property(property="ingredients", type="list", example={"Salt", "pepper"})     
     *         )
     *     ),
     *     @OA\Response(response="200", description="OK")
     * )
     */
    public function create(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'ingredients' => 'required|array',
    
        ]);
        $ingredients = json_encode($validatedData['ingredients']);

        $recipe = Recipe::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'], 
            'ingredients' => $ingredients,
            'user_id' => $user->id
        ]);
    
        return response()->json(['message' => 'Recipe Created Successfully', 'data' => $recipe,]);

    }

    /**
    * @OA\Put(
    *     path="/api/recipes/edit/{recipeId}",
    *     tags={"Recipe"},
    *     security={{"bearerAuth": {}}},
    *     @OA\Parameter(
    *         name="recipeId",
    *         in="path",
    *         description="ID of the recipe",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),      
    *     @OA\RequestBody(
    *         @OA\JsonContent(
    *             required={"name"},
    *             @OA\Property(property="name", type="string", example="gerrome")
    *         )  
    *     ), 
    *     @OA\Response(response="200", description="OK")
    * )
    */    

    public function edit(Request $request, Recipe $recipe, $recipeId)
    {
        $user = Auth::user();

        $recipe = Recipe::findOrFail($recipeId);

        $recipe->update($request->all());

        return response()->json(['message' => 'Recipe updated successfully', 'data' => $recipe]);
    }    

    /**  
    * @OA\Delete(
    *     path="/api/recipes/delete/{recipeId}",
    *     tags={"Recipe"}, 
    *     security={{"bearerAuth": {}}},
    *     @OA\Parameter(
    *         name="recipeId",
    *         in="path",
    *         description="ID of the recipe",
    *         required=true,
    *         @OA\Schema(
    *             type="integer"
    *         )
    *     ),          
    *     @OA\Response(response="200", description="OK")
    * )
    */ 
    public function delete(Recipe $recipe, $recipeId)
    {
        $user = Auth::user();

        $recipe = Recipe::findOrFail($recipeId);

        $recipe->delete();
        return response()->json(['message' => 'recipe deleted successfully']);
    }
}
