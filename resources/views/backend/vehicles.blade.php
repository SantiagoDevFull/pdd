@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Vehículos')
@section('subtitle', '')

@section('content')
	<vehicle-form
		:companies = "{{ $companies }}"
		:modelos = "{{ $modelos }}"
		:brands = "{{ $brands }}"
		:fuel_types = "{{ $fuel_types }}"
		:body_types = "{{ $body_types }}"
		:url = "'{{ route('dashboard.vehicle.store') }}'"
	></vehicle-form>

	<vehicle-table
        :url = "'{{ route('dashboard.vehicle.list') }}'"
        :url_delete = "'{{ route('dashboard.vehicle.delete') }}'"
        :url_detail = "'{{ route('dashboard.vehicle.detail') }}'"
        :url_export_excel = "'{{ route('dashboard.vehicle.export') }}'"
        
	></vehicle-table>

	<vehicle-modal
        :companies = "{{ $companies }}"
		:modelos = "{{ $modelos }}"
		:brands = "{{ $brands }}"
		:fuel_types = "{{ $fuel_types }}"
		:body_types = "{{ $body_types }}"
        :url_update = "'{{ route('dashboard.vehicle.update') }}'"
	></vehicle-modal>

	<loading></loading>
@endsection