@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Control GLP')
@section('subtitle', 'Impresión guías Preventa Chofer')

@section('content')

    <preventa-report-table 
    :url_list="'{{ route('dashboard.preventa.list') }}'"
    :url_download="'{{ route('dashboard.preventa.descargar') }}'">
    </preventa-report-table>

    <loading></loading>

@endsection
