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
        $users    = User::with(['hasSector', 'belongsSector']);
        $authUser = auth()->user();

        if ($request->role) {
            $users->where('role_id', $request->role->id);

            if ($request->role->tag === 'responsible') $users->doesntHave('hasSector');
        }

        if ($authUser->role->tag !== 'administrator') $users->whereHas('role', fn ($query) => $query->where('tag', '<>', 'administrator'));
        if ($authUser->role->tag === 'responsible') $users->where('sector_id', auth()->user()->sector->id)->where('id', '<>', $authUser->id);

        return response()->json([
            'message' => __('Users listed successfully'),
            'data'    => $users->get()
        ], 200);
    }

    public function store(StoreUser $request)
    {
        $data = $request->validated();

        User::create([
            'sector_id' => auth()->user()->role->tag === 'responsible' ? auth()->user()->sector->id : $data['sector'],
            'role_id'   => $data['role'],
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'active'    => $data['active'],
        ]);

        return response()->json([
            'message' => __('User created successfully')
        ], 200);
    }

    public function update(UpdateUser $request, $user)
    {
        $data = $request->validated();

        if ($data['password'] && !Hash::check($data['password'], $user->password)) $user->password = Hash::make($data['password']);

        $user->sector_id = auth()->user()->role->tag === 'responsible' ? auth()->user()->sector->id : $data['sector'];
        $user->role_id   = $data['role'];
        $user->name      = $data['name'];
        $user->email     = $data['email'];
        $user->active    = $data['active'];

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

    public function toggleDarkMode()
    {
        $user = auth()->user();
        $user->dark_mode = !$user->dark_mode;

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
