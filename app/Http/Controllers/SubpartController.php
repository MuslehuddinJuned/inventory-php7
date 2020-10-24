<?php

namespace App\Http\Controllers;

use App\Subpart;
use DB;
use Illuminate\Http\Request;

class SubpartController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Subpart  $subpart
     * @return \Illuminate\Http\Response
     */
    public function show(Subpart $subpart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subpart  $subpart
     * @return \Illuminate\Http\Response
     */
    public function edit(Subpart $subpart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subpart  $subpart
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subpart $subpart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subpart  $subpart
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subpart $subpart)
    {
        //
    }
}
