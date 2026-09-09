@extends('backend.templates.app')

@section('title', 'Clientes de Punto Motor')
@section('subtitle', '')

@section('content')
	<client-pm-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.voucher.clients-pm.validate_form') }}'"
	></client-pm-form>

	<client-pm-table
		:url = "'{{ route('dashboard.voucher.clients-pm.list') }}'"
		:url_detail = "'{{ route('dashboard.voucher.clients-pm.detail') }}'"
		:url_delete = "'{{ route('dashboard.voucher.clients-pm.delete') }}'"
	></client-pm-table>

	<client-pm-modal
		:url = "'{{ route('dashboard.voucher.clients-pm.store') }}'"
		:url_get_ubigeos = "'{{ route('dashboard.voucher.clients-pm.get_ubigeos') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.clients-pm.get_clients') }}'"
		:url_get_select2 = "'{{ route('dashboard.voucher.clients-pm.get_select2') }}'"
		:document_types = "{{ $document_types }}"
		:payments = "{{ $payments }}"
		:perceptions = "{{ $perceptions }}"
		:business_units = "{{ $business_units }}"
		:client_zones = "{{ $client_zones }}"
		:client_channels = "{{ $client_channels }}"
		:client_types = "{{ $client_types }}"
		:client_routes = "{{ $client_routes }}"
		:client_sectors = "{{ $client_sectors }}"
	></client-pm-modal>

	<client-pm-address-modal
		:url = "'{{ route('dashboard.voucher.clients-pm.address_store') }}'"
		:url_get_ubigeos = "'{{ route('dashboard.voucher.clients-pm.get_ubigeos') }}'"
		:url_address_list = "'{{ route('dashboard.voucher.clients-pm.address_list') }}'"
		:url_address_detail = "'{{ route('dashboard.voucher.clients-pm.address_detail') }}'"
		:url_address_delete = "'{{ route('dashboard.voucher.clients-pm.address_delete') }}'"
		:address_types = "{{ $address_types }}"
	></client-pm-address-modal>

	<loading></loading>
@endsection