// resources/views/spa.blade.php (Vue app entrypoint)
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <title>Vue App</title>
        <script src="{{ mix('js/app.js') }}" defer></script>
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>