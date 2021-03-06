<!DOCTYPE html>
<html lang="bg">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title></title>
        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/datepicker.min.css') }}">
    </head>
    <body>
        <div class="container-fluid nano absolute-body">
            <div class="nano-content">
                @yield('content')
            </div>
        </div>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
@php
    http2_push_style('/css/app.css');
    http2_push_style('/css/datepicker.min.css');
    http2_push_script('/js/app.js');
@endphp