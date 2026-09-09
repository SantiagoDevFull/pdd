@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Asignación de Vehículo a Cliente')
@section('subtitle', '')

@section('content')

	<assign-vehicle-table
        :url = "'{{ route('dashboard.assign_vehicle.list') }}'"
        :url_delete = "'{{ route('dashboard.assign_vehicle.delete_asign') }}'"
        :url_view_asign = "'{{ route('dashboard.assign_vehicle.view_asign') }}'"
	></assign-vehicle-table>

    <assign-vehicle-modal
    :url = "'{{ route('dashboard.assign_vehicle.asignar') }}'"
    :url_get_clients = "'{{ route('dashboard.assign_vehicle.list_clients') }}'">
    </assign-vehicle-modal>

	<loading></loading>
@endsection