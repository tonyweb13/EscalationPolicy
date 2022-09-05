<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate"/>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
	<meta http-equiv="Pragma" content="no-cache"/>
	<meta http-equiv="Expires" content="0"/>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('/img/favicon-32x32-ep.png') }}" sizes="32x32" type="image/png" >
    <title>{{ config('app.name', 'EP') }}</title>
    @include('layouts.js_vars')
    <link href="{{ mix('css/public.css') }}" rel="stylesheet">
</head>
<body>
    <div id="ep-app"></div>
    <script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
