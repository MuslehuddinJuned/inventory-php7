<?php

namespace App\Http\Controllers;

use App\Rechead;
use Illuminate\Http\Request;

class RecheadController extends Controller
{
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
        $Rechead = $request->user()->rechead()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'RecheadID' => $Rechead->id
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function show(Rechead $rechead)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function edit(Rechead $rechead)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rechead $rechead)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Rechead  $rechead
     * @return \Illuminate\Http\Response
     */
    public function destroy(Rechead $rechead)
    {
        //
    }
}
