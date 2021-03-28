<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Reservierungを使って予約管理しませんか？Reservierungはサロン・美容室の無料予約管理システムです。予約受付の自動化、予約情報の管理と効率化を可能にする機能を使ってみましょう。">
        <meta name="keyword" content="reservierung lnoueryo inoueryo sns 井上領">
        <meta property="og:url" content="https://lnoueryo98.sakura.ne.jp/booking" />
        <meta property="og:title" content="Reservierung" />
        <meta property="og:image" content="/images/logo/favicon.png" />
        <script src="{{ asset('js/app.js') }}"defer></script>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <title>Reservierung</title>
        {{--  <link rel="shortcut icon" href="/images/logo/logo01.png" />  --}}
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