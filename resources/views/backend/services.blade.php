@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Servicios')
@section('subtitle', '')

@section('content')
	<service-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.services.validate_form') }}'"
        :url_list_areas="'{{ route('dashboard.services.list.areas') }}'"
	></service-form>

	<service-table
    :url = "'{{ route('dashboard.services.list') }}'"
    :url_detail = "'{{ route('dashboard.services.detail') }}'"
    :url_delete = "'{{ route('dashboard.services.delete') }}'"
    :url_export_record = "'{{ route('dashboard.services.export_record') }}'"
	></service-table>

	<service-modal
        :url = "'{{ route('dashboard.services.store') }}'"
	></service-modal>

	<loading></loading>
@endsection