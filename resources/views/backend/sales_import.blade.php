@extends('backend.templates.app')

@section('title', 'Importar')
@section('subtitle', 'Facturación')

@section('content')
	<facturation-import-form
		:url_post = "'{{ route('facturations.import') }}'"
	></facturation-import-form>

	<loading></loading>
@endsection