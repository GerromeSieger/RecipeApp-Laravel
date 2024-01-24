<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Post(
     *     path="/api/users/register",
     *     tags={"User"}, 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "username", "password", "number"},
     *             @OA\Property(property="email", type="string", example="victor@email.com"),
     *             @OA\Property(property="username", type="string", example="victor"),
     *             @OA\Property(property="number", type="string", example="08108903913"),
     *             @OA\Property(property="password", type="string", example="password123")     
     *         )
     *     ),
     *     @OA\Response(response="200", description="OK")
     * )
     */

     public function register(Request $request)
     {
        $validatedData = $request->validate([
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users',
                'number' => 'required|string|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);
        
            $user = User::create([
                'username' => $validatedData['username'],
                'email' => $validatedData['email'], 
                'number' => $validatedData['number'],             
                'password' => Hash::make($validatedData['password']),
            ]);
        
            return response()->json(['message' => 'User Created Successfully', 'data' => $user,]);
    
     }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Post(
     *     path="/api/users/login",
     *     tags={"User"}, 
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="victor@email.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response="200", description="OK")
     * )
     */

     public function login(Request $request)
     {
         $credentials = $request->only('email', 'password');
     
         if (!auth()->attempt($credentials)) {
             return response()->json(['error' => 'Invalid credentials'], 401);
         }
     
         $token = auth('api')->attempt($credentials);
     
         return $this->respondWithToken($token);
     }
     
     protected function respondWithToken($token)
     {
         return response()->json([
             'message' => 'Login Was successful',  
             'user' => auth()->user(),
             'access_token' => $token,
             'token_type' => 'bearer',
             'expires_in' => auth()->factory()->getTTL() * 60
         ]);
     }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Get(
     *     path="/api/users/getall",
     *     tags={"User"},
     *     @OA\Response(response="200", description="OK")
     * )
     */

     public function getUsers() 
     {
        return User::all();
     }
 
     /**
      * Get a JWT via given credentials.
      *
      * @return \Illuminate\Http\JsonResponse
      */
 
     /**  
      * @OA\Get(
      *     path="/api/users/oneuser",
      *     tags={"User"}, 
      *     security={{"bearerAuth": {}}},   
      *     @OA\Response(response="200", description="OK")
      * )
     */
 
     public function getUserDetails() 
     {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        $user->load('recipes');
        return response()->json([
            'user' => $user  
        ]);
     }

          /**
      * Get a JWT via given credentials.
      *
      * @return \Illuminate\Http\JsonResponse
      */
 
     /**
      * @OA\Put(
      *     path="/api/users/update",
      *     tags={"User"},
      *     security={{"bearerAuth": {}}},
      *     @OA\RequestBody(
      *         @OA\JsonContent(
      *             required={"name"},
      *             @OA\Property(property="name", type="string", example="gerrome")
      *         )  
      *     ), 
      *     @OA\Response(response="200", description="OK")
      * )
      */
 
      public function updateUserDetails(Request $request)
      {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $user = Auth::user();
        $user->update($request->all());
        return response()->json(['message' => 'User updated successfully', 'user' => $user]);
      }
  
      /**
       * Get a JWT via given credentials.
       *
       * @return \Illuminate\Http\JsonResponse
       */
  
      /**  
       * @OA\Delete(
       *     path="/api/users/delete",
       *     tags={"User"}, 
       *     security={{"bearerAuth": {}}}, 
       *     @OA\Response(response="200", description="OK")
       * )
      */ 
      
      public function deleteUser(Request $request)
      {
        $user = Auth::user();
        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
      }

      /**
       * @OA\Put(
       *     path="/api/users/changepassword",
       *     tags={"User"},
       *     security={{"bearerAuth": {}}},
       *     @OA\RequestBody(
       *         @OA\JsonContent(
       *             required={"old_password", "new_password"},
       *             @OA\Property(property="old_password", type="string", example="oldpass123"), 
       *             @OA\Property(property="new_password", type="string", example="newpass456")
       *         )  
       *     ),  
       *     @OA\Response(response="200", description="OK") 
       * )
      */

      public function changePassword(Request $request) 
      {

        $user = Auth::user();

        // Validate old password
        if(!Hash::check($request->old_password, $user->password)){
            return response(["message" => "Old password does not match"], 400);
        }

        // Check new password is not the same as old password
        if (Hash::check($request->new_password, $user->password)) {
            return response(["message" => "New password cannot be same as old password"], 400); 
        }

        // Update password
        $user->update(['password' => Hash::make($request->new_password)]);

        return response(["message" => "Password changed successfully"], 200);

      }

}