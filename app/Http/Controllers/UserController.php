<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;

class UserController extends Controller
{
    // add the ApiResponser trait to standardize API responses
    use ApiResponser;
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getUsers()
    {
        $users = User::all();
        return $this->successResponse($users);
    }

    public function index()
    {
        $users = User::all();
        return $this->successResponse($users);
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
            return $this->successResponse($user, 
                \Illuminate\Http\Response::HTTP_CREATED);
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return $this->errorResponse('Username already exists', \Illuminate\Http\Response::HTTP_CONFLICT);
            }
            throw $e;
        }
    }
    public function show($id)
    {
        $user = User::findOrFail($id);
        return $this->successResponse($user);
    }
    public function delete($id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User not found', \Illuminate\Http\Response::HTTP_NOT_FOUND);
        }
        $user->delete();
        return $this->successResponse(['message' => 'User deleted']);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return $this->errorResponse('User not found', \Illuminate\Http\Response::HTTP_NOT_FOUND);
        }

        // Check each field - if provided, it must not be empty
        if ($request->has('username') && empty($request->input('username'))) {
            return $this->errorResponse('Username cannot be empty', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
        if ($request->has('password') && empty($request->input('password'))) {
            return $this->errorResponse('Password cannot be empty', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
        if ($request->has('gender') && empty($request->input('gender'))) {
            return $this->errorResponse('Gender cannot be empty', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }

        // Check at least one field is provided
        if (!$request->has('username') && !$request->has('password') && !$request->has('gender')) {
            return $this->errorResponse('Provide at least one field to update', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }

        $rules = [
            'username' => 'max:20',
            'password' => 'max:20',
            'gender'   => 'in:Male,Female',
        ];

        $this->validate($request, $rules);
        $user->update($request->all());
        return $this->successResponse($user);
    }
}
