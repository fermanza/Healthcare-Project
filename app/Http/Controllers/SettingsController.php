<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Settings Credentials view.
     *
     * @return \Illuminate\Http\Response
     */
    public function credentials()
    {
        return view('admin.settings.credentials');
    }

    /**
     * Settings Update Credentials.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateCredentials(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'email' => 'nullable|email',
            'password' => 'nullable|min:6',
        ]);

        $user = $request->user();

        if (! password_verify($request->current_password, $user->password)) {
            flash(__('Current Password incorrect.'), 'error');

            return back()->exceptInput('current_password');
        }

        $user->email = $request->email ?: $user->email;
        $user->password = $request->password ? bcrypt($request->password) : $user->password;
        $user->save();

        flash(__('Credentials Updated.'));

        return redirect()->route('admin.accounts.index');
    }
}
