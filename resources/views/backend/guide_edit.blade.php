@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Edición de Guías de Remisión')
@section('subtitle', '')

@section('content')

	<guide-edit-table
        :url = "'{{ route('dashboard.guide_edit.list') }}'"
        :url_get_detail = "'{{ route('dashboard.guide_edit.detail') }}'"
	></guide-edit-table>

    <guide-edit-modal
        :url_update = "'{{ route('dashboard.guide_edit.update') }}'"
    ></guide-edit-modal>

	<loading></loading>
@endsection
