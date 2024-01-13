<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $users = User::all();
        $users = User::paginate();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);
        $request['password'] = bcrypt($request->password) ;
        $user = User::create($request->all());
        $request['remember_token'] = $user->createToken('LaravelAuthApp')->accessToken;
        $user->update($request->only(['remember_token']));
        $user->token = $request['remember_token'];
        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
         // check if currently authenticated user is admin
        if ($user->role_id !== 1) {
            return response()->json(['error' => 'You not Admin'], 403);
        }
        $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'required|min:8',
        ]);
        // $user->update($request->all());
        if(!empty($request->password)){
            $request['password'] = bcrypt($request->password) ;
            $user->update($request->only(['name', 'email','password']));
        }else{
            $user->update($request->only(['name', 'email']));
        }
        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //
        $user->delete();
        return response(null, 204);
    }

    public function login(Request $request)
    {
        //
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];
 
        if (auth()->attempt($data)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }
}
