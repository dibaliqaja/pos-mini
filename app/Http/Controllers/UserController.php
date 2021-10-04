<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api');
    }

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

            $response = [
                'status'     => 'success',
                'message'    => 'List User query get success',
                'data'       => $data->items(),
                'page'       => $page,
                'per_page'   => $per_page,
                'total_data' => $data->total(),
                'total_page' => ceil($data->total() / $per_page)
            ];

            return response()->json($response, 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
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
            $user = User::findOrFail($id);
        
            return response()->json([
                'status' => 'success',
                'message' => 'User query get success',
                'data' => [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|confirmed|min:6',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 422);
            }

            $user = User::create(array_merge($validator->validated(),
                        ['password' => bcrypt($request->password)]
                    ));

            return response()->json([
                'status' => 'success',
                'message' => 'User successfully created',
                'data' => [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|confirmed|min:6',
                'password_confirmation' => 'required|string|min:6',
            ]);

            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()
                ], 422);
            }

            $user = User::findOrFail($id);
            $user->update(array_merge($validator->validated(),
                ['password' => bcrypt($request->password)]
            ));

            return response()->json([
                'status' => 'success',
                'message' => 'User successfully updated',
                'data' => [
                    'user_id' => $id,
                    'name' => $request->name,
                    'email' => $request->email
                ]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
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
            User::findOrFail($id)->delete();
            
            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
