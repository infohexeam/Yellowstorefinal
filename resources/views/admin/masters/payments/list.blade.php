@extends('admin.layouts.app')
@section('content')
@php
$date = Carbon\Carbon::now();
@endphp
<div class="container">
<div class="row justify-content-center">
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
         <div class="card-body">
       <form action="{{route('admin.list_payment')}}" method="GET"
                enctype="multipart/form-data">
          @csrf
            <div class="row">


               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Sub Admin</label>
                     <div id="subadminl"></div>
                           <select  name="subadmin_id" id="subadmin" class="form-control"  >
                              <option value=""> Select Sub Admin</option>
                                 @foreach($subadmins as $key)
                                 <option {{request()->input('subadmin_id') == $key->id ? 'selected':''}} value="{{$key->id}}"> {{$key->name }} </option>
                                 @endforeach
                           </select>
                  </div>
               </div>

                <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Store</label>
                     <div id="storel"></div>
                      <select class="form-control" id="store" required name="store_id" ">
                      @if(request()->input('store_id') || request()->input('store_id') == 0 )

                            @php
                            $store_dataz = \DB::table('mst_stores')->where('store_id',request()->input('store_id'))->first();
                            @endphp
                            @if(request()->input('store_id') == 0)
                                 <option value="0"> All </option>
                             @else
                                 <option value="{{request()->input('store_id')}}"> {{$store_dataz->store_name}}  </option>
                             @endif
                        @else
                                 <option value=""> Select Store </option>

                         @endif
                           </select>
                  </div>
               </div>

                 <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label"> Payment Type</label>
                     <div id="payment_type_idl"></div>
                      <select class="form-control" name="payment_type_id" id="payment_type_id">
                                 <option value=""> Select Payment Type</option>
                              @foreach ($payment_type as $key)
                              <option {{request()->input('payment_type_id') == $key->payment_type_id ? 'selected':''}} value=" {{ $key->payment_type_id}} "> {{ $key->payment_type}}
                              </option>
                              @endforeach
                           </select>
                  </div>
               </div>

               {{-- @php
                   if(!@$datefrom)
                   {
                        $datefrom = $date->toDateString();
                   }

                    if(!@$dateto)
                   {
                        $dateto = $date->toDateString();
                   }
               @endphp --}}

         <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Select Year</label>
                     <div id="date_fromly"></div>
                     {{-- <input type="month" class="form-control" id="date_from" name="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date"> --}}
  	                <select name="year" required id="year" class="form-control custom-select">
                        <option value="">Select Year</option>
                      @for ($y=2010; $y<=2040;  $y++)
							<option {{ request()->input('year') == $y ? 'selected':''}} value="{{$y}}">{{$y}}</option>
                      @endfor

						</select>

                  </div>
               </div>

             <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Select Month</label>
                     <div id="date_fromlm"></div>
                     {{-- <input type="month" class="form-control" id="date_from" name="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date"> --}}
  	                <select required name="month" id="month" class="form-control custom-select">
    <option  value="">Select Month</option>
    <option {{ request()->input('month') == '01' ? 'selected':''}} value="01">January</option>
    <option {{ request()->input('month') == '02' ? 'selected':''}} value="02">February</option>
    <option {{ request()->input('month') == '03' ? 'selected':''}}  value="03">March</option>
    <option {{ request()->input('month') == '04' ? 'selected':''}} value="04">April</option>
    <option {{ request()->input('month') == '05' ? 'selected':''}} value="05">May</option>
    <option {{ request()->input('month') == '06' ? 'selected':''}} value="06">June</option>
    <option {{ request()->input('month') == '07' ? 'selected':''}} value="07">July</option>
    <option {{ request()->input('month') == '08' ? 'selected':''}} value="08">August</option>
    <option {{ request()->input('month') == '09' ? 'selected':''}} value="09">September</option>
    <option {{ request()->input('month') == '10' ? 'selected':''}} value="10">October</option>
    <option {{ request()->input('month') == '11' ? 'selected':''}} value="11">November</option>
    <option {{ request()->input('month') == '12' ? 'selected':''}} value="12">December</option>
						</select>

                  </div>
               </div>

              {{-- <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">To </label>
                     <div id="date_tol"></div>
                      <input type="month" required  class="form-control" name="date_to" id="date_to" value="{{@$dateto}}" placeholder="To Date">

                  </div>
               </div> --}}
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('admin.list_payment')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
 <div class="card-body">

     @if($_GET)
      @if( request()->input('subadmin_id') && request()->input('year') && request()->input('month'))
        @php

            $year = request()->input('year');
            $month = request()->input('month');

            @$a1 =Carbon\Carbon:: parse($year.'-'.$month)->startOfMonth();
            @$a2 =Carbon\Carbon:: parse($year.'-'.$month)->endOfMonth();

          @$s_count = \DB::table('users')
            ->where('id', request()->input('subadmin_id'))
            ->where('created_at', '<', Carbon\Carbon::parse($year.'-'.$month)->startOfMonth())
            ->count();


        @endphp
             <!--<h1>  {{ @$s_count->created_at }} </h1>-->

        @if ($s_count > 0)
            <div class="table-responsive">
               <table id="exampletable" class="table table-striped table-bpaymented text-nowrap w-100">
                  <thead>
                     <tr>
                        <th class="wd-15p">SL.No</th>
                         <th class="wd-15p">Order<br>Number</th>
                        <th class="wd-15p">{{ __('Customer') }}</th>
                        <th class="wd-20p">Payment<br>Type</th>
                        <th class="wd-20p">{{__('Store')}}</th>
                        <th class="wd-20p">Total<br>Amount</th>
                        <th class="wd-20p">Order<br>date</th>
                        <th class="wd-20p">{{__('Subadmin')}}</th>
                        {{-- <th class="wd-20p">{{__('Commision Amount')}}</th> --}}
                        <th class="wd-20p">Commission<br>percentage</th>
                        <th class="wd-20p">Commission<br>Amount</th>
                        <th class="wd-20p">Delivery<br>Charge</th>
                        <th class="wd-20p">Delivery<br>Commission<br>Amount</th>
                        {{-- <th class="wd-20p">{{__('Delivery Commision percentage')}}</th> --}}
                       <th class="wd-15p">{{__('Action')}}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     $store_distint[] = 0;
                     @endphp

                     @foreach ($payments as $payment)
                     <tr>
                        <td>{{ ++$i }}</td>
                      <td>{{ @$payment->order_data['order_number']}}</td>
                        <td>{{ $payment->customer['customer_first_name'] }}</td>
                        <td>{{$payment->payment_type['payment_type']}}</td>
                        <td>{{ $payment->store['store_name']}}</td>
                        <td>{{$payment->total_amount}}</td>
                       <td>
                        {!! date('d/M/y', strtotime(@$payment->order_data['created_at'])) !!}
                       </td>
                       <td>
                          @php
                             @$subadmin_name = \DB::table('users')->select("name")->where('id', $payment->store['subadmin_id'])->first();
                          @endphp
                          {{@$subadmin_name->name}}
                       </td>
                       @php
                           @$subadmin_data = \DB::table('mst_subadmin_details')->select("subadmin_commision_amount","subadmin_commision_percentage")->where('subadmin_id', $payment->store['subadmin_id'])->first();
                           @$delivery_data = \DB::table('trn_delivery_boy_payment_settlments')->select("delivery_boy_commision_amount")->where('order_id', $payment->order_id)->first();

                       @endphp
                       {{-- <td> {{@$subadmin_data->subadmin_commision_amount}} </td> --}}
                       <td> {{@$payment->store_commision_percentage}} </td>

                        <td>{{ (@$payment->store_commision_percentage / 100)  * $payment->total_amount}}</td>
                       <td> {{@$payment->order_data['delivery_charge']}} </td>
                       <td> {{@$delivery_data->delivery_boy_commision_amount}} </td>

                        <td>
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$payment->payment_id}}" > View</button>

                        </td>
                     </tr>
                     @php
                            @$total = $total + $payment->total_amount;
                            @$total_delivery_charge = $total_delivery_charge + @$payment->order_data['delivery_charge'];
                            @$dcommision = $dcommision + @$delivery_data->delivery_boy_commision_amount;
                            @$commision = $commision + ((@$payment->store_commision_percentage / 100)  * $payment->total_amount);

                     @endphp

                    @php
                        $store_distint[] = $payment->store['store_id'];
                    @endphp
                     @endforeach
                  </tbody>
               </table>
             </div>
  <br>
  @php
