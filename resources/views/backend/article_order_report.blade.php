@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Logística')
@section('subtitle', 'Reporte de Orden de Compra')

@section('content')

    <article-order-report-form
    :companies="{{ $companies }}"
    :url="'{{ route('dashboard.logistics.report.order.validate_form') }}'"
    ></article-order-report-form>

    <article-order-report-table
    :url_list="'{{ route('dashboard.logistics.report.order.list') }}'"
    :url_get_detail="'{{ route('dashboard.logistics.report.order.detail') }}'"
    :url_export="'{{ route('dashboard.logistics.report.order.export') }}'"
    ></article-order-report-table>

    <article-order-report-modal>
    </article-order-report-modal>

	<loading></loading>
@endsection