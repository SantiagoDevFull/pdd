<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('backend/css/main.css') }}" rel="stylesheet" type="text/css" />
</head>
<body>
    <main id="app">
        <preventa-login 
            :client_routes="{{ $client_routes }}"
            :url="'{{ route('dashboard.preventa.login') }}'"
            :url_get_guias="'{{ route('dashboard.preventa.guias') }}'"
        ></preventa-login>

        <preventa-deposit
            :url="'{{ route('dashboard.preventa.register_voucher') }}'"
            :bank_accounts="{{ $bank_accounts }}"
        ></preventa-deposit>

        <preventa-cobranza
            :url_get_clients_cobranzas = "'{{ route('dashboard.preventa.get_clients_cobranza') }}'"
            :url_get_sale_cobranzas = "'{{ route('dashboard.preventa.get_sale_cobranza') }}'"
            :url_update_cobranzas = "'{{ route('dashboard.preventa.update_cobranza') }}'"
            :payment_methods = "{{ $payment_methods }}"
            :csrf_token="'{{ csrf_token() }}'"
            :currencies = "{{ $currencies }}"
            :get_deposits="'{{ route('dashboard.preventa.get_deposit') }}'"
        ></preventa-cobranza>

        <preventa-sale 
            :url_get_clients="'{{ route('dashboard.preventa.get_clients') }}'"
            :url_get_list_cobranzas="'{{ route('dashboard.preventa.list_cobranzas_pagadas') }}'"
            :url_store="'{{ route('dashboard.preventa.store') }}'"
            :url_store_pre="'{{ route('dashboard.preventa.store_pre') }}'"
            :url_get_price="'{{ route('dashboard.preventa.get_price') }}'" 
            :csrf_token="'{{ csrf_token() }}'"
            :warehouse_document_types="{{ $warehouse_document_types }}"
        ></preventa-sale>

        <preventa-liquidation 
            :payments="{{ $payments }}"
            :currencies="{{ $currencies }}"
            :csrf_token="'{{ csrf_token() }}'"
            :bank_accounts="{{ $bank_accounts }}"
            :url_get_payment_methods="'{{ route('dashboard.preventa.get_payment_method') }}'" 
            :url_get_employees="'{{ route('dashboard.preventa.get_employees') }}'" 
            :url_verify_credit="'{{ route('dashboard.preventa.verify_credit') }}'" 
            :get_deposits="'{{ route('dashboard.preventa.get_deposit') }}'"
        ></preventa-liquidation>


        <loading></loading>
    </main>

    <script src="{{ asset('backend/js/main.js?' . time()) }}" type="text/javascript"></script>
    <script src="{{ asset('backend/js/app.js?' . time()) }}" type="text/javascript"></script>

    <script>
        var KTAppOptions = {
            "colors": {
                "state": {
                    "brand": "#5d78ff",
                    "dark": "#282a3c",
                    "light": "#ffffff",
                    "primary": "#5867dd",
                    "success": "#34bfa3",
                    "info": "#36a3f7",
                    "warning": "#ffb822",
                    "danger": "#fd3995"
                },
                "base": {
                    "label": ["#c5cbe3", "#a1a8c3", "#3d4465", "#3e4466"],
                    "shape": ["#f0f3ff", "#d9dffa", "#afb4d4", "#646c9a"]
                }
            }
        };

    </script>
</body>


</html>
