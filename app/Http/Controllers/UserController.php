<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $sid)
    {
        if ($request->cond_user !='') {
            $query = User::query();
            $query->where(function ($query) use ($request) {
                $query->where('last_name', 'like', $request->cond_user . '%')
                ->orWhere('first_name', 'like', $request->cond_user. '%')
                ->orWhere('email', 'like', $request->cond_user. '%')
                ->orWhere('phone', 'like', $request->cond_user. '%');
            });
            return $query->take(5)->get();
        } else {
            return '';
        }
        // $cond_name = $request->name;
        // if ($cond_name !='') {
        //     $users = User::where('screen_name', 'like', '%'.$cond_name. '%')->take(5)->get();
        // } else {
        //     $users = User::inRandomOrder()->take(5)->get();
        // }
        // return $users;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
