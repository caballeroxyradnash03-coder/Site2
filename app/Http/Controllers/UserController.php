<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
        return response()->json($users, 200);
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
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
            return response()->json($user, 201);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return response()->json(['message' => 'Username already exists'], 409);
            }
            throw $e;
        }
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 200);
    }
    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->delete();
        return response()->json(['message' => 'User deleted'], 200);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $rules = [
            'username' => 'max:20',
            'password' => 'max:20',
            'gender'   => 'in:Male,Female',
        ];

        $this->validate($request, $rules);
        $user->update($request->all());
        return response()->json($user, 200);
    }
}