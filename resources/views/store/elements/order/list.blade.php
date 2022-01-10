@extends('store.layouts.app')
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
      <form action="{{route('store.list_order')}}" method="GET"
                enctype="multipart/form-data">
          @csrf
            <div class="row">

                <div class="col-md-4">
                <div class="form-group">
                     <label class="form-label"> Status</label>
                      <select class="form-control" name="status_id" id="status_id">
                                 <option value=""> Select Status</option>
                              @foreach ($status as $key)
                              <option {{request()->input('status_id') == $key->status_id ? 'selected':''}} value=" {{ $key->status_id}} "> {{ $key->status}}
                              </option>
                              @endforeach
                           </select>
                  </div>
               </div>
                <div class="col-md-4">
                <div class="form-group">

                       <label class="form-label">Delivery Boy </label>
                           <select name="delivery_boy_id" class="form-control" >
                                 <option value=""> Select Delivery Boy</option>
                                @foreach($delivery_boys as $key)
                                <option {{request()->input('delivery_boy_id') == $key->delivery_boy_id ? 'selected':''}} value="{{$key->delivery_boy_id}}"> {{$key->delivery_boy_name }} </option>
                                @endforeach
                              </select>
                  </div>
               </div>
               <div class="col-md-4">
                  <div class="form-group">
                      <label class="form-label">Select Customer</label>
                          <select name="customer_id" id="customer_id" class="form-control select2-show-search" data-placeholder="Select Customer" >
                               <option value="" >Select Customer</option>
                               @foreach ($customer as $data)
                                    <option value="{{ $data->customer_id }}" {{request()->input('customer_id') == $data->customer_id ? 'selected':''}} >{{ @$data->customer_first_name }} {{ $data->customer_last_name }} - {{ $data->customer_mobile_number }} </option>
                               @endforeach
                          </select>
                   </div>
              </div>


         <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">From Date</label>
                     <input type="date" class="form-control" name="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

                  </div>
               </div>
                 <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">To Date</label>
                     <input type="date" class="form-control" name="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

                  </div>
               </div>
              

                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('store.list_order')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
 <div class="card-body">

            <div class="table-responsive">
               <table  class="table table-striped table-bordered text-nowrap w-100">
                  <thead>
                     <tr>
                        <th class="wd-15p">SL.No</th>
                         <th class="wd-15p">Order<br>Number</th>
                        <th class="wd-15p">Customer<br>Name</th>
                        <th class="wd-15p">Order<br>Date</th>
                        <th class="wd-15p">Total<br>Order Amount</th>
                        <th class="wd-15p">{{__('Status')}}</th>
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
                        <td>
                           {{ @$order->customer->customer_first_name}}
                           {{ @$order->customer->customer_last_name}}
                        </td>

                        <td>{{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y')}}</td>
                        <td>{{ $order->product_total_amount}}</td>

                        <td>
                            @if($order->service_booking_order == 0)
                                <button type="button"  @if($order->status_id == 5) disabled @endif data-toggle="modal" data-target="#StockModal{{$order->order_id}}"  class="btn btn-sm
                                    @if($order->status_id == 1) btn-info @elseif($order->status_id == 5) btn-danger @else btn-success @endif">
                                   
                                    @if(isset($order->status_id))
                                    {{ @$order->status->status }}
                                    @else
                                    --
                                   @endif
                                </button>
                            @endif
                         </td>

                          <td>
                       <a class="btn btn-sm btn-blue"  href="{{url('store/order/view/'.Crypt::encryptString($order->order_id))}}">View</a>
                        {{-- <a class="btn btn-sm btn-info"  href="{{url('store/assign_order/delivery_boy/'.Crypt::encryptString($order->order_id))}}">Assign Order</a> --}}
                        @if($order->status_id == 6 || $order->status_id == 9 || $order->status_id == 4 || $order->status_id == 7 || $order->status_id == 8)
                          {{--  <a href="{{url('store/product_invoice/pdf/'.$order->order_id)}}" class="btn btn-info btn-sm">Generate Invoice</a> --}}
                       <a class="btn btn-sm btn-indigo"
                        href="{{url('store/order/invoice/'.Crypt::encryptString($order->order_id))}}">Invocie</a>
                         <a id="genInvoice{{$i}}" onclick="hideInvBtn(this.id)" class="btn btn-sm btn-info" href="{{url('store/product_invoice/pdf/'.Crypt::encryptString($order->order_id))}}">Generate Invoice </a>
                    @php
                    $url = url('get/invoice/'.Crypt::encryptString($order->order_id));
                    $cus_name = @$order->customer->customer_first_name;
                      $msg = 'Hi '.$cus_name.' your invoice is ready.     '.$url;
                   //  $msg = nl2br("hi $cus_name\r\n$url");
                     //  echo $msg;die;
                    @endphp
<br>

                      <a class="mt-1 btn btn-sm btn-success" target="_blank" href="https://api.whatsapp.com/send?phone=+91{{@$order->customer->customer_mobile_number}}&text={!!$msg!!}" data-action="share/whatsapp/share">Share Invoice On Whatsapp</a>
<br>

                       {{-- <a class="btn btn-sm btn-indigo"  href="{{url('store/product_invoice/whatsup/send/'.Crypt::encryptString($order->order_id))}}">Send to Whatsup</a> --}}
@endif
@if($order->status_id == 4 )
<!--<a class=" btn btn-sm btn-secondary text-white mt-1"   data-toggle="modal" data-target="#ShareItemlist{{$order->order_id}}">Share Items List</a>-->
@endif
@if($order->status_id == 7 )
<a class=" btn btn-sm btn-cyan text-white mt-1" data-toggle="modal" onclick="findAvailableDBoys({{$order->order_id}})" data-target="#AssignDesliveryBoy{{$order->order_id}}" >Assign Delivery Boy</a>
@endif

                        </td>
                     </tr>
                     @endforeach
                  </tbody>
               </table>
            </div>
            <div class="float-right"> {!! $orders->links() !!} </div>
           
         </div>
      </div>
   </div>
</div>
 @foreach($orders as $order)
            <div class="modal fade" id="StockModal{{$order->order_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action="{{ route('store.order_status',$order->order_id) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
                    <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}" >

                   <label class="form-label"> Status</label>
                      <select class="form-control" name="status_id" id="status_id">
                                 <option value=""> Select Status</option>
                            
                              @foreach ($status as $key)
                                     @if($order->status_id != 9)

                                      @if(($key->status_id >= $order->status_id ) || ($key->status_id == 5 ))
                                          <option {{request()->input('status_id',$order->status_id) == $key->status_id ? 'selected':''}} 
                                              value="
                                              @if($key->status_id != $order->status_id )
                                                {{ $key->status_id}}
                                              @endif
        
                                              "> 
                                              
                                              {{ $key->status}}
                                        </option>
                                      @endif
                                      
                                      @else
                                          @if($key->status_id == 9)
                                            <option  value="9"> {{ $key->status}} </option>
                                          @endif
                                      @endif
                              @endforeach
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


@foreach($orders as $order)

<div class="modal fade" id="ShareItemlist{{$order->order_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="example-Modal3">Share Item List</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         {{-- {{ route('store.share_item_list') }} --}}

     <form action="#" method="GET" enctype="multipart/form-data" >
     @csrf
      <div class="modal-body">
        <input type="hidden" class="form-control"  value="{{$order->order_id}}" name="order_id" id="orderId" >
         <input type="hidden" class="form-control"  value={{ Crypt::encryptString($order->order_id) }}" name="eorder_id" id="eorderId" >
         <input type="hidden" class="form-control"  value="{{ $order->order_number }}" name="ord_number" id="ord_number" >
      
        <div class="col-md-12">
         <div class="form-group">
             <label class="form-label">Mobile Number</label>
              <input type="text" max="10" class="form-control" id="mobile_number{{$order->order_id}}" name="mobile_number" placeholder="Mobile Number">
           </div>
        </div>

      </div>

         <div class="modal-footer">
           <button onclick="shareItemList({{ $order->order_id }})"  data-dismiss="modal" class="btn btn-raised btn-primary">
            <i class="fa fa-check-square-o"></i> Share</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
          </form>
      </div>
   </div>
</div>
@endforeach




@foreach($orders as $order)
<div class="modal fade" id="AssignDesliveryBoy{{$order->order_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         @php
         $linked_dboy = DB::table('mst_order_link_delivery_boys')->where('order_id',$order->order_id)->orderBy('order_link_id','DESC')->first(); 
         @endphp
         
         <div class="modal-header">
            <h5 class="modal-title" id="example-Modal3">
               @if (@$order->delivery_boy_id)
               <b>Reassign</b> 
               @else
               Assign  
               @endif
               
               Delivery Boy </h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         
        
            <form action="{{ route('store.assign_store_order',$order->order_id) }} " method="POST" enctype="multipart/form-data" >
            @csrf
               <div class="modal-body">
                  <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}" >
                  <label class="form-label"> Delivery Boys</label>
                  <select class="form-control" name="delivery_boy_id" id="delivery_boy_id">
                              <option value=""> Delivery Boys</option>
                           @foreach ($assign_delivery_boys as $key)
                           <option {{request()->input('delivery_boy_id',@$order->delivery_boy_id) == $key->delivery_boy_id ? 'selected':''}} value="{{ $key->delivery_boy_id}} "> {{ $key->delivery_boy_name}}
                           </option>
                           @endforeach
                        </select>
               </div>
               <div class="modal-footer">
                  <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Assign</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
               </div>
            </form>
      </div>
   </div>
</div>
@endforeach

<script>

function findAvailableDBoys(oId)
{
    console.log(oId);
}


function hideInvBtn(id)
{
   $('#'+id).hide();
}

function shareItemList(orderId)
{

   var eorderId =  $('#eorderId').val();
   var mobile_number = $('#mobile_number'+orderId).val();
   var ord_number = $('#ord_number').val();
   
//   alert(orderId);
//   alert(mobileNumber);

   var url1 = 'http://yellowstore.hexeam.org/item/list/'+eorderId;
console.log(url1);

        var msg = 'Order number '+ord_number+' items list.       '+url1;
   //alert(ord_number);
   var url = 'https://api.whatsapp.com/send?phone=+91'+mobile_number+'&text='+msg;
   window.open(url, '_blank')
}
</script>

@endsection
