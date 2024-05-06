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
            <div class="">
               <!-- CONTAINER OPEN -->
               <div class="col col-login mx-auto">
                  <div class="text-center">
                    <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img" alt="">
                  </div>
               </div>
               <div class="container-login100">
                  <div class="wrap-login100 p-6" style="width:400px;">
                      
                     <form method="POST" id="toLogin" action="{{ route('store.tologin') }} ">
                         @csrf
                         
                        
                        
                        <span class="login100-form-title">
                        {{ __('OTP Verification') }}
                        </span>
                        @if ($message = Session::get('status'))
                        <div class="alert alert-success">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                     @if ($message = Session::get('validation_error'))
                        <div class="alert alert-danger">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                     @if ($message = Session::get('expiry_error'))
                        <div class="alert alert-warning">
                            <p>{{ $message }}</p>
                        </div>
                    @endif
                        @if (session()->has('message'))
                        <p class="alert alert-success">{{ session('message') }}</p>
                        @endif
                        
                         <div class="form-group">
                                <!--<label for="phone_no">Phone Number</label>-->
                                 <a href="#" class="mb-2" style="color:blue" onclick="phoneSendAuth()"  id="getcode" >Click here to get code! </a>
                                 <p id="sentSuccessMsg"></p> <br>

                                <input readonly type="hidden" class="form-control" value="{{$stores->store_mobile}}" name="phone_no" id="store_mobile" >
                            </div>
                            <div id="recaptcha-container"></div>
                             <div class="form-group mt-4">
                                 <input class="input100" type="hidden" id="otpSessionId" name="otp_session_id" value="">
                                <input type="text" name="" id="codeToVerify" name="getcode" class="form-control" placeholder="Enter Code">
                            </div>

                                <a href="#" class="btn btn-primary btn-block" onclick="codeverify()"  id="verifPhNum">Verify Phone No</a>
                                

                        <!--<div class="wrap-input100 validate-input">-->
                        <!--    <input type="hidden" name="store_id" value="{{$stores->store_id}}" >-->

                        <!--   <input class="input100" id="store_otp" type="store_otp" name="store_otp" placeholder="Enter 4 Digit OTP" value="{{ old('store_otp') }}" required autocomplete="store_otp" >-->
                        <!--   @error('store_otp')-->
                        <!--   <span class="invalid-feedback" role="alert">-->
                        <!--   <strong>{{ $message }}</strong>-->
                        <!--   </span>-->
                        <!--   @enderror-->
                        <!--</div>-->
                    
                        
                        <!-- <div class="container-login100-form-btn">-->
                          <!--<a onclick="return confirm('Do you want to Resend OTP?');" href="{{url('store/registration/otp_verify/resend_otp/'.$stores->store_id)}}" >Resend OTP?</a>-->
                        <!--  <a onclick="return confirm('Do you want to Resend OTP?');" href="#" id="resendOTP" >Resend OTP?</a>-->
                        <!--</div>-->

                        <!--<div class="container-login100-form-btn">-->
                        <!--   <button type="submit" class="login100-form-btn btn-primary">-->
                        <!--   {{ __('Submit') }}-->
                        <!--   </button>-->
                        <!--</div>-->
                        <!--<div class="container-login100-form-btn">-->
                        <!--   <button type="reset" class="login100-form-btn btn-danger">-->
                        <!--   {{ __('Clear') }}-->
                        <!--   </button><br>-->
                        <!--</div>-->
                     </form>
                  </div>
               </div>
               <!-- CONTAINER CLOSED -->
            </div>
         </div>
         <!-- End PAGE -->
      </div>


      <script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js"></script>
      <script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js"></script>
      <script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-auth.js"></script>
      <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
      

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

          window.onload=function () {
            render();
          };
        
            function render() {
                window.recaptchaVerifier=new firebase.auth.RecaptchaVerifier('recaptcha-container');
                recaptchaVerifier.render();
            }


        function phoneSendAuth() {
            var number = '+91'+$("#store_mobile").val();
            var _token= $('input[name="_token"]').val();
            console.log(number);
            console.log(window.recaptchaVerifier) 
            
          /*  firebase.auth().signInWithPhoneNumber(number,window.recaptchaVerifier).then(function (confirmationResult) {
                
                window.confirmationResult=confirmationResult;
                coderesult=confirmationResult;
                console.log(coderesult);
    
            console.log("hey! otp is on air");
                
            }).catch(function (error) {
                console.log(error.message);
            });*/
              $.ajax({
    url:"{{ route('store.sendotp') }}",
    method:"POST",
    data:{phone:number, _token:_token},
    success:function(result)
    {
      //alert("dszczs");
      console.log(result.session_id);
     if(result.status == 'success')
     {
      
      $('#otpSessionId').val(result.session_id);
      $('#sentSuccessMsg').html('<label class="text-success">Otp Has been Sent to'+number+'</label>');
      
     }
     else
     {
     
      $('#sentSuccessMsg').html('<label class="text-danger">Error! </label>');
      
       $('#submit').attr('disabled', 'disabled');

     }
    }
   });
        }


        function codeverify() {

  

var code = $("#codeToVerify").val();
var otp_session_id=$("#otpSessionId").val();
var _token= $('input[name="_token"]').val();
$.ajax({
    url:"{{ route('store.verifyotp') }}",
    method:"POST",
    data:{otp_session_id:otp_session_id,otp:code, _token:_token},
    success:function(result)
    {
      //alert("dszczs");
      console.log(result.session_id);
     if(result.status == 'success')
     {
            $("#sentSuccessMsg").text("OTP verified successfully.");

            $("#sentSuccessMsg").show();
            
            $('#toLogin').submit();
      
     }
     else
     {
            $("#sentSuccessMsg").html('<label class="text-danger">Otp Invalid! </label>');

            $("#sentSuccessMsg").show();

     }
    }
   });


    /*coderesult.confirm(code).then(function (result) {

        var user=result.user;

        console.log(user);

        console.log("otp success");



        }).catch(function (error) {
            console.log("otp invalid");
        });*/

    }


        </script>

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
      <script src="{{URL::to('/assets/plugins/input-mask/jquery.mask.min.js')}}"></script>
      <!-- CUSTOM SCROLL BAR JS-->
      <script src="{{URL::to('/assets/plugins/scroll-bar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
      <!-- CUSTOM JS-->
      <script src="{{URL::to('/assets/js/custom.js')}}"></script>




   </body>
</html>

