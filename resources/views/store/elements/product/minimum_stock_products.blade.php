@extends('store.layouts.app')
@section('content')
 
<style>
  /*  @keyframes blink {
      0%, 100% {
        opacity: 0;
      }
      50% {
        opacity: 1;
      }
    }
    .blink {
      animation: blink 500s linear infinite;
    }*/
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
                            <div class="card-body " id="newProducts">
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
        const alarmSound = new Audio('https://hexprojects.in/Yellowstore/assets/alaram.wav');
        alarmSound.play();
        new Notification(noteTitle, noteOptions);
    });
   
</script>

@endsection
