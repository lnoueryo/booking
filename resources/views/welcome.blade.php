<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="{{ asset('js/app.js') }}"defer></script>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        <!-- Styles -->
        <style>
            [v-cloak] {
                opacity: 0;
            }
        </style>
    </head>
    <body>
        <div id="app" v-cloak>
            <v-app style="background: radial-gradient(circle 700px at center, #57f6fe00 0%, #30c6ec42 90%, #46adf753 100%);">
                <index></index>
            </v-app>
        </div>
    </body>
</html>