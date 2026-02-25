<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getUsers()
    {
        $users = User::all();
        return response()->json(['data' => $users], 200);
    }

    public function index()
    {
        $users = User::all();
        return response()->json(['data' => $users], 200);
    }

    public function add(Request $request)
    {
        $rules = [
            'username' => 'required|max:20',
            'password' => 'required|max:20',
            'gender' => 'required|in:Male,Female',
        ];

        $this->validate($request, $rules);

        try {
            $user = User::create($request->all());
            return response()->json(['data' => $user], 201);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json(['error' => 'Username already exists'], 409);
            }
            throw $e;
        }
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['data' => $user], 200);
    }
    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['data' => ['message' => 'User deleted']], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Check each field - if provided, it must not be empty
        if ($request->has('username') && empty($request->input('username'))) {
            return response()->json(['error' => 'Username cannot be empty'], 400);
        }
        if ($request->has('password') && empty($request->input('password'))) {
            return response()->json(['error' => 'Password cannot be empty'], 400);
        }
        if ($request->has('gender') && empty($request->input('gender'))) {
            return response()->json(['error' => 'Gender cannot be empty'], 400);
        }

        // Check at least one field is provided
        if (!$request->has('username') && !$request->has('password') && !$request->has('gender')) {
            return response()->json(['error' => 'Provide at least one field to update'], 400);
        }

        $rules = [
            'username' => 'max:20',
            'password' => 'max:20',
            'gender'   => 'in:Male,Female',
        ];

        $this->validate($request, $rules);
        $user->update($request->all());
        return response()->json(['data' => $user], 200);
    }
}
