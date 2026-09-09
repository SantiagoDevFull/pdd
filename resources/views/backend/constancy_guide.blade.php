@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Control GLP')
@section('subtitle', 'Guías - Constancias de Verificación de Pesos y Medidas')

@section('content')
    <constancy-guide-form 
        :companies="{{ $companies }}"
        :url="'{{ route('dashboard.constancy_guide.validar_form') }}'">
    </constancy-guide-form>

    <constancy-guide-table 
        :url="'{{ route('dashboard.constancy_guide.list') }}'"
        :url_get_detail="'{{ route('dashboard.constancy_guide.detalle') }}'"
        :url_download="'{{ route('dashboard.constancy_guide.descargar') }}'">
    </constancy-guide-table>

    <constancy-guide-modal></constancy-guide-modal>

    <loading></loading>
@endsection
