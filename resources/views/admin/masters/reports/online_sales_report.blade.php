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
                                <form action="{{route('admin.online_sales_reports')}}" method="GET" enctype="multipart/form-data">
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
                                         
                                         
                                         
                                          <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Customer</label>
                                                      <select name="customer_id" id="customer_id" class="form-control select2-show-search" data-placeholder="Customer" >
                                                           <option value="" >Customer</option>
                                                           @foreach ($customers as $key)
                                                                <option value="{{ $key->customer_id }}" {{request()->input('customer_id') == $key->customer_id ? 'selected':''}} >{{ $key->customer_first_name }} {{ $key->customer_last_name }} - {{ $key->customer_mobile_number }} </option>
                                                           @endforeach
                                                      </select>
                                               </div>
                                          </div>
                                          
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
                                                   <a href="{{route('admin.online_sales_reports')}}"  class="btn btn-info">Cancel</a>
                                                </center>
                                            </div>
                                          </div>
                     
                     
                                    </div>
                                </form>
                            </div>
                        
                            <div class="card-body">
                                <div class="table-responsive">
                                   <table id="exampletable" class="table table-striped table-bordered text-nowrap">
                                      <thead>
                                         <tr>
                                            <th class="wd-15p">SL.No</th>

                                            <th class="wd-15p">Date</th>
                                            <th class="wd-15p">Order Number</th>
                                            
                                            <th class="wd-15p">Store</th>
                                            <th class="wd-15p">Subadmin</th> 
                                            
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
                                            <th class="wd-15p">Admin<br>Points Used</th>
                                            <th class="wd-15p">Store<br>Points Used</th>
                                           
                                            <th class="wd-15p">Delivery Boy</th>
                                            <th class="wd-15p">Delivery Status</th>
                                            
                                         </tr>
                                      </thead>
                                      <tbody>
                                          
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($data as $d)
                                        <tr>
                                            <td>{{ ($data->currentPage()-1) * $data->perPage() + $loop->index + 1 }}</td>
                                            <td>{{ \Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}</td>

                                            <td>{{ $d->order_number }}</td>
                                            
                                              <td>{{ $d->store_name }}@if($d->store_code != NULL)-{{$d->store_code}}@endif</td>
                                            <td>{{ (new \App\Helpers\Helper)->subAdminName($d->subadmin_id) }}</td>
                                            
                                            
                                            <td>{{ $d->order_type }}</td>
                                            <td>{{ @$d->status->status }}</td>
                                            
                                            <td>{{ $d->customer_first_name }} {{ $d->customer_last_name }}</td>
                                            <td>{{ $d->customer_mobile_number }}</td>

                                            
                                            <td>{{ $d->product_total_amount }}</td>
                                            <td>{{ @$d->delivery_charge }}</td>
                                            <td>{{ @$d->packing_charge }}</td>
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
                                                @if(isset($d->reward_points_used))
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
                                                    ---
                                                @endif
                                            </td>

                                        </tr>
                                        @endforeach
                               
                                    
                                      </tbody>
                                   </table>
                                    {{ $data->appends(request()->query())->links('custom_pagination') }}
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
$(document).ready(function() {
    $('#exampletable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdfHtml5',
                title: 'Online Sales Report',
                orientation: 'landscape',
                footer: true,
                exportOptions: {
                    columns: ':visible' // Include all visible columns in PDF export
                },
                customize: function(doc) {
                    // Adjust column widths to prevent truncation
                    doc.content[1].table.widths = Array(doc.content[1].table.body[0].length).fill('*');
                }
            },
            {
                extend: 'excel',
                title: 'Online Sales Report',
                footer: true
            }
        ]
    });
});
</script>





<script>
     $(document).ready(function() {
        
        $("#subadminId").on('change', function(){    
            
         let subadminId = $('#subadminId').val();
         
         var _token= $('input[name="_token"]').val();
            $.ajax({
              type:"GET",
              url:"{{ url('admin/store-name-list') }}?subadmin_id="+subadminId,
    
              success:function(res){
                   if (res) {
                        $('#storeId').prop("disabled", false);
                        $('#storeId').empty();
                        $('#storeId').append('<option value="">Store</option>');
                        $.each(res, function (index, store) {
                        var optionText = store.store_code ? store.store_name + ' -' + store.store_code: store.store_name;
                            $('#storeId').append('<option value="' + store.store_id + '">' + optionText + '</option>');
                            let storeIdUrl = getUrlParameter('store_id');
                            if ( typeof storeId !== "undefined" && storeId) {
                                $("#storeId option").each(function(){
                                    if($(this).val()==storeIdUrl){ 
                                        $(this).attr("selected","selected");    
                                    }
                                });
                            } 
                        });
                    } else {
                        $('#storeId').empty();
                    }
                }
    
            });
        });
    });
    
    
    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };
    
    
</script>

@endsection

