@extends('backend.templates.app')

@section('title', 'Reportes')
@section('subtitle', 'Facturación Transportes')

@section('content')
	<facturation-cochera-report-form
		:companies = "{{ $companies }}"
		:current_date = "'{{ $current_date }}'"
		:current_date_1 = "'{{ $current_date_1 }}'"
   		:current_date_2 = "'{{ $current_date_2 }}'"
		:url = "'{{ route('dashboard.report.facturation_cochera.validate_form') }}'"
	></facturation-cochera-report-form>
	
	<facturation-cochera-report-table
		:url = "'{{ route('dashboard.report.facturation_cochera.list') }}'"
        :url_delete = "'{{ route('dashboard.report.facturation_cochera.delete_asign') }}'"
        :url_aprobar = "'{{ route('dashboard.report.facturation_cochera.aprobar_asign') }}'"
        :url_get_detail = "'{{ route('dashboard.report.facturation_cochera.view_asign') }}'"
        :url_get_file = "'{{ route('dashboard.report.facturation_cochera.get_file') }}'"

	></facturation-cochera-report-table>

	<facturation-cochera-report-modal>
    </facturation-cochera-report-modal>

	<facturacion-cochera-report-liquidate
	:payments = "{{ $payments }}"
    :currencies = "{{ $currencies }}"
	:url = "'{{ route('dashboard.report.facturation_cochera.add_liquidar') }}'"
	:url_get_payment_method = "'{{ route('dashboard.report.facturation_cochera.method') }}'"
    :url_get_bank_accounts = "'{{ route('dashboard.report.facturation_cochera.bank') }}'"
    :url_store = "'{{ route('dashboard.report.facturation_cochera.store') }}'"
	>
	</facturacion-cochera-report-liquidate>

	<loading></loading>
@endsection 