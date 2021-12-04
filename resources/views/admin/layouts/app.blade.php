<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      @include('admin.includes.head')
   </head>
   <body class="app sidebar-mini">
    
      <div id="app" class="page">
         <div class="page-main">
            @include('admin.includes.sidebar')
            @include('admin.includes.header')
            @section('content')
            @show
            @include('admin.includes.footer')
         </div>
      </div>
   </body>
</html>