
<footer class="footer dash">
   <div class="container">
      <div class="row align-items-center flex-row-reverse">
         <div class="col-md-12 col-sm-12 text-center" style="padding: 7px;">
            Copyright &copy; {{date('Y')}} | All rights reserved.
         </div>
      </div>
   </div>
</footer>
</div>

<!-- BACK-TO-TOP -->
<a href="#top" id="back-to-top"><i class="fa fa-angle-up"></i></a>
<!-- JQUERY JS -->
<script src="{{URL::to('/assets/js/jquery-3.4.1.min.js')}}"></script>

<script src="{{URL::to('/assets/plugins/picker/monthpicker.min.js')}}"></script>
<link href="{{URL::to('/assets/plugins/picker/monthpicker.css')}}" rel="stylesheet"/>

<!-- BOOTSTRAP JS -->
<script src="{{URL::to('/assets/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{URL::to('/assets/plugins/bootstrap/js/popper.min.js')}}"></script>
<!-- SPARKLINE JS-->
<script src="{{URL::to('/assets/js/jquery.sparkline.min.js')}}"></script>
<!-- CHART-CIRCLE JS-->
<script src="{{URL::to('/assets/js/circle-progress.min.js')}}"></script>
<!-- RATING STARJS -->
<script src="{{URL::to('/assets/plugins/rating/jquery.rating-stars.js')}}"></script>
<!-- CHARTJS CHART JS-->
<script src="{{URL::to('/assets/plugins/chart/Chart.bundle.js')}}"></script>
<script src="{{URL::to('/assets/plugins/chart/utils.js')}}"></script>
<!-- PIETY CHART JS-->
<script src="{{URL::to('/assets/plugins/peitychart/jquery.peity.min.js')}}"></script>
<script src="{{URL::to('/assets/plugins/peitychart/peitychart.init.js')}}"></script>
<!-- DATA TABLE JS-->
<script src="{{URL::to('/assets/plugins/datatable/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::to('/assets/plugins/datatable/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{URL::to('/assets/plugins/datatable/datatable.js')}}"></script>
<script src="{{URL::to('/assets/plugins/datatable/datatable-2.js')}}"></script>
<script src="{{URL::to('/assets/plugins/datatable/dataTables.responsive.min.js')}}"></script>
<!-- ECHART JS-->
<script src="{{URL::to('/assets/plugins/echarts/echarts.js')}}"></script>
<!-- SIDE-MENU JS-->
<script src="{{URL::to('/assets/plugins/sidemenu/sidemenu.js')}}"></script>
<!-- CUSTOM SCROLLBAR JS-->
<script src="{{URL::to('/assets/plugins/scroll-bar/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<!-- SIDEBAR JS -->
<script src="{{URL::to('/assets/plugins/sidebar/sidebar.js')}}"></script>
<!-- APEXCHART JS -->
<script src="{{URL::to('/assets/js/apexcharts.js')}}"></script>
<!-- INDEX JS -->
<script src="{{URL::to('/assets/js/index1.js')}}"></script>
<!-- MULTI SELECT JS-->
<script src="{{URL::to('/assets/plugins/multipleselect/multiple-select.js')}}"></script>
<script src="{{URL::to('/assets/plugins/multipleselect/multi-select.js')}}"></script>
<!-- CUSTOM JS -->
<script src="{{URL::to('/assets/js/custom.js')}}"></script>



	<!-- FORMELEMENTS JS -->
	<script src="{{URL::to('assets/js/form-elements.js')}}"></script>

	<!-- DATEPICKER JS -->
	<script src="{{URL::to('/assets/plugins/date-picker/spectrum.js')}}"></script>
	<script src="{{URL::to('/assets/plugins/date-picker/jquery-ui.js')}}"></script>
	<script src="{{URL::to('/assets/plugins/input-mask/jquery.maskedinput.js')}}"></script>

		<!-- FILE UPLOADES JS -->
        <script src="{{URL::to('/assets/plugins/fileuploads/js/fileupload.js')}}"></script>
        <script src="{{URL::to('/assets/plugins/fileuploads/js/file-upload.js')}}"></script>

		<!-- SELECT2 JS -->
		<script src="{{URL::to('/assets/plugins/select2/select2.full.min.js')}}"></script>

		<!-- BOOTSTRAP-DATERANGEPICKER JS -->
		<script src="{{URL::to('/assets/plugins/bootstrap-daterangepicker/moment.min.js')}}"></script>
		<script src="{{URL::to('/assets/plugins/bootstrap-daterangepicker/daterangepicker.js')}}"></script>

		<!-- TIMEPICKER JS -->
		<script src="{{URL::to('/assets/plugins/time-picker/jquery.timepicker.js')}}"></script>
		<script src="{{URL::to('/assets/plugins/time-picker/toggles.min.js')}}"></script>
		
		<!-- C3 CHART JS -->
		<script src="{{URL::to('/assets/plugins/charts-c3/d3.v5.min.js')}}"></script>
		<script src="{{URL::to('/assets/plugins/charts-c3/c3-chart.js')}}"></script>

		<!-- INPUT MASK JS-->
		<script src="{{URL::to('/assets/plugins/tabs/jquery.multipurpose_tabcontent.js')}}"></script>


	<!--- TABS JS -->
		<script src="{{URL::to('/assets/plugins/tabs/jquery.multipurpose_tabcontent.js')}}"></script>
		<script src="{{URL::to('/assets/plugins/tabs/tab-content.js')}}"></script>
		
		

{{-- data table export js --}}

<script src="{{URL::to('/assets/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::to('/assets/js/jszip.min.js')}}"></script>
<script src="{{URL::to('/assets/js/pdfmake.min.js')}}"></script>
<script src="{{URL::to('/assets/js/vfs_fonts.js')}}"></script>
<script src="{{URL::to('/assets/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::to('/assets/js/buttons.print.min.js')}}"></script>
<script type="text/javascript">
   $('#err_msg').fadeOut(5000);


     $(document).ready(function() {       
      $('.imgValidation').bind('change', function() {
         var a=(this.files[0].size);
         //return true; oooyi?? 300 kb mathiyooiii
         if(a > 100000) {
               alert('Image size too large!');
               $(this).val('');
         };
      });
   });
         
		 
</script>



{{-- @section('footerSection')
@show
</footer> --}}
