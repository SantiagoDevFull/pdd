@extends('backend.templates.app')

@section('title', 'Facturación')
@section('subtitle', 'Liquidaciones')

@section('content')
	<liquidation-envasado-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.voucher.liquidations_envasado.validate_form') }}'"
		:url_get_warehouse_movements = "'{{ route('dashboard.voucher.liquidations_envasado.get_warehouse_movements') }}'"
	></liquidation-envasado-form>

	<liquidation-envasado-table
		:url = "'{{ route('dashboard.voucher.liquidations_envasado.list') }}'"
		:url_store = "'{{ route('dashboard.voucher.liquidations_envasado.store') }}'"
	></liquidation-envasado-table>

	<liquidation-envasado-table-sale
	></liquidation-envasado-table-sale>

	<liquidation-envasado-modal-sale
		:warehouse_document_types = "{{ $warehouse_document_types }}"
		:currencies = "{{ $currencies }}"
		:fact_series = "{{ $fact_series }}"
		:url = "'{{ route('dashboard.voucher.liquidations_envasado.list') }}'"
		:url_get_clients = "'{{ route('dashboard.voucher.liquidations_envasado.get_clients') }}'"
		:url_get_article_price = "'{{ route('dashboard.voucher.liquidations_envasado.get_article_price') }}'"
		:url_verify_document_type = "'{{ route('dashboard.voucher.liquidations_envasado.verify_document_type') }}'"
	></liquidation-envasado-modal-sale>

	<liquidation-envasado-modal-liquidation
		:payment_methods = "{{ $payment_methods }}"
		:payments = "{{ $payments }}"
		:deposits = "{{ $deposits }}"
		:currencies = "{{ $currencies }}"
		:url_get_bank_accounts = "'{{ route('dashboard.voucher.liquidations_envasado.get_bank_accounts') }}'"
		:url_get_employees = "'{{ route('dashboard.voucher.liquidations_envasado.get_employees') }}'"
		:url_validate_client = "'{{ route('dashboard.voucher.liquidations_envasado.validate_client') }}'"
		:payment_cash = "{{ $payment_cash }}"
		:payment_credit = "{{ $payment_credit }}"
	></liquidation-envasado-modal-liquidation>

	{{-- <liquidation-envasado-modal
		:url = "'{{ route('dashboard.voucher.liquidations_envasado.store') }}'"
	></liquidation-envasado-modal> --}}

	<loading></loading>
@endsection
