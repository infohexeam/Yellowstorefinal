@extends('admin.layouts.app')
@section('content')
@php
$date = Carbon\Carbon::now();
@endphp
<div class="container">
<div class="row justify-content-center" style="min-height: 70vh;">
<div class="col-md-12 col-lg-12">
<div class="card">
<div class="row">
  <div class="col-12" >

      @if ($message = Session::get('status'))
      <div class="alert alert-success">
         <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
      </div>
      @endif
      <div class="col-lg-12">
         @if ($errors->any())
         <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
               @foreach ($errors->all() as $error)
               <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
         @endif
         <div class="card-header">
            <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
         </div>


         <div class="card-header">
                                <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                             </div>
                             
                              <div class="card-body border">
                                <form action="{{ url('/admin/payment/list')}}" method="GET" enctype="multipart/form-data">
                                   @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">From Date</label>
                                                   <input type="date" class="form-control"  name="date_from" id="date_fromc"  value="{{@$datefrom}}" placeholder="From Date">
                            
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">To Date</label>
                                                 <input type="date" class="form-control" name="date_to"   id="date_toc" value="{{@$dateto}}" placeholder="To Date">
                                            </div>
                                         </div>
                                         
                                          @if(auth()->user()->user_role_id  == 0) 
                                            <div class="col-md-6">
                                              <div class="form-group">
                                                 <label class="form-label">Sub Admin</label>
                                                 <div id="subadminl"></div>
                                                       <select  name="subadmin_id" id="subadminId" class="form-control select2-show-search" data-placeholder="Sub Admin" >
                                                          <option value="">Sub Admin</option>
                                                             @foreach($subadmins as $key)
                                                             <option {{request()->input('subadmin_id') == $key->id ? 'selected':''}} value="{{$key->id}}"> {{$key->name }} </option>
                                                             @endforeach
                                                       </select>
                                              </div>
                                           </div>
                                        @endif
                                         
                                         <div class="col-md-6">
                                            <div class="form-group">
                                              <label class="form-label">Store </label>
                                               <select  name="store_id" id="storeId" class="form-control select2-show-search" data-placeholder="Store"  >
                                                     <option value="">Store</option>
                                                    @foreach($stores as $key)
                                                    <option {{request()->input('store_id') == $key->store_id ? 'selected':''}} value="{{$key->store_id }}"> {{$key->store_name }} </option>
                                                    @endforeach
                                                  </select>
                                            </div>
                                         </div>
                                      
                                         
                                         <div class="col-md-12">
                                            <div class="form-group">
                                                <center>
                                                   <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Filter</button>
                                                   <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button>
                                                   <a href="{{ url('/admin/payment/list')}}"  class="btn btn-info">Cancel</a>
                                                </center>
                                            </div>
                                          </div>
                     
                     
                                    </div>
                                </form>
                            </div>

        
        <div class="card-body">
            <div class="table-responsive">
               <table id="exampletable" class="table table-striped table-bpaymented text-nowrap w-100">
                  <thead>
                      <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">Order<br>number</th>
                        <th class="wd-15p">{{ __('Customer') }}</th>
                        <th class="wd-15p">{{ __('Store') }}</th>
                        <th class="wd-20p">{{__('Subadmin')}}</th>
                        <th class="wd-20p">Total<br>amount</th>
                        <th class="wd-20p">Commission<br>Amount</th>
                        <th class="wd-20p">Reference Id</th>
                        <th class="wd-20p">Delivery<br>Charge</th>
                        <th class="wd-15p">{{__('Action')}}</th>
                     </tr>
                     
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     $store_distint[] = 0;
                     @endphp

                      @foreach ($payments as $row)
                     <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ @$row->order_number }}</td>
                        <td>{{ (new App\Helpers\Helper)->findCustomerName($row->customer_id) }}</td>
                        <td>{{ (new App\Helpers\Helper)->findStoreName($row->store_id) }}</td>
                        <td>{{ (new App\Helpers\Helper)->findSubAdminName($row->store_id) }}</td>
                      
                        <td>{{ @$row->orderAmount }}</td>
                        <td>{{ @$row->splitAmount }}</td>
                        <td>{{ @$row->referenceId }}</td>

                        <td>{{ @$row->delivery_charge }}</td>
                        <td>
                                <button data-toggle="modal" data-target="#viewModal{{$row->opt_id}}"  class="btn btn-sm btn-cyan">View</button>
                        </td>

                     </tr>
                     @endforeach

                  </tbody>
               </table>
             </div>
             
             

         </div>
      </div>
   </div>
</div>
       </div>
      </div>
   </div>
</div>



