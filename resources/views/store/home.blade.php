@extends('store.layouts.app')
@section('content')
 @php
//use App\Models\admin\category\Category;
//use App\Models\admin\insurance\Insurance;
//use App\Models\admin\template\Template;
//use App\Models\admin\job_seeker\TrnJobSeeker;
use App\Models\admin\Mst_StoreAppBanner;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_store;
$store = Mst_store::Find(auth()->user()->store_id);

$banners = Mst_StoreAppBanner::where('town_id',@$store->town_id)->get();




@endphp
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!-- ROW-1 -->
@if(count($banners) > 0)

<div class="row" >
<div class="pb-5 col-lg-12 col-md-12 col-sm-12 col-xl-12">




        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <ol class="carousel-indicators">
                @foreach ($banners as $data)
                    <li data-target="#carouselExampleIndicators" data-slide-to="{{$loop->iteration}}" class="@if($loop->iteration == 1) active @endif"></li>
                @endforeach
            </ol>
            <div class="carousel-inner">
                @foreach ($banners as $data)
                        <div class="carousel-item @if($loop->iteration == 1) active @endif">
                        <img class=" w-70" src="{{asset('assets/uploads/store_banner/'.$data->image)}}" >
                        </div>
                @endforeach
            </div>
             <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a> 
            </div>
</div>
@endif

   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-6">
      <div class="row">




        {{-- <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-info mb-0 box-info-shadow">
													<i class="fa fa-sliders text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Categories') }}</h6>
                        <h2 class="mb-2 number-font">{{ Mst_categories::count() }}</h2>
                        <p class="text-muted">{{ __('Total Categories ') }}</p>
                    </div>
               </a>
            </div>
        </div> --}}


         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('store.list_product') }}">
                    <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-primary mb-0 box-primary-shadow">
                            <i class="fa fa-product-hunt text-white"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Products') }}</h6>
                        <h2 class="mb-2 number-font">{{$product}}</h2>
                        <p class="text-muted">{{ __('Store Products ') }}</p>
                    </div>
               </a>
            </div>
        </div>







           {{-- <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="#">
                    <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-info mb-0 box-info-shadow">
													<i class="fa fa-truck text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Delivery Boys') }}</h6>
                        <h2 class="mb-2 number-font">{{ @$delivery_boys }}</h2>
                        <p class="text-muted">{{ __('Total Delivery Boys ') }}</p>
                    </div>
               </a>
            </div>
        </div> --}}

        <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
         <div class="card">
            <a href="{{ url('store/today-order/list') }}">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-warning mb-0 box-info-shadow">
                                    <i class="fe fe-trending-up text-white"></i>
                  </div>
                     <h6 class="mt-4 mb-1">{{ __('Today\'s Sales') }}</h6>
                     <h2 class="mb-2 number-font">	<i class="fa fa-rupee"></i> {{ @$today_sale }}</h2>
                     <p class="text-muted">{{ __('Today\'s Sales Amount ') }}</p>
                 </div>
            </a>
         </div>
     </div>


    <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
         <div class="card">
            <a href="{{ route('store.store_visit_reports') }}">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-success mb-0 box-info-shadow">
                                    <i class="fe fe-trending-up text-white"></i>
                  </div>
                     <h6 class="mt-4 mb-1">{{ __('Today\'s Visit') }}</h6>
                     <h2 class="mb-2 number-font"> {{ @$recentvisitCountToday }}</h2>
                     <p class="text-muted">{{ __('Today\'s Customer Visit ') }}</p>
                 </div>
            </a>
         </div>
     </div>
     
     
      <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
         <div class="card">
            <a href="{{ route('store.store_visit_reports') }}">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-primary mb-0 box-info-shadow">
                                    <i class="fe fe-trending-up text-white"></i>
                  </div>
                     <h6 class="mt-4 mb-1">{{ __('Weekly Visit') }}</h6>
                     <h2 class="mb-2 number-font"> {{ @$recentvisitCountWeek }}</h2>
                     <p class="text-muted">{{ __('Weekly Customer Visit ') }}</p>
                 </div>
            </a>
         </div>
     </div>
     
     
    



        <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('store.current_issues') }}">
                  <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-warning mb-0 box-info-shadow">
													<i class="fa fa-comments text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Current Issues') }}</h6>
                        <h2 class="mb-2 number-font">{{ @$dispute_current }}</h2>
                        <p class="text-muted">{{ __('Current Issues Count ') }}</p>
                    </div>
               </a>
            </div>
        </div>

         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('store.new_issues') }}">
                  <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-danger mb-0 box-info-shadow">
													<i class="fa fa-comments text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('New Issues') }}</h6>
                        <h2 class="mb-2 number-font">{{ @$dispute_new }}</h2>
                        <p class="text-muted">{{ __('New Issues Count') }}</p>
                    </div>
               </a>
            </div>
        </div>

  <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('store.list_product') }}">
                  <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-danger mb-0 box-info-shadow">
													<i class="fa fa-comments text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __(' Categories') }}</h6>
                        <h2 class="mb-2 number-font">{{ @$catCount }}</h2>
                        <p class="text-muted">{{ __('Total Categories Count') }}</p>
                    </div>
               </a>
            </div>
        </div>





      </div>

   </div> 


   <!-- COL END -->
   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-6">


      <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
         <div class="card">
            <a href="{{ url('store/today-order/list') }}">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-cyan mb-0 box-info-shadow">
                                    <i class="fa fa-calendar text-white"></i>
                  </div>
                     <h6 class="mt-4 mb-1">{{ __('Daily Sales') }}</h6>
                     <h2 class="mb-2 number-font">	 {{ @$today_sale_count }}</h2>
                     <p class="text-muted">{{ __('Daily Sales Count ') }}</p>
                 </div>
            </a>
         </div>
     </div>




  <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('store.list_order') }}">
                  <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-info mb-0 box-info-shadow">
													<i class="fe fe-trending-up text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Total Sales') }}</h6>
                        <h2 class="mb-2 number-font">	<i class="fa fa-rupee"></i> {{ @$total_sale }}</h2>
                        <p class="text-muted">{{ __('Total Sales Amount ') }}</p>
                    </div>
               </a>
            </div>
        </div>
        
        
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
         <div class="card">
            <a href="{{ route('store.store_visit_reports') }}">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-secondary mb-0 box-info-shadow">
                                    <i class="fe fe-trending-up text-white"></i>
                  </div>
                     <h6 class="mt-4 mb-1">{{ __('Monthly Visit') }}</h6>
                     <h2 class="mb-2 number-font"> {{ @$recentvisitCountMonth }}</h2>
                     <p class="text-muted">{{ __('Monthly Customer Visit ') }}</p>
                 </div>
            </a>
         </div>
     </div>


        <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
         <div class="card">
            <a href="{{ route('store.list_disputes') }}">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-primary mb-0 box-info-shadow">
                                    <i class="fa fa-comments text-white"></i>
                  </div>
                     <h6 class="mt-4 mb-1">{{ __('Issues') }}</h6>
                     <h2 class="mb-2 number-font">{{ @$dispute }}</h2>
                     <p class="text-muted">{{ __('Total Issues ') }}</p>
                 </div>
            </a>
         </div>
     </div>

        <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
         <div class="card">
            <a href="{{ route('store.list_order') }}">
               <div class="card-body text-center statistics-info">
               <div class="counter-icon bg-secondary mb-0 box-secondary-shadow">
                  <i class="fe fe-codepen text-white"></i>
               </div>
               <h6 class="mt-4 mb-1">{{ __('Orders') }}</h6>
               <h2 class="mb-2 number-font">{{$order}}</h2>
               <p class="text-muted">{{ __('Total Orders') }}</p>
            </div>
            </a>
         </div>
      </div>

        <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ url('store/delivery-boys/list') }}">
                  <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-info mb-0 box-info-shadow">
													<i class="ti ti-truck text-white"></i>
							</div>
                        <h6 class="mt-4 mb-1">{{ __('Delivery Boys') }}</h6>
                        <h2 class="mb-2 number-font"> {{ @$deliveryBoys }}</h2>
                        <p class="text-muted">{{ __('Delivery Boys Count ') }}</p>
                    </div>
               </a>
            </div>
        </div>



    </div>





