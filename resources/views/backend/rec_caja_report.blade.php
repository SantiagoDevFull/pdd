@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Finanzas')
@section('subtitle', 'Reporte de Recaudación de Caja')

@section('content')

    <rec-caja-report-form

        :max_datetime = "'{{ $max_datetime }}'"
        :url="'{{ route('dashboard.finanzas.report.rec_caja.export') }}'"

    ></rec-caja-report-form>

	<loading></loading>
@endsection