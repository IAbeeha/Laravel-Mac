<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
    //
    public function login(Request $request)
    {

        $validate_fields = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:4']
        ]);
        $user = User::where('email', $validate_fields['email'])->first();
 
    if (!$user) {
        // User not found
        return "User not found.";
    }
    if (Hash::check($validate_fields['password'], $user->password)) {
        // Password is correct
        auth()->login($user);
        $request->session()->regenerate();
        return "User logged in.";
    } else {
        // Password is incorrect
        return "Incorrect password.";
    }
        // if (auth()->attempt(['email' => $validate_fields['email'], 'password' => $validate_fields['password']])) {
        //     $request->session()->regenerate();
        // }
        // return "user logged in";
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }

    public function register(Request $request)
    {
        // $request = json_decode($request);
        // print_r($request);
        // echo $request;
        if (User::where('email', $request['email'])->first()) {
            return "Email already taken";
        } elseif (strlen($request['password']) < 5) {
            return "The length of the password should be atleast 5 characters.";
        } else {
            $validate_fields = $request->validate([
                'name' => 'required',
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'password' => ['required', 'min:4']
            ]);
            $validate_fields['password'] = bcrypt($validate_fields['password']);
            $user = User::create($validate_fields);
            auth()->login($user);
            return "user created";
        }
    }

    public function editPage(User $user)
    {
        if (auth()->user()->id !== $user['id']) {
            return redirect('/');
        }
        return view('edit_user', ['user' => $user]);
    }
    //

    public function userEdit(User $user, Request $request)
    {
        if (auth()->user()->id !== $user['id']) {
            return redirect('/');
        }
        $incomingFields = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email'],
            'password' => ['required', 'min:4']
        ]);
        $incomingFields['name'] = strip_tags($incomingFields['name']);
        $incomingFields['email'] = strip_tags($incomingFields['email']);
        $incomingFields['password'] = strip_tags($incomingFields['password']);

        if (User::where('email', $incomingFields['email'])->first() && auth()->user()->email !== $incomingFields['email']) {
            return "Email already taken";
        } else {
            $user->update($incomingFields);
            return redirect('/');
        }
    }

    public function deleteUser(User $user)
    {
        if (auth()->user()->id === $user['id']) {
            // $user->delete();
            $user->Posts()->delete();
            $user->delete();
        }
        return redirect('/');
    }
}
