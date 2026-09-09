@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Reporte de Ordenes de Trabajo')
@section('subtitle', '')

@section('content')

	<order-work-report-table
        :url = "'{{ route('dashboard.order_work.list') }}'"
        :url_delete = "'{{ route('dashboard.order_work.delete_asign') }}'"
        :url_aprobar = "'{{ route('dashboard.order_work.aprobar_asign') }}'"
        :url_pdf = "'{{ route('dashboard.order_work_pdf') }}'"
        :url_get_detail = "'{{ route('dashboard.order_work.view_asign') }}'"
	></order-work-report-table>

    <order-work-report-modal>
    </order-work-report-modal>

	<loading></loading>
@endsection