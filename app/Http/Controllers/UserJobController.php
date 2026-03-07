<?php

namespace App\Http\Controllers;

// use App\User;
use App\Models\UserJob; // your model located inside Models folder
use Illuminate\Http\Response; // Response components
use App\Traits\ApiResponser; // used to standardize API responses
use Illuminate\Http\Request; // handling HTTP requests in Lumen
use DB; // if not using Eloquent you can use DB component

class UserJobController extends Controller
{
    // add ApiResponser trait
    use ApiResponser;

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Return the list of user jobs
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $usersjob = UserJob::all();
        return $this->successResponse($usersjob);
    }

    /**
     * Obtain and show one user job
     * @return Illuminate\Http\Response
     */
    public function show($id)
    {
        $userjob = UserJob::findOrFail($id);
        return $this->successResponse($userjob);
    }
}
