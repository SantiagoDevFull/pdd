@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Almacenes')
@section('subtitle', '')

@section('content')
	<store-form
		:url = "'{{ route('dashboard.stores.validate_form') }}'"
        :companies = "{{ $companies }}"
	></store-form>

	<store-table
		:url = "'{{ route('dashboard.stores.list') }}'"
		:url_detail = "'{{ route('dashboard.stores.detail') }}'"
		:url_delete = "'{{ route('dashboard.stores.delete') }}'"
	></store-table>

	<store-modal
		:url = "'{{ route('dashboard.stores.store') }}'"
		:agents = "{{ $agents }}"
	></store-modal>

	<loading></loading>
@endsection