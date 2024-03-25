<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\CommonTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use CommonTrait;

    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            return $this->sendSuccess('Logged in Successfully', ['access_token' => $token, 'token_type' => 'Bearer','user' =>  $user]);
        } else {
            return $this->sendError(['error' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }


    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users|max:255',
//                'phone' => 'required',
                'password' => 'required|string|min:8|confirmed', // "confirmed" validates "password_confirmation" field
            ], [
                'name.required' => 'The name field is required.',
                'name.string' => 'The name must be a string.',
                'name.max' => 'The name must not exceed 255 characters.',
                'email.required' => 'The email field is required.',
                'email.email' => 'Invalid email format.',
                'email.unique' => 'This email is already registered.',
                'email.max' => 'The email must not exceed 255 characters.',
                'password.required' => 'The password field is required.',
                'password.string' => 'The password must be a string.',
                'password.min' => 'The password must be at least 8 characters long.',
                'password.confirmed' => 'The password confirmation does not match.',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return $this->sendError('validations errors',422,  ['errors' => $validator->errors()] ); // 422 Unprocessable Entity status code
            }

            // Create a new user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
//                'phone' => $request->phone, // Hash the password
                'password' => bcrypt($request->password), // Hash the password
            ]);

            // Generate a token for the new user
            $token = $user->createToken('authToken')->plainTextToken;

            // Return a response with the user and token
            return $this->sendSuccess("User Register Successfully", [
                'user' => $user,
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 201);
        }
        catch (\Exception $e) {
            // Handle any unexpected exceptions here
            return $this->sendError(['message' => $e->getMessage()], 500);
        }

    }

    public function loggedInUser()
    {
        return $this->sendSuccess("Logged In user fetch Successfully.", \auth()->user());
    }

}
