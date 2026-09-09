@extends('backend.templates.app')

@section('title', 'Presupuesto de Ventas')
@section('subtitle', '')

@section('content')
	<sale-presupuesto-form
		:url = "'{{ route('dashboard.voucher.sale_presupuesto.validate_form') }}'"
	></sale-presupuesto-form>

	<sale-presupuesto-table
		:url = "'{{ route('dashboard.voucher.sale_presupuesto.list') }}'"
		:url_detail = "'{{ route('dashboard.voucher.sale_presupuesto.detail') }}'"
		:url_delete = "'{{ route('dashboard.voucher.sale_presupuesto.delete') }}'"
		:url_export = "'{{ route('dashboard.voucher.sale_presupuesto.export') }}'"
	></sale-presupuesto-table>

	<sale-presupuesto-modal
        :business_units = "{{ $business_units }}"
        :client_sectors = "{{ $client_sectors }}"
        :client_channels = "{{ $client_channels }}"
        :client_routes = "{{ $client_routes }}"
        :managers = "{{ $managers }}"
		:url = "'{{ route('dashboard.voucher.sale_presupuesto.store') }}'"
	></sale-presupuesto-modal>

	<loading></loading>
@endsection
