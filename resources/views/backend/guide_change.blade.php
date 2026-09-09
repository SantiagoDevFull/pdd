@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Operaciones')
@section('subtitle', 'Registro de Cambios')

@section('content')

    <guide-change-register-form
        :client_routes = "{{ $client_routes }}"
        :url = "'{{ route('dashboard.operations.guide_change.store') }}'"
        :url_get_reception_plants = "'{{ route('dashboard.operations.guide_change.get_reception_plants') }}'"
        :url_get_reception_plant = "'{{ route('dashboard.operations.guide_change.get_reception_plant') }}'"
    ></guide-change-register-form>

    <loading></loading>

@endsection