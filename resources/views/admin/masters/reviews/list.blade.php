@extends('admin.layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">


        <div class="card">
            <div class="card-body border">
                <div class="row">
                    <div class="col-12" >
                            @if ($message = Session::get('status'))
                            <div class="alert alert-success">
                                <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                            </div>
                            @endif
                              <div class="card-header">
                        <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                     </div>

                     <form  action="{{route('admin.list_reviews')}}" method="GET" enctype="multipart/form-data">
                        @csrf
                            <div class="row">



                        {{-- <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Sub Admin</label>
                            <div id="subadminl"></div>
                                <select  name="subadmin_id"  id="subadminId"  class="form-control"  >
                                    <option value=""> Select Sub Admin</option>
                                        @foreach($subadmins as $key)
                                        <option {{request()->input('subadmin_id') == $key->id ? 'selected':''}} value="{{$key->id}}"> {{$key->name }} </option>
                                        @endforeach
                                </select>
                        </div> 
                    </div> --}}

                        <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Store</label>
                            <select name="store_id" class="form-control" >
                                <option value=""> Store</option>
                                @foreach($stores as $key)
                                <option {{request()->input('store_id')  == $key->store_id ? 'selected':''}} value="{{$key->store_id}}"> {{$key->store_name}} </option>
                                    @endforeach
                                    </select>
                            </div>
                        </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Visibility Status</label>
                            <div id="isVisiblel"></div>
                            
                            <select class="form-control" name="isVisible" id="isVisible">
                                        
                                    <option {{request()->input('isVisible') == 1 ? 'selected':''}} value="1"> Visible </option>
                                   
                                    <option value="0"  {{request()->input('isVisible') == 0 ? 'selected':''}}> Not Visible </option>
                                    <option  value="" @if(is_null(request()->input('isVisible'))) selected @endif> Visibility Status</option>
                                    
                                </select>
                        </div>
                        @php
                             //dd(request()->input('isVisible'));
                            @endphp
                    </div>

                       {{-- <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label">Product </label>
                            <select  name="product_id" id="productId" class="form-control select2-show-search" data-placeholder="Product"  >
                                <option value="">Product</option>
                                
                                </select>
                        </div>
                        </div> --}}
                        


                    
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
                            <label class="form-label">Rating</label>
                            <select class="form-control" name="rating" id="rating">
                                    <option value=""> Rating</option>
                                    <option {{request()->input('rating') == 1 ? 'selected':''}} value=" 1 "> 1 </option>
                                    <option {{request()->input('rating') == 2 ? 'selected':''}} value=" 2 ">  2 </option>
                                    <option {{request()->input('rating') == 3 ? 'selected':''}} value=" 3 ">  3 </option>
                                    <option {{request()->input('rating') == 4 ? 'selected':''}} value=" 4 ">  4 </option>
                                    <option {{request()->input('rating') == 5 ? 'selected':''}} value=" 5 ">  5 </option>
                                </select>
                        </div>
                    </div>


                 <div class="col-md-12">
                 <div class="form-group">
                       <center>
                       <button type="submit" class="btn btn-raised btn-primary">
                       <i class="fa fa-check-square-o"></i> Filter</button>
                       <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button>
                      <a href="{{route('admin.list_reviews')}}"  class="btn btn-info">Cancel</a>
                       </center>
                    </div>
              </div>
</div>
</div>
   </form>

                        <div class="card-body">

                          

                            <div class="table-responsive">
                            <table id="exampletable" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Product')}}</th>
                                        <th class="wd-15p">{{__('Rating')}}</th>
                                        <th class="wd-15p">{{__('Store')}}</th>
                                        <th class="wd-15p">{{__('Customer')}}</th>
                                        <th class="wd-15p">{{__('Visibility Status')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($reviews as $row)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>
                                            
                                            
                                            {{ @$row->product_varient->variant_name}}
                                            
                                            
                                        </td>
                                        <td>{{ $row->rating}}</td>
                                        <td>{{ @$row->store->store_name}}</td>
                                        <td>{{ @$row->customer->customer_first_name}} {{ @$row->customer->customer_last_name}}</td>
                                        <td>
                                            <form action="{{route('admin.review_status',$row->reviews_id)}}" method="POST">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" class="btn btn-sm @if ($row->isVisible == 1) btn-success @else btn-warning @endif">
                                                    @if ($row->isVisible == 1)
                                                        Visible
                                                    @else
                                                        Not visible
                                                    @endif
                                                </button>
                                            </form>
                                        
                                        </td>
                                        <td>
                                            <form action="{{route('admin.destroy_review',$row->reviews_id)}}" method="POST">
                                                @csrf
                                            <a class="btn btn-sm btn-cyan text-white"  data-toggle="modal" data-target="#viewModal{{$row->reviews_id}}">View</a>
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                            </form>

                                        </td>
                                      
                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>

                            {{-- table responsive end --}}
                            </div>
                        {{-- Card body end --}}
                        </div>
                    {{-- col 12 end --}}
                </div>
            {{-- row end --}}
            </div>
        {{-- card --}}



        </div>
        {{-- row justify end --}}
    </div>
{{-- container end --}}
</div>




    @foreach ($reviews as $row)
              <div class="modal fade" id="viewModal{{$row->reviews_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">View Review and Rating</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">

                      <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Product Name:</strong> 
                                        @if(@$row->product_varient->product->product_name == @$row->product_varient->variant_name)
                                        <!--{{ @$row->product_varient->product->product_name}} -->
                                        
                                        {{ @$row->product_varient->variant_name}}
                                        @else
                                        {{ @$row->product_varient->product->product_name}}
                                        @endif
                                    </td>
                                 </tr>

                                 <tr>
                                     <td><strong>Store:</strong> {{ @$row->store->store_name}}</td>
                                 </tr>

                                 <tr>
                                    <td><strong>Review:</strong> {{ @$row->review}}</td>
                                </tr>


                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                     <td><strong>Customer:</strong> {{ @$row->customer->customer_first_name}} {{ @$row->customer->customer_last_name}}</td>
                                 </tr>

                                 <tr>
                                    <td><strong>Rating:</strong> {{ @$row->rating}}</td>
                                </tr>

                                <tr>
                                    <td><strong>Visiblity:</strong> 
                                    @if ($row->isVisible == 1)
                                        Visible
                                    @else
                                        Not Visible
                                    @endif
                                    </td>
                                </tr>
                               
                              </tbody>
                           </table>
                        </div>
                    </div>

              
                  </div>
               </div>
            </div>
    @endforeach

<script>
     $(document).ready(function() {
 
 let subadminId = ''

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

</script>


                         <script>

$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Customer rating and reviews',
                // orientation:'landscape',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5],
                     alignment: 'right',
                 },
                  customize: function(doc) {
                      doc.content[1].margin = [ 100, 0, 100, 0 ]; //left, top, right, bottom
				   doc.content.forEach(function(item) {
					if (item.table) {
						item.table.widths = [40, 'auto','auto','auto','auto','auto']
					 }
				   })
				 }
            },
            {
                extend: 'excel',
                title: 'Customer rating and reviews',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5],
                 }
            }
         ]
    } );

} );
            </script>
@endsection
