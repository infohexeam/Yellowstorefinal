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
         <style>

            .password-show {
              position: relative;
            }
            .password-show input {
              padding-right: 2.5rem;
            }
            .password-show__toggle {
              position: absolute;
              top: 15px;
              right: 0;
              bottom: 0;
              width: 2.5rem;
            }
            .password-show_toggleshow-icon, .password-showtoggle_hide-icon {
              position: absolute;
              top: 50%;
              left: 50%;
              transform: translate(-50%, -50%);
              color: #555;
            }
            .password-show_toggle_show-icon {
              display: block;
            }
            .password-show.show .password-show_toggle_show-icon {
              display: none;
            }
            .password-show_toggle_hide-icon {
              display: none;
            }
            .password-show.show .password-show_toggle_hide-icon {
              display: block;
            }
            </style>  
              <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSqyoP-FHj6nJpuIvNYmb1YaGqBmh3xdQ&libraries=places"></script>

          <script type="text/javascript">
        function initialize() {
            var input3 = document.getElementById('store_place_id'); // replace textbox id here
            var autocomplete3 = new google.maps.places.Autocomplete(input3);

            // Listen for the 'place_changed' event
            google.maps.event.addListener(autocomplete3, 'place_changed', function () {
                var place = autocomplete3.getPlace();
                
                // Check if the place has a geometry location
                if (place.geometry) {
                    var latitude = place.geometry.location.lat();
                    var longitude = place.geometry.location.lng();

                    // Use latitude and longitude as needed
                    console.log("Latitude: " + latitude);
                    console.log("Longitude: " + longitude);
                  document.getElementById('latitude').value = latitude;
                document.getElementById('longitude').value = longitude;
                    //alert(latitude)
                }
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
    </script>

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
                  <div id="firstDiv" class="wrap-login100 p-6" style="width:800px;">
                                             <div class="alert alert-success" id="sentSuccess" style="display: none;"></div>

                     <form method="POST" id="myForm" onsubmit="return validateForm()" action="{{ route('register.store') }} ">
                        @csrf
                        <span class="login100-form-title">
                        {{ __('Store Registration') }}
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

                        <div class="row">
                            <div class="col-md-12">
                                <div class="wrap-input100 validate-input">
                                    <input class="input100" id="store_name" type="store_name" name="store_name" placeholder="Store Name *" value="{{ old('store_name') }}" required autocomplete="store_name" >
                                                               <span id="error_username"></span>
                                                               <span id="error_sname" style="color:red;" ></span>

                                    {{-- @error('store_name')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>
                            
                             <div class="col-md-6">
                                 
                                <div class="wrap-input100 validate-input">
                                    <input class="input100" id="store_mobile" onchange="mobileValidation()" type="text" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  name="store_mobile" placeholder="Store Mobile Number *" value="{{ old('store_mobile') }}" required autocomplete="store_mobile" >
                        <span id="error_smob" style="color:red;" ></span>

                                    <span id="error_store_mobile"></span>

                                    {{-- @error('store_mobile')
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>
                           <div class="col-md-6">
                                <div class="wrap-input100 validate-input">
                                    <input class="input100" id="store_contact_person_phone_number" type="text" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  name="store_contact_person_phone_number" placeholder="Contact Person Number *" value="{{ old('store_contact_person_phone_number') }}" required autocomplete="store_username" >
                                     <span id="error_cmob" style="color:red;" ></span>
                                    {{-- @error('store_contact_person_phone_number')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>
                             <!-- <div class="col-md-6">
                                <div class="wrap-input100 validate-input">
                                    <input class="input100" id="email" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autocomplete="email" >
                                    <span id="error_email"></span>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>  -->
                        </div>

                        <!-- <div class="row">

                            <div class="col-md-6">
                                <div class="wrap-input100 validate-input">
                                    <input class="input100" id="store_contact_person_name" type="text" name="store_contact_person_name" placeholder="Contact Person Name *" value="{{ old('store_contact_person_name') }}" required  >
                                    {{-- @error('store_contact_person_name')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>

                             <div class="col-md-6">
                                <div class="wrap-input100 validate-input">
                                    <input class="input100" id="store_contact_person_phone_number" type="text" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  name="store_contact_person_phone_number" placeholder="Contact Person Number *" value="{{ old('store_contact_person_phone_number') }}" required autocomplete="store_username" >
                                    {{-- @error('store_contact_person_phone_number')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>

                        </div> -->
                        <div class="row">
                        <div class="col-md-6">
                      <div class="wrap-input100 validate-input">
                        <input class="input100" id="store_gst_number" type="text" name="store_gst_number" placeholder="Registered GSTIN" value="{{ old('store_gst_number') }}"   >
                        @error('store_gst_number')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        </div>
                        </div>
                        <div class="col-md-6">
                                <div class="wrap-input100 validate-input">
                                    <input class="input100" id="store_place_id" type="text" name="store_place" placeholder="Store Location *" value="{{ old('store_place') }}" required  >
                                     <input type="hidden" id="latitude" name="latitude">
                                        <input type="hidden" id="longitude" name="longitude">
                                        @error('store_place')
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                       
                                </div>
                            </div>
                            </div>

                        <!-- <div class="wrap-input100 validate-input">
                                <textarea  class="input100" id="store_contact_address"  name="store_contact_address" placeholder="Store Address *" required>{{ old('store_contact_address') }}</textarea>
                                    {{-- @error('store_contact_address')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                        </div> -->

                        <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="wrap-input100 validate-input">

                                             <div id="countryl"></div>
                                <select required name="store_country_id"   class="input100"  id="country" >
                                 <option value="">Country *</option>
                                @foreach($countries as $key)
                                <option {{old('store_country_id') == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>

                                    {{-- @error('store_country_id')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="wrap-input100 validate-input">

                                <select required name="store_state_id"   class="input100"  id="state" >
                                <option value="">State *</option>
                                            @php
                                                $states = \DB::table('sys_states')->get();
                                             //   dd($states);
                                            @endphp
                                        @foreach(@$states as $key)
                                                <option {{old('store_state_id') == @$key->state_id ? 'selected':''}} value="{{@$key->state_id}}"> {{@$key->state_name }} </option>
                                        @endforeach
                                </select>

                                    {{-- @error('store_state_id')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>
                        </div> -->

                        <!-- <div class="row">
                            <div class="col-md-6">
                                <div class="wrap-input100 validate-input">

                     <select required name="store_district_id"  class="input100"  id="city">
                             <option value="">District *</option>
                               @php
                                        $districts = \DB::table('mst_districts')->get();
                                    @endphp
                                  @foreach(@$districts as $key)
                                    <option {{old('store_district_id') == @$key->district_id ? 'selected':''}} value="{{@$key->district_id}}"> {{@$key->district_name }} </option>
                                    @endforeach
                          </select>

                                    {{-- @error('store_district_id')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="wrap-input100 validate-input">
                     <select required name="store_town_id"  class="input100"  id="town">
                             <option value="">Town *</option>
                                 @php
                                        $town = \DB::table('mst_towns')->get();
                                    @endphp 
                                    @foreach(@$town as $key)
                                    <option {{old('store_town_id') == @$key->town_id ? 'selected':''}} value="{{@$key->town_id}}"> {{@$key->town_name }} </option>
                                @endforeach
                        </select>

                                    {{-- @error('store_town')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>
                        </div> -->

                        <div class="row">
                            <!--<div class="col-md-6">-->
                            <!--    <div class="wrap-input100 validate-input">-->
                            <!--        <input class="input100" id="store_place" type="text" name="store_place" placeholder="Store Location *" value="{{ old('store_place') }}" required  >-->
                            <!--            @error('store_place')-->
                            <!--            <span class="invalid-feedback" role="alert">-->
                            <!--            <strong>{{ $message }}</strong>-->
                            <!--            </span>-->
                            <!--            @enderror-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="col-md-12">
                                <div class="wrap-input100 validate-input">
                                    <select name="business_type_id" id="business_type_id" required="" class="input100"  >
                                        <option value="">Business Type *</option>
                                        @foreach(@$business_types as $key)
                                        <option {{old('business_type_id') == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                                        @endforeach
                                    </select>
                                    <span id="error_bti" style="color:red;" ></span>

                                    {{-- @error('business_type_id')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror --}}
                                </div>
                            </div>
                        </div>

                        <!-- <div class="wrap-input100 validate-input">
                           <input class="input100" type="text" name="username" value="{{old('username')}}" placeholder="Username *" id="username" required >
                           {{-- @error('username')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror --}}
                           <span class="focus-input100"></span>
                        </div>
 -->


                        <div id="passlabel" class="wrap-input100 validate-input form-group">
                            <div class="password-show">

                           <input class="input100 form-control" type="password" onkeyup="validatePassLength()" oninput="checkPasswordComplexity(this.value)" name="password" value="{{old('password')}}" placeholder="Password *" id="password" type="password" required autocomplete="current-password">
                           <div class="password-show__toggle">
                            <i class="fa fa-eye password-show_toggle_show-icon"></i>
                            <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                          </div> 
                                                              <span id="error_pass" style="color:red;" ></span>

                          
                          <p id="showpassmessage"><p>
                            <p id="showpassmessage2"><p>
    
                           {{-- @error('password')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror --}}
                           <span  class="focus-input100"></span>
                        </div>

                        </div>

                        <div class="wrap-input100 validate-input form-group">
                            <div class="password-show">

                           <input class="input100 form-control" type="password" onkeyup="validatePass()" name="password_confirmation" placeholder="Confirm Password *" value="{{old('password_confirmation')}}" id="confirm_password" type="password" required autocomplete="current-password">
                           <div class="password-show__toggle">
                            <i class="fa fa-eye password-show_toggle_show-icon"></i>
                            <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                          </div>        
                           <p id="showmessage"><p>
           {{-- @error('password_confirmation')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror --}}

                           <span  class="focus-input100"></span>
                        </div>

                        </div>
                        
                    <label class="float-center">
                        <input required type="checkbox" value="1" name="tc" id="tc"> I Agree to the 
                        <a target="_blank" href="{{ url('store/terms-and-condition') }}">Terms and Conditions</a>
                            <br>                                
                        <span id="error_tc" style="color:red" ></span>

                    </label>
                    
                                <div id="recaptcha-container"></div>


                        <div class="container-login100-form-btn">
                           <button type="submit" id="submit" onclick="phoneSendAuth();" class="text-white login100-form-btn btn-primary">
                           {{ __('Register Now') }}
                           </button>
                        </div>
                         <div class="container-login100-form-btn">
                           <a href="{{ url('store-login') }}"  class="login100-form-btn btn-primary">
                           {{ __('Login') }}
                           </a>
                        </div>
                        <div class="container-login100-form-btn">
                           <button type="reset" class="login100-form-btn btn-danger">
                           {{ __('Clear') }}
                           </button><br>
                        </div>
                     </form>
                  </div>
                  
                <div id="secDiv" class="wrap-login100 p-6" style="width:800px;">
    
                        <p id="sentSuccessMsg"></p> <br>

                    <div class="row">
                        <input class="input100" type="hidden" id="otpSessionId" name="otp_session_id" value="">
                        <div class="col-md-12">
                            <div class="wrap-input100 validate-input">
                                <input class="input100" id="otp" type="text" name="otp" placeholder="OTP" value="{{ old('otp') }}" >
                

                            </div>
                        </div>
                    </div>
                    <div class="container-login100-form-btn">
                        <a href="#" onclick="phoneSendAuth()" style="btn btn-warning btn-sm" >Resend otp?</a>
                    </div>

                    <div class="container-login100-form-btn">
                       <a href="#" id="otpVerify" onclick="codeverify();" class="login100-form-btn btn-primary">
                       {{ __('Verify') }}
                       </a>
                    </div>
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
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>


 <script>

  $(document).ready(function() {
    $(".password-show__toggle").on("click", function(e) {
      console.log("click");
      if (
        !$(this)
          .parent()
          .hasClass("show")
      ) {
        $(this)
          .parent()
          .addClass("show");
        $(this)
          .prev()
          .attr("type", "text");
      } else {
        $(this)
          .parent()
          .removeClass("show");
        $(this)
          .prev()
          .attr("type", "password");
      }
    });
  });
     </script>
     

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
//sentSuccess
//store_mobile
//

  window.onload=function () {
              $('#secDiv').hide();

    render();
  };

  function render() {
      window.recaptchaVerifier=new firebase.auth.RecaptchaVerifier('recaptcha-container');
      recaptchaVerifier.render();
  }
  
  
  function phoneSendAuth() {
      
      var sName = $('#store_name').val();
      var sPhone = $('#store_mobile').val();
      var sBusinessType = $('#business_type_id').val();
       var sContactNumber = $('#store_contact_person_phone_number').val();
      
      var sPass = $('#password').val();
      var sConfPass = $('#confirm_password').val();
      var sTC = $('#tc').val();
      
      if(sName == ''){
          $('#error_sname').text('Store name required');
      }else{
          $('#error_sname').text('');
      }
      
       if(sPhone == ''){
          $('#error_smob').text('Store mobile required');
      }else{
          $('#error_smob').text('');
      }
       if(sContactNumber == ''){
          $('#error_cmob').text('Store contact person phone number required ');
      }else{
          $('#error_cmob').text('');
      }
      
      if(sBusinessType == ''){
          $('#error_bti').text('Business type required');
      }else{
          $('#error_bti').text('');
      }
      
      if(sPass == ''){
          $('#error_pass').text('Password required');
      }else{
          $('#error_pass').text('');
      }
      
     
      if ($('#tc').is(':checked')) {
          $('#error_tc').text('');
      }else{
         $('#error_tc').text('Please confirm terms and condition.');
      }

      
      
      
       
          if(sName != '' && sPhone != '' && sContactNumber!='' && sBusinessType != '' && sPass != '' && sConfPass != '' && sTC != ''){
          }
          else{
                return false;
          }
          
      
      $('#firstDiv').hide();
      $('#secDiv').show();
         
      var number = '+91'+$("#store_mobile").val();
      var _token= $('input[name="_token"]').val();
      
      console.log(number);
      console.log(window.recaptchaVerifier) 
        
     /* firebase.auth().signInWithPhoneNumber(number,window.recaptchaVerifier).then(function (confirmationResult) {
            
          window.confirmationResult=confirmationResult;
          coderesult=confirmationResult;
          console.log(coderesult);*/
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
    $('#firstDiv').hide();
    $('#secDiv').show();
    $('#otpSessionId').val(result.session_id);
    $('#sentSuccessMsg').html('<label class="text-success">Otp Has been Sent to'+number+'</label>');
    
   }
   else
   {
    $('#firstDiv').show();
    $('#secDiv').hide();
    $('#sentSuccessMsg').html('<label class="text-danger">Error! </label>');
    
     $('#submit').attr('disabled', 'disabled');

   }
  }
 });

  }
  
  
      function codeverify() {



      var code = $("#otp").val();
      var otp_session_id=$("#otpSessionId").val();
      var _token= $('input[name="_token"]').val();



      /*coderesult.confirm(code).then(function (result) {

          var user=result.user;

          console.log(user);



          $("#sentSuccessMsg").text("OTP verified successfully.");

          $("#sentSuccessMsg").show();
          
          $('#myForm').submit();




      }).catch(function (error) {

          $("#sentSuccessMsg").text("OTP invalid");

          $("#sentSuccessMsg").show();

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
          
          $('#myForm').submit();
    
   }
   else
   {
          $("#sentSuccessMsg").html('<label class="text-danger">Otp Invalid! </label>');

          $("#sentSuccessMsg").show();

   }
  }
 });

  }



</script>






<script type = "text/javascript" >  
    function preventBack() { window.history.forward(); }  
    setTimeout("preventBack()", 0);  
    window.onunload = function () { null };  
</script> 


<script type="text/javascript">
	function mobileValidation(){
	var number= document.forms["myForm"]["store_mobile_"].value;///get id with value
	var numberpattern=/^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;////Regular expression
	if(numberpattern.test(number))
	   {
		// document.forms["myForm"]["store_mobile"].style.backgroundColor='yellow';
                 var _token= $('input[name="_token"]').val();
            $.ajax({
                url:"{{ route('store.unique_store_mobile') }}",
                method:"POST",
                data:{number:number, _token:_token},
                success:function(result)
                {
                //alert("dszczs");
                //alert(result);
                if(result == 'unique')
                {
                                $('#error_store_mobile').empty();
                                $('#submit').hide();

                }
                else
                {
                   $('#submit').show();

                }
               
            }
        });

       }
    else
      {
    	//document.forms["myForm"]["store_mobile"].style.backgroundColor='red';
      }
	}

</script>




<script>

function checkPasswordComplexity(pwd) {
 var re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/

    if(pwd != '')  
    {
        
      if(re.test(pwd) == false)
      {
           document.getElementById('showpassmessage2').style.color = 'red';
   //         document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
        document.getElementById('showpassmessage2').innerHTML = 'Password must include at least one upper case letter, lower case letter, number, and special character';
                                $('#submit').attr('disabled', 'disabled');
   validatePass();
      }
      else
      {
             document.getElementById('showpassmessage2').innerHTML = '';
                        $('#submit').attr('disabled', false);
    validatePass();
      }
    }
    else
    {
           document.getElementById('showpassmessage2').innerHTML = '';
                        $('#submit').attr('disabled', false);
      validatePass();

    }
}

function validatePass() {
  var x = document.forms["myForm"]["password"].value;
  var y = document.forms["myForm"]["confirm_password"].value;
   document.getElementById('showmessage').innerHTML = '';
   if(y != '')
   {
    if (x == y) {
   // document.getElementById('password').border.color = 'green';
    //document.getElementById('confirm_password').border.color = 'green';


    } else {
        document.getElementById('showmessage').style.color = 'red';
        document.getElementById('showmessage').innerHTML = 'Passwords not matching';
    }
   }
}
</script>

<script>
function validatePassLength() {
  var x = document.forms["myForm"]["password"].value;
     if(x != '')
{
   if(x.length < 8)
   {
     document.getElementById('showpassmessage').style.color = 'red';
            document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 digits!';
   }
   else
   {
                   document.getElementById('showpassmessage').innerHTML = '';

   }
}
else
{
                   document.getElementById('showpassmessage').innerHTML = '';

}

}
</script>



<script>
function validateForm() {
  var x = document.forms["myForm"]["password"].value;
  var y = document.forms["myForm"]["confirm_password"].value;
   if(x.length >= 8)
    {
        if (x != y) {
            document.getElementById('showmessage').style.color = 'red';
            document.getElementById('showmessage').innerHTML = 'Passwords not matching';
            var elmnt = document.getElementById("passlabel");
            elmnt.scrollIntoView();
            return false;
        }
    }
    else
    {
           document.getElementById('showpassmessage').style.color = 'red';
            document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 digits!';
            var elmnt = document.getElementById("passlabel");
            elmnt.scrollIntoView();
            return false;
    }
}
</script>




<script type="text/javascript">
   $(document).ready(function(){
  $("#email").blur(function(){
   var error_email = '';
  var email = $(this).val();
  //alert(email);
  var _token = $('input[name="_token"]').val();
 /*var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!filter.test(email))
  {
   $('#error_email').html('<label class="text-danger">Invalid Email</label>');
   $('#email').addClass('has-error');
   $('#submit').attr('disabled', 'disabled');
  }*/
     $.ajax({
    url:"{{ route('store.unique_email') }}",
    method:"POST",
    data:{email:email, _token:_token},
    success:function(result)
    {
      //alert("dszczs");
     if(result == 'unique')
     {
      $('#error_email').html('<label class="text-success">Email Available</label>');
      $('#email').removeClass('has-error');
       $('#submit').attr('disabled', false);
     }
     else
     {
      $('#error_email').html('<label class="text-danger">Email Already Exist </label>');
      $('#email').addClass('has-error');
       $('#submit').attr('disabled', 'disabled');

     }
    }
   })
  });
});


</script>
<script>


    $(document).ready(function() {
        //  var coc = 0;
       $('#country').change(function(){

          // if(coc != 0)
         //  {



       /* $('#city').empty();
         $('#city').append('<option value="">Select City</option>');*/
        var country_id = $(this).val();
            //alert(country_id);
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('store/register/ajax/get_state') }}?country_id="+country_id,


          success:function(res){
           // alert(data);
            if(res){
            $('#state').prop("diabled",false);
            $('#state').empty();

            $('#state').append('<option value="">State *</option>');
            $.each(res,function(state_id,state_name)
            {
              $('#state').append('<option value="'+state_id+'">'+state_name+'</option>');
            });

            }else
            {
              $('#state').empty();

            }
            }

        });

       // }
      //  else{
      //         coc++;
       //    }

      });

    });



//display town

    $(document).ready(function() {
        //var cc = 0;
       $('#city').change(function(){
//if(cc != 0 )
//{


        var city_id = $(this).val();
       // alert(city_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('store/register/ajax/get_town') }}?city_id="+city_id ,

          success:function(res){

           if(res){
            //  console.log(res);
            $('#town').prop("diabled",false);
            $('#town').empty();

            $('#town').append('<option value="">Town *</option>');
            $.each(res,function(town_id,town_name)
            {
              $('#town').append('<option value="'+town_id+'">'+town_name+'</option>');
            });

            }else
            {
              $('#town').empty();

             }
            }

        });
     // }
   // else
   // {
   //     cc++;
   // }


      });

    });


    $(document).ready(function() {

//var sc = 0;

       $('#state').change(function(){
   //   if(sc != 0)
   //   {


        var state_id = $(this).val();
        //alert(state_id);
        var _token= $('input[name="_token"]').val();

         $.ajax({
          type:"GET",
          url:"{{ url('store/register/ajax/get_city') }}?state_id="+state_id ,


          success:function(res){
           // alert(data);
            if(res){
            $('#city').prop("diabled",false);
            $('#city').empty();
            $('#city').append('<option value="">District *</option>');
            $.each(res,function(district_id,district_name)
            {
              $('#city').append('<option value="'+district_id+'">'+district_name+'</option>');
            });

            }else
            {
              $('#city').empty();

            }
            }

        });

  //  }
  //    else
   //   {
    //      sc++;
   //   }
      });

    });



$("#store_name").blur(function(){
    
      var error_username = '';
      var store_username = $(this).val();
      var _token = $('input[name="_token"]').val();
      
      $.ajax({
        url:"{{ route('unique_storename') }}",
        method:"POST",
        data:{store_name:store_username, _token:_token},
        success:function(result)
        {
            if(result == 'unique')
            {
               // $('#error_username').html('<label class="text-success"></label>');
                $('#error_username').empty();
                $('#store_name').removeClass('has-error');
                $('#submit').attr('disabled', false);
            }
            else
            {
               /* $('#error_username').html('<label class="text-danger">Store name already exist </label>');

                $('#store_name').addClass('has-error');
                $('#submit').attr('disabled', 'disabled');*/
            }
        }
   })
  });




  $("#store_mobile").blur(function(){
    
      var error_store_mobile = '';
      var store_username = $(this).val();
      var _token = $('input[name="_token"]').val();
      
      $.ajax({
        url:"{{ route('unique_store_mobile') }}",
        method:"POST",
        data:{store_mobile:store_username, _token:_token},
        success:function(result)
        {
            if(result == 'unique')
            {
              //  $('#error_store_mobile').html('<label class="text-success"></label>');
                
                                $('#error_store_mobile').empty();
$('#store_mobile').removeClass('has-error');
                $('#submit').attr('disabled',false);
                //$('#submit').show();
            }
            else
            {
                $('#error_store_mobile').html('<label class="text-danger">Store phone already exist </label>');
                $('#store_mobile').addClass('has-error');
                $('#submit').attr('disabled',true);
                //$('#submit').hide();
            }
        }
   })
  });



</script>

   </body>
</html>






