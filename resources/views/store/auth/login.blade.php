<!doctype html>
<html lang="en" dir="ltr">
   <head>
      <head>
         <!-- META DATA -->
         <meta charset="UTF-8">
         <meta name="csrf-token" content="{{ csrf_token() }}">
         <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
         <meta http-equiv="X-UA-Compatible" content="IE=edge">
         <!-- FAVICON -->
         <link rel="shortcut icon" type="image/x-icon" href="{{URL::to('/assets/uploads/favicon.png')}}" />
         <!-- TITLE -->
         <title>{{ __('YellowStore | Store') }}</title>
         <!-- BOOTSTRAP CSS -->
         <link href="{{URL::to('/assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" />
         <!-- STYLE CSS -->
         <link href="{{URL::to('/assets/css/style.css')}}" rel="stylesheet"/>
         <link href="{{URL::to('/assets/css/skin-modes.css')}}" rel="stylesheet"/>
         <!-- SIDE-MENU CSS -->
         <link href="{{URL::to('/assets/plugins/sidemenu/closed-sidemenu.css')}}" rel="stylesheet">
         <!-- SINGLE-PAGE CSS -->
         <link href="{{URL::to('/assets/plugins/single-page/css/main.css')}}" rel="stylesheet" type="text/css">
         <!--C3 CHARTS CSS -->
         <link href="{{URL::to('/assets/plugins/charts-c3/c3-chart.css')}}" rel="stylesheet"/>
         <!-- CUSTOM SCROLL BAR CSS-->
         <link href="{{URL::to('/assets/plugins/scroll-bar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
         <!--- FONT-ICONS CSS -->
         <link href="{{URL::to('/assets/css/icons.css')}}" rel="stylesheet"/>
         <!-- COLOR SKIN CSS -->
         <link id="theme" rel="stylesheet" type="text/css" media="all" href="{{URL::to('/assets/colors/color1.css')}}" />
        



   </head>
   <body>
      <!-- BACKGROUND-IMAGE -->
      <div class="login-img">
         <!-- GLOABAL LOADER -->
         <div id="global-loader">
            <img src="../assets/images/loader.svg" class="loader-img" alt="Loader">
         </div>
         <!-- /GLOABAL LOADER -->
         <!-- PAGE -->
         <div class="page">
            <!--   <center>-->
            <!--    <button id="btn-nft-enable" onclick="initFirebaseMessagingRegistration()" class="btn btn-danger btn-xs btn-flat">Allow for Notification</button>-->
            <!--</center>-->
            
            <div class="">
               <!-- CONTAINER OPEN -->
               <div class="col col-login mx-auto">
                  <div class="text-center">
                     <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img" alt="">
                  </div>
               </div>
            <!--    <center>-->
            <!--    <button id="btn-nft-enable" onclick="initFirebaseMessagingRegistration()" class="btn btn-danger btn-xs btn-flat">Allow for Notification</button>-->
            <!--</center><br>-->
               <div class="container-login100">
                  <div class="wrap-login100 p-6" style="width:400px;">
                     <form method="POST" action="{{ route('store_login') }} ">
                        @csrf
                        <span class="login100-form-title">
                        {{ __('Store Login') }}
                        </span>
                        @if(session('status'))
                        <div class="alert alert-success" id="err_msg">
                           <p>{{session('status')}}</p>
                        </div>
                        @endif
                        @if (count($errors) > 0)
                        @foreach ($errors->all() as $error)
                        <p class="alert alert-danger">{{ $error }}</p>
                        @endforeach
                        @endif
                        @if (session()->has('message'))
                        <p class="alert alert-success">{{ session('message') }}</p>
                        @endif
                        <div class="wrap-input100 validate-input">
                           {{-- <input class="input100" id="store_username" type="store_username" name="store_username" placeholder="Username" value="{{ old('store_username') }}" required autocomplete="store_username" >
                           @error('store_username')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror --}}

                            <input class="input100" id="store_mobile" type="username" name="store_username" placeholder="Mobile Number" value="{{ old('store_mobile') }}" required autocomplete="store_mobile" >
                           @error('username')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror

                        </div>
                        <div class="wrap-input100 validate-input">
                           <input class="input100" type="password" name="password" placeholder="Password" id="password" type="password" required autocomplete="current-password">
                           <div class="input-group-addon">
                              <a href=""><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                            </div>
                          
                           @error('password')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                           <span class="focus-input100"></span>
                        </div>
                         <div class="container-login100-form-btn">
                          <a href="{{route('store_password.request')}}">Forgot Password?</a>
                           </button>
                        </div>
                        <div class="container-login100-form-btn">
                           <button type="submit" class="login100-form-btn btn-primary">
                           {{ __('Login') }}
                           </button>
                        </div>
                        <div class="container-login100-form-btn">
                           <button type="reset" class="login100-form-btn btn-danger">
                           {{ __('Clear') }}
                           </button><br>
                        </div>
                     </form>
                  </div>
               </div>
               <!-- CONTAINER CLOSED -->
            </div>
         </div>
         <!-- End PAGE -->
      </div>
      
      
      
      
      
 
    
   </body>

   <script>
      $(document).ready(function() {
    $("#password a").on('click', function(event) {
        event.preventDefault();
        if($('#password input').attr("type") == "text"){
            $('#password input').attr('type', 'password');
            $('#password i').addClass( "fa-eye-slash" );
            $('#password i').removeClass( "fa-eye" );
        }else if($('#password input').attr("type") == "password"){
            $('#password input').attr('type', 'text');
            $('#password i').removeClass( "fa-eye-slash" );
            $('#password i').addClass( "fa-eye" );
        }
    });
});
   </script>
   
   
        <script src="{{URL::to('/assets/js/jquery-3.4.1.min.js')}}"></script>

