<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
   <head>
      @include('store.includes.head')
   </head>
   <body class="app sidebar-mini">
    
      <div id="app" class="page">
         <div class="page-main">
            @include('store.includes.sidebar')
            @include('store.includes.header')
            @section('content')
            @show
            @include('store.includes.footer')
         </div>
      </div>
   </body>
</html>