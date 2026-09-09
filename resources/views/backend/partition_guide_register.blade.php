@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Operaciones')
@section('subtitle', 'Registro de Particiones - Guías')

@section('content')

    <partition-guide-register-form
        :client_routes = "{{ $client_routes }}"
        :current_date = "'{{ $current_date }}'"
        :max_datetime = "'{{ $max_datetime }}'"
        :url = "'{{ route('dashboard.operations.partition_guide_register.store') }}'"
        :url_get_plant_orders = "'{{ route('dashboard.operations.partition_guide_register.get_plan_order') }}'"
        :url_get_plant = "'{{ route('dashboard.operations.partition_guide_register.get_plant') }}'"
    ></partition-guide-register-form>

    <loading></loading>

@endsection