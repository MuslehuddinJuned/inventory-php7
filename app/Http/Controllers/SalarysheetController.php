<?php

namespace App\Http\Controllers;

use App\Salarysheet;
use DB;
use Illuminate\Http\Request;

class SalarysheetController extends Controller
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
     * @param  \App\Salarysheet  $salarysheet
     * @return \Illuminate\Http\Response
     */
    public function show(Salarysheet $salarysheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Salarysheet  $salarysheet
     * @return \Illuminate\Http\Response
     */
    public function edit(Salarysheet $salarysheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Salarysheet  $salarysheet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Salarysheet $salarysheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Salarysheet  $salarysheet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salarysheet $salarysheet)
    {
        //
    }
}
