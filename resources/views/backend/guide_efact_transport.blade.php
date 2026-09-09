@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'Facturación')
@section('subtitle', 'Envío de Guías Transportista PUNTO')

@section('content')
    <guide-efact-transport-form 
        :companies="{{ $companies }}"
        :url="'{{ route('dashboard.guide_transport.efact.validate_voucher_form') }}'"
        :user_name="'{{ $user_name }}'">
    </guide-efact-transport-form>

    <guide-efact-transport-table 
        :url_list="'{{ route('dashboard.guide_transport.efact.list') }}'"
        :url_send_voucher="'{{ route('dashboard.guide_transport.efact.send_voucher') }}'"
        :url_get_detail="'{{ route('dashboard.guide_transport.efact.get_detail') }}'"
        :user_name="'{{ $user_name }}'">
    </guide-efact-transport-table>

    <guide-efact-transport-table-modal></guide-efact-transport-table-modal>
    
    <loading></loading>
@endsection
