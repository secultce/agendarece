<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\StoreUser;
use App\Http\Requests\UpdateUser;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function list(Request $request)
    {
        return response()->json([
            'message' => __('Users listed successfully'),
            'data'    => $request->role ? User::where('role_id', $request->role->id)->get() : User::all()
        ], 200);
    }

    public function store(StoreUser $request)
    {
        $data = $request->validated();

        User::create([
            'role_id'  => $data['role'],
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'active'   => $data['active'],
        ]);

        return response()->json([
            'message' => __('User created successfully')
        ], 200);
    }

    public function update(UpdateUser $request, $user)
    {
        $data = $request->validated();

        if ($data['password'] && !Hash::check($data['password'], $user->password)) $user->password = Hash::make($data['password']);

        $user->role_id = $data['role'];
        $user->name    = $data['name'];
        $user->email   = $data['email'];
        $user->active  = $data['active'];

        $user->save();

        return response()->json([
            'message' => __('User updated successfully')
        ], 200);
    }

    public function toggleActivation($user)
    {
        $user->active = !$user->active;

        $user->save();

        return response()->json([
            'message' => __('User updated successfully')
        ], 200);
    }

    public function destroy($user)
    {
        if ($user->id === auth()->user()->id) return abort(403, __('User in current session cannot be removed'));

        $user->delete();

        return response()->json([
            'message' => __('User removed successfully')
        ], 200);
    }
}
