@extends('store.layouts.app')
@section('content')
<div class="container">
   <div class="row">
      <div class="col-md-12">
         <div class="card">
            <div class="card-header">
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body">
               @if ($message = Session::get('status'))
               <div class="alert alert-success">
                  <p>{{ $message }}</p>
               </div>
               @endif
            </div>
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
                   <form action="{{route('store.update_order',$order->order_id)}}" method="POST">
                       @csrf
               
                 <input type="hidden" class="form-control" name="order_id" value="{{$order->order_id}}">

                  <div class="row" >
                     <div class="col-md-12">
                        <div class="card">
                           <div style="    justify-content: space-between;" class="card-header">
                              <div class="card-title">Order Details</div>
                                                           <!--<a  href="{{route('store.list_order')}}" class="btn btn-cyan float-right "  >Back</a>-->
                                                           <a  href="{{ url('store/delivery-boys/list') }}" class="btn btn-cyan float-right "  >Back</a>

                           </div>
                           <div class="card-body">
                              <div class="table-responsive">
                                 <table class="table row table-borderless">
                                    <tbody class="col-lg-12 col-xl-6 p-0">
                                       <tr>
                                          <td><strong>Order Number: </strong> </td> <td> {{$order->order_number}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Order Date: </strong> </td> <td>{{ \Carbon\Carbon::parse(@$order->created_at)->format('d/m/Y')}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Customer Name: </strong> </td> <td>{{ @$order->customer->customer_first_name}} {{ @$order->customer->customer_last_name}} </td>
                                       </tr>
                                 
                                       <tr>
                                          <td> <h3> <strong>Total Amount: </strong> </h3>  </td> <td> <h3> <i class="fa fa-inr"></i> {{ @$order->product_total_amount }} </h3></td>
                                       </tr>
                                         <tr>
                                          <td><strong>Order Type: </strong> </td> <td>{{ @$order->order_type}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Payment Mode: </strong> </td> <td>{{ @$order->payment_type->payment_type}}</td>
                                       </tr>
                                       <tr>
                                          <td><strong>Processed By: </strong> </td> <td> -- </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>{{-- card body end --}}
                     </div><!-- COL END -->
                  </div>
                   <div class="col-md-2">
                   </div>
                   <div class="col-md-4">
                        <div class="form-group">
                            <select name="delivery_boy_id"  class="attr_value form-control" >
                              <option value="">Select Delivery Boy</option>
                              @foreach ($delivery_boys as $data)
                                 <option {{($order->delivery_boy_id == $data->delivery_boy_id) ? 'selected':''}}  value="{{$data->delivery_boy_id}}">{{ $data->delivery_boy_name}}</option>
                              @endforeach
                            </select>
                        </div>
                   </div>
                   <div class="  col-md-4">
                        <div class="form-group">
                            <select name="status_id" class="attr_value form-control" >
                                 <option value="">Status</option>
                              @foreach ($status as $key)
                              <option {{$order->status_id == $key->status_id ? 'selected':''}} value=" {{ $key->status_id}} "> {{ $key->status}}</option>
                              @endforeach                            
                              </select>
                        </div>
                   </div>
                   <div class="col-md-2">
                   </div>
               </div>

                  <div  class="row">
                     <div class="col-md-12">
                        <div class="card">
                           <div class="card-body">
                              <div style="background-color:#f1f1f9;" class="table-responsive">
                               <table class="table table-striped table-bordered text-nowrap w-100">
                                 <thead>
                                    <tr>
                                       <td>Item Name</td>
                                       <td>Qty</td>
                                       <td>Rate</td>
                                       <td>Discount Amount</td>
                                       <td>Tax </td>
                                       <td>Total</td>
                                       <td></td>
                                    </tr>
                                 </thead>
                                 <tbody>
                                    @foreach ($order_items as $order_item)
                                    @php
                                       // dd($order_item);
                                    @endphp
                                       <tr>
                                          <td>
                                              <table>
                                                <tr>
                                                   <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.@$order_item->product_varient->product_varient_base_image)}}"  width="50" ></td>
                                                   <td>{{@$order_item->product->product_name}}
                                                      @if (isset($order_item->product_varient_id) && $order_item->product_varient_id != 0 )
                                                     @if (@$order_item->product->product_name != @$order_item->product_varient->variant_name )
                                                -
                                              {{ @$order_item->product_varient->variant_name }}
                                              @endif
                                                      @endif
                                                      <td>
                                                </tr>
                                              </table>
                                          </td>
                                          <td>{{@$order_item->quantity}} </td>
                                          <td>{{@$order_item->unit_price}} </td>
                                          <td>
                                             @if (isset($order_item->discount_amount))
                                             {{@$order_item->discount_amount}} 
                                                @else
                                                0
                                             @endif
                                           </td>
                                          <td>{{@$order_item->tax_amount}} </td>
                                          <td>{{@$order_item->total_amount}} </td>
                                          <td>
                                             <input type="hidden" name="order_item_id[{{@$order_item->order_item_id}}]" value="{{ @$order_item->order_item_id }}">
                                             <input type="hidden" name="product[{{@$order_item->order_item_id}}]" value=0>
                                             <input type="checkbox" @if ($order_item->tick_status)
                                             checked
                                             @endif name="product[{{@$order_item->order_item_id}}]" value=1>
                                          </td>
                                       </tr>
                                    @endforeach

                                 </tbody>
                               </table>
                              <div>
                           <div>   
                        </div>
                     </div>
                  </div>
                  <br>

                  <div class="row">
                     <div class="col-md-12">
                           <div class="form-group">
                              <textarea name="order_note" placeholder="Note" class="form-control">{{ $order->order_note}}</textarea>
                           </div>
                     </div>
                   </div>

               
                  <br>
                  <center>
                     <!--<button type="submit"  class="btn btn-block btn-blue"  >Submit</button>-->
                     {{-- <a type="button" class="btn btn-cyan" onclick="history.back()">Cancel</a> --}}
                  </center>
               </br>
               </form>
              
          
         </div>
      </div>
   @endsection
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript">
$('#print').click(function(){
$('#print'). hide();
});

</script>