@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Reporte de Ordenes de Trabajo')
@section('subtitle', '')

@section('content')

	<liquidation-pm-table
        :url = "'{{ route('dashboard.liquidation_pm.list') }}'"
        :url_get_detail = "'{{ route('dashboard.liquidation_pm.detail') }}'"
	></liquidation-pm-table>

    <liquidation-pm-modal>
    </liquidation-pm-modal>

    <liquidation-pm-modal-liquidation
    :payments = "{{ $payments }}"
    :currencies = "{{ $currencies }}"
    :url_get_payment_method = "'{{ route('dashboard.liquidation_pm.list.method') }}'"
    :url_get_bank_accounts = "'{{ route('dashboard.liquidation_pm.list.bank_account') }}'"
    :url = "'{{ route('dashboard.liquidation_pm.add_liquidation') }}'"
    :url_store = "'{{ route('dashboard.liquidation_pm.store') }}'"
    >
    </liquidation-pm-modal-liquidation>

	<loading></loading>
@endsection