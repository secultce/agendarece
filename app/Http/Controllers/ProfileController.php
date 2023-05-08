<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('profile')->with('user', auth()->user());
    }

    public function update(UpdateProfile $request, $user)
    {
        $data = $request->validated();

        if (isset($data['avatar'])) {
            if ($user->avatar) Storage::delete($user->avatar);

            $user->avatar = $data['avatar']->store('avatars', 'public');
        }

        if (isset($data['password']) && !Hash::check($data['password'], $user->password)) $user->password = Hash::make($data['password']);

        $user->name  = $data['name'];
        $user->email = $data['email'];

        $user->save();

        return redirect()
            ->route('profile')
            ->with('status', __('Profile updated successfully'))
        ;
    }
}
