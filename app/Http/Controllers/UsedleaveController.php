<?php

namespace App\Http\Controllers;

use App\Usedleave;
use Illuminate\Http\Request;
use DB;

class UsedleaveController extends Controller
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
        $this->validate($request, [
            'leave_type'=> 'required'
        ]);
        
        $Usedleave = $request->user()->usedleave()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'Usedleave' => $Usedleave
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Usedleave  $usedleave
     * @return \Illuminate\Http\Response
     */
    public function show($year)
    {
        $Usedleave = DB::SELECT("SELECT id, B.employee_id, first_name, designation, department, start_date, (earned_day/18)earned_day,
        (CASE WHEN casual_leave IS null THEN 0 ELSE casual_leave END)casual_leave,
        (CASE WHEN sick_leave IS null THEN 0 ELSE sick_leave END)sick_leave,
        (CASE WHEN annual_leave IS null THEN 0 ELSE annual_leave END)annual_leave,
        (CASE WHEN earned_leave IS null THEN 0 ELSE earned_leave END)earned_leave,
        (CASE WHEN maternity_leave IS null THEN 0 ELSE maternity_leave END)maternity_leave,
        (CASE WHEN paternity_leave IS null THEN 0 ELSE paternity_leave END)paternity_leave,
        (CASE WHEN half_leave IS null THEN 0 ELSE half_leave END)half_leave,
        (CASE WHEN compensatory_leave IS null THEN 0 ELSE compensatory_leave END)compensatory_leave, 
        (CASE WHEN unpaid_leave IS null THEN 0 ELSE unpaid_leave END)unpaid_leave, 
        (CASE WHEN half_leave IS null THEN 0 ELSE half_leave END)half_leave 
        FROM(SELECT employee_id, 
        SUM(CASE WHEN leave_type = 'casual_leave' THEN day_count ELSE 0 END)casual_leave,
        SUM(CASE WHEN leave_type = 'sick_leave' THEN day_count ELSE 0 END)sick_leave,
        SUM(CASE WHEN leave_type = 'annual_leave' THEN day_count ELSE 0 END)annual_leave,
        SUM(CASE WHEN leave_type = 'earned_leave' THEN day_count ELSE 0 END)earned_leave,
        SUM(CASE WHEN leave_type = 'maternity_leave' THEN day_count ELSE 0 END)maternity_leave,
        SUM(CASE WHEN leave_type = 'paternity_leave' THEN day_count ELSE 0 END)paternity_leave,
        SUM(CASE WHEN leave_type = 'half_leave' THEN day_count ELSE 0 END)half_leave,
        SUM(CASE WHEN leave_type = 'compensatory_leave' THEN day_count ELSE 0 END)compensatory_leave,
        SUM(CASE WHEN leave_type = 'unpaid_leave' THEN day_count ELSE 0 END)unpaid_leave
        FROM(SELECT leave_type,day_count, employee_id, leave_start FROM usedleaves WHERE deleted_by = 0 AND YEAR(leave_start) = ?
            )A GROUP BY employee_id
        )A  RIGHT JOIN (SELECT id, employee_id, first_name, designation, department, start_date FROM employees WHERE status = 'active' and deleted_by = 0
        )B ON A.employee_id = B.id LEFT JOIN (SELECT ac_no, SUM(earned_day)earned_day FROM (SELECT ac_no, (CASE WHEN (CHAR_LENGTH(in_time_1) > 0 && in_time_1 != '00:00') THEN COUNT(in_time_1) END)earned_day FROM attendances WHERE YEAR(date) = ? GROUP BY ac_no, in_time_1)A GROUP BY ac_no
        )C ON B.employee_id = C.ac_no", [$year, $year]);

        $Leave = DB::SELECT("SELECT year, casual_leave, sick_leave, annual_leave, maternity_leave, paternity_leave, compensatory_leave, 
            unpaid_leave, half_leave FROM leaves WHERE year = ?", [$year]);
        
        return compact('Usedleave', 'Leave');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Usedleave  $usedleave
     * @return \Illuminate\Http\Response
     */
    public function edit(Usedleave $usedleave)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Usedleave  $usedleave
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Usedleave $usedleave)
    {
        $usedleave->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Usedleave  $usedleave
     * @return \Illuminate\Http\Response
     */
    public function destroy(Usedleave $usedleave)
    {
        $usedleave->delete();
    }
}
