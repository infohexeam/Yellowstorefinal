<meta charset="UTF-8">
<meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- FAVICON -->
<link rel="shortcut icon" type="image/x-icon" href="{{URL::to('/assets/uploads/favicon.png')}}" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<!-- TITLE -->
<title>{{ __('YellowStore | Store') }}</title>
<!-- BOOTSTRAP CSS -->
<link href="{{URL::to('/assets/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet" />
<!-- STYLE CSS -->
<link href="{{URL::to('/assets/css/style.css')}}" rel="stylesheet"/>
<link href="{{URL::to('/assets/css/skin-modes.css')}}" rel="stylesheet"/>
<!-- SIDE-MENU CSS -->
<link href="{{URL::to('/assets/plugins/sidemenu/closed-sidemenu.css')}}" rel="stylesheet">
<!--C3 CHARTS CSS -->
<link href="{{URL::to('/assets/plugins/charts-c3/c3-chart.css')}}" rel="stylesheet"/>
<!-- CUSTOM SCROLL BAR CSS-->
<link href="{{URL::to('/assets/plugins/scroll-bar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
<!--- FONT-ICONS CSS -->
<link href="{{URL::to('/assets/css/icons.css')}}" rel="stylesheet"/>
<!-- DATA TABLE CSS -->
<link href="{{URL::to('/assets/plugins/datatable/dataTables.bootstrap4.min.css')}}" rel="stylesheet"/>
<!-- SIDEBAR CSS -->
<link href="{{URL::to('/assets/plugins/sidebar/sidebar.css')}}" rel="stylesheet">
<!-- MULTI SELECT CSS -->
<link rel="stylesheet" href="{{URL::to('/assets/plugins/multipleselect/multiple-select.css')}}">
<!-- COLOR SKIN CSS -->
<link id="theme" rel="stylesheet" type="text/css" media="all" href="{{URL::to('/assets/colors/color1.css')}}" />

<!-- FILE UPLODE CSS -->
<link href="{{URL::to('/assets/plugins/fileuploads/css/fileupload.css')}}" rel="stylesheet" type="text/css"/>

<!-- SELECT2 CSS -->
<link href="{{URL::to('/assets/plugins/select2/select2.min.css')}}" rel="stylesheet"/>

<!--BOOTSTRAP-DATERANGEPICKER CSS-->
<link rel="stylesheet" href="{{URL::to('/assets/plugins/bootstrap-daterangepicker/daterangepicker.css')}}">

<!-- TIME PICKER CSS -->
<link href="{{URL::to('/assets/plugins/time-picker/jquery.timepicker.css')}}" rel="stylesheet"/>

<!-- DATE PICKER CSS -->
<link href="{{URL::to('/assets/plugins/date-picker/spectrum.css')}}" rel="stylesheet"/>


		<!-- TABS STYLES -->
<link href="{{URL::to('/assets/plugins/tabs/tabs.css')}}" rel="stylesheet"/>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
<style>
/* Custom CSS for Toastify */
.toastify {
  font-family: Arial, sans-serif;
  font-size: 14px;
  border-radius: 10px;
  padding: 15px 20px;
  min-width: 240px; /* Set your desired width here */
  max-width: 300px; /* Set your desired max width here */
}

/* Custom CSS for the close button */
.toastify button.toastify-close {
  font-size: 18px;
  font-weight: bold;
  color: #fff;
  background-color: #333;
  border: none;
  border-radius: 50%;
  cursor: pointer;
}

/* Custom CSS for the notification text */
.toastify p.toastify-text {
  margin: 0;
  color: #333;
}

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script type="text/javascript" src="{{URL::to('/assets/js/lan.js')}}"></script>
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBSqyoP-FHj6nJpuIvNYmb1YaGqBmh3xdQ&libraries=places"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
@section('headSection')
@show