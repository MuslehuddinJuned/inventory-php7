<?php

namespace App\Http\Controllers;

use App\Wagehike;
use DB;
use Illuminate\Http\Request;

class WagehikeController extends Controller
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
        $Increment = DB::SELECT("SELECT A.id, employee_id, first_name, last_name, designation, department, section, work_location, start_date, 
        employee_image, effective_date, amount, remarks, file_link, next_increment, total_salary salary FROM (
        SELECT A.id, A.employee_id, first_name, last_name, designation, department, section, work_location, start_date, 
        employee_image, effective_date, amount, remarks, file_link, total_salary,
        (CASE WHEN effective_date IS null THEN (CASE WHEN department != 'Management' THEN DATE_ADD(start_date, INTERVAL 6 MONTH) ELSE DATE_ADD(start_date, INTERVAL 12 MONTH) END) ELSE DATE_ADD(effective_date, INTERVAL 12 MONTH) END)next_increment FROM (
            SELECT id, employee_id, first_name, last_name, designation, department, section, work_location, start_date, employee_image FROM employees WHERE deleted_by = 0 AND status = 'active'
            )A LEFT JOIN (SELECT A.effective_date, A.employee_id, next_increment, amount, remarks, file_link FROM (
                SELECT MAX(effective_date)effective_date, employee_id FROM wagehikes GROUP BY employee_id
                )A LEFT JOIN (SELECT effective_date, employee_id, next_increment, amount, remarks, file_link FROM wagehikes
                )B ON A.effective_date = B.effective_date AND A.employee_id = B.employee_id
            )B ON A.id = B.employee_id LEFT JOIN (SELECT basic_pay, medic_alw, house_rent, ta, da, providant_fund, tax, total_salary, bank_name, acc_no, employee_id FROM salaries
            )C ON A.id = C.employee_id
        )A ORDER BY next_increment");

        return compact('Increment');
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
        $Wagehike = $request->user()->wagehike()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'Wagehike' => $Wagehike
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Wagehike  $wagehike
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $Wagehike = Wagehike::where('employee_id', $id)->orderBy('effective_date', 'desc')->get();
        return compact('Wagehike');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Wagehike  $wagehike
     * @return \Illuminate\Http\Response
     */
    public function edit(Wagehike $wagehike)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Wagehike  $wagehike
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Wagehike $wagehike)
    {
        $wagehike->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Wagehike  $wagehike
     * @return \Illuminate\Http\Response
     */
    public function destroy(Wagehike $wagehike)
    {
        $wagehike->delete();
    }
}
