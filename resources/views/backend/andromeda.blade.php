@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Andromeda')
@section('subtitle', 'this is my first chamba xd')

@section('content')
	
	<andromeda-form
		:document_types = "{{ $document_types }}"
		:employee_types = "{{ $employee_types }}"
		:companies = "{{ $companies }}"
		{{-- :url_get_areas = "'{{ route('dashboard.andromeda.get_areas') }}'" --}}
		{{-- Route::get('/beta/reporte-andromeda', 'AndromedaController@getAreas')->name('dashboard.andromeda.get_areas'); --}}
	></andromeda-form>

	<loading></loading>
@endsection