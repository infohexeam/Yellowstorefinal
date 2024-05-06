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
                   <img src="{{URL::to('/assets/front-end/image/logo-white.png')}}" class="header-brand-img" alt=""> 
                  </div>
               </div>
               <div class="container-login100">
                   
                   
                    
                        
                  <div   class="wrap-login100 p-6" style="width:400px;">
                     <form id="myForm" method="POST" action="{{ route('store_password.change') }}">
                        @csrf
                        <span class="login100-form-title">
                        {{ __('Password Reset') }}
                        </span>
                        
                        <input type="hidden" id="storeId" id="store_id" />
                        
                         <section id="sendOTPSec">

                            <div class="wrap-input100 validate-input">
                                <input class="input100" id="store_mobile" oninput="mobileValidation()" type="text" 
                                onchange="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  name="store_mobile" 
                                placeholder="Mobile Number" value="{{ old('store_mobile') }}" autocomplete="store_mobile" >
                                <p id="error_store_mobile"></p>
                            </div>
                            
                            <div id="recaptcha-container"></div>
    
                            <div class="container-login100-form-btn">
                               <a id="SendOTPBtn" onclick="sendOTP()" class="login100-form-btn btn-primary">
                               {{ __('Send OTP') }}
                               </a>
                            </div>
                        </section>
                        
                       
                       

                        
                          <section  id="confirmOTPSec" >
                              <p id="sentSuccessMsg"></p>
                              <input type="hidden" value="{{url()->full()}}"  name="curUrl" id="curUrl" />
                            
                            <div class="wrap-input100 validate-input">
                                <input class="input100" id="otp"  type="number" name="otp" placeholder="Enter OTP" value="{{ old('otp') }}" autocomplete="otp" >
                                <p id="error_otp"></p>
                            </div>
                            
                            <div class="container-login100-form-btn">
                                <input class="input100" type="hidden" id="otpSessionId" name="otp_session_id" value="">
                               <a  onclick="codeverify()" class="login100-form-btn btn-primary">
                               {{ __('Confirm OTP') }}
                               </a>
                            </div>
                            <div class="container-login100-form-btn">
                               <a  onclick="goBack()"  class="login100-form-btn btn-secondary">
                               {{ __('Go Back') }}
                               </a>
                            </div>
                            
                        </section>

                        
                        
                        
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

function goBack(){
    $('#confirmOTPSec').hide();
            $('#sendOTPSec').show();
    
}

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
        $('#confirmOTPSec').hide();
        render();
     };
     
    function render() {
        window.recaptchaVerifier=new firebase.auth.RecaptchaVerifier('recaptcha-container');
        recaptchaVerifier.render();
    }
    
    function sendOTP(){
       console.log(mobileValidation());
        if(mobileValidation()){
             
             $('#error_store_mobile').empty();

            var number = '+91'+$("#store_mobile").val();
            var _token= $('input[name="_token"]').val();
            
             /*firebase.auth().signInWithPhoneNumber(number,window.recaptchaVerifier).then(function (confirmationResult) {
                  
                window.confirmationResult=confirmationResult;
                coderesult=confirmationResult;
                console.log(coderesult);
      
                $("#sentSuccessMsg").text("OTP send successfully.");
                $("#sentSuccessMsg").show();
                  
            }).catch(function (error) {
                $("#sentSuccessMsg").text(error.message);
                $("#sentSuccessMsg").show();
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
       $("#sentSuccessMsg").show();
      $('#otpSessionId').val(result.session_id);
      $('#sentSuccessMsg').html('<label class="text-success">Otp Has been Sent to'+number+'</label>');
      $("#sentSuccessMsg").text("OTP send successfully.");
     
       $('#confirmOTPSec').show();
     $('#sendOTPSec').hide();
      
     }
     else
     {
     
       $('#error_store_mobile').html('<label class="text-danger">Mobile number invalid </label>');
            
            $("form").submit(function(e){
                e.preventDefault(e);
            }); 

     }
    }
   });
            
            
            
        }else{
            $('#error_store_mobile').html('<label class="text-danger">Mobile number invalid </label>');
            
            $("form").submit(function(e){
                e.preventDefault(e);
            }); 
        }
    }
    
   function codeverify() {
        var code = $("#otp").val();
        var number= $("#store_mobile").val();
        var _token= $('input[name="_token"]').val();
        var otp_session_id=$("#otpSessionId").val();

       /* coderesult.confirm(code).then(function (result) {
            var user=result.user;
            //console.log(user);
            $("#sentSuccessMsg").text("OTP verified successfully.");
            $("#sentSuccessMsg").show();
            console.log("is  working");
        
        
        
              

               
               $.ajax({
                url:"{{ route('store.store_hashcode') }}",
                method:"POST",
                data:{number:number, _token:_token},
                success:function(result)
                {
                    if(result != false){
                                      var curUrl = $("#curUrl").val();
                            window.location.replace(curUrl+"/user?user_id="+result);

                        // console.log(curUrl+"/user?user_id="+result);

                    }
                    else
                        alert("error! Please reload page...");

                }
            });  
            

            
              //console.log(curUrl+"/customer?customer_id=12121212");


            

        }).catch(function (error) {
            $("#sentSuccessMsg").text("OTP invalid");
            $("#sentSuccessMsg").show();
             $("form").submit(function(e){
                e.preventDefault(e);
            }); 
        });*/
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
            
             $.ajax({
                url:"{{ route('store.store_hashcode') }}",
                method:"POST",
                data:{number:number, _token:_token},
                success:function(result)
                {
                    if(result != false){
                                      var curUrl = $("#curUrl").val();
                            window.location.replace(curUrl+"/user?user_id="+result);

                        // console.log(curUrl+"/user?user_id="+result);

                    }
                    else
                        alert("error! Please reload page...");

                }
            });  
      
     }
     else
     {
            $("#sentSuccessMsg").text("OTP invalid");
            $("#sentSuccessMsg").show();
             $("form").submit(function(e){
                e.preventDefault(e);
            }); 

     }
    }
   });
        
        // $("form").submit(function(e){
        //     e.preventDefault(e);
        // }); 

    }
    
</script>

<script type="text/javascript">
	
	function mobileValidation(){
	    
    	var number= $("#store_mobile").val();

    	
    	//document.forms["myForm"]["store_mobile"].value;///get id with value
    	var numberpattern=/^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;////Regular expression
    	
	    if(numberpattern.test(number))
	    {
            $('#error_store_mobile').empty();
            $('#submit').attr('disabled', false);
            var _token= $('input[name="_token"]').val();
            
            $.ajax({
                url:"{{ route('store.store_mobile_isExists') }}",
                method:"POST",
                data:{number:number, _token:_token},
                success:function(result)
                {
                    console.log(result);
                    if(result == 'exists')
                    {
                      $('#error_store_mobile').empty();
                      $('#SendOTPBtn').attr('disabled', false);
                      return true;
                    }
                    else
                    {
                        $('#error_store_mobile').html('<label class="text-danger">Store account desn\'t exist with this number </label>');
                        $('#SendOTPBtn').prop('disabled', true);
                        return false;
                    }
                }
            });
        }
        else
        {
            $('#error_store_mobile').html('<label class="text-danger">Mobile number invalid </label>');
            $('#SendOTPBtn').attr('disabled', true);
            return false;
        }
        
        return true;

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
