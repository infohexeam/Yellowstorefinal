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
         <title>{{ __('YellowStore | Administration') }}</title>
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
         
         <script src='https://www.google.com/recaptcha/api.js'></script>    
         
         <script src="https://www.google.com/recaptcha/api.js?render=6LdaDPgdAAAAAB82l56R-aueMZstr_Xe3iowacF8"></script>

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
   </head>
   <body>
      <!-- BACKGROUND-IMAGE -->
          <div class="container-fluid login-img">
      <div class="row pt-5">
       
             <div class="col-lg-7 pt-5">
                 <div class="login-bg ">
                <img src="{{URL::to('/assets/login-page-bg.png')}}"  alt=""> 
                </div>
            </div>
        
      <div class="  ">
         <!-- GLOABAL LOADER -->
         <div id="global-loader">
            <img src="../assets/images/loader.svg" class="loader-img" alt="Loader">
         </div>
         <!-- /GLOABAL LOADER -->
         <!-- PAGE -->
         <div class=" page" style="background: #a5a5a5;">
            <div class="">
               <!-- CONTAINER OPEN -->
               <div class="col col-login mx-auto">
                  <div class="text-center">
                    <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img" alt=""> 
                  </div>
               </div>
               <div class="container-login100">
                  <div class="wrap-login100 p-6" style="width:400px;">
                     <form method="POST" id="userForm" action="{{ route('login') }}">
                        @csrf
                        <span class="login100-form-title">
                        {{ __('Admin Login') }}
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
                           <input class="input100" id="name" type="name" name="name" placeholder="Username" value="{{ old('email') }}" required autocomplete="name" >
                           @error('name')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                        </div>
                        <div class="wrap-input100 validate-input">
                                <div class="password-show">

                           <input class="input100 form-control" type="password" name="password" placeholder="Password" id="exampleInputPassword1" type="password" required autocomplete="current-password">
                           <div class="password-show__toggle">
                              <i class="fa fa-eye password-show_toggle_show-icon"></i>
                              <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                            </div>
                           @error('password')
                           <span class="invalid-feedback" role="alert">
                           <strong>{{ $message }}</strong>
                           </span>
                           @enderror
                           <span class="focus-input100"></span>
                        </div>
                        </div>
                        <!-- <div class="container-login100-form-btn">-->
                        <!--  <a href="{{route('password.request')}}">ForgotPassword?</a>-->
                        <!--   </button>-->
                        <!--</div>-->
                        <div class="container-login100-form-btn">
                            <button type="submit" class="login100-form-btn btn-primary">
                           {{ __('Login') }}
                           </button> 
                           
                           <!-- <button type="submit" data-sitekey="6LfkNvcdAAAAAKlMeQ0Jy_zmPRCTg714kKnWzkCd" data-callback="submitForm" class="g-recaptcha login100-form-btn btn-primary">-->
                           <!--{{ __('Login') }}-->
                           <!--</button>-->
                           
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
      </div>
      </div>
         <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

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
            function submitForm() {
                document.getElementById('userForm').submit();
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