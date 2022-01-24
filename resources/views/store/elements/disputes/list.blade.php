@extends('store.layouts.app')
@section('content')
@php
$date = Carbon\Carbon::now();
@endphp
<div class="container">
   <div class="row justify-content-center" >
      <div class="col-md-12 col-lg-12">
         <div class="card" style="min-height: 70vh;">
            <div class="row" >
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
                 {{-- <form action="{{route('store.list_disputes')}}" method="GET"
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">

              



                <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Order Number</label>
                    <div id="order_numberl"></div>
                       <input type="text" class="form-control" name="order_number"  id="order_number" value="{{request()->input('order_number')}}"  placeholder="Order Number">
                  </div>
               </div>


                

                 <div class="col-md-6">
                 <div class="form-group">
                    <label class="form-label">From Date</label>
                    <div id="date_froml"></div>
                     <input type="date" class="form-control" name="date_from" id="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

                  </div>
               </div>
                 <div class="col-md-6">
               <div class="form-group">
                    <label class="form-label">To Date</label>
                    <div id="date_tol"></div>
                     <input type="date" class="form-control"  name="date_to"  id="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

                  </div>
               </div>
           </div>
            <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('store.list_disputes')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                   </form> --}}
               </div>

                    <div class="card-body">
                     
     {{-- @if($_GET) --}}
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-10p">Dispute<br>Date</th>
                                    <th class="wd-15p">{{__('Issue')}}</th>
                                    <th class="wd-15p">Order<br>Date</th>
                                    <th class="wd-15p">Order<br>Number</th>
                                    <th class="wd-15p">Store<br>Name</th>
                                    <th class="wd-10p">Sub<br>Admin</th>
                                    <th class="wd-20p">Dispute<br>status</th>
                                    <th class="wd-10p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($disputes as $dispute)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ \Carbon\Carbon::parse($dispute->dispute_date)->format('M d, Y')}}</td>

                                       @php
                                        $subadmin = \DB::table('users')->where('id',$dispute->subadmin_id)->first();
                                        $issue = \DB::table('mst__issues')->where('issue_id',$dispute->issue_id)->first();
                                        $store = \DB::table('mst_stores')->where('store_id',$dispute->store_id)->first();
                                        $order = \DB::table('trn_store_orders')->where('order_id',$dispute->order_id)->first();
                                    @endphp

                                    <td>{{@$issue->issue}}</td>

                                    <td>{{  date("d-m-Y", strtotime(@$order->created_at)) }}</td>
                                    <td>{{ @$dispute->order_number}}</td>
                                    <td>{{ @$store->store_name}}</td>

                                    <td>{{ @$subadmin->name}}</td>
                        <td>
                            

                           <button type="button" class="btn btn-sm @if($dispute->dispute_status == '1') btn-success @elseif($dispute->dispute_status == '2') btn-danger @elseif($dispute->dispute_status == '4') btn-info @else btn-warning @endif"
                           data-toggle="modal" data-target="#StockModal{{$dispute->dispute_id}}" >

                            @if($dispute->dispute_status == '1' )Closed
                                @elseif($dispute->dispute_status == '2') Open
                                @elseif($dispute->dispute_status == '3') Inprogress
                                @elseif($dispute->dispute_status == '4') Return
                                @else 
                           @endif
                           </button>
                    </td>

                                    <td>
                                          <button data-toggle="modal" data-target="#viewModal{{$dispute->dispute_id}}"  class="btn btn-sm btn-cyan">View</button>
                <a class="btn btn-sm btn-blue"  href="{{url('store/order/view/'.Crypt::encryptString($dispute->order_id))}}">View Order</a>

                    <button type="button" class="btn btn-sm btn-secondary" data-toggle="modal" data-target="#storeResponseModal{{$dispute->dispute_id}}" >Store Response</button>

                                    </td>
                                
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

            <!-- MESSAGE MODAL CLOSED -->


@foreach($disputes as $dispute)
            <div class="modal fade" id="storeResponseModal{{$dispute->dispute_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Update Dispute Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" "  enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                    <label class="form-label">Store Response</label>

                   <textarea class="form-control" name="store_response" id="store_response" placeholder="Store response...">{{$dispute->store_response}}</textarea>
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




