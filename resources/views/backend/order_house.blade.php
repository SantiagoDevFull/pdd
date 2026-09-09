@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'CASA DEL GAS')
@section('subtitle', 'Pedidos')

@section('content')

    <order-house-form
                :url="'{{ route('dashboard.order_house.store') }}'"
                :url_get_clients="'{{ route('dashboard.order_house.list_clients') }}'"
                :url_get_employees="'{{ route('dashboard.order_house.list_employees') }}'"
                :origin_houses ="{{ $origin_houses }}"
                :articles ="{{ $articles }}"
                :max_correlative ="{{ $max_correlative }}"
                :current_date ="'{{ $current_date }}'"
	></order-house-form>

	<order-house-table
                :url_list="'{{ route('dashboard.order_house.list') }}'"
                :url_detail="'{{ route('dashboard.order_house.detail') }}'"
                :url_delete="'{{ route('dashboard.order_house.delete') }}'"
	></order-house-table>

	<order-house-modal
                :url_update="'{{ route('dashboard.order_house.update') }}'"
                :url_get_voucher="'{{ route('dashboard.order_house.get_voucher') }}'"
                :payment_methods ="{{ $payment_methods }}"
	></order-house-modal>

        <order-house-client-modal
                :url = "'{{ route('dashboard.order_house.register_client') }}'"
                :url_get_ubigeos = "'{{ route('dashboard.order_house.get_ubigeos') }}'"
                :document_types = "{{ $document_types }}"
                :companies = "{{ $companies }}"
                :payments = "{{ $payments }}"
                :perceptions = "{{ $perceptions }}"
                :business_units = "{{ $business_units }}"
                :client_zones = "{{ $client_zones }}"
                :client_channels = "{{ $client_channels }}"
                :client_types = "{{ $client_types }}"
                :client_routes = "{{ $client_routes }}"
                :client_sectors = "{{ $client_sectors }}"
                :osi_agents = "{{ $osi_agents }}"
        ></order-house-client-modal>

	<loading></loading>
@endsection
