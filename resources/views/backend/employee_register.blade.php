@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Artículos')
@section('subtitle', '')

@section('content')

	<article-register-form
		:warehouse_types = "{{ $warehouse_types }}"
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.articles_register.validate_form') }}'"
		:url_get_sede = "'{{ route('dashboard.articles_register.list_sede') }}'"
		:url_get_warehouse_type = "'{{ route('dashboard.articles_register.list_almacen') }}'"
	></article-register-form>

	<article-register-table
		:url = "'{{ route('dashboard.articles_register.list') }}'"
		:url_detail = "'{{ route('dashboard.articles_register.detail') }}'"
		:url_delete = "'{{ route('dashboard.articles_register.delete') }}'"
		:url_export_record = "'{{ route('dashboard.articles_register.export_record') }}'"
	></article-register-table>

	<article-register-modal
		:url = "'{{ route('dashboard.articles_register.store') }}'"
		:families = "{{ $families }}"
		:groups = "{{ $groups }}"
		:subgroups = "{{ $subgroups }}"
		:operations = "{{ $operations }}"
		:units = "{{ $units }}"
	></article-register-modal>

	<loading></loading>
@endsection