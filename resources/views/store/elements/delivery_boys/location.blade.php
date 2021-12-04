@extends('store.layouts.app')
@section('content')


  <style type="text/css">

    	#map {

      		border:1px solid red;

      		width: 100%;

      		height: 300px;

    	}

  	</style>	
  	
<div class="container">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>

                <div class="col-lg-12">
                          <div  class="row">
                             <div class="col-md-12">
                                <div class="card">
                                   <div class="card-body">
<iframe src="https://maps.google.com/?q={{$lastLoc->latitude}},{{$lastLoc->longitude}}&output=embed" width="100%" height="350" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>	
                                <!--<div id="map"></div>-->

                <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
                <script
                  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSqyoP-FHj6nJpuIvNYmb1YaGqBmh3xdQ&callback=initMap&libraries=&v=weekly&channel=2"
                  async
                ></script>         
           <script>
               // Initialize and add the map
              const uluru = { lat: '11.6775838', lng: '75.7792992' };
            function initMap() {
              // The location of Uluru
         //     const uluru = { lat: -25.344, lng: 131.036 };
              // The map, centered at Uluru
              const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 4,
                center: uluru,
              });
              // The marker, positioned at Uluru
              const marker = new google.maps.Marker({
                position: uluru,
                map: map,
              });
}
           </script>
        
                                   <div>   
                                </div>
                             </div>
                          </div>
                          
                          <center>
                            <a type="button" class="btn btn-cyan text-white" onclick="history.back()">Cancel</a> 
                          </center>

          
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
   @endsection
