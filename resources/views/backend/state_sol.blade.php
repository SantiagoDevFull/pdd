@extends('backend.templates.app')

@section('title', 'Solicitudes de Pagos')
@section('subtitle', '')

@section('content')
	<state-sol-form
		:companies = "{{ $companies }}"
		:url = "'{{ route('dashboard.voucher.state_sol.validate_form') }}'"
		:url_get_area_alls = "'{{ route('dashboard.voucher.state_sol.get_area_alls') }}'"
		:adm_payments = "{{ $adm_payments }}"
	></state-sol-form>

	<state-sol-table
		:url = "'{{ route('dashboard.voucher.state_sol.list') }}'"
		:url_detail = "'{{ route('dashboard.voucher.state_sol.detail') }}'"
		:url_get_import_cont = "'{{ route('dashboard.voucher.state_sol.get_import_cont') }}'"
		:url_get_import_fi = "'{{ route('dashboard.voucher.state_sol.get_import_fi') }}'"
		:url_aprobar_cont = "'{{ route('dashboard.voucher.state_sol.aprobar_cont') }}'"
		:url_desaprobar_cont = "'{{ route('dashboard.voucher.state_sol.desaprobar_cont') }}'"
	></state-sol-table>



	<state-sol-modal
		:url_verify_register_cont = "'{{ route('dashboard.voucher.state_sol.verify_register_cont') }}'"
	></state-sol-modal>



	<state-conta-modal
		:url_update = "'{{ route('dashboard.voucher.state_sol.update_cont') }}'"
	></state-conta-modal>

	<state-fi-modal
		:url_update = "'{{ route('dashboard.voucher.state_sol.update_fi') }}'"
	></state-fi-modal>

	<state-tes-modal
		:url_update = "'{{ route('dashboard.voucher.state_sol.update_tes') }}'"
		:bank_accounts = "{{ $bank_accounts }}"
		:current_date = "'{{ $current_date }}'"
	></state-tes-modal>

	<loading></loading>
@endsection