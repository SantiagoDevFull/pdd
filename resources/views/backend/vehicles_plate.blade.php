@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Placa de Vehículos')
@section('subtitle', '')

@section('content')
	<vehicle-plate-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.vehicle_plate.store') }}'"
	></vehicle-plate-form>

	<vehicle-plate-table
        :url = "'{{ route('dashboard.vehicle_plate.list') }}'"
        :url_delete = "'{{ route('dashboard.vehicle_plate.delete') }}'"
        :url_detail = "'{{ route('dashboard.vehicle_plate.detail') }}'"
        :url_export_excel = "'{{ route('dashboard.vehicle_plate.export') }}'"
        
	></vehicle-plate-table>

	<vehicle-plate-modal
        :companies = "{{ $companies }}"
        :url_update = "'{{ route('dashboard.vehicle_plate.update') }}'"
	></vehicle-plate-modal>

	<loading></loading>
@endsection