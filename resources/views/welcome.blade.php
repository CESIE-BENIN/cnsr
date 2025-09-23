<!DOCTYPE html>
<html lang="fr">
    <head>
        <!-- Meta -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
      
        <title>@yield('title') </title>
        <!-- Latest Bootstrap min CSS -->
       
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="shortcut icon" href="{{ asset('image\cnsr.jpg') }}" type="image/x-icon">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @yield('extra-style')
    </head>
    <body>


        <div id="app">
            @yield('content')
        </div>
     
        @yield('extra-scripts')
    </body>
</html>





