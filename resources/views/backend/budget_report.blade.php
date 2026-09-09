@extends('backend.templates.app')

@section('body-class', '')
@section('title', 'Reportes')
@if (Route::currentRouteName() === 'dashboard.report.budget')
    @section('subtitle', 'Reporte de Presupuestos')
@endif

@section('content')
    <budget-report-form 
        :current_date="'{{ $current_date }}'" 
        :url="'{{ route('dashboard.report.budget.validate_form') }}'">
    </budget-report-form>

    @if (Route::currentRouteName() === 'dashboard.report.budget')
        <budget-report-table :url="'{{ route('dashboard.report.budget.list') }}'"
            :url_detail="'{{ route('dashboard.report.budget.detail') }}'">
        </budget-report-table>
    @endif

    <budget-report-modal :url="'{{ route('dashboard.report.budget.update') }}'">
    </budget-report-modal>

    <loading></loading>
@endsection
