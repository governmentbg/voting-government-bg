<!DOCTYPE html>
<html lang="bg">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title></title>
        <!-- Styles -->
        <link rel="stylesheet" href="/css/app.css">
        <!-- <link href="{{ asset('fonts/vendor/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">
        <link href="{{ asset('css/nanoscroller.css') }}" rel="stylesheet">
        <link href="{{ asset('css/colorpicker.css') }}" rel="stylesheet">
        <link href="{{ asset('css/bootstrap-clockpicker.min.css') }}" rel="stylesheet"> -->

    </head>
    <body>
        <div class="container-fluid">
            @yield('content')
        </div>
        <footer class="p-t-15">
            <div >

            </div>
        </footer>

        <script src="{{ asset('js/app.js') }}"></script>
        <!-- <script src="{{ asset('js/jquery.smartmenus.min.js') }}"></script>
        <script src="{{ asset('js/jquery.smartmenus.bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/jquery.nanoscroller.min.js') }}"></script>
        <script src="{{ asset('js/bootstrap-colorpicker.js') }}"></script>
        <script src="{{ asset('js/bootstrap-clockpicker.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote.js"></script> -->
    </body>
</html>
