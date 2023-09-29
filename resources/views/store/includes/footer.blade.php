<footer style="font-size: 14px;padding: 0.21rem 1.25rem 1.25rem 110px;" class="footer">
   <div class="container">
      <div class="row align-items-center flex-row-reverse">
         <div class="col-md-12 col-sm-12 text-center">
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


<script src="{{URL::to('/assets/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::to('/assets/js/jszip.min.js')}}"></script>
<script src="{{URL::to('/assets/js/pdfmake.min.js')}}"></script>
<script src="{{URL::to('/assets/js/vfs_fonts.js')}}"></script>
<script src="{{URL::to('/assets/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::to('/assets/js/buttons.print.min.js')}}"></script>
 
 
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
      <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
      


<script type="text/javascript">
   $('#err_msg').fadeOut(5000);


      $(document).ready(function() {       
      $('.imgValidation').bind('change', function() {
         var a=(this.files[0].size);
		 //alert(a)
         //return true; oooyi?? 300 kb mathiyooiii
         if(a > 30720) {
               alert('Image size should not exceed 30KB!');

               $(this).val('');
         };
      });
	  $('.imgValidationBanner').bind('change', function() {
         var a=(this.files[0].size);
		 //alert(a)
         //return true; oooyi?? 300 kb mathiyooiii
         if(a > 51200) {
               alert('Image size should not exceed 50KB!');

               $(this).val('');
         };
      });
   });
         

</script>

<script>
var lastToastTime = 0;
getMiniumStockProducts();
setInterval(function(){ getMiniumStockProducts();}, 10000);
function getMiniumStockProducts(){
   //alert(123);
    $.ajax({
      type: 'get',
      url: "{{ route('store_get_minimum_stock_products') }}",
      dataType: 'json',
      contentType: false,
      cache: false,
      processData:false,

      success: function(response){ 
        minProducts = response.minimumStockProducts;
        var productCount = minProducts.length;
        console.log(minProducts,productCount)
         //alert(123);

         if(productCount>0)
        {
          var currentTime = Date.now();
          var timeDiff = currentTime - lastToastTime;
         //alert(1);
         //playNotificationSound();
         $('#newProducts').html("");
        if (timeDiff > 5000) { // Allow toast every 10 seconds
        playNotificationSound();
         
                  Toastify({
                     text: "Low Stock Alert:Minimum Stock Level Reached for Some Products <a class='btn btn-warning' href='{{route('store.minimum-stock-notifications')}}'>View Products("+productCount+")</a>",
                     position: "right",
                     duration: 5000, // 5 seconds
                     gravity: "bottom",
                     close: true,
                     stopOnDuplicate: true, // Add this line
                     backgroundColor: "red", // Set background color to red
                     stopOnFocus: false, // Stop on focus
                      escapeMarkup: false,
                    
                  }).showToast();
                  lastToastTime = currentTime;
                  
                  
               }           
           $('#mspDiv').show();
           $('#mspCount').html(productCount);
        }
        else
        {
         $('#mspDiv').hide();
         $('#newProducts').html("<div class='alert alert-info'><h4>NO PRODUCTS TO SHOW!!!</h4></div>");

        }
        //RenderNewOrders();
        RenderMiniumStockProducts();
       
        
      },
      
    })

  }
  function RenderMiniumStockProducts()
  {
      $('.newProduct').remove();
    $.each(minProducts, function(index, value){
     var slNo=index+1;
    var html='<div class="card newProduct blink col-lg-3 col-md-3 col-sm-3 col-xl-3"><div class="card-header bg-info text-white">'+value.product_name+'</div><div class="card-body"><p class="card-text" style="color:red;">Current Stock:'+value.stock_count+'</p><p class="card-text">Minimum Stock:'+value.minimum_stock+'</p><a href="{{ url('store/inventory/list') }}?product_name=' + value.product_name + '">Manage Stock</a></div></div>';
     //var html='<tr class="newProduct"><td>'+slNo+'</td><td>'+value.product_name+'</td><td>'+value.stock_count+'</td><td>'+value.minimum_stock+'</td><td><a href="{{ url('store/inventory/list') }}?product_name=' + value.product_name + '">Manage Stock</a></td></tr>';
      $('#newProducts').append(html);
    });
  }
   function playNotificationSound() {
    var alarmSound = new Audio('https://hexprojects.in/Yellowstore/assets/stock-voice-simple.mp3');
    alarmSound.play();
  }
</script>

{{-- @section('footerSection')
@show
</footer> --}}