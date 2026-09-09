@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Registro de Efectivo')

@section('content')
	<register-efective
		:min_datetime = "'{{ $min_datetime }}'"
		:max_datetime = "'{{ $max_datetime }}'"
		:url = "'{{ route('dashboard.efective.register_efective') }}'"
		:url_get_warehouse_movements = "'{{ route('dashboard.efective.register_efective.get_warehouse_movements') }}'"
	></register-efective>

	<register-efective-table
		:url_list = "'{{ route('dashboard.data_efective') }}'"
		:url_delete = "'{{ route('dashboard.efective.register_efective.delete') }}'"
		:url_detail = "'{{ route('dashboard.efective.register_efective.detail') }}'"
	></register-efective-table>

	<register-efective-modal
		:min_datetime = "'{{ $min_datetime }}'"
		:max_datetime = "'{{ $max_datetime }}'"
		:url_get_warehouse_movements = "'{{ route('dashboard.efective.register_efective.get_warehouse_movements') }}'"
		:url_update = "'{{ route('dashboard.efective.register_efective.update') }}'"
	></register-efective-modal>

	<loading></loading>
@endsection
