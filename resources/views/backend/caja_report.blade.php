@extends('backend.templates.app')

@section('title', 'ADMIISTRACION')
@section('subtitle', 'Reporte liquidacion caja')

@section('content')
	<caja-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:current_date_1 = "'{{ $current_date_1 }}'"
   		:current_date_2 = "'{{ $current_date_2 }}'"
		:url = "'{{ route('dashboard.report.facturation_report_caja.validate_form') }}'"
	></caja-report-form>
	
	<caja-report-table
		:url = "'{{ route('dashboard.report.facturation_report_caja.list') }}'"
        :url_delete = "'{{ route('dashboard.report.facturation_report_caja.delete_asign') }}'"
        :url_aprobar = "'{{ route('dashboard.report.facturation_report_caja.aprobar_asign') }}'"
        :url_get_detail = "'{{ route('dashboard.report.facturation_report_caja.view_asign') }}'"
        :url_get_file = "'{{ route('dashboard.report.facturation_report_caja.get_file') }}'"

	></caja-report-table>

	<caja-report-modal>
    </caja-report-modal>

	<caja-report-liquidate
	:payments = "{{ $payments }}"
    :currencies = "{{ $currencies }}"
	:url = "'{{ route('dashboard.report.facturation_report_caja.add_liquidar') }}'"
	:url_get_payment_method = "'{{ route('dashboard.report.facturation_report_caja.method') }}'"
    :url_get_bank_accounts = "'{{ route('dashboard.report.facturation_report_caja.bank') }}'"
    :url_store = "'{{ route('dashboard.report.facturation_report_caja.store') }}'"
	>
	</caja-report-liquidate>

	<loading></loading>
@endsection 