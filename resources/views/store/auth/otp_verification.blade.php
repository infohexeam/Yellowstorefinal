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
                     <!--<form method="POST" action="{{ route('otp_verify.store',$stores->store_id) }} ">-->
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
                                 <a href="#" class="mb-2" style="color:blue" id="getcode" >Click here to get code! </a>

                                <input readonly type="hidden" class="form-control" value="{{$stores->store_mobile}}" name="phone_no" id="number" >
                            </div>
                            <div id="recaptcha-container"></div>
                             <div class="form-group mt-4">
                                <input type="text" name="" id="codeToVerify" name="getcode" class="form-control" placeholder="Enter Code">
                            </div>

                                <a href="#" class="btn btn-primary btn-block" id="verifPhNum">Verify Phone No</a>
                                

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
                     <!--</form>-->
                  </div>
               </div>
               <!-- CONTAINER CLOSED -->
            </div>
         </div>
         <!-- End PAGE -->
      </div>


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


   
<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-auth.js"></script>
{{-- <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script> --}}


   
   {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/8.0.1/firebase.js"></script> --}}
<script>
    


$(document).ready(function() {

  

    const firebaseConfig = {
        apiKey: "AIzaSyABJjLKVYHKL020Zdi8pbHsNS2ZLQ1Ka4Q",
        authDomain: "yellowstore-web-application.firebaseapp.com",
        projectId: "yellowstore-web-application",
        storageBucket: "yellowstore-web-application.appspot.com",
        messagingSenderId: "444886856017",
        appId: "1:444886856017:web:935481722416346323e370",
        measurementId: "G-VX5SKTNN3F"
      };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig); 
    window.onload=function () {
                // $('#secDiv').hide();

      render();
    };

     function render() {
        window.recaptchaVerifier=new firebase.auth.RecaptchaVerifier('recaptcha-container');
        recaptchaVerifier.render();
    }


    // window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
    //     'size': 'invisible',
    //     'callback': function (response) {
    //         // reCAPTCHA solved, allow signInWithPhoneNumber.
    //       //  console.log('recaptcha resolved');
    //     }
    // }); 
    onSignInSubmit();
});



function onSignInSubmit() {
    $('#verifPhNum').on('click', function() {
        let phoneNo = '';
        var code = $('#codeToVerify').val();
      //  console.log(code);
        $(this).attr('disabled', 'disabled');
        $(this).text('Processing..');
        confirmationResult.confirm(code).then(function (result) {
            
            var _token = $('input[name="_token"]').val();
            var phoneNumber = $('#number').val();

              $.ajax({
                    url:"{{ route('saveOVS') }}",
                    method:"POST",
                    data:{phoneNumber:phoneNumber, _token:_token},
                    success:function(result)
                    {
                       // console.log(result);
                       window.location('https://yellowstore.in/store-login');

                    }
               })   
       
                  //  alert('Succecss');
            var user = result.user;
             console.log(user);

        }.bind($(this))).catch(function (error) {
        
            // User couldn't sign in (bad verification code?)
            // ...
            $(this).removeAttr('disabled');
            $(this).text('Invalid Code');
            setTimeout(() => {
                $(this).text('Verify Phone No');
            }, 2000);
        }.bind($(this)));
    
    });
     
    
    $('#getcode').on('click', function () {
        var phoneNo = $('#number').val();
        console.log(phoneNo);
        // getCode(phoneNo);
        var appVerifier = window.recaptchaVerifier;
           console.log(appVerifier); 
        phoneNo = '+91'+phoneNo;
            //  console.log(phoneNo);
            firebase.auth().signInWithPhoneNumber(phoneNo,window.recaptchaVerifier).then(function (confirmationResult) {
              
            window.confirmationResult=confirmationResult;
            coderesult=confirmationResult;
  
        }).catch(function (error) {
            // $("#error").text(error.message);
            // $("#error").show();
            console.log(error.message);

        });

//   firebase.auth().signInWithPhoneNumber(phoneNo, appVerifier)
//         .then(function (confirmationResult) {
    
//             window.confirmationResult=confirmationResult;
//             coderesult=confirmationResult;
//             console.log(coderesult);
//         }).catch(function (error) {
//             console.log(error.message);
    
//         });
    });
}



</script>


</html>