</div>
</div>

<!-- ROW-1 END -->
</div>

</div>
<!-- CONTAINER END -->
</div>



<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js"></script>
<script>
  var firebaseConfig = {
     apiKey: "AIzaSyABJjLKVYHKL020Zdi8pbHsNS2ZLQ1Ka4Q",
    authDomain: "yellowstore-web-application.firebaseapp.com",
    projectId: "yellowstore-web-application",
    storageBucket: "yellowstore-web-application.appspot.com",
    messagingSenderId: "444886856017",
    appId: "1:444886856017:web:935481722416346323e370",
    measurementId: "G-VX5SKTNN3F"
  };
      
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
  
  document.addEventListener("DOMContentLoaded", function(){

            messaging
            .requestPermission()
            .then(function () {
              //  console.log("working");
                return messaging.getToken({ vapidKey: 'BA6V328NpU3KBKusQbV067G1jKrBpypf1KmnNd21d5wt8gYmHDJIOFUvs0UeYGE1KvTrntnSTkBy3Otg0VQUFmc' });
            })
            .then(function(token) {
               // console.log(token);
     var _token = $('input[name="_token"]').val();

      $.ajax({
            url:"{{ route('store.saveBrowserToken') }}",
            method:"POST",
            data:{token:token, _token:_token},
            success:function(result)
            {
                console.log(result);
            }
       })         
  
            }).catch(function (err) {
                console.log('User Chat Token Error'+ err);
            });
  });      
    messaging.onMessage(function(payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(noteTitle, noteOptions);
    });
   
</script>

@endsection
