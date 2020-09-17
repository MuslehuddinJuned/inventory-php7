<?php

namespace App\Http\Controllers;

use App\Holiday;
use Illuminate\Http\Request;

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
        $HolidayList = Holiday::orderBy('yearly_holiday', 'asc')->orderBy('weekly_holiday', 'asc')->get();
        return view('holiday.index', compact('HolidayList'));
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
        if($request->weekly_holiday != ''){            
            $this->validate($request, [
                'weekly_holiday'=> 'unique:holidays,weekly_holiday'
            ]); 
        } else {
            $this->validate($request, [
                'yearly_holiday'=> 'unique:holidays,yearly_holiday'
            ]); 
        }

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
        //
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

        $HolidayList = Holiday::orderBy('yearly_holiday', 'asc')->orderBy('weekly_holiday', 'asc')->get();
        if(request()->expectsJson()){
            return response()->json([
                'HolidayList' => $HolidayList
            ]);
        } 
    }
}
