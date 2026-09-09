@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Blue Line')
@section('subtitle', 'Gestión de Flota')

@section('content')
	<blue-plate-form
		:companies = "{{ $companies }}"
		:bl_asesores = "{{ $bl_asesores }}"
		:bl_clients = "{{ $bl_clients }}"
		:bl_services = "{{ $bl_services }}"
		:bl_clases = "{{ $bl_clases }}"
		:bl_body_types = "{{ $bl_body_types }}"
		:bl_brands = "{{ $bl_brands }}"
		:bl_modelos = "{{ $bl_modelos }}"

		:url = "'{{ route('dashboard.blue_plate.store') }}'"
	></blue-plate-form>

	<blue-plate-table
        :url = "'{{ route('dashboard.blue_plate.list') }}'"
        :url_delete = "'{{ route('dashboard.blue_plate.delete') }}'"
        :url_detail = "'{{ route('dashboard.blue_plate.detail') }}'"
        :url_export_excel = "'{{ route('dashboard.blue_plate.export') }}'"
        
	></blue-plate-table>

	<blue-plate-modal
        :companies = "{{ $companies }}"
        :url_update = "'{{ route('dashboard.blue_plate.update') }}'"
	></blue-plate-modal>

	<loading></loading>
@endsection