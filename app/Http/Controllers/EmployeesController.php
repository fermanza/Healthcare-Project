<?php

namespace App\Http\Controllers;

use App\Person;
use App\Employee;
use App\PositionType;
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
        $employees = Employee::with('person', 'status', 'positionType', 'manager.person')
            ->where('active', true)->get();

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
        $action = 'create';
        $view = 'admin.employees.create';

        return $this->form($employee, $action, $view);
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
        $action = 'edit';
        $view = 'admin.employees.edit';

        return $this->form($employee, $action, $view);
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

    /**
     * Show the form for the specified resource.
     *
     * @param  \App\Employee  $employee
     * @param  string  $action
     * @param  string  $view
     * @return \Illuminate\Http\Response
     */
    protected function form($employee, $action, $view)
    {
        $employee->load('person');
        $people = Person::where('active', true)->get()->sortBy->fullName();
        $statuses = EmployementStatus::orderBy('name')->get();
        $positionTypes = PositionType::where('active', true)->orderBy('name')->get();
        $managers = Employee::with('person')
            ->whereIn('positionTypeId', [
                config('instances.position_types.manager'),
                config('instances.position_types.director'),
            ])
            ->get()->sortBy->fullName();

        $params = compact(
            'employee', 'people', 'statuses', 'positionTypes', 'managers', 'action'
        );

        return view($view, $params);
    }
}
