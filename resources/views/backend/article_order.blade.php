@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Logística')
@section('subtitle', 'Orden de Compra')

@section('content')

    <article-order-form
    :companies="{{ $companies }}"
    :providers="{{ $providers }}"
    :currencies="{{ $currencies }}"
    :url_list="'{{ route('dashboard.logistics.list') }}'"
    :url_get_area="'{{ route('dashboard.logistics.get_area') }}'"
    >
    </article-order-form>

    <article-order-table
    :url_store="'{{ route('dashboard.logistics.store') }}'"
    :url_get_article="'{{ route('dashboard.logistics.get_article') }}'"
    >
    </article-order-table>

    <article-order-modal
    :url_validate_modal="'{{ route('dashboard.logistics.validate_modal') }}'"
    >
    </article-order-modal>

	<loading></loading>
@endsection