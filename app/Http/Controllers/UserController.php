<?php

namespace App\Http\Controllers;

use App\Models\images;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        $user =  User::all();
        $response = response()->json([
            'message' => count($user) . " Users Found",
            'data' => $user,
            'status' => true,
        ], 200);

        return $response;
    }

    // insert data using api 

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => 'please fix the errors',
                'error' => $validator->errors(),
                'status' => false,
            ], 200);
        }
    
        // Check if the user already exists
        $existingUser = User::where('email', $request->email)->first();
    
        if ($existingUser) {
            return response()->json([
                'message' => 'User already exists',
                'data' => $existingUser,
                'status' => false
            ], 200);
        }
    
        // Create a new user if they don't exist
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->save();
        return response()->json([
            'message' => 'data inserted',
            'data' => $user,
            'status' => true
        ], 200);
    }
    //find single user

    public function show($id)
    {
        $user = User::find($id);

        if (!empty($user)) {
            return response()->json([
                'message' => 'User Found',
                'data' => $user,
                'status' => true,
            ], 200);
        } else {
            return response()->json([
                'message' => 'User Not Found',
                'data' => [],
                'status' => true,
            ], 200);
        }
    }

    //update user

    public function updateUser(Request $request, $id)
    {
        $user = User::find($id);
        if ($user == null) {
            return response()->json([
                'message' => 'User not found',
                'data' => [],
                'status' => false
            ], 200);
        }
        $validator = validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'please fix the errors',
                'error' => $validator->errors(),
                'status' => false,
            ]);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'message' => 'user updated successfully',
            'data' => $user,
            'status' => true
        ], 200);
    }

    public function delete(Request $request, $id)
    {
        $user = User::find($id);
        if ($user == null) {
            return response()->json([
                'message' => 'User not found.',
                'status' => false
            ]);
        }
        $user->delete();

        return response()->json([
            'message' => "User Deleted",
            'status' => true
        ]);
    }

    public function upload(Request $request)
    {
        $validator = validator::make($request->all(), [
            'image' => 'required|mimes:png,jpg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'please fix the error',
                'error' => $validator->errors(),
            ]);
        }

        $img = $request->image;
        $ext = $img->getClientOriginalExtension();
        $imageName = time() . '.' . $ext;
        $img->move(public_path() . '/uploads/', $imageName);

        $image = new images();
        $image->image = $imageName;
        $image->save();

        return response()->json([
            'messgae' => 'image upload successfully',
            'data' => $image,
            'path' => asset('uploads/' . $imageName),
            'status' => true
        ]);
    }

    public function updateImage(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'sometimes|mimes:png,jpg'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Please fix the error',
                'error' => $validator->errors(),
            ]);
        }

        $image = Images::find($id);

        if (!$image) {
            return response()->json([
                'status' => false,
                'message' => 'Image not found',
            ], 404);
        }

        // Delete the old image file
        $oldImagePath = public_path('uploads/' . $image->image);
        if (file_exists($oldImagePath)) {
            unlink($oldImagePath);
        }

        // Upload the new image
        $img = $request->file('image');
        $ext = $img->getClientOriginalExtension();
        $imageName = time() . '.' . $ext;
        $img->move(public_path('uploads'), $imageName);

        // Update the image record in the database
        $image->image = $imageName;
        $image->save();

        return response()->json([
            'message' => 'Image updated successfully',
            'data' => $image,
            'path' => asset('uploads/' . $imageName),
            'status' => true
        ]);
    }
}
