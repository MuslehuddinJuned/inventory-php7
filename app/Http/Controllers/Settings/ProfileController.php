<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\User;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::get();
        return compact('users');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request['check']) DB::SELECT('INSERT INTO role_user(user_id, role_id) VALUES (?, ?)', [$request['user_id'], $request['role_id']]);
        else DB::SELECT('DELETE FROM role_user WHERE user_id = ? AND role_id = ?', [$request['user_id'], $request['role_id']]);
    }

    public function show($id)
    {
        $roles = DB::SELECT('SELECT A.id, user_id, role_id, name FROM(
            SELECT id, user_id, role_id, created_at, updated_at FROM role_user WHERE user_id = ?
            )A LEFT JOIN (SELECT id, name FROM roles
            )B ON A.role_id = B.id', [$id]);

        return compact('roles');
        
    }

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
        ]);

        return tap($user)->update($request->only('name', 'email'));
    }

    
}
