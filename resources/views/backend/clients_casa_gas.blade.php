@extends('backend.templates.app')

@section('title', 'Casa del Gas')
@section('subtitle', 'Clientes')

@section('content')
	<client-gas-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.voucher.clients_casa_gas.validate_form') }}'"
	></client-gas-form>

	<client-gas-table
		:url = "'{{ route('dashboard.voucher.clients_casa_gas.list') }}'"
		:url_detail = "'{{ route('dashboard.voucher.clients_casa_gas.detail') }}'"
		:url_delete = "'{{ route('dashboard.voucher.clients_casa_gas.delete') }}'"
	></client-gas-table>

	<client-gas-modal
		:url = "'{{ route('dashboard.voucher.clients_casa_gas.store') }}'"
		:url_get_ubigeos = "'{{ route('dashboard.voucher.clients_casa_gas.get_ubigeos') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.clients_casa_gas.get_clients') }}'"
		:url_get_select2 = "'{{ route('dashboard.voucher.clients_casa_gas.get_select2') }}'"
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
	></client-gas-modal>

	<client-address-gas-modal
		:url = "'{{ route('dashboard.voucher.clients_casa_gas.address_store') }}'"
		:url_get_ubigeos = "'{{ route('dashboard.voucher.clients_casa_gas.get_ubigeos') }}'"
		:url_address_list = "'{{ route('dashboard.voucher.clients_casa_gas.address_list') }}'"
		:url_address_detail = "'{{ route('dashboard.voucher.clients_casa_gas.address_detail') }}'"
		:url_address_delete = "'{{ route('dashboard.voucher.clients_casa_gas.address_delete') }}'"
		{{-- :url_get_address_select2 = "'{{ route('dashboard.voucher.clients.get_address_select2') }}'" --}}
		:address_types = "{{ $address_types }}"
	></client-address-gas-modal>

	<client-price-list-gas-modal
		:url = "'{{ route('dashboard.voucher.clients_casa_gas.price_store') }}'"
		:url_price_list = "'{{ route('dashboard.voucher.clients_casa_gas.price_list') }}'"
		:url_price_articles = "'{{ route('dashboard.voucher.clients_casa_gas.price_articles') }}'"
		:url_price_min_effective_date = "'{{ route('dashboard.voucher.clients_casa_gas.price_min_effective_date') }}'"
	></client-price-list-gas-modal>

	<loading></loading>
@endsection