@foreach($disputes as $dispute)
            <div class="modal fade" id="StockModal{{$dispute->dispute_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Update Dispute Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('store.dispute_status',$dispute->dispute_id ) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                    <label class="form-label">Dispute Status</label>

                   <select class="form-control" name="dispute_status" id="dispute_status">
                     <option value=""> Select Status</option>
                              <option {{$dispute->dispute_status == '1' ? 'selected':''}} value="1"> Closed     </option>
                              <option {{$dispute->dispute_status == '2' ? 'selected':''}} value="2"> Open     </option>
                              <option {{$dispute->dispute_status == '3' ? 'selected':''}} value="3"> Inprogress     </option>
                              <option {{$dispute->dispute_status == '4' ? 'selected':''}} value="4"> Return     </option>
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


@foreach($disputes as $dispute)
            <div class="modal fade" id="viewModal{{$dispute->dispute_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
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
                            @php
                                $issue = \DB::table('mst__issues')->where('issue_id',$dispute->issue_id)->first();
                                 $customer = \DB::table('trn_store_customers')->where('customer_id',$dispute->customer_id)->first();
                            @endphp
                                 <tr>
                                    <td><h6>Customer Name : {{@$customer->customer_first_name}} {{@$customer->customer_last_name }}</h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Customer Phone : {{$customer->customer_mobile_number}}</h6></td>
                                 </tr>
                                 <tr>
                                    <td><h6>Dispute Status : 
                                       @if($dispute->dispute_status == '1' )Closed
                                            @elseif($dispute->dispute_status == '2') Open
                                            @elseif($dispute->dispute_status == '3') Inprogress
                                            @elseif($dispute->dispute_status == '4') Return
                                            @else 
                                       @endif
                                    </h6></td>
                                 </tr>
                                 
                                  <tr>
                                    <td><h6>Issue : {{$issue->issue}}</h6></td>
                                 </tr>
                                
                              </tbody>
                              @if($issue->issue_type_id == 2)
                                @php
                                    $colorsArray = explode(",", $dispute->item_ids);
                                    $product_varient = \DB::table('mst_store_product_varients')->where('product_varient_id',@$dispute->product_id)->first();
                                    $productdata = \DB::table('mst_store_products')->where('product_id',@$product_varient->product_id)->first();
                                    $orderItemData = \DB::table('trn_order_items')
                                    ->where('customer_id',@$dispute->customer_id)
                                    ->where('product_varient_id',@$dispute->product_id)
                                    ->where('product_id',@$productdata->product_id)
                                    ->first();
                                    
                                    
                                      $orderItemsDataz = \DB::table('trn_order_items')
                                    ->whereIn('order_item_id',@$colorsArray)
                                    ->get();

                                @endphp
                            
                              <tbody class="col-lg-12 col-xl-12 p-0">
                                  <tr>
                                    <td><h4>Product Details</h4></td>
                                 </tr>
                                 @foreach($orderItemsDataz as $key)
                                 
                                 @php
                                   $product_varientD = \DB::table('mst_store_product_varients')->where('product_varient_id',@$key->product_id)->first();
                                    $productdataD = \DB::table('mst_store_products')->where('product_id',@$key->product_id)->first();
                                 @endphp
                                 <tr>
                                    <td><h6>Product Name : 
                                    @if(@$product_varientD->variant_name == @$productdataD->product_name)
                                        {{@$productdataD->product_name}}
                                    @else
                                        {{@$product_varientD->variant_name}} {{@$productdataD->product_name}}
                                    @endif
                                    </h6></td>
                                 </tr>
                                 
                                 
                                 <tr>
                                    <td><h6>Purchased Quantity  : {{$key->quantity}}</h6></td>
                                 </tr>
                                 
                                 @endforeach
                                 
                              </tbody>
                              
                                
                              @endif
                              
                              <tr>
                                <td><h6>Description : {{@$dispute->discription}}
                               </h6></td>
                             </tr>
                             
                              <tr>
                                <td><h6>Store Response : {{@$dispute->store_response}}
                               </h6></td>
                             </tr>
                                 
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


                      <script>

$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Disputes',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5]
                 }
            },
            {
                extend: 'excel',
                title: 'Disputes',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5]
                 }
            }
         ]
    } );

} );



$(document).ready(function() {
 $('#reset').click(function(){

     //$('#store_id').val('');
     //$('#order_number').val('');

     $('#store_id').remove();
     $('#order_number').remove();
     $('#date_from').remove();
    $('#date_to').remove();
    $('#date_froml').append('<input type="date" class="form-control" name="date_from" id="date_from"  placeholder="From Date">');
    $('#date_tol').append('<input type="date" class="form-control" name="date_to"   id="date_to" placeholder="To Date">');

 $('#order_numberl').append('<input type="text" class="form-control" name="order_number"  id="order_number" placeholder="Order Number">');

   });
});

            </script>


@endsection


