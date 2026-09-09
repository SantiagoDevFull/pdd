@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Choferes')
@section('subtitle', '')

@section('content')
	<employee-form
		:document_types = "{{ $document_types }}"
		:employee_types = "{{ $employee_types }}"
		:url = "'{{ route('dashboard.employee.store') }}'"
	></employee-form>

	<employee-table
        :url = "'{{ route('dashboard.employee.list') }}'"
        :url_delete = "'{{ route('dashboard.employee.delete') }}'"
        :url_detail = "'{{ route('dashboard.employee.detail') }}'"
        :url_export_excel = "'{{ route('dashboard.employee.export') }}'"
        
	></employee-table>

	<employee-modal
        :document_types = "{{ $document_types }}"
        :employee_types = "{{ $employee_types }}"
        :url_update = "'{{ route('dashboard.employee.update') }}'"
	></employee-modal>

	<loading></loading>
@endsection