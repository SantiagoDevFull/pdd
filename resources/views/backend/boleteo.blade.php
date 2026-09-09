@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Boleteo')
@section('subtitle', '')

@section('content')
	<boleteo-form
		:articles = "{{ $articles }}"
        :current_date = "'{{ $current_date }}'"
		:url = "'{{ route('dashboard.boleteo.validate_form') }}'"
	></boleteo-form>

	<boleteo-table
        :url = "'{{ route('dashboard.boleteo.list') }}'"
	></boleteo-table>

	<boleteo-modal
        :url = "'{{ route('dashboard.boleteo.store') }}'"
		:current_date = "'{{ $current_date }}'"
	></boleteo-modal>

	<loading></loading>
@endsection
