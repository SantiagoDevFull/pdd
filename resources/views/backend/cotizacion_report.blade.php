@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Logística')
@section('subtitle', 'Reporte de Cotización')

@section('content')
	<cotizacion-report-form
    :current_date_1 = "'{{ $current_date_1 }}'"
    :current_date_2 = "'{{ $current_date_2 }}'"
    :url = "'{{ route('dashboard.logistics.cotizacion_validar_form') }}'"
	></cotizacion-report-form>

    <cotizacion-report-table
    :url="'{{ route('dashboard.logistics.cotizacion_report_list') }}'"
    :url_fact="'{{ route('dashboard.logistics.cotizacion_report_fact_create') }}'"
    :url_export_cot="'{{ route('dashboard.logistics.cotizacion_report_cot') }}'"
    :url_export_fact="'{{ route('dashboard.logistics.cotizacion_report_fact') }}'"
    :url_aprob = "'{{ route('dashboard.logistics.cotizacion_report_aprob') }}'"
    :url_detail = "'{{ route('dashboard.logistics.cotizacion_report_detail') }}'"
    >
    </cotizacion-report-table>

    <kotizacion-report-modal
    :url_detail_article = "'{{ route('dashboard.logistics.cotizacion_report_detail_article') }}'"
    :url_delete_detail_article = "'{{ route('dashboard.logistics.cotizacion_report_delete_article') }}'"
    >
    </kotizacion-report-modal>


    <cotizacion-report-modal-article
    :url_list_article = "'{{ route('dashboard.logistics.cotizacion_articles') }}'"
    :url = "'{{ route('dashboard.logistics.cotizacion_report_update_article') }}'"
    >
    </cotizacion-report-modal-article>

	<loading></loading>

@endsection