@foreach($payments as $row)
            <div class="modal fade" id="viewModal{{$row->opt_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog modal-lg" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">

                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                           
                                  <tr>
                                    <td><h6>Order Number : {{ @$row->order_number }}</h6></td>
                                 </tr>
                                 
                                <tr>
                                    <td><h6>Customer Name : {{ (new App\Helpers\Helper)->findCustomerName($row->customer_id) }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Customer Phone : {{ (new App\Helpers\Helper)->findCustomerPhone($row->customer_id) }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Order Amount :{{ @$row->orderAmount }}</h6></td>
                                 </tr>
                                
                                 <tr>
                                    <td><h6>Commission Amount :{{ @$row->splitAmount }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Payment Gateway Order ID :{{ @$row->PGOrderId }}</h6></td>
                                 </tr>
                                 
                                  <tr>
                                    <td><h6>Transaction Status :{{ @$row->txStatus }}</h6></td>
                                 </tr>
                                 
                                
                              </tbody>
                              
                            
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                  
                                 <tr>
                                    <td><h6>Store Name  : {{ (new App\Helpers\Helper)->findStoreName($row->store_id) }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Store Phone  : {{ (new App\Helpers\Helper)->findStorePhone($row->store_id) }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Subadmin Name  : {{ (new App\Helpers\Helper)->findSubAdminName($row->store_id) }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Delivery Charge :{{ @$row->delivery_charge }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Payment Mode :{{ @$row->paymentMode }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Reference ID :{{ @$row->referenceId }}</h6></td>
                                 </tr>
                                 
                                 
                                 
                                 
                              </tbody>
                               @php
                                $storeSplit = \DB::table('trn__order_split_payments')->where('order_id',$row->order_id)->where('opt_id',$row->opt_id)->where('paymentRole',1)->first();
                                $storeSplitCount = \DB::table('trn__order_split_payments')->where('order_id',$row->order_id)->where('opt_id',$row->opt_id)->where('paymentRole',1)->count();
                               @endphp
                               @if($storeSplitCount > 0)
                               
                               <tbody class="col-lg-12 col-xl-12 p-0">
                                   <tr>
                                      <td> <h4><u>Store's split amount</u></h4> </td>
                                   </tr>
                                   
                                 <tr>
                                    <td><h6>Settlement ID :{{ @$storeSplit->settlementId }}</h6></td>
                                 </tr>
                                 
                                  <tr>
                                    <td><h6>Split Amount :{{ @$storeSplit->settlementAmount }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Payment Gateway Service Charge :{{ @$storeSplit->serviceCharge }}</h6></td>
                                 </tr>
                                 
                                <tr>
                                    <td><h6>Payment Gateway Service Tax :{{ @$storeSplit->serviceTax }}</h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Split Service Charge :{{ @$storeSplit->splitServiceCharge }}</h6></td>
                                 </tr>
                                 
                                   <tr>
                                    <td><h6>Split Service Tax :{{ @$storeSplit->splitServiceTax }}</h6></td>
                                 </tr>
                                 
                                  <!--<tr>-->
                                  <!--  <td><h6>Settlement Amount :{{ @$storeSplit->settlementAmount }}</h6></td>-->
                                 <!--</tr>-->
                                 
                              </tbody>
                              
                              @endif
                              
                                
                              
                                 
                           </table>
                        </div>

                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
            @endforeach
<!-- MESSAGE MODAL CLOSED -->



<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script type="text/javascript">


   $(document).ready(function() {
 $('#reset').click(function(){
   //  $('#subadmin').val('');
    // $('#store').val('');
    // $('#payment_type_id').val('');

  $('#subadmin').remove();
  $('#store').remove();
  $('#payment_type_id').remove();

  $('#date_from').remove();
    $('#date_to').remove();
    $('#date_tol').append('<input type="month" required  class="form-control" name="date_to" id="date_to"  placeholder="To Date">');
    $('#date_froml').append('<input type="month" required class="form-control" name="date_from" id="date_from"  placeholder="From Date">');

     $('#subadminl').append('<select  name="subadmin_id" id="subadmin" class="form-control"  ><option value=""> Select Sub Admin</option>@foreach($subadmins as $key)<option  value="{{$key->id}}"> {{$key->name }} </option>@endforeach</select>');
     $('#payment_type_idl').append('<select class="form-control" name="payment_type_id" id="payment_type_id"><option value=""> Select Payment Type</option>@foreach ($payment_type as $key)<option value=" {{ $key->payment_type_id}} "> {{ $key->payment_type}}</option>@endforeach</select>');
     $('#storel').append('<select  class="form-control" id="store" required name="store_id" "><option> Select Status</option></select>');
 $('#year').remove();
  $('#month').remove();
    $('#date_fromly').append(' <select required name="year" id="year" class="form-control custom-select"><option value="">Select Year</option>@for ($y=2010; $y<=2040;  $y++)<option value="{{$y}}">{{$y}}</option>@endfor</select>');
    $('#date_fromlm').append('  <select required name="month" id="month" class="form-control custom-select"><option  value="">Select Month</option><option  value="01">January</option><option  value="02">February</option><option  value="03">March</option><option  value="04">April</option><option  value="05">May</option><option  value="06">June</option><option  value="07">July</option><option  value="08">August</option><option  value="09">September</option><option  value="10">October</option><option  value="11">November</option><option  value="12">December</option></select>');



   });
});



$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Incoming Payments',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9]
                 }
            },
            {
                extend: 'excel',
                title: 'Incoming Payments',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9]
                 }
            }
         ]
    } );

} );

//id="subadmin"
    $(document).ready(function() {

        var sc = 0;


       $('#subadmin').change(function(){
           if(sc !=0 )
           {
        var subadmin_id = $(this).val();
        var storeID =  $("#store").val();

        //alert(storeID);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_store') }}?subadmin_id="+subadmin_id ,

          success:function(res){

            if(res){
            $('#store').prop("diabled",false);
            $('#store').empty();

          //  $('#store').append('<option value="">Select Store</option>');
            $('#store').append('<option value="0">All</option>');
            $.each(res,function(store_name,store_id)
            {
              $('#store').append('<option value="'+store_id+'">'+store_name+'</option>');
            });

          //  $('#store option[value="'+storeID+'"]').prop('selected', true);


            }else
            {
              $('#store').empty();

            }
            }

        });
           }
           else
           {
               sc++;
           }
      });

    });



</script>
@endsection
