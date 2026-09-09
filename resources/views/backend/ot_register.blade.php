@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Logística')
@section('subtitle', 'Orden de Trabajo')

@section('content')
	<ot-register-form
        :companies = "{{ $companies }}"
		:company_securities="{{ $company_securities }}"
		:asesores="{{ $asesores }}"
		:series="{{ $series }}"
        :url_get_vehicles = "'{{ route('dashboard.logistics.ot_list_vehicles') }}'"
        :url_get_series = "'{{ route('dashboard.logistics.ot_series') }}'"
        :url_next_correlative = "'{{ route('dashboard.logistics.ot_correlative') }}'"
        :url_get_client = "'{{ route('dashboard.logistics.ot_client') }}'"
        :url="'{{ route('dashboard.logistics.ot_list_detalle') }}'"
	></ot-register-form>

    <ot-register-table
    :url_get_article = "'{{ route('dashboard.logistics.ot_list_articles') }}'"
    :url = "'{{ route('dashboard.logistics.ot_register_final') }}'"
    >
    </ot-register-table>

    <ot-register-modal
    :url = "'{{ route('dashboard.logistics.ot_validate_modal') }}'"
    :url_list_article = "'{{ route('dashboard.logistics.ot_articles') }}'"
    >
    </ot-register-modal>

	<loading></loading>
@endsection