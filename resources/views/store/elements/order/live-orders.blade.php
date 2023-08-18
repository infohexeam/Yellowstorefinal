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

$banners = Mst_StoreAppBanner::where('town_id',@$store->town_id)->orWhere('town_id', null)->get();




@endphp
<style>
    @keyframes blink {
      0%, 100% {
        opacity: 0;
      }
      50% {
        opacity: 1;
      }
    }
    .blink {
      animation: blink 5s linear infinite;
    }
  </style>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<!-- ROW-1 -->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-lg-12">
           
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class=card>
                            <button type="button" style="display:none" id="activateBtn"></button>
                            <div class="card-body " id="newOrders">
                            </div>
                            </div>
                        </div>
                    </div>
                
            </div>
            </div>
            </div>
            


  


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                console.log("working");
                return messaging.getToken({ vapidKey: 'BP_JpvDrfy-521nigpqvOv5WTXBK5i5AMa2g8CvI-nXK_mxHAQFHHrtwJc-kLAWNBtM-AJjqEARZ_49e6Wph5e8' });
            })
            .then(function(token) {
                console.log("token : "+token);
     var _token = $('input[name="_token"]').val();
     console.log("token_2 : "+_token);

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
        // Play the alarm sound when a notification is received
        const alarmSound = new Audio('https://hexprojects.in/Yellowstore/assets/Alaram.wav');
        alarmSound.play();
        new Notification(noteTitle, noteOptions);
    });
   
</script>
<script>
var newOrders = [];
document.addEventListener("DOMContentLoaded", function() {
    var button = document.getElementById('activateBtn');
    button.click(); // Simulate the button click event
});
  getData();

  setInterval(function(){ getData(); }, 4000);

  function getData(){
   //alert(123);
    $.ajax({
      type: 'get',
      url: "{{ route('store_new_orders_all') }}",
      dataType: 'json',
      contentType: false,
      cache: false,
      processData:false,

      success: function(response){ 
        newOrders = response.newOrders;
        var ordersCount = newOrders.length;
        console.log(newOrders,ordersCount)
         //alert(123);

         if(ordersCount>0)
        {
           
        playNotificationSound();
        $('#newOrders').html("");


        }
        else
        {

            //lert('No live orders available');
            $('#newOrders').html("<h4>NO LIVE ORDERS NOW!!!</h4>");
        }
        RenderNewOrders();
       
        
      },
      
    })
  }
   function RenderNewOrders() {
    $('.neworder').remove();
    $.each(newOrders, function(index, value){
      if (value.TEST == 0) {
        //NotifyNewOrder(value.id);
         
      }
      var html='<div class="card neworder blink col-lg-12 col-md-12 col-sm-12 col-xl-12"><div class="card-header bg-info text-white">Notification</div><div class="card-body"><h3 class="card-title">New Order('+value.order_number+') Received</h3><p class="card-text">'+value.updated_at+'</p><p class="card-text">Total:&#8377;'+value.total+'</p><a href="{{ url('store/order/view') }}/' + value.order_id + '">View Order</a></div></div>';
      $('#newOrders').append(html);
    });
  }
  function playNotificationSound() {
    var alarmSound = new Audio('https://hexprojects.in/Yellowstore/assets/alaram.wav');
    alarmSound.play();
  }

  
  </script>
@endsection
