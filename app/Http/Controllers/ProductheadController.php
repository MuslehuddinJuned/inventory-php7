<?php

namespace App\Http\Controllers;

use App\Producthead;
use Illuminate\Http\Request;
use DB;

class ProductheadController extends Controller
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
     * @param  \App\Producthead  $producthead
     * @return \Illuminate\Http\Response
     */
    public function show(Producthead $producthead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Producthead  $producthead
     * @return \Illuminate\Http\Response
     */
    public function edit(Producthead $producthead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Producthead  $producthead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producthead $producthead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Producthead  $producthead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producthead $producthead)
    {
        //
    }
}
