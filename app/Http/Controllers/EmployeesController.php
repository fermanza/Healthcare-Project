<?php

namespace App\Http\Controllers;

use App\Person;
use App\Employee;
use App\EmployementStatus;
use Illuminate\Http\Request;
use App\Http\Requests\EmployeeRequest;

class EmployeesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $employees = Employee::with('person', 'status')->where('active', true)->get();

        return view('admin.employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $employee = new Employee;
        $people = Person::where('active', true)->get()->sortBy->fullName();
        $statuses = EmployementStatus::orderBy('name')->get();
        $action = 'create';

        $params = compact('employee', 'people', 'statuses', 'action');

        return view('admin.employees.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EmployeeRequest $request)
    {
        $employee = new Employee;
        $request->save($employee);

        flash(__('Employee created.'));

        return redirect()->route('admin.employees.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function edit(Employee $employee)
    {
        $employee->load('person');
        $people = Person::where('active', true)->get()->sortBy->fullName();
        $statuses = EmployementStatus::orderBy('name')->get();
        $action = 'edit';

        $params = compact('employee', 'people', 'statuses', 'action');

        return view('admin.employees.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EmployeeRequest  $request
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function update(EmployeeRequest $request, Employee $employee)
    {
        $request->save($employee);

        flash(__('Employee updated.'));

        return redirect()->route('admin.employees.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employee  $employee
     * @return \Illuminate\Http\Response
     */
    public function destroy(Employee $employee)
    {
        $employee->active = false;
        $employee->save();

        flash(__('Employee deleted.'));

        return back();
    }
}
