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
                                <form action="{{route('store.overall_product_reports')}}" method="GET" enctype="multipart/form-data">
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
                                         
                                         
                                      
                                          
                                           <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Products</label>
                                                      <select name="product_id" id="product_id" class="form-control select2-show-search" data-placeholder="Products" >
                                                           <option value="" >Products</option>
                                                           @foreach ($products as $key)
                                                                <option value="{{ $key->product_id }}" {{request()->input('product_id') == $key->product_id ? 'selected':''}} > {{ $key->product_name }} </option>
                                                           @endforeach
                                                      </select>
                                               </div>
                                          </div>
                                            <div class="col-md-6">
                                              <div class="form-group">
                                                  <label class="form-label">Base/Variant</label>
                                                      <select name="type_id" id="type_id" class="form-control select2-show-search" data-placeholder="Base/Variant" >
                                                           <option value="" >Select</option>
                                                           <option value="1" {{request()->input('type_id') == 1 ? 'selected':''}}>Base Product</option>
                                                            <option value="2" {{request()->input('type_id') == 2 ? 'selected':''}} >Variant Product</option>
                                                         
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
                                                   <a href="{{route('store.overall_product_reports')}}"  class="btn btn-info">Cancel</a>
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
                                            <th class="wd-15p">Product code</th>
                                            <th class="wd-15p">Created Date <br> and Time</th>
                                            <th class="wd-15p">Product Price</th>

                                            <th class="wd-15p">Category</th>
                                            <th class="wd-15p">Sub Category</th>
                                            <th class="wd-15p">Brand</th>
                                            <th class="wd-15p">Product Type</th>
                                            <th class="wd-15p">Base/Variant Product</th>
                                            {{-- <th class="wd-15p">Minimum Stock</th> --}}
                                            <th class="wd-15p">Product Status</th>
                                          
                                       
                                         </tr>
                                      </thead>
                                      <tbody>
                                          
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($data as $d)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $d->variant_name }}
                                            @if($d->is_base_variant==1)
                                            <br>
                                            <span class="badge badge-sm badge-success"><small>Base Product</small></span>

                                            @else
                                            <br>
                                             <span class="badge badge-sm badge-warning"><small>Variant Product</small></span>

                                            @endif
                                            </td>
                                            <td>{{ $d->product_code }}</td>
                                            <td>
                                                {{ \Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}
                                            <br>
                                                {{ \Carbon\Carbon::parse($d->created_at)->format('H:i:s')}}
                                            </td>
                                            <td>{{ $d->product_varient_offer_price }}</td>
                                           
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
                                            <td>
                                            @if($d->product_type==1)
                                             Product
                                            @else
                                             Service
                                                @if($d->service_type==1)
                                                - Booking
                                                @else
                                                 - Purchase
                                                @endif
                                            @endif
                                            </td>
                                             <td>
                                             @if($d->is_base_variant==1)
                                             Base Product
                                             @else
                                             Variant Product
                                             @endif
                                             </td>
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
                title: 'Overall Product Report',
                footer: true,
              
                 orientation : 'landscape',
                pageSize : 'LEGAL',
            },
            {
                extend: 'excel',
                title: 'Overall Product Report',
                footer: true,
               
            }
         ]
    } );

} );
</script>

<script>
      $(document).ready(function() {
        
        $("#categoryId").on('change', function(){    
            
        let categoryId = $('#categoryId').val();
        
       // console.log(categoryId);

        var _token= $('input[name="_token"]').val();
        
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

