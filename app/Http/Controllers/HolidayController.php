<?php

namespace App\Http\Controllers;

use App\Holiday;
use Illuminate\Http\Request;
use DB;

class HolidayController extends Controller
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
        $HolidayList = Holiday::orderBy('yearly_holiday', 'asc')->get();
        $remainHoliday = DB::select('SELECT event, yearly_holiday FROM holidays WHERE yearly_holiday >= CURDATE() ORDER BY yearly_holiday ASC');

        return compact('HolidayList', 'remainHoliday');
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
        $this->validate($request, [
            'yearly_holiday'=> 'required|unique:holidays,yearly_holiday'
        ]);

        $NewWeeklyHoliday = $request->user()->holidays()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'NewWeeklyHoliday' => $NewWeeklyHoliday
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function show(Holiday $holiday)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function edit(Holiday $holiday)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Holiday $holiday)
    {
        $this->validate($request, [
            'yearly_holiday'=> 'required|unique:holidays,yearly_holiday,'.$holiday->id
        ]);

        $holiday->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Holiday  $holiday
     * @return \Illuminate\Http\Response
     */
    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        $HolidayList = Holiday::orderBy('yearly_holiday', 'asc')->get();
        if(request()->expectsJson()){
            return response()->json([
                'HolidayList' => $HolidayList
            ]);
        } 
    }
}
