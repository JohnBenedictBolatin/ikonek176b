<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
    </head>
    <body >
        <?php
            if(DB::connection()->getDatabaseName()) {
                echo "Connected to database: " . DB::connection()->getDatabaseName();
            } else {
                echo "Not connected to any database.";
            }
        ?>
    </body>
</html>
