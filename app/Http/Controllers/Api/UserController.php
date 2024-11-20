<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use App\Models\User;
// use Illuminate\Support\Facades\Validator;
use Validator;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($flag)
    {
        // p('Get Api Working');

        // $users = User::select('name', 'email')->where('status', 1)->get();
        $query = User::select('name', 'email');
        if ($flag == 1) {
            $query->where('status', 1);
        } elseif ($flag == 0) {
            $query->where('status', 0);
        } else {
            return response()->json([
                'message' => 'Invalid parameter passed,it can be either 1 or 0',
                'status' => 0
            ], 400);
        }
        $users = $query->get();
        if (count($users) > 0) {
            $response = [
                'message' => count($users) . ' user(s) found',
                'status' => 1,
                'data' => $users
            ];
        } else {
            $response = [
                'message' => 'No users found',
                'status' => 0
            ];
        }

        return response()->json($response, 200);

        // echo Count($users);
        // p($users);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $request->validate([
        //     'name' => ['required'],
        //     'email' => ['required','email'],
        //     'password' => ['required','min:8'],
        // ]);
        // p($request->all());

        $validator = Validator::make($request->all(), [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ];
        DB::beginTransaction();
        try {
            $user = User::create($data);
            DB::commit();
            return response()->json(['message' => 'User registered successfully'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = ['message' => 'User not Found', 'status' => 0];
        } else {
            $response = ['message' => 'User Found', 'status' => 1, 'data' => $user];
        }
        return response()->json($response, 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json(['message' => 'User Does Not Exits', 'status' => 0], 400);
        } else {
            DB::beginTransaction();
            try {
                //code...
                $user->name = $request['name'];
                $user->email = $request['email'];
                $user->contact = $request['contact'];
                $user->pincode = $request['pincode'];
                $user->address = $request['address'];
                $user->save();
                DB::commit();
            } catch (\Exception $err) {
                DB::rollBack();
                $user = null;
            }
            if (is_null($user)) {
                return response()->json([
                    'message' => 'Internal Server Error',
                    'status' => 0,
                    'error_msg' => $err->getMessage()
                ], 500);
            } else {
                return response()->json([
                    'message' => 'Data Updated Successfully',
                    'status' => 0,
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            $response = [
                'message' => "User doesn't exists",
                'status' => 0
            ];
            $responseCode = 404;
        } else {
            DB::beginTransaction();
            try {
                $user->delete();
                Db::commit();
                $response = [
                    'message' => "User Deleted Successfully",
                    'status' => 1
                ];
                $responseCode = 200;
            } catch (\Exception $e) {
                DB::rollBack();
                $response = [
                    'message' => 'Internal server error',
                    'status' => 0
                ];
                $responseCode = 500;
            }
        }
        return response()->json($response, $responseCode);
    }

    public function changePassword(Request $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) {
            return response()->json([
                'message' => "User doesn't exists",
                'status' => 0
            ], 400);
        } else {
            if (Hash::check($request['old_password'], $user->password)) {
                if ($request['new_password'] == $request['confirm_password']) {
                    DB::beginTransaction();
                    try {
                        $user->password = $request['new_password'];
                        $user->save();
                        DB::commit();
                    } catch (\Exception $e) {
                        $user = null;
                        DB::rollBack();
                    }

                    if (is_null($user)) {
                        return response()->json([
                            'message' => 'Internal Server Error',
                            'status' => 0,
                            'error_msg' => $e->getMessage()
                        ], 500);
                    } else {
                        return response()->json([
                            'message' => 'Password Updated Successfully',
                            'status' => 0,
                        ], 500);
                    }
                } else {
                    return response()->json([
                        'status' => 0,
                        'message' => 'New password and confirmed password does not match'
                    ], 400);
                }

                
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'old password does not match'
                ], 400);
            }
        }
    }
}
