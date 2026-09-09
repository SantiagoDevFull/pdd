@extends('backend.templates.app')

@section('title', 'Facturación')
@section('subtitle', 'Otros Ingresos de Caja')

@section('content')
	<liquidation-other-final-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.voucher.liquidations_other.validate_form') }}'"
		:url_get_warehouse_movements = "'{{ route('dashboard.voucher.liquidations_other.get_warehouse_movements') }}'"
	></liquidation-other-final-form>
	
	<liquidation-other-final-table
		:url = "'{{ route('dashboard.voucher.liquidations_other.list') }}'"
		:url_store = "'{{ route('dashboard.voucher.liquidations_other.store') }}'"
	></liquidation-other-final-table>

	<liquidation-other-final-table-sale
	></liquidation-other-final-table-sale>

	<liquidation-other-final-modal-sale
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:currencies = "{{ $currencies }}"
		:fact_series = "{{ $fact_series }}"
		:url = "'{{ route('dashboard.voucher.liquidations_other.list') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.liquidations_other.get_clients') }}'"
		:url_get_article_price = "'{{ route('dashboard.voucher.liquidations_other.get_article_price') }}'"
		:url_verify_document_type = "'{{ route('dashboard.voucher.liquidations_other.verify_document_type') }}'"
	></liquidation-other-final-modal-sale>

	<liquidation-other-final-modal-liquidation
		:payment_methods = "{{ $payment_methods }}"
		:payments = "{{ $payments }}"
		:currencies = "{{ $currencies }}"                                
		:url_get_bank_accounts = "'{{ route('dashboard.voucher.liquidations_other.get_bank_accounts') }}'"
		:payment_cash = "{{ $payment_cash }}"
		:payment_credit = "{{ $payment_credit }}"
	></liquidation-other-final-modal-liquidation>

	{{-- <liquidation-other-final-modal
		:url = "'{{ route('dashboard.voucher.liquidations_other.store') }}'"
	></liquidation-other-final-modal> --}}

	<loading></loading>
@endsection