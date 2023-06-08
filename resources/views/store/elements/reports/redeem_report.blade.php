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
                                <form action="{{route('store.wallet_reports')}}" method="GET" enctype="multipart/form-data">
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
                                         
                                        <!--  @if(auth()->user()->user_role_id  == 0) -->
                                        <!--    <div class="col-md-6">-->
                                        <!--      <div class="form-group">-->
                                        <!--         <label class="form-label">Sub Admin</label>-->
                                        <!--         <div id="subadminl"></div>-->
                                        <!--               <select  name="subadmin_id" id="subadminId" class="form-control select2-show-search" data-placeholder="Sub Admin" >-->
                                        <!--                  <option value="">Sub Admin</option>-->
                                        <!--                     @foreach($subadmins as $key)-->
                                        <!--                     <option {{request()->input('subadmin_id') == $key->id ? 'selected':''}} value="{{$key->id}}"> {{$key->name }} </option>-->
                                        <!--                     @endforeach-->
                                        <!--               </select>-->
                                        <!--      </div>-->
                                        <!--   </div>-->
                                        <!--@endif-->
                                         
                                        <!-- <div class="col-md-6">-->
                                        <!--    <div class="form-group">-->
                                        <!--      <label class="form-label">Store </label>-->
                                        <!--       <select  name="store_id" id="storeId" class="form-control select2-show-search" data-placeholder="Store"  >-->
                                        <!--             <option value="">Store</option>-->
                                        <!--          -->
                                        <!--          </select>-->
                                        <!--    </div>-->
                                        <!-- </div>-->
                                        <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Customer Mobile Number</label>
                                                      <input type="number" class="form-control"  name="customer_mobile_number" id="date_fromc"  value="{{@$customer_mobile}}" placeholder="Customer Mobile">
                            
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
                                      
                                         
                                         <div class="col-md-12">
                                            <div class="form-group">
                                                <center>
                                                   <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Filter</button>
                                                   {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                                                   <a href="{{route('admin.redeem_reports')}}"  class="btn btn-info">Cancel</a>
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
                                            <th class="wd-15p">Total Amount</th>
                                            <th class="wd-15p">Redeem Points<br> Used(Admin)</th>
                                             <th class="wd-15p">Redeem Points<br> Used(store)</th>
                                             <th class="wd-15p">Amount<br> Reduced(Admin)</th>
                                             <th class="wd-15p">Amount<br> Reduced(store)</th>
                                            <th class="wd-15p">Amount before <br> applying Points</th>
                                             <th class="wd-15p">Customer</th>
                                            <th class="wd-15p">Customer Phone</th>
                                            <th class="wd-15p">Store</th>
                                            <!--<th class="wd-15p">Subadmin</th>-->
                                            <th class="wd-15p">Order Status</th>

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
                                            <td>{{ $d->product_total_amount }}</td>
                                            <td>{{ $d->reward_points_used }}</td>
                                             <td>{{$d->reward_points_used_store}}</td>
                                            <td>{{$d->amount_reduced_by_rp}}</td>
                                            <td>{{$d->amount_reduced_by_rp_store}}</td>
                                            @php
                                             $item_price=DB::table('trn_order_items')->where('order_id',$d->order_id)->sum('total_amount');
        //$w->amount_before_applying_rp=$item_price+$$w->amount_reduced_by_rp??0+$w->packing_charge??0+$w->delivery_charge??0+$w->amount_reduced_by_rp_store??0+$w->amount_reduced_by_coupon??0;
        //$d->amount_before_applying_rp=strval(number_format($item_price+$w->packing_charge+$w->delivery_charge,2));

                                            @endphp
                                            <td>{{ number_format($item_price+$d->packing_charge+$d->delivery_charge+$d->amount_reduced_by_coupon??0,2) }}</td>
                                           
                                            <td>{{ $d->customer_first_name }} {{ $d->customer_last_name }}</td>
                                            <td>{{ @$d->customer_mobile_number }}</td>
                                            
                                             <td>{{ @$d->store_name }}</td>
                                            
                                            
                                            
                                            <td>{{ @$d->status->status }}</td>
                                            
                                            

                                          
                                            
                                            

                                           
                                           
                                           

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
                title: 'Wallet Redeem Report',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9]
                 },
                 orientation : 'landscape',
                pageSize : 'LEGAL',
            },
            {
                extend: 'excel',
                title: 'Wallet Redeem Report',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9]
                 }
            }
         ]
    } );

} );
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
                    if(res){
                       // console.log(res);
                        $('#storeId').prop("diabled",false);
                        $('#storeId').empty();
                        $('#storeId').append('<option value="">Store</option>');
                        $.each(res,function(store_id,store_name)
                        {
                          $('#storeId').append('<option value="'+store_id+'">'+store_name+'</option>');
                        });
                    }else
                    {
                      $('#storeId').empty();
                    }
                }
    
            });
        });
    });
    
    
    $(document).ready(function() {
 
     let subadminId = $('#subadminId').val();
      if ( typeof subadminId === "undefined") {
          subadminId = '';
      }
     let storeId = $('#storeId').val();
     
     var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('admin/product-name-list') }}?subadmin_id="+subadminId+'&store_id'+storeId,

          success:function(res){
                if(res){
                   // console.log(res);
                    $('#productId').prop("diabled",false);
                    $('#productId').empty();
                    $('#productId').append('<option value="">Product</option>');
                    $.each(res,function(product_id,product_name)
                    {
                      $('#productId').append('<option value="'+product_id+'">'+product_name+'</option>');
                    });
                    
                    let productId = getUrlParameter('product_id');
                    if ( typeof productId !== "undefined" && productId) {
                        $("#productId option").each(function(){
                            if($(this).val()==productId){ 
                                $(this).attr("selected","selected");    
                            }
                        });
                    } 
    
                }else
                {
                  $('#storeId').empty();
                }
            }

        });

    });
    
    
    $(document).ready(function() {
        
        $("#categoryId").on('change', function(){    
            
        let categoryId = $('#categoryId').val();
        
       // console.log(categoryId);

        var _token= $('input[name="_token"]').val();
        
            $.ajax({
              type:"GET",
              url:"{{ url('admin/sub-category-list') }}?category_id="+categoryId,
    
              success:function(res){
                    if(res){
                       // console.log(res);
                        $('#subCategoryId').prop("diabled",false);
                        $('#subCategoryId').empty();
                        $('#subCategoryId').append('<option value="">Sub Category</option>');
                        $.each(res,function(sub_category_id,sub_category_name)
                        {
                          $('#subCategoryId').append('<option value="'+sub_category_id+'">'+sub_category_name+'</option>');
                        });
                        
                        let subCategoryId = getUrlParameter('sub_category_id');
                        if ( typeof subCategoryId !== "undefined" && subCategoryId) {
                            $("#subCategoryId option").each(function(){
                                if($(this).val()==subCategoryId){ 
                                    $(this).attr("selected","selected");    
                                }
                            });
                        } 
                    
                    
                    }else
                    {
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

