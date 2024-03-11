@extends('store.layouts.app')
@section('content')

@php
    use App\Models\admin\Trn_RecentlyVisitedStore;
    use App\Models\admin\Trn_store_order;

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
                                <form action="{{route('store.sales_reports')}}" method="GET" enctype="multipart/form-data">
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

                                         <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Customer Mobile</label>
                                                    <input type="text"  maxlength="10" name="customer_mobile_number" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" class="form-control" value="{{ request()->input('customer_mobile_number') }}"  placeholder="Customer mobile Number">
                                             </div>
                                            </div>
                                         
                                          {{-- <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Customer</label>
                                                      <select name="customer_id" id="customer_id" class="form-control select2-show-search" data-placeholder="Customer" >
                                                           <option value="" >Customer</option>
                                                           @foreach ($customers as $key)
                                                                <option value="{{ $key->customer_id }}" {{request()->input('customer_id') == $key->customer_id ? 'selected':''}} >{{ $key->customer_first_name }} {{ $key->customer_last_name }} - {{ $key->customer_mobile_number }} </option>
                                                           @endforeach
                                                      </select>
                                               </div>
                                          </div> --}}
                                          
                                        <div class="col-md-6">
                                          <div class="form-group">
                                              <label class="form-label">Delivery Boy</label>
                                                  <select name="delivery_boy_id" id="delivery_boy_id" class="form-control select2-show-search" data-placeholder="Delivery Boy" >
                                                       <option value="" >Delivery Boy</option>
                                                       @foreach ($deliveryBoys as $key)
                                                            <option value="{{ $key->delivery_boy_id }}" {{request()->input('delivery_boy_id') == $key->delivery_boy_id ? 'selected':''}} >{{ $key->delivery_boy_name }} </option>
                                                       @endforeach
                                                  </select>
                                           </div>
                                      </div>
                                          
                                          <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Order Status</label>
                                                      <select name="status_id" id="status_id" class="form-control select2-show-search" data-placeholder="Order Status" >
                                                           <option value="" >Order Status</option>
                                                           @foreach ($orderStatus as $key)
                                                            <option value="{{ $key->status_id }}" {{request()->input('status_id') == $key->status_id ? 'selected':''}} >{{ $key->status }} </option>
                                                           @endforeach
                                                      </select>
                                               </div>
                                          </div>
                                          
                                          
                                          <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Order Type</label>
                                                      <select name="order_type" id="order_type" class="form-control select2-show-search" data-placeholder="Order Type" >
                                                           <option value="" >Order Type</option>
                                                            <option value="APP" {{request()->input('order_type') == 'APP' ? 'selected':''}} >APP </option>
                                                            <option value="POS" {{request()->input('order_type') == 'POS' ? 'selected':''}} >POS </option>
                                                      </select>
                                               </div>
                                          </div>
                                      
                                         
                                         <div class="col-md-12">
                                            <div class="form-group">
                                                <center>
                                                   <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Filter</button>
                                                   {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                                                   <a href="{{route('store.sales_reports')}}"  class="btn btn-info">Cancel</a>
                                                </center>
                                            </div>
                                          </div>
                     
                     
                                    </div>
                                </form>
                            </div>
                        
                            <div class="card-body">
                                <div class="table-responsive">
                                   <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                                      <thead>
                                         <tr>
                                            <th class="wd-15p">SL.No</th>

                                            <th class="wd-15p">Date</th>
                                            <th class="wd-15p">Order Number</th>
                                            <th class="wd-15p">Order Type</th>
                                            <th class="wd-15p">Order Status</th>
                                            
                                            <th class="wd-15p">Customer</th>
                                            <th class="wd-15p">Customer Phone</th>
                                            
                                            <th class="wd-15p">Price</th>
                                            <th class="wd-15p">Delivery Charge</th>
                                            <th class="wd-15p">Packing Charge</th>
                                            <th class="wd-15p">Discount</th>
                                            <th class="wd-15p">Tax Amount</th>
                                            <th class="wd-15p">Coupon<br>Redeemed Value</th>
                                            <th class="wd-15p">Wallet<br>Points Used</th>
                                           <th class="wd-15p">Store Wallet<br>Points Used</th>
                                           
                                            <th class="wd-15p">Delivery Boy</th>
                                            <th class="wd-15p">Delivery Status</th>
                                            <th class="wd-15p">Delivery Date & Time</th>
                                         </tr>
                                      </thead>
                                      <tbody>
                                          
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($data as $d)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ \Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}</td>

                                            <td>{{ $d->order_number }}</td>
                                            <td>{{ $d->order_type }}</td>
                                            <td>{{ @$d->status->status }}</td>
                                            
                                            <td>{{ $d->customer_first_name }} {{ $d->customer_last_name }}</td>
                                            <td>{{ $d->customer_mobile_number }}</td>

                                            
                                            <td>{{ $d->product_total_amount }}</td>
                                            <td>{{ $d->delivery_charge }}</td>
                                            <td>{{ $d->packing_charge }}</td>
                                            <td>
                                                {{ (new \App\Helpers\Helper)->orderTotalDiscount($d->order_id) }}
                                            </td>
                                            <td>
                                                {{ (new \App\Helpers\Helper)->orderTotalTax($d->order_id) }}
                                            </td>
                                            <td>
                                                @if(isset($d->amount_reduced_by_coupon))
                                                    {{ $d->amount_reduced_by_coupon }}
                                                @else
                                                ---
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($d->reward_points_used))
                                                    {{ $d->reward_points_used }}
                                                @else
                                                ---
                                                @endif
                                                
                                            </td>
                                             <td>
                                                @if(isset($d->reward_points_used_store))
                                                    {{ $d->reward_points_used_store }}
                                                @else
                                                ---
                                                @endif
                                                
                                            </td>
                                            
                                            <td>
                                                @if(isset($d->delivery_boy_name))
                                                    {{ $d->delivery_boy_name }}
                                                @else
                                                ---
                                                @endif

                                                </td>
                                            <td>
                                                @if($d->delivery_status_id == 1)
                                                    Assigned
                                                @elseif($d->delivery_status_id == 2)
                                                    Inprogress
                                                @elseif($d->delivery_status_id == 3)
                                                    Completed
                                                @else
                                                    --
                                                @endif
                                            </td>
  <td>@if(@$d->delivery_date!=NULL){{ @$d->delivery_date }},{{ @$d->delivery_time }} @else -- @endif</td>
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

<script>
    $(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Sales Report',
                footer: true,
             
                 orientation : 'landscape',
                pageSize : 'LEGAL',
            },
            {
                extend: 'excel',
                title: 'Sales Report',
                footer: true,
               
            }
         ]
    } );

} );
</script>


@endsection