<!--<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js"></script>-->
<script>
//   var firebaseConfig = {
//      apiKey: "AIzaSyABJjLKVYHKL020Zdi8pbHsNS2ZLQ1Ka4Q",
//     authDomain: "yellowstore-web-application.firebaseapp.com",
//     projectId: "yellowstore-web-application",
//     storageBucket: "yellowstore-web-application.appspot.com",
//     messagingSenderId: "444886856017",
//     appId: "1:444886856017:web:935481722416346323e370",
//     measurementId: "G-VX5SKTNN3F"
//   };
      
//     firebase.initializeApp(firebaseConfig);
//     const messaging = firebase.messaging();
  
//     function initFirebaseMessagingRegistration() {
//             messaging
//             .requestPermission()
//             .then(function () {
//               //  console.log("working");
//                 return messaging.getToken({ vapidKey: 'BA6V328NpU3KBKusQbV067G1jKrBpypf1KmnNd21d5wt8gYmHDJIOFUvs0UeYGE1KvTrntnSTkBy3Otg0VQUFmc' });
//             })
//             .then(function(token) {
//                 console.log(token);
   
               
  
//             }).catch(function (err) {
//                 console.log('User Chat Token Error'+ err);
//             });
//      }  
      
//     messaging.onMessage(function(payload) {
//         const noteTitle = payload.notification.title;
//         const noteOptions = {
//             body: payload.notification.body,
//             icon: payload.notification.icon,
//         };
//         new Notification(noteTitle, noteOptions);
//     });
   
</script>
   
   <script type="module">
  // Import the functions you need from the SDKs you need
//   import { initializeApp } from "https://www.gstatic.com/firebasejs/9.1.3/firebase-app.js";
//   import { getAnalytics } from "https://www.gstatic.com/firebasejs/9.1.3/firebase-analytics.js";
//  import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/9.1.3/firebase-messaging.js";


  // Your web app's Firebase configuration
  // For Firebase JS SDK v7.20.0 and later, measurementId is optional
//   const firebaseConfig = {
//     apiKey: "AIzaSyABJjLKVYHKL020Zdi8pbHsNS2ZLQ1Ka4Q",
//     authDomain: "yellowstore-web-application.firebaseapp.com",
//     projectId: "yellowstore-web-application",
//     storageBucket: "yellowstore-web-application.appspot.com",
//     messagingSenderId: "444886856017",
//     appId: "1:444886856017:web:935481722416346323e370",
//     measurementId: "G-VX5SKTNN3F"
//   };

//   const app = initializeApp(firebaseConfig);
//   const analytics = getAnalytics(app);

// const messaging = getMessaging(); 
//   console.log(app , analytics, messaging);
 



</script>
   
      <!-- firebase integration started -->

    <!--<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase.js"></script>-->
    <!--    <script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-messaging.js"></script>-->

    <!-- Firebase App is always required and must be first -->
    <!--<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-app.js"></script>-->
    
    <!-- Add additional services that you want to use -->
    <!--<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-auth.js"></script>-->
    <!--<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-database.js"></script>-->
    <!--<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-firestore.js"></script>-->
    <!--<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-functions.js"></script>-->
    
    <!-- firebase integration end -->
    
    <!-- Comment out (or don't include) services that you don't want to use -->
    <!-- <script src="https://www.gstatic.com/firebasejs/5.5.9/firebase-storage.js"></script> -->
    
    <!--<script src="https://www.gstatic.com/firebasejs/5.5.9/firebase.js"></script>-->
    <!--<script src="https://www.gstatic.com/firebasejs/7.8.0/firebase-analytics.js"></script>-->
    

   
     <!-- BACKGROUND-IMAGE CLOSED -->
      <!-- JQUERY JS -->
      <script src="{{URL::to('/assets/js/jquery-3.4.1.min.js')}}"></script>
      <!-- BOOTSTRAP JS -->
      <script src="{{URL::to('/assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
      <script src="{{URL::to('/assets/plugins/bootstrap/js/popper.min.js')}}"></script>
      <!-- SPARKLINE JS -->
      <script src="{{URL::to('/assets/js/jquery.sparkline.min.js')}}"></script>
      <!-- CHART-CIRCLE JS -->
      <script src="{{URL::to('/assets/js/circle-progress.min.js')}}"></script>
      <!-- RATING STAR JS -->
      <script src="{{URL::to('/assets/plugins/rating/jquery.rating-stars.js')}}"></script>
      <!-- INPUT MASK JS -->
      <!-- CUSTOM SCROLL BAR JS-->
      <script src="{{URL::to('/assets/plugins/scroll-bar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
      <!-- CUSTOM JS-->
      <script src="{{URL::to('/assets/js/custom.js')}}"></script>
      
      
          

      
</html>
