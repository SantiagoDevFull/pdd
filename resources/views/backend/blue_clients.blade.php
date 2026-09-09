@extends('backend.templates.app')

@section('title', 'Blue Line')
@section('subtitle', 'Gestión de Clientes')

@section('content')
	<blue-client-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.voucher.blue_clients.validate_form') }}'"
	></blue-client-form>

	<blue-client-table
		:url = "'{{ route('dashboard.voucher.blue_clients.list') }}'"
		:url_detail = "'{{ route('dashboard.voucher.blue_clients.detail') }}'"
		:url_delete = "'{{ route('dashboard.voucher.blue_clients.delete') }}'"
	></blue-client-table>

	<blue-client-modal
		:url = "'{{ route('dashboard.voucher.blue_clients.store') }}'"
		:url_get_ubigeos = "'{{ route('dashboard.voucher.blue_clients.get_ubigeos') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.blue_clients.get_clients') }}'"
		:url_get_select2 = "'{{ route('dashboard.voucher.blue_clients.get_select2') }}'"
		:document_types = "{{ $document_types }}"
		:payments = "{{ $payments }}"
		:perceptions = "{{ $perceptions }}"
		:business_units = "{{ $business_units }}"
		:client_zones = "{{ $client_zones }}"
		:client_channels = "{{ $client_channels }}"
		:client_types = "{{ $client_types }}"
		:client_routes = "{{ $client_routes }}"
		:client_sectors = "{{ $client_sectors }}"
		:osi_agents = "{{ $osi_agents }}"
	></blue-client-modal>

	<blue-client-address-modal
		:url = "'{{ route('dashboard.voucher.blue_clients.address_store') }}'"
		:url_get_ubigeos = "'{{ route('dashboard.voucher.blue_clients.get_ubigeos') }}'"
		:url_address_list = "'{{ route('dashboard.voucher.blue_clients.address_list') }}'"
		:url_address_detail = "'{{ route('dashboard.voucher.blue_clients.address_detail') }}'"
		:url_address_delete = "'{{ route('dashboard.voucher.blue_clients.address_delete') }}'"
		{{-- :url_get_address_select2 = "'{{ route('dashboard.voucher.clients.get_address_select2') }}'" --}}
		:address_types = "{{ $address_types }}"
	></blue-client-address-modal>

	<blue-client-price-list-modal
		:url = "'{{ route('dashboard.voucher.blue_clients.price_store') }}'"
		:url_price_list = "'{{ route('dashboard.voucher.blue_clients.price_list') }}'"
		:url_price_articles = "'{{ route('dashboard.voucher.blue_clients.price_articles') }}'"
		:url_price_min_effective_date = "'{{ route('dashboard.voucher.blue_clients.price_min_effective_date') }}'"
	></blue-client-price-list-modal>

	<loading></loading>
@endsection