if( request()->input('year') && request()->input('month'))
    {

        $year = request()->input('year');
        $month = request()->input('month');

        @$a1 =Carbon\Carbon:: parse($year.'-'.$month)->startOfMonth();
        @$a2 =Carbon\Carbon:: parse($year.'-'.$month)->endOfMonth();

    @$store_datas1 = \DB::table('mst_stores')
            ->where('created_at', '<=', Carbon\Carbon::parse($year.'-'.$month)->startOfMonth())
    ->get()->toArray();
  }

  @$store_datas2 = \DB::table('mst_stores')->whereIn("store_id", $store_distint)->get()->toArray();

  $store_datas = array_merge($store_datas1,$store_datas2);


  $fix_amount_total = 0;
  @endphp
               <div class="row">
               <div class="col-md-7">
              <div class="form-group"  >
                  <div style="overflow-x: hidden;overflow-y: auto;height: 150px;" >
                <table >

                        @foreach ($store_datas as $data)

                         <tr>
                         <td>  <h4 > {{$data->store_name}} </h4> </td>
                         <td>  <h4 >- </h4> </td>
                        <td>  <h4 > {{$data->store_commision_amount}} </h4> </td>
                        </tr>
                            @php
                           $fix_amount_total = $fix_amount_total + @$data->store_commision_amount;
                           @endphp
                        @endforeach

               </table>
                    </div>
                    <h4 class="mt-4"> <b> Total Fixed Amount : </b> {{@$fix_amount_total}} </h4>

                </div>
                
                
               </div>
               <div class="col-md-5">
                <div class="form-group">

                    <h4 > <b> Total Amount : </b> {{@$total}} </h4>
                    <h4 > <b> Total Commission : </b> {{@$commision}} </h4>
                    <h4 > <b>Total Delivery Charge :</b>  {{@$total_delivery_charge}} </h4>
                    <h4 > <b>Total Delivery Commission Amount :</b>  {{@$dcommision}} </h4>
                  </div>
               </div>
            </div>
  @endif
      @endif
    @endif


         </div>
      </div>
   </div>
