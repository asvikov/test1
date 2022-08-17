<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/app.fa3dfaa7.css">
</head>
<body>
    @include('flash_message')
    @yield('main_content')

</body>
</html>
