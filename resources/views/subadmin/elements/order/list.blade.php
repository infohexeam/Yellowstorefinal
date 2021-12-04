@extends('admin.layouts.app')
@section('content')
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
       <form action="{{route('admin.list_subadmin_order')}}" method="GET"
                enctype="multipart/form-data">
          @csrf
            <div class="row">
               <div class="col-md-6">
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
                <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Store</label>
                      <div id="store_idl"></div>
                      <select class="form-control" name="store_id" id="store_id">
                                 <option value=""> Select Store</option>
                              @foreach ($store as $key)
                              <option {{request()->input('store_id') == $key->store_id ? 'selected':''}} value=" {{ $key->store_id}} "> {{ $key->store_name}}
                              </option>
                              @endforeach
                           </select>
                  </div>
               </div>



             <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">From Date</label>
                      <div id="date_froml"></div>
                     <input type="date" class="form-control" id="date_from"  name="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

                  </div>
               </div>
                 <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">To Date</label>
                      <div id="date_tol"></div>
                     <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

                  </div>
               </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('admin.list_subadmin_order')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
       <div class="card-body">

            <div class="table-responsive">
               <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                  <thead>
                     <tr>
                        <th class="wd-15p">S.No</th>
                         <th class="wd-15p">Order<br>Number</th>
                          <th class="wd-20p">Order<br>Date</th>
                         <th class="wd-15p">Order<br>Amount</th>
                        {{-- <th class="wd-15p">Customer</th> --}}
                        <th class="wd-15p">Delivery<br>Boy</th>
                        <th class="wd-15p">Delivery<br>Phone</th>
                        <th class="wd-15p">Delivery<br>Status</th>
                        {{-- <th class="wd-20p">Product</th> --}}
                        <th class="wd-20p">{{__('Store')}}</th>

                        {{-- <th class="wd-20p">{{__('Status')}}</th> --}}
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
                        <td>{{ @$order->product_total_amount}}</td>
                        {{-- <td>{{ $order->customer['customer_first_name'] }}</td> --}}
                        <td>{{ @$order->delivery_boy['delivery_boy_name'] }}</td>
                        <td>{{ @$order->delivery_boy['delivery_boy_mobile'] }}</td>
                        <td>{{ @$order->status['status']}}</td> {{-- <td>{{substr( $order->product->product_name->product_name,0,10)}}</td> --}}
                        <td>{{ @$order->store['store_name']}}</td>


                        {{-- <td> --}}
                        {{--   <form action="{{route('admin.status_order',$order->order_id)}}" method="POST">

                             @csrf
                              @method('POST') --}}
                               {{-- <button type="button"  class="btn btn-sm
                                @if($order->status_id == 1) btn-info @elseif($order->status_id == 5) btn-danger @else btn-success @endif">
                                @if($order->status_id == 1)Pending
                                @elseif($order->status_id == 2)Payment Successful
                                @elseif($order->status_id == 3)Payment Cancelled
                                @elseif($order->status_id == 4)Confirmed
                                @elseif($order->status_id == 5)Cancelled
                                @else Completed
                          @endif</button> --}}
                           {{-- </form> --}}
                         {{-- </td> --}}
                        <td>

                        <a class="btn btn-sm btn-blue"
                        href="{{url('admin/order/view/'.Crypt::encryptString($order->order_id))}}">View</a>
                            <br>
                      <a class="btn btn-sm btn-indigo mt-2"
                        href="{{url('admin/order/invoice/'.Crypt::encryptString($order->order_id))}}">Invocie</a>


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

<script>

$(document).ready(function() {
 $('#reset').click(function(){
     $('#status_id').remove();
     $('#store_id').remove();
     $('#date_from').remove();
     $('#date_to').remove();

    $('#date_froml').append('<input type="date" class="form-control"  name="date_from" id="date_from"   placeholder="From Date">');
    $('#date_tol').append('<input type="date" class="form-control" name="date_to"   id="date_to" placeholder="To Date">');

     $('#status_idl').append('<select class="form-control" name="status_id" id="status_id"><option value=""> Select Status</option>@foreach ($status as $key)<option value=" {{ $key->status_id}} "> {{ $key->status}}</option>@endforeach</select>');
     $('#store_idl').append('  <select class="form-control" name="store_id" id="store_id"><option value=""> Select Store</option>@foreach ($store as $key)<option value=" {{ $key->store_id}} "> {{ $key->store_name}}</option>@endforeach</select>');


   });
});

</script>

<!-- MESSAGE MODAL CLOSED -->
@endsection
