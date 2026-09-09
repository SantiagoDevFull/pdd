@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Seguridad')
@section('subtitle', 'Usuarios')

@section('content')
	<security-form
    	:url="'{{ route('dashboard.security.store') }}'"
	></security-form>

	<security-table
		:url_list="'{{ route('dashboard.security.list') }}'"
		:url_delete="'{{ route('dashboard.security.delete') }}'"
		:url_detail="'{{ route('dashboard.security.detail') }}'"
		:url_export_record="'{{ route('dashboard.security.export') }}'"
	></security-table>

	<security-modal
    	:url_update="'{{ route('dashboard.security.update') }}'"
	></security-modal>

	<loading></loading>
@endsection
