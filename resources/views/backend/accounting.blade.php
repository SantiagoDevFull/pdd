@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Contabilidad')
@section('subtitle', 'Lista de Precios')

@section('content')

	<accounting-table
    :url_list="'{{ route('dashboard.accounting.list') }}'"
    :url_delete="'{{ route('dashboard.accounting.delete') }}'"
    :url_detail="'{{ route('dashboard.accounting.detail') }}'"
    :url_export_record="'{{ route('dashboard.accounting.export') }}'"
	></accounting-table>
    
	<accounting-modal
    :url="'{{ route('dashboard.accounting.store') }}'"
    :max_datetime = "'{{ $max_datetime }}'"
    :articles = "{{ $articles }}"
	></accounting-modal>

	<loading></loading>
@endsection