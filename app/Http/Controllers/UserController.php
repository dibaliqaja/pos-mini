<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new UserController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $per_page = $request->per_page ? $request->per_page : 10;
            $page     = $request->page ? $request->page : 1;
            $search   = $request->search;
            $users    = User::query();
            $data     = $search
                        ? $users->where('email', 'LIKE', "%$search%")
                            ->orWhere('name', 'LIKE', "%$search%")
                            ->orderBy('created_at', 'DESC')
                            ->paginate($per_page)
                        : $users->orderBy('created_at', 'DESC')
                            ->paginate($per_page);

            return collectionResponse(
                'List User query get success',
                $data->items(),
                $page,
                $per_page,
                $data->total(),
                ceil($data->total() / $per_page),
                200
            );
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $user = User::find($id);
            if(!$user) return errorResponse('No User found', 404);
            
            return successResponse('User query get success',new UserResource($user));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        try {
            $user = User::create(array_merge($request->validated(),['password' => bcrypt($request->password)]));

            return successResponse('User successfully created',new UserResource($user));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Requests\UserRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        try {
            $user = User::find($id);
            if(!$user) return errorResponse('No User found', 404);
            $user->update(array_merge($request->validated(),['password' => bcrypt($request->password)]));

            return successResponse('User successfully updated',new UserResource($user));
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $user = User::find($id);        
            if(!$user) return errorResponse('No User found', 404);
            $user->delete();
            
            return successResponse('User successfully deleted',[]);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }
}
