<?php

namespace App\Http\Controllers;

use App\Inventoryreceive;
use Illuminate\Http\Request;

class InventoryreceiveController extends Controller
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
        $Inventoryreceive = Inventoryreceive::get();
        return compact ('Inventoryreceive');
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
        $InventoryReceive = $request->user()->inventoryreceives()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'InventoryReceiveId' => $InventoryReceive->id
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Inventoryreceive  $inventoryreceive
     * @return \Illuminate\Http\Response
     */
    public function show(Inventoryreceive $inventoryreceive)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Inventoryreceive  $inventoryreceive
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventoryreceive $inventoryreceive)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Inventoryreceive  $inventoryreceive
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventoryreceive $inventoryreceive)
    {
        $inventoryreceive->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Inventoryreceive  $inventoryreceive
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventoryreceive $inventoryreceive)
    {
        $inventoryreceive->delete();
    }
}
