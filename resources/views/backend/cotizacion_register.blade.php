@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Logística')
@section('subtitle', 'Cotización')

@section('content')
	<cotizacion-register-form
        :current_date = "'{{ $current_date }}'"
		:currencies="{{ $currencies }}"
        :companies = "{{ $companies }}"
        :url_get_clients = "'{{ route('dashboard.logistics.cotizacion_list_clients') }}'"
        :url="'{{ route('dashboard.logistics.cotizacion_list_detalle') }}'"
        :url_get_vehicles = "'{{ route('dashboard.logistics.cotizacion_list_vehicles') }}'"
        :url_get_client = "'{{ route('dashboard.logistics.cotizacion_client') }}'"
	></cotizacion-register-form>

    <cotizacion-register-table
        :url_get_article = "'{{ route('dashboard.logistics.cotizacion_list_articles') }}'"
        :url = "'{{ route('dashboard.logistics.cotizacion_register_final') }}'"
    >
    </cotizacion-register-table>

    <cotizacion-register-modal
        :url = "'{{ route('dashboard.logistics.cotizacion_validate_modal') }}'"
        :url_list_article = "'{{ route('dashboard.logistics.cotizacion_articles') }}'"
    >
    </cotizacion-register-modal>

	<loading></loading>
@endsection