<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Traits\ApiResponser;
use App\Models\UserJob; 

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
            'jobid' => 'required|numeric|min:1|not_in:0',
        ];

        $this->validate($request, $rules);
        
        // validate if jobid is found in the table tbluserjob
        $userjob = UserJob::findOrFail($request->jobid);


        try {
            // Create the user and ensure jobid is set even if fillable isn't
            $user = new User($request->all());
            $user->jobid = $request->jobid;
            $user->save();

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

        // Lumen often does not populate PUT/PATCH form-data, so try multiple sources
        $payload = $request->all();
        if (empty($payload)) {
            $payload = $request->json()->all();
        }
        if (empty($payload)) {
            // If still empty, parse raw form-encoded body manually
            $raw = file_get_contents('php://input');
            if (!empty($raw)) {
                parse_str($raw, $rawParsed);
                if (!empty($rawParsed)) {
                    $payload = $rawParsed;
                }
            }
        }

        // If the payload contains the same username as the current record, ignore it.
        // This prevents a unique-key error when the user submits their current username.
        if (array_key_exists('username', $payload) && $payload['username'] === $user->username) {
            unset($payload['username']);
        }

        // Check each field - if provided, it must not be empty
        if (array_key_exists('username', $payload) && empty($payload['username'])) {
            return $this->errorResponse('Username cannot be empty', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
        if (array_key_exists('password', $payload) && empty($payload['password'])) {
            return $this->errorResponse('Password cannot be empty', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
        if (array_key_exists('gender', $payload) && empty($payload['gender'])) {
            return $this->errorResponse('Gender cannot be empty', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }
        if (array_key_exists('jobid', $payload) && empty($payload['jobid'])) {
            return $this->errorResponse('Job ID cannot be empty', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }

        // Check at least one field is provided
        if (!array_key_exists('username', $payload) && !array_key_exists('password', $payload) && !array_key_exists('gender', $payload) && !array_key_exists('jobid', $payload)) {
            return $this->errorResponse('Provide at least one field to update', \Illuminate\Http\Response::HTTP_BAD_REQUEST);
        }

        $rules = [];
        if (array_key_exists('username', $payload)) {
            // unique except current user id
            $rules['username'] = 'max:20|unique:users,username,' . $id;
        }
        if (array_key_exists('password', $payload)) {
            $rules['password'] = 'max:20';
        }
        if (array_key_exists('gender', $payload)) {
            $rules['gender'] = 'in:Male,Female';
        }
        if (array_key_exists('jobid', $payload)) {
            $rules['jobid'] = 'numeric|min:1|not_in:0';
        }

        if (!empty($rules)) {
            $validator = Validator::make($payload, $rules);
            if ($validator->fails()) {
                return $this->errorResponse($validator->errors(), \Illuminate\Http\Response::HTTP_BAD_REQUEST);
            }
        }

        // If jobid is provided, ensure it exists in tbluserjob
        if (array_key_exists('jobid', $payload)) {
            UserJob::findOrFail($payload['jobid']);
        }

        // Only update the fields that were provided
        // Update regular fields via fill
        $user->fill($payload);

        // Always set jobid explicitly to avoid mass-assignment / fillable issues
        if (array_key_exists('jobid', $payload)) {
            $user->jobid = $payload['jobid'];
        }

        try {
            $user->save();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return $this->errorResponse('Username already exists', \Illuminate\Http\Response::HTTP_CONFLICT);
            }
            throw $e;
        }

        return $this->successResponse($user);

    }
}
