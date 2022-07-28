<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{mix('app.css', 'assets/css/', true)}}">
</head>
<body>
    @include('flash_message')
    @yield('main_content')

<script src="assets/js/app.js"></script>
</body>
</html>
