<?php

namespace App\Http\Controllers;

use App\Salary;
use Illuminate\Http\Request;
use DB;

class SalaryController extends Controller
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
        $Salary = DB::SELECT("SELECT B.id, A.id employee_id, A.employee_id official_id, first_name, designation, department, section, employee_image, 
            0 medic_alw_percent, 0 house_rent_percent, 0 ta_percent, 0 da_percent, 0 providant_fund_percent, 0 tax_percent, 0 fixed_allowance_percent,
            (CASE WHEN current_pay_doller IS NULL THEN 0 ELSE cast(current_pay_doller as decimal(12,2)) END)current_pay_doller, 
            (CASE WHEN basic_pay IS NULL THEN 0 ELSE cast(basic_pay as decimal(12,2)) END)basic_pay, 
            (CASE WHEN medic_alw IS NULL THEN 0 ELSE cast(medic_alw as decimal(12,2)) END)medic_alw, 
            (CASE WHEN house_rent IS NULL THEN 0 ELSE cast(house_rent as decimal(12,2)) END)house_rent, 
            (CASE WHEN ta IS NULL THEN 0 ELSE cast(ta as decimal(12,2)) END)ta, 
            (CASE WHEN da IS NULL THEN 0 ELSE cast(da as decimal(12,2)) END)da, 
            (CASE WHEN fixed_allowance IS NULL THEN 0 ELSE cast(fixed_allowance as decimal(12,2)) END)fixed_allowance, 
            (CASE WHEN providant_fund IS NULL THEN 0 ELSE cast(providant_fund as decimal(12,2)) END)providant_fund, 
            (CASE WHEN tax IS NULL THEN 0 ELSE cast(tax as decimal(12,2)) END)tax, 
            (CASE WHEN total_salary IS NULL THEN 0 ELSE cast(total_salary as decimal(12,2)) END)total_salary,  bank_name, acc_no FROM(
            SELECT id, employee_id, first_name, designation, department, section, employee_image, status, deleted_by FROM employees WHERE deleted_by = 0 and status = 'active'
            )A LEFT JOIN (SELECT id, current_pay_doller, basic_pay, medic_alw, house_rent, ta, da, providant_fund, tax, fixed_allowance, other_field, other_pay, total_salary, bank_name, acc_no, employee_id FROM salaries
            )B ON A.id = B.employee_id");

        return compact('Salary');
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
        $Salary = $request->user()->salary()->create($request->all());

        if(request()->expectsJson()){
            return response()->json([
                'SalaryId' => $Salary->id
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function show(Salary $salary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function edit(Salary $salary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Salary $salary)
    {
        $salary->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Salary  $salary
     * @return \Illuminate\Http\Response
     */
    public function destroy(Salary $salary)
    {
        //
    }
}