</div>
@foreach($payments as $payment)
            <div class="modal fade" id="viewModal{{$payment->payment_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
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
                              <tbody class="col-lg-12 col-xl-12 p-0">
                                 <tr>
                                    <input type="hidden" class="form-control" name="payment_id" value="{{$payment->payment_id}}" >
                                    {{-- <td><h6 class="">TransactionID :{{$payment->payment_id}}</h6> </td> --}}
                                 </tr>

                                 <tr>
                                    <td><h6>Order Number:    </h6></td>
                                    <td>{{ @$payment->order_data['order_number']}}</td>
                                 </tr>
                                 <tr>
                                    <td><h6>Order Date:</td><td>{!! date('d/M/y', strtotime(@$payment->order_data['created_at'])) !!}</h6></td>
                                 </tr>
                                 <tr>
                                    {{-- <td><h6>Customer Name  : {{ @$payment->customer['customer_first_name'] }} </h6></td> --}}
                                 </tr>
                                 <tr>
                                    <td><h6>Store Name:  </td><td> {{ @$payment->store['store_name']}}</h6></td>
                                 </tr>

                                 <tr>
                                    <td><h6>Total amount:  </td><td> {{$payment->total_amount}}</h6></td>
                                 </tr>



                                 <tr>
                                    <td><h6>Payment Type: </td><td> {{$payment->payment_type['payment_type']}}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Return Amount: </td><td> {{$payment->return_amount}}
                                   </h6></td>
                                 </tr>
                                 {{-- <tr>
                                    <td><h6>Store :{{ $payment->store['store_name'] }}
                                   </h6></td>
                                 </tr> --}}
                                 <tr>
                                    <td><h6>Admin Commission Amount: </td><td> {{ @$payment->admin_commision_amount }}
                                   </h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Commission Percentage: </td><td> {{ @$payment->store_commision_percentage }}
                                   </h6></td>
                                 </tr>

                                 <tr>
                                    <td><h6>Customer: </td><td> {{ @$payment->customer['customer_first_name'] }}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Delivery Boy: </td><td> {{ @$payment->delivery_boy['delivery_boy_name'] }}
                                   </h6></td>
                                 </tr>

                                  <tr>
                                    <td><h6>Amount Collected: </td><td> {{ @$payment->total_amount }}
                                   </h6></td>
                                 </tr>


                              </tbody>
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
