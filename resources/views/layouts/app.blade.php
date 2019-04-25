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
        <div class="nano" >
            <div class="nano-content">
                @yield('content')
            </div>
        </div>
        <footer class="p-t-15">
        </footer>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
