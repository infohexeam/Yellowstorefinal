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
                                    <div id="floating-panel">
                                       <select id="mode" style="display:none;">
                                         <option value="DRIVING">Driving</option>
                                         <option value="WALKING">Walking</option>
                                         <option value="BICYCLING">Bicycling</option>
                                         <option value="TRANSIT">Transit</option>
                                       </select>
                                     </div>
                                     <div id="map"></div>
                                     <input type="hidden" value="{{$lastLoc->latitude}}" id="latTo" style="display:none;">
                                     <input type="hidden" value="{{$lastLoc->longitude}}" id="lngTo" style="display:none;">
{{-- <iframe src="https://maps.google.com/?q={{$lastLoc->latitude}},{{$lastLoc->longitude}}&output=embed" width="100%" height="350" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>	 --}}
                                <!--<div id="map"></div>-->

                <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
<script
                  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSqyoP-FHj6nJpuIvNYmb1YaGqBmh3xdQ&callback=initMap&libraries=&v=weekly&channel=2"
                  async
                ></script>         
           <script>

           
           function initMap() {
  const directionsRenderer = new google.maps.DirectionsRenderer();
  const directionsService = new google.maps.DirectionsService();
  const map = new google.maps.Map(document.getElementById("map"), {
    zoom: 14,
    center: { lat: 11.25380152502914, lng: 75.80086787925326 },
  });

  directionsRenderer.setMap(map);
  calculateAndDisplayRoute(directionsService, directionsRenderer);
  document.getElementById("mode").addEventListener("change", () => {
    calculateAndDisplayRoute(directionsService, directionsRenderer);
  });
}

function calculateAndDisplayRoute(directionsService, directionsRenderer) {
  const selectedMode = document.getElementById("mode").value;

//   const latTo = document.getElementById("latTo").value;
//   const lngTo = document.getElementById("lngTo").value;

  directionsService
    .route({
      origin: { lat: document.getElementById("latTo").value, lng:document.getElementById("lngTo").value  },
      destination: { lat: 11.292650586051085, lng: 75.77336701588105 },
      // Note that Javascript allows us to access the constant
      // using square brackets and a string value as its
      // "property."
      travelMode: google.maps.TravelMode['DRIVING'],
    })
    .then((response) => {
      directionsRenderer.setDirections(response);
    })
    .catch((e) => window.alert("Directions request failed due to " + status));
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
