@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Blue Line')
@section('subtitle', 'Gestión de Choferes')

@section('content')
	<blue-driver-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.blue_driver.store') }}'"
	></blue-driver-form>

	<blue-driver-table
        :url = "'{{ route('dashboard.blue_driver.list') }}'"
        :url_delete = "'{{ route('dashboard.blue_driver.delete') }}'"
        :url_detail = "'{{ route('dashboard.blue_driver.detail') }}'"
        :url_export_excel = "'{{ route('dashboard.blue_driver.export') }}'"

	></blue-driver-table>

	<blue-driver-modal
        :companies = "{{ $companies }}"
        :url_update = "'{{ route('dashboard.blue_driver.update') }}'"
	></blue-driver-modal>

	<loading></loading>
@endsection
