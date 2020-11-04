<?php

namespace App\Http\Controllers;

use App\Employee;
use DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
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
        $EmployeeList = DB::SELECT("SELECT id, employee_id, CONCAT(CASE WHEN last_name IS NULL THEN '' ELSE last_name END, CASE WHEN (first_name IS NOT NULL AND last_name IS NOT NULL) THEN  ', ' ELSE '' END, CASE WHEN first_name IS NULL THEN '' ELSE first_name END)AS name, 
        first_name, last_name,  address, mobile_no, email, blood_group, gender, date_of_birth, marital_status, designation, department, section, work_location, start_date, 
        contact_name, contact_address, contact_phone, relationship, employee_image, status, weekly_holiday, start_time, end_time, user_id, deleted_by, created_at, updated_at
        FROM employees WHERE deleted_by = 0 and status = 'active'");

        return compact('EmployeeList');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $EmployeeList = DB::SELECT("SELECT id, employee_id, CONCAT(CASE WHEN last_name IS NULL THEN '' ELSE last_name END, CASE WHEN (first_name IS NOT NULL AND last_name IS NOT NULL) THEN  ', ' ELSE '' END, CASE WHEN first_name IS NULL THEN '' ELSE first_name END)AS name, first_name, last_name,  address, mobile_no, email, blood_group, gender, date_of_birth, marital_status, designation, department, section, work_location, start_date, contact_name, contact_address, contact_phone, relationship, employee_image, status, user_id, deleted_by, created_at, updated_at
        FROM employees WHERE deleted_by = 0");

        return $EmployeeList;
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
            'employee_id'=> 'required|unique:employees,employee_id'
        ]);

        if($request->weekly_holiday){
            $holiday = json_encode($request->weekly_holiday);
        }

        if($request->employee_image){
            $exploded = explode(',', $request->employee_image);
            $decoded = base64_decode($exploded[1]);
    
            if(str_contains($exploded[0], 'png'))
                $extesion = 'png';
            else
                $extesion = 'jpg';
    
            $fileName = str_random().'.'.$extesion;
            $path = public_path().'/images/employee/'.$fileName;
            // $path = '/home/sustipe/inventory.sustipe.com/images/employee/'.$fileName;
    
            // store new image
            file_put_contents($path, $decoded);
            $employeeList = $request->user()->employees()->create($request->except('employee_image', 'weekly_holiday') + [
                'employee_image' => $fileName,
                'weekly_holiday' => $holiday
            ]);
        } else {
            $employeeList = $request->user()->employees()->create($request->except('employee_image', 'weekly_holiday') + [
                'weekly_holiday' => $holiday
            ]);
        }

        if(request()->expectsJson()){
            return response()->json([
                'employeeList' => $employeeList
            ]);
        } 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //for employee exit
        $EmployeeList = DB::SELECT("SELECT id, employee_id, CONCAT(CASE WHEN last_name IS NULL THEN '' ELSE last_name END, CASE WHEN (first_name IS NOT NULL AND last_name IS NOT NULL) THEN  ', ' ELSE '' END, CASE WHEN first_name IS NULL THEN '' ELSE first_name END)AS name, 
        first_name, last_name,  address, mobile_no, email, blood_group, gender, date_of_birth, marital_status, designation, department, section, work_location, start_date, 
        contact_name, contact_address, contact_phone, relationship, employee_image, status, weekly_holiday, start_time, end_time, exit_type, reason, resign_date, effective_date, user_id, deleted_by, created_at, updated_at
        FROM employees WHERE deleted_by = 0 and status != 'active'");

        return compact('EmployeeList');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Employee $employee)
    { 
        if($request->exit_type) {
            $employee->update($request->all());
        } else {
            $this->validate($request, [
                'employee_id'=> 'required|unique:employees,employee_id,'.$employee->id
            ]);

            if($request->weekly_holiday){
                $holiday = json_encode($request->weekly_holiday);
            }

            if($request->employee_image){
                $exploded = explode(',', $request->employee_image);
                $decoded = base64_decode($exploded[1]);
        
                if(str_contains($exploded[0], 'png'))
                    $extesion = 'png';
                else
                    $extesion = 'jpg';
        
                $fileName = str_random().'.'.$extesion;
                $path = public_path().'/images/employee/'.$fileName;
                // $path = '/home/sustipe/inventory.sustipe.com/images/employee/'.$fileName;

                // store new image
                file_put_contents($path, $decoded);
                
                // delete previous image
                $Employee = Employee::find($employee->id);

                if($Employee->employee_image != 'noimage.jpg'){
                    //Delete Image
                    $path = public_path().'/images/employee/'.$Employee->employee_image;
                    // $path = '/home/sustipe/inventory.sustipe.com/images/employee/'.$Employee->employee_image;
                    @unlink($path);
                }

                // save Data
                $employee->update($request->except('employee_image', 'weekly_holiday') + [
                    'employee_image' => $fileName,
                    'weekly_holiday' => $holiday
                ]);

            } else {
                $employee->update($request->except('employee_image', 'weekly_holiday') + [
                    'weekly_holiday' => $holiday
                ]);

                $image = Employee::select('employee_image')->where('id', $request->id)->get();
                $fileName = $image[0]['employee_image'];
            }

            if(request()->expectsJson()){
                return response()->json([
                    'fileName' => $fileName
                ]);
            }  
        }     
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        //
    }
}
