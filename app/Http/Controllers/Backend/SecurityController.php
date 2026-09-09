<?php

namespace App\Http\Controllers\Backend;

use App\ClientRoute;
use App\Exports\SecurityReportExport;
use App\Http\Controllers\Controller;
use App\User;
use App\WarehouseMovement;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class SecurityController extends Controller
{
    public function index()
    {

        

        return view('backend.security');
    }

    public function validateForm()
    {
        $messages = [
            'name.required'         => 'Debe ingresar un nombre.',
            'user.required'         => 'Debe ingresar un usuario.',
            'password.required'     => 'Debe ingresar una contraseña.'
        ];

        $rules = [
            'name'                  => 'required',
            'user'                  => 'required',
            'password'              => 'required'
        ];

        request()->validate($rules, $messages);
        return request()->all();
    }

    public function store()
    {
        $this->validateForm();

        $name = request('name');
        $user = request('user');
        $password = bcrypt(request('password'));

        $element = new User();
        $element->name = $name;
        $element->user = $user;
        $element->password = $password;
        $element->remember_token = $password;
        $element->save();

        $type = 1;
        $title = 'Bien';
        $msg = 'Usuario creado exitosamente.';

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg
        ]);
    }

    public function list()
    {
        $users = User::select('id', 'name', 'user', 'created_at', 'updated_at')->get();
        return $users;
    }

    public function delete()
    {
        $element = User::findOrFail(request('id'));
        $element->delete();
    }

    public function detail()
    {
        $element = User::select('id', 'name', 'user', 'password')->findOrFail(request('id'));
        return $element;
    }

    public function update()
    {
        $this->validateForm();

        $id = request('id');
        $name = request('name');
        $user = request('user');
        $password = bcrypt(request('password'));

        $element = User::findOrFail($id);
        $element->name = $name;
        $element->user = $user;
        $element->password = $password;
        $element->remember_token = $password;
        $element->update();

        $type = 1;
        $title = 'Bien';
        $msg = 'Usuario actualizado exitosamente.';

        return response()->json([
            'type'  => $type,
            'title' => $title,
            'msg'   => $msg
        ]);
    }

    public function export()
    {
        $users = User::select('id', 'name', 'user', 'created_at', 'updated_at')->get();
        $datetime = CarbonImmutable::now()->format('d/m/Y h:i:s a');

        $excel = new SecurityReportExport($datetime, $users);
        return Excel::download($excel, 'reporte-usuarios.xlsx');
    }
}
