@extends('backend.templates.app')

@section('body-class', 'kt-header--fixed kt-header-mobile--fixed')
@section('title', 'PDD')
@section('subtitle', 'Punto De Distribución')

@section('content')
	
    <div class="text-center">
        <h1 class="bg-dark text-white p-4">
            <marquee behavior="" direction="">
                Bienvenido(a) , {{ Auth::user()->name }} 
            </marquee>
        </h1> 
    </div>

@endsection