@extends('backend.templates.app')

@section('title', 'Facturación')
@section('subtitle', 'Liquidacion final de Rutas')

@section('content')
	<liquidation-preventa-form
		:client_routes = "{{ $client_routes }}"
		:url = "'{{ route('dashboard.liquidation_preventa.validate_form') }}'"
		:url_get_warehouse_movements = "'{{ route('dashboard.liquidation_preventa.get_warehouse_movements') }}'"
	></liquidation-preventa-form>
	
	<liquidation-preventa-table
		:url = "'{{ route('dashboard.liquidation_preventa.list_sales') }}'"
		:url_sale_detail = "'{{ route('dashboard.liquidation_preventa.list_sale_detail') }}'"
		:url_liquidation_detail = "'{{ route('dashboard.liquidation_preventa.list_liquidation_detail') }}'"
		:url_asign_scop = "'{{ route('dashboard.liquidation_preventa.asign_scop') }}'"
		:url_detail_money = "'{{ route('dashboard.liquidation_preventa.detail_money') }}'"
		:url_finish_liquidation = "'{{ route('dashboard.liquidation_preventa.finish_liquidation') }}'"
	></liquidation-preventa-table>

    <liquidation-preventa-detail></liquidation-preventa-detail>
    <liquidation-preventa-liquidation></liquidation-preventa-liquidation>

    <liquidation-preventa-scop
        :url_update = "'{{ route('dashboard.liquidation_preventa.update_scop') }}'"
    ></liquidation-preventa-scop>

    <liquidation-preventa-register-money
		:url_register_money = "'{{ route('dashboard.liquidation_preventa.register_money') }}'"
	></liquidation-preventa-register-money>
	
	<loading></loading>
@endsection