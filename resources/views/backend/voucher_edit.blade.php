@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Edición de Facturas General')
@section('subtitle', '')

@section('content')

	<voucher-edit-table
        :url = "'{{ route('dashboard.fact_edit.list') }}'"
        :url_get_detail = "'{{ route('dashboard.fact_edit.detail') }}'"
	></voucher-edit-table>

    <voucher-edit-modal
        :url_update = "'{{ route('dashboard.fact_edit.update') }}'"
    ></voucher-edit-modal>

	<loading></loading>
@endsection