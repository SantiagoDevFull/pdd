@extends('backend.templates.app')

@section('title', 'Blue Line')
@section('subtitle', 'Gestión de mantenimientos')

@section('content')

	<blue-maintenance-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.gestion.blue_maintenance.validate_form') }}'"
	></blue-maintenance-form>

	<blue-maintenance-table
		:url = "'{{ route('dashboard.gestion.blue_maintenance.list') }}'"
		:url_detail = "'{{ route('dashboard.gestion.blue_maintenance.detail') }}'"
		:url_delete = "'{{ route('dashboard.gestion.blue_maintenance.delete') }}'"
	></blue-maintenance-table>

	<blue-maintenance-modal
	></blue-maintenance-modal>

	<loading></loading>
@endsection