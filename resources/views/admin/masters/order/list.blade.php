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
         <div class="card-body border">
       <form onsubmit="return emptyCheck()" action="{{route('admin.list_order')}}" method="GET"
                enctype="multipart/form-data">
          @csrf
            <div class="row">



                  <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Sub Admin</label>
                      <div id="subadminl"></div>
                           <select  name="subadmin_id"  id="subadmin"  class="form-control"  >
                              <option value=""> Select Sub Admin</option>
                                 @foreach($subadmins as $key)
                                 <option {{request()->input('subadmin_id') == $key->id ? 'selected':''}} value="{{$key->id}}"> {{$key->name }} </option>
                                 @endforeach
                           </select>
                   </div>
               </div>

                <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label">Store</label>
                      <div id="storel"></div>
                      <select class="form-control" name="store_id" id="store">
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
                    <label class="form-label"> Status</label>
                      <div id="status_idl"></div>
                      <select class="form-control" name="status_id" id="status_id">
                                 <option value=""> Select Status</option>
                              @foreach ($status as $key)
                              <option {{request()->input('status_id') == $key->status_id ? 'selected':''}} value=" {{ $key->status_id}} "> {{ $key->status}}
                              </option>
                              @endforeach
                           </select>
                  </div>
               </div>

                {{-- @php
                   if(!@$datefrom)
                   {
                        //$datefrom = $date->toDateString();
                   }

                    if(!@$dateto)
                   {
                       // $dateto = $date->toDateString();
                   }
               @endphp --}}

         <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">From Date</label>
                       <div id="date_froml"  ></div>
                       <input type="date" class="form-control"  name="date_from" id="date_fromc"  value="{{@$datefrom}}" placeholder="From Date">

                  </div>
               </div>
                 <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">To Date</label>
                    <div  id="date_tol" ></div>
                     <input type="date" class="form-control" name="date_to"   id="date_toc" value="{{@$dateto}}" placeholder="To Date">

                  </div>
               </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('admin.list_order')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
 <div class="card-body">
      @if($_GET)

            <div class="table-responsive">
               <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                  <thead>
                     <tr>
                        <th class="wd-15p">SL.<br>No</th>
                         <th class="wd-15p">Order<br>Number</th>
                                <th class="wd-15p">Order<br>Date</th>
                            <th class="wd-15p">Order<br>Amount</th>

                        {{-- <th class="wd-15p">{{__('Product')}}</th> --}}
                        <th class="wd-15p">{{__('Store')}}</th>
                        <th class="wd-15p">Store<br>Phone</th>
                        <th class="wd-15p">{{__('Subadmin')}}</th>
                        <!--<th class="wd-15p">Order<br>Status</th>-->
                        <!-- <th class="wd-15p">Payment<br>Status</th>-->
                        <th class="wd-15p">{{__('Action')}}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     @endphp
                     @foreach ($orders as $order)
                     <tr>
                        <td>{{ ++$i }}</td>
                        <td>{{ $order->order_number}}</td>
                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y')}}</td>

                        <td>{{ $order->product_total_amount}}</td>

                        {{-- <td>{{$order->product->product_name->product_name}}</td> --}}
                        <td>{{ @$order->store['store_name']}}</td>
                        <td>{{ @$order->store['store_contact_person_phone_number']}}</td>
                        <td>{{ @$order->store->subadmin->name}}</td>

                    <!--    <td>-->
                    <!--{{-- <form action="{{route('admin.status_order',$order->order_id)}}" method="POST"> --}}-->

                    <!--         @csrf-->
                    <!--          @method('POST')-->
                    <!--           <button class="btn btn-sm-->
                    <!--            @if($order->status_id == 1) btn-info @elseif($order->status_id == 5) btn-danger @else btn-success @endif">-->
                               
                    <!--            @if(isset($order->status_id))-->
                    <!--            {{ @$order->status->status }}-->
                    <!--            @else-->
                    <!--            ---->
                    <!--           @endif-->
                    <!--    </button>-->
                    <!--{{--  </form>  --}}-->
                    <!--     </td>-->
                          <!--<td>-->
                          <!-- <button type="button" class="btn btn-sm @if($order->payment_status == 0) btn-warning @elseif($order->payment_status == 1) btn-success @else btn-danger @endif"-->
                          <!-- data-toggle="modal" data-target="#StockModal{{$order->order_id}}" >-->

                          <!--  @if($order->payment_status == 0 )Pending-->
                          <!--      @elseif($order->payment_status == 1)Complete-->
                          <!--      @else Canceled-->
                          <!-- @endif-->
                          <!-- </button>-->
                          <!--     </td>-->

                        <td>

                        <a class="btn btn-sm btn-blue"
                        href="{{url('admin/order/view/'.Crypt::encryptString($order->order_id))}}">View</a>
                            <br>
                              @if($order->service_booking_order == 0)
                                 <a class="btn btn-sm btn-indigo mt-2" href="{{url('admin/order/invoice/'.Crypt::encryptString($order->order_id))}}">Invocie</a>
                              @endif
                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
          @endif

         </div>
      </div>
   </div>
