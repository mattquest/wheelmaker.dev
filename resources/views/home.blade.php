<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link href="https://use.fontawesome.com/releases/v5.11.1/css/all.css" rel="stylesheet">
        <script>
            window.vaporAssets = {
                logoImage: "{{ asset('/logo.svg') }}",
            };
        </script>


    </head>
    <body>
        <div id="app">
            <wh-home>
            </wh-home>
        </div>
        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>
