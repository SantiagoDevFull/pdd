<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\DocumentType;
use App\Employee;
use App\EmployeeType;
use App\Exports\EmployeeReportExport;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index()
    {

        $document_types = DocumentType::select('id', 'name')->get();

        if (Auth::user()->id == 45) {
            $employee_types = EmployeeType::select('id', 'name')->whereIn('id', [7])->get();
        } else {
            $employee_types = EmployeeType::select('id', 'name')->get();
        }

        return view('backend.employees')->with(compact('document_types', 'employee_types'));
    }

    public function validateForm()
    {
        $messages = [
            'document_type_id.required'    => 'Debe seleccionar Tipo de Documento.',
            'document_number.required'     => 'Debe completar el N° Documento.',
            'first_name.required'          => 'Debe completar los Apellidos.',
            'last_name.required'           => 'Debe completar los Nombres.',
            'employee_type_id.required'    => 'Debe seleccionar Tipo.',
        ];

        $rules = [
            'document_type_id' => 'required',
            'document_number'  => 'required',
            'first_name'       => 'required',
            'last_name'        => 'required',
            'employee_type_id' => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function list()
    {

        $q = request('query');
        $search = $q['generalSearch'];

        $employee = Employee::join('document_types', 'document_types.id', '=', 'employees.document_type_id')
            ->leftJoin('employee_types', 'employee_types.id', '=', 'employees.employee_type_id')
            ->select(
                'employees.id',
                'document_types.name as document_type_name',
                'employees.document_number',
                DB::raw('CONCAT(employees.first_name," ",employees.last_name) as name_complete'),
                'employees.license',
                'employee_types.name as employee_type_name'
            );

        if (Auth::user()->id == 45) {
            $employee->where('employees.employee_type_id', 7);
        }

        $employee = $employee
            ->orderBy('employees.id', 'desc')
            ->get();


        return $employee;
    }

    public function update()
    {
        $this->validateModalForm();

        $id = request('id');
        $document_type_id = request('document_type_id');
        $document_number = request('document_number');
        $first_name = request('first_name');
        $last_name = request('last_name');
        $employee_type_id = request('employee_type_id');

        $employee = Employee::findOrFail($id);
        $employee->document_type_id = $document_type_id;
        $employee->document_number = $document_number;
        $employee->first_name = $first_name;
        $employee->last_name = $last_name;
        $employee->license = 'Q' . $document_number;
        $employee->employee_type_id = $employee_type_id; //VENTA PLANTA
        $employee->update();


        $type = 1;
        $title = '¡Ok!';
        $msg = 'Chofer actualizado exitosamente.';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }

    public function exportExcel()
    {

        $employees = Employee::leftjoin('document_types', 'document_types.id', 'employees.document_type_id')
            ->leftjoin('employee_types', 'employee_types.id', 'employees.employee_type_id')
            ->select(
                'employees.id as id',
                'document_types.name as document_type_name',
                'employees.document_number as document_number',
                DB::Raw('CONCAT(employees.first_name," ",employees.last_name) as name_complete'),
                'employees.license as license',
                'employee_types.name as employee_type_name'
            );

        if (Auth::user()->id == 45) {
            $employees->where('employees.employee_type_id', 7);
        }

        $employees = $employees
            ->orderBy('employees.id', 'desc')
            ->get();


        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new EmployeeReportExport($datetime, $employees);
        return Excel::download($excel, 'reporte-choferes.xlsx');
    }

    public function detail()
    {

        $id = request('id');
        $employee = Employee::findOrFail($id);

        return $employee;
    }

    public function delete()
    {
        $id = request('id');
        $element = Employee::findOrFail($id);
        $element->delete();
    }

    public function validateModalForm()
    {
        $messages = [
            'document_type_id.required'                => 'El Código de Artículo es obligatorio.',
            'document_number.required'                => 'El Código de Artículo es obligatorio.',
            'first_name.required'                    => 'El Código de Artículo es obligatorio.',
            'last_name.required'                    => 'El Código de Artículo es obligatorio.',
            'employee_type_id.required'                => 'El Código de Artículo es obligatorio.'
        ];

        $rules = [
            'document_type_id'              => 'required',
            'document_number'               => 'required',
            'first_name'                    => 'required',
            'first_name'                    => 'required',
            'employee_type_id'              => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {

        $this->validateForm();

        $document_type_id = request('document_type_id');
        $document_number = request('document_number');
        $first_name = request('first_name');
        $last_name = request('last_name');
        $employee_type_id = request('employee_type_id');

        $employee = new Employee();
        $employee->document_type_id = $document_type_id;
        $employee->document_number = $document_number;
        $employee->first_name = $first_name;
        $employee->last_name = $last_name;
        $employee->license = 'Q' . $document_number;
        $employee->employee_type_id = $employee_type_id; //VENTA PLANTA
        $employee->save();

        $type = 1;
        $title = '¡Ok!';
        $msg = 'Chofer creado exitosamente.';

        return response()->json([
            'type'    => $type,
            'title'    => $title,
            'msg'    => $msg,
        ]);
    }
}