</div>
@foreach($orders as $order)
            <div class="modal fade" id="StockModal{{$order->order_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Update Payment Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.pay_status_order',$order->order_id ) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                    <label class="form-label">Payment Status</label>
                    <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}" >

                   <select class="form-control" name="payment_status" id="payment_status">
                     <option value=""> Select Status</option>
                              <option {{$order->payment_status == 0 ? 'selected':''}} value="0"> Pending     </option>
                              <option {{$order->payment_status == 1 ? 'selected':''}} value="1"> Completed     </option>
                              <option {{$order->payment_status == 2 ? 'selected':''}} value="2"> Cancelled     </option>
                           </select>
                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
            @endforeach
<!-- MESSAGE MODAL CLOSED -->


<script>





   $(document).ready(function() {




var sc = 0;

       $('#subadmin').change(function(){
           if(sc != 0){
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

            $('#subadmin option[value="'+storeID+'"]').prop('selected', true);


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



$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Store Orders',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7]
                 }
            },
            {
                extend: 'excel',
                title: 'Store Orders',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7]
                 }
            }
         ]
    } );

} );

$(document).ready(function() {
 $('#reset').click(function(){
     $('#store').remove();
     $('#subadmin').remove();
     $('#status_id').remove();
     $('#date_fromc').remove();
    $('#date_toc').remove();
    $('#date_froml').append('<input type="date" class="form-control"  name="date_from" id="date_fromc"   placeholder="From Date">');
    $('#date_tol').append('<input type="date" class="form-control" name="date_to"   id="date_toc" placeholder="To Date">');

  $('#subadminl').append('   <select  name="subadmin_id"  id="subadmin"  class="form-control"  ><option value=""> Select Sub Admin</option>@foreach($subadmins as $key)<option  value="{{$key->id}}"> {{$key->name }} </option>@endforeach</select>');
$('#storel').append(' <select class="form-control" name="store_id" id="store"><option value=""> Select Store</option>@foreach ($store as $key)<option  value=" {{ $key->store_id}} "> {{ $key->store_name}}</option>@endforeach </select>');
$('#status_idl').append('   <select class="form-control" name="status_id" id="status_id"><option value=""> Select Status</option>@foreach ($status as $key)<option value=" {{ $key->status_id}} "> {{ $key->status}}</option>@endforeach</select>');

   });
});




function emptyCheck() {
    var x;
    a = document.getElementById("store_id").value;
    b = document.getElementById("subadmin_id").value;
    c = document.getElementById("status_id").value;
    d = document.getElementById("date_from").value;
    e = document.getElementById("date_to").value;

    if (a == "" && b == "" && c == "" && d == "" && e == "") {
        return false;
    };
}


</script>

@endsection
