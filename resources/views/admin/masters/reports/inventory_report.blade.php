@extends('admin.layouts.app')
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
                                <form action="{{route('admin.inventory_reports')}}" method="GET" enctype="multipart/form-data">
                                   @csrf
                                    <div class="row">
                                        <!--<div class="col-md-6">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label class="form-label">From Date</label>-->
                                        <!--           <input type="date" class="form-control"  name="date_from" id="date_fromc"  value="{{@$datefrom}}" placeholder="From Date">-->
                            
                                        <!--     </div>-->
                                        <!--</div>-->
                                        <!--<div class="col-md-6">-->
                                        <!--    <div class="form-group">-->
                                        <!--        <label class="form-label">To Date</label>-->
                                        <!--         <input type="date" class="form-control" name="date_to"   id="date_toc" value="{{@$dateto}}" placeholder="To Date">-->
                                        <!--    </div>-->
                                        <!-- </div>-->
                                         
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
                                                      @if(request()->input('subadmin_id'))
                                                      @php
                                                      
                            
                                                        $storesData = \DB::table('mst_stores')->where('subadmin_id',request()->input('subadmin_id'))->get();
                                                      @endphp
                                                    @else
                                                    @php
                                                      $subadminId = auth()->user()->id;
                            
                                                      $storesData = \DB::table('mst_stores')->where('subadmin_id',$subadminId) ->get();
                                                    @endphp

                                                      @endif

                                                      @foreach($storesData as $key)
                                                      <option {{request()->input('store_id') == $key->store_id ? 'selected':''}} value="{{$key->store_id }}"> {{$key->store_name }} </option>
                                                      @endforeach
                                                   
                                                  </select>
                                            </div>
                                         </div>
                                         
                                      
                                          
                                           <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Products</label>
                                                      <select name="product_id" id="productId" class="form-control select2-show-search" data-placeholder="Products" >
                                                           <option value="" >Products</option>
                                                           @foreach ($products as $key)
                                                                <option value="{{ $key->product_id }}" {{request()->input('product_id') == $key->product_id ? 'selected':''}} > {{ $key->product_name }} </option>
                                                           @endforeach
                                                      </select>
                                               </div>
                                          </div>
                                          
                                          <div class="col-md-6">
                                            <div class="form-group">
                                              <label class="form-label">Vendor </label>
                                               <select  name="vendor_id" id="VendorId" class="form-control select2-show-search" data-placeholder="Vendor"  >
                                                <option value="">Vendor</option>
                                                    @foreach($agencies as $key)
                                                    <option {{request()->input('vendor_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id }}"> {{$key->agency_name }} </option>
                                                    @endforeach
                                                  </select>
                                            </div>
                                         </div>
                                         
                                         
                                         <div class="col-md-6">
                                            <div class="form-group">
                                              <label class="form-label">Category </label>
                                               <select  name="category_id" id="categoryId" class="form-control select2-show-search" data-placeholder="Category"  >
                                                <option value="">Category</option>
                                                    @foreach($categories as $key)
                                                    <option {{request()->input('category_id') == $key->category_id ? 'selected':''}} value="{{$key->category_id }}"> {{$key->category_name }} </option>
                                                    @endforeach
                                                  </select>
                                            </div>
                                         </div>
                                         
                                         <div class="col-md-6">
                                            <div class="form-group">
                                              <label class="form-label">Sub Category </label>
                                               <select  name="sub_category_id" id="subCategoryId" class="form-control select2-show-search" data-placeholder="Sub Category"  >
                                                <option value="">Sub Category</option>
                                                    @foreach($subCategories as $key)
                                                    <option {{request()->input('sub_category_id') == $key->sub_category_id ? 'selected':''}} value="{{$key->sub_category_id }}"> {{$key->sub_category_name }} </option>
                                                    @endforeach
                                                  </select>
                                            </div>
                                         </div>
                                         
                                         <div class="col-md-12">
                                            <div class="form-group">
                                                <center>
                                                   <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Filter</button>
                                                   {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                                                   <a href="{{route('admin.inventory_reports')}}"  class="btn btn-info">Cancel</a>
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
                                            <th class="wd-15p">Product Name</th>
                                            {{-- <th class="wd-15p">Variant</th> --}}
                                            <th class="wd-15p">Store</th>
                                            <th class="wd-15p">Subadmin</th>
                                            <th class="wd-15p">Stock</th>
                                            <th class="wd-15p">Updated Date <br> and Time</th>
                                            <th class="wd-15p">Variant Price</th>
                                            <th class="wd-15p">Vendor</th>
                                            <th class="wd-15p">Category</th>
                                            <th class="wd-15p">Sub Category</th>
                                            <th class="wd-15p">Brand</th>
                                            {{-- <th class="wd-15p">Minimum Stock</th> --}}
                                            <th class="wd-15p">Product Status</th>
                                            <th class="wd-15p">Old Stock</th>
                                            <th class="wd-15p">Newly<br>Added Stock</th>
                                         </tr>
                                      </thead>
                                      <tbody>
                                          
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($data as $d)
                                        <tr>
                                            <td>{{ ($data->currentPage()-1) * $data->perPage() + $loop->index + 1 }}</td>
                                            {{-- <td>{{ $d->product_name }}</td> --}}
                                            <td>{{ $d->variant_name }}</td>
                                            
                                            <td>{{ $d->store_name }}@if($d->store_code != NULL)-{{$d->store_code}} @endif</td>
                                            <td>{{ (new \App\Helpers\Helper)->subAdminName($d->subadmin_id) }}</td>

                                            <td>{{ $d->stock_count }}</td>
                                            <td>{{ \Carbon\Carbon::parse($d->updated_time)->format('d-m-Y')}} {{ \Carbon\Carbon::parse($d->updated_time)->format('H:i:s')}}</td>
                                            <td>{{ $d->product_varient_offer_price }}</td>
                                            <td>
                                                @if(isset($d->agency_name))
                                                {{ $d->agency_name }}
                                                @else
                                                ---
                                                @endif
                                            </td>
                                            <td>{{ $d->category_name }}</td>
                                            <td>
                                                @if(isset($d->sub_category_name))
                                                {{ $d->sub_category_name }}
                                                 @else
                                                ---
                                                @endif
                                                </td>
                                            <td>
                                                @if(isset($d->product_brand))
                                                {{ $d->product_brand }}
                                                 @else
                                                ---
                                                @endif
                                                </td>
                                            {{-- <td>{{ $d->min_stock }}</td> --}}
                                            <td> 
                                            @if($d->is_base_variant == 1)
                                                @if($d->product_status == 1)
                                                    Active
                                                @else
                                                    Inactive
                                                @endif
                                            @else
                                             @if($d->variant_status == 1)
                                                    Active
                                             @else
                                                    Inactive
                                             @endif
                                            @endif
                                            </td>
                                            <td>{{ $d->prev_stock }}</td>
                                            <td>{{ $d->stock }}</td>

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
   $(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Inventory report',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13]
                 },
                 orientation : 'landscape',
                pageSize : 'LEGAL',
            },
            {
                extend: 'excel',
                title: 'Inventory report',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9,10,11,12,13]
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
      if ( typeof subadminId === "undefined") {
          subadminId = '';
      }
         
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
    
    
    $(document).ready(function() {
 
     let subadminId = $('#subadminId').val();
      if ( typeof subadminId === "undefined") {
          subadminId = '';
      }
      
    $("#storeId").on('change', function(){   
    // alert(storeId);
     let storeId = $('#storeId').val();
      if ( typeof storeId === "undefined") {
          storeId = '';
      }
     
     var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('admin/product-name-list') }}?store_id="+storeId,

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

    });
       $(document).ready(function() {
        
        $("#categoryId").on('change', function(){    
            
        let categoryId = $('#categoryId').val();
        
       // console.log(categoryId);

        //var _token= $('input[name="_token"]').val();
        
            $.ajax({
              type:"GET",
              url:"{{ url('store/product/ajax/get_subcategory') }}?category_id="+categoryId,
    
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
                          $('#subCategoryId').append('<option value="0">Others</option>');
                        
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

