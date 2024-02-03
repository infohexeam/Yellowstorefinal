@extends('admin.layouts.app')
@section('content')
@php
$date = Carbon\Carbon::now();
use App\Models\admin\Trn_store_order;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Mst_delivery_boy;
use App\User;

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
                        <h6>Whoops!</h6> There were some problems with your input.<br><br>
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
                <form action="{{route('admin.list_delivery_boy_order')}}" method="GET"
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"> Payment Type</label>
                                           <div id="payment_type_idl"></div>
 <select class="form-control" name="payment_type_id" id="payment_type_id" >
                                 <option value=""> Select Payment Type</option>
                              @foreach ($payment_types as $key)
                              <option {{request()->input('payment_type_id') == $key->payment_type_id ? 'selected':''}} value=" {{ $key->payment_type_id}} "> {{ $key->payment_type}}
                              </option>
                              @endforeach
                           </select>
                  </div>
               </div>
                   <div class="col-md-6">
                   <div class="form-group">
                     <label class="form-label"> Delivery Boy</label>
                                           <div id="delivery_boy_idl"></div>

                      <select class="form-control"  name="delivery_boy_id"  id="delivery_boy_id" >
                           <option value=""> Select Delivery Boy</option>
                              @foreach ($delivery_boy as $key)
                              <option {{request()->input('delivery_boy_id') == $key->delivery_boy_id ? 'selected':''}} value=" {{ $key->delivery_boy_id}} "> {{ $key->delivery_boy_name}}
                              </option>
                              @endforeach
                           </select>

                  </div>
               </div>
               <div class="col-md-6">
                  <div class="form-group">
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
                      <label class="form-label">From Date</label>
                      <div id="date_froml"></div>
                       <input type="date" class="form-control" name="date_from" id="date_from" value="{{ @$datefrom }}"  placeholder="From Date">

                    </div>
                 </div>
                   <div class="col-md-6">
                  <div class="form-group">
                      <label class="form-label">To Date</label>
                      <div id="date_tol"></div>
                       <input type="date" class="form-control" id="date_to"  name="date_to" value="{{@$dateto}}"  placeholder="To Date">

                    </div>
                 </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                          <a href="{{route('admin.list_delivery_boy_order')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                   </form>
                </div>

                    <div class="card-body">
                       {{--  <a href="  {{route('admin.create_delivery_boy_order')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create delivery_boy_order
                        </a> --}}
                        </br>
            {{-- @if($_GET) --}}

                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                     <tr>
                        <th class="wd-15p">SL.No</th>
                        <th class="wd-15p">{{ __('Order Date') }}</th>

                         <th class="wd-15p">{{ __('Order Number') }}</th>
                        <th class="wd-15p">{{ __('Delivery Boy') }}</th>
                        <th class="wd-15p">{{ __('Delivery Mobile') }}</th>
                        <th class="wd-20p">{{__('Store')}}</th>
                        <th class="wd-20p">{{__('Subadmin')}}</th>

                         <th class="wd-20p">{{__('Status')}}</th>
                        <th class="wd-15p">{{__('Action')}}</th>
                     </tr>
                  </thead>
                  <tbody>
                     @php
                     $i = 0;
                     @endphp
                     @foreach ($delivery_boy_orders as $delivery_boy_order)
                     <tr>
                        @php
                           $orderDAta = Trn_store_order::find($delivery_boy_order->order_id);
                           $dBoyDAta = Mst_delivery_boy::find($delivery_boy_order->delivery_boy_id);
                           $subadmin = User::find($delivery_boy_order->subadmin_id);
                           $statusInfo = Sys_store_order_status::find($delivery_boy_order->status_id);
                        @endphp
                        <td>{{ ++$i }}</td>
                        <td>{{ \Carbon\Carbon::parse($orderDAta->created_at)->format('M d, Y')}}</td>
                        <td>{{ @$orderDAta->order_number}}</td>
                        <td>{{ @$dBoyDAta->delivery_boy_name }}</td>
                        <td>{{ @$dBoyDAta->delivery_boy_mobile }}</td>
                        <td>{{@$delivery_boy_order->store['store_name']}}@if(@$delivery_boy_order->store['store_code']!=NULL)<br>({{@$delivery_boy_order->store['store_code']}})@endif</td>
                        <td>
                            @if(isset($subadmin->name))
                            {{@$subadmin->name}}
                            @else
                            ---
                            @endif
                            
                            </td>



                       <td>
                            {{@$statusInfo->status }}
                       </td>
                       <td>
                        <a class="btn btn-sm btn-blue"href="{{url('admin/order/view/'.Crypt::encryptString($delivery_boy_order->order_id))}}">View</a>

                       </td>

                        {{-- <td>
                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$delivery_boy_order->delivery_boy_order_id}}" > View</button>

                        </td> --}}
                     </tr>
                     @endforeach
                  </tbody>
                </table>
                        </div>
       {{-- @endif --}}
                     </div>
                  </div>
               </div>
            </div>
 @foreach($delivery_boy_orders as $delivery_boy_order)
            <div class="modal fade" id="viewModal{{$delivery_boy_order->delivery_boy_order_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
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
                           <table  class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-12 p-0">
                                 <tr>
                                    <input type="hidden" class="form-control" name="delivery_boy_order_id" value="{{$delivery_boy_order->delivery_boy_order_id}}" >
                                    <td><h6 class="">Delivery Boy : {{ @$delivery_boy_order->order_item->delivery_boy['delivery_boy_name'] }}</h6> </td>
                                 </tr>
                                  <tr>
                                    <td><h6 class="">Delivery Boy Mobile : {{ @$delivery_boy_order->order_item->delivery_boy['delivery_boy_mobile'] }}</h6> </td>
                                 </tr>
                                  <tr>
                                    <td><h6>Store Name : {{@$delivery_boy_order->store['store_name']}}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Order Number  : {{@$delivery_boy_order->order['order_number']}}</h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Payment Type : {{@$delivery_boy_order->payment_type['payment_type']}}
                                   </h6></td>
                                 </tr>
                                   <tr>
                                    <td><h6>Order date  : {{ \Carbon\Carbon::parse(@$delivery_boy_order->created_at)->format('M d, Y')}}</h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Delivery Date : {{ \Carbon\Carbon::parse(@$delivery_boy_order->delivery_date_time)->format('M d, Y')}}
                                   </h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>
                                    @if(@$delivery_boy_order->delivery_status_id == 1 && @$delivery_boy_order->payment_type['payment_type_id'] == 1)
                                     Amount to be Collected
                                    @elseif(@$delivery_boy_order->delivery_status_id == 9 && @$delivery_boy_order->payment_type['payment_type_id'] == 1)
                                     Amount Collected
                                    @elseif(@$delivery_boy_order->payment_type['payment_type_id'] == 2)
                                     Amount Collected
                                    @else
                                     Return Amount
                                    @endif
                                     :
@if(@$delivery_boy_order->payment_type['payment_type_id'] != 2)

                                     {{ @$delivery_boy_order->order['product_total_amount'] }}
@else
                                     0
@endif

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
<!-- MESS
            <!-- MESSAGE MODAL CLOSED -->

           <script>

$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Delivery Boy Orders',
                orientation : 'landscape',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7]
                 }
            },
            {
                extend: 'excel',
                title: 'Delivery Boy Orders',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7]
                 }
            }
         ]
    } );

} );



     

           </script>



            @endsection
