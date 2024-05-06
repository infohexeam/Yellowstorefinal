@extends('admin.layouts.app')
@section('content')
<div class="row" id="user-profile">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <div class="wideget-user">
               <h4>{{$pageTitle}}</h4>
            </div>
         </div>

         <div class="border-top">
            <div class="wideget-user-tab">
               <div class="tab-menu-heading">
                  <div class="tabs-menu1">
                     <ul class="nav">
                        <li class=""><a href="#tab-51" class="active show" 
                           data-toggle="tab">Basic Information</a></li>
                        <li><a href="#tab-61" data-toggle="tab" class="">Images</a></li>
                         <li><a href="#tab-71" data-toggle="tab" class="">Attributes</a></li>
                        
                       
                       
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="product_id" value="{{$product->product_id}}">
      <div class="card">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>product Information</strong></h5>
                        </div>
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
               <form action="{{route('admin.update_product',$product->product_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Product Name</label>
                           <input type="text" class="form-control"
                              name="product_name" value="{{old('product_name',$product->product_name)}}" placeholder="Product Name">
                        </div>
                        <div class="form-group">
                           <label class="form-label" >Product Category </label>
                          <select name="product_cat_id" id="category" class="form-control"  >
                       <option  selected="" value="{{$product->product_cat_id}}">  
                        {{$product->categories->category_name}}</option>
                              
                          </select>
                         
                        </div>
                     </div>
                       <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Business Type</label>
                    <select name="business_type_id" id="business_type" required="" class="form-control" >
                      <option value=""> Select Business Type</option>
                      @foreach($business_types as $key)
                       <option {{old('business_type_id',$product->business_type_id) == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                        @endforeach
                      </select>
                         
                        </div>
                        <div class="form-group">

                       <label class="form-label">Stock Count </label>
                        <input type="number" class="form-control" name="stock_count" value="{{old('stock_count',$product->stock_count)}}" placeholder="Stock Count">

                     </div>
                     
                 </div>
                      
                   <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Attribut Group </label>
                       <select name="attr_group_id" class="attr_group form-control" >
                         <option value=""> Select Attribute Group</option>
                          @foreach($attr_groups as $key)
                          <option {{old('attr_group_id',$product->attr_group_id) == $key->attr_group_id ? 'selected':''}} value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                @endforeach
                              </select>
                     </div>
                   </div>
              
                 <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Attribute Value</label>
                        <select name="attr_value_id"  class="attr_value form-control" >
                         <option value=""> Select Attribute Value</option>
                               <option  selected="" value="{{$product->attr_value_id}}">  
                       </option>
                         
                              </select>
                       
                     </div>
                 </div>
                    
                 
                     <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Product Price </label>
                        <input type="text" class="form-control" name="product_price" value="{{old('product_price',$product->product_price)}}" placeholder="Product Price">
                     </div>
              
                
                     <div class="form-group">
                        <label class="form-label">Product Offer From Date</label>
                        <input type="date" class="form-control" name="product_offer_from_date" value="{{old('product_offer_from_date',$product->product_offer_from_date)}}" placeholder="Product Offer From Date">
                       
                     </div>
                 </div>
               
                <div class="col-md-6">
                     <div class="form-group">
                         <label class="form-label">Product Offer Price</label>
                        <input type="text" class="form-control" name="product_price_offer" value="{{old('product_price_offer',$product->product_price_offer)}}" placeholder="Product Offer Price">
                     </div>
              
                     <div class="form-group">
                        <label class="form-label">Product Offer To Date </label>
                        <input type="date" class="form-control" name="product_offer_to_date" value="{{old('product_offer_to_date',$product->product_offer_to_date)}}" placeholder="Product Offer To Date">
                     </div>
                </div>
               

                
                   <div class="col-md-6">
                      <div class="form-group">
                      
                       <label class="form-label">Store </label>
                       <select name="store_id" class="form-control" >
                         <option value=""> Select Store</option>
                          @foreach($store as $key)
                          <option {{old('store_id',$product->store_id) == $key->store_id ? 'selected':''}} value="{{$key->store_id}}"> {{$key->store_name}} </option>
                                @endforeach
                              </select>

                     </div>
                     
                 </div>
                  <div class="col-md-6">
                   <div class="form-group">
                     <label class="form-label">Product Delivery Information </label>
                      
                          <textarea class="form-control" id="product_delivery_info"
                              name="product_delivery_info" rows="2" cols="3" placeholder="Product Delivery Information">{{old('product_delivery_info',$product->product_delivery_info)}}</textarea>
                       </div>
                     </div>
                   
                    <div class="col-md-6">
                      <div class="form-group">
                         <label class="form-label">Product Shipping Information </label>
                        
                          <textarea class="form-control" id="product_shipping_info"
                              name="product_shipping_info" rows="2" cols="3" placeholder="Product Shipping Information">{{old('product_shipping_info',$product->product_shipping_info)}}</textarea>
                        
                        
                     </div>
                 </div>
                  {{-- <div class="col-md-4">
                   <div class="form-group">
                    <div class="BaseFeatureArea">
                        <label class="form-label">Product Feature Images </label>
                        <input type="file" class="form-control" name="product_image[]" multiple="" value="{{old('product_image',$product->product_image)}}" placeholder="Product Feature Image">
                        
                     </div>
                     </div>
                 </div>
                 
                  <div class="col-md-2">
                   <div class="form-group">
                         <label class="form-label">Add More</label>
                            <button type="button" class="addBaseFeatureImage btn btn-raised btn-success">
                      Add More</button>
                       
                     </div>
                </div> --}}

                
               </div>
                   <div class="row">
                   <div class="col-md-6">
                    <div class="form-group">
                           <label class="form-label" >Product Description</label>
                           <textarea class="form-control" id="product_description"
                              name="product_description" rows="2" cols="3" placeholder="Product Description">{{old('product_description',$product->product_description)}}</textarea> 
                        </div>
                     </div>
                     <div class="col-md-6">
                        
                        <div class="form-group">
                           <label class="form-label">Product Specification </label>
                           <textarea class="form-control" id="product_specification"
                              name="product_specification" rows="2" cols="3" placeholder="Product Specification">{{old('product_specification',$product->product_specification)}}</textarea>
                        </div>
                     </div>     
                  </div>
                <br>
              <div class="row">
                      <div class="col-md-12">
                     <div class="form-group">
                     <center>
                            <button type="submit" class="btn btn-raised btn-info">
                           Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_product') }}">Cancel</a>
                       </center>
                     </div>
                  </div>
                </div>
              </form>

                      </div>
                
                        
                     </div>
                 </div>
              <div class="tab-pane" id="tab-61">
                     
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Product Images</strong></h5>
                        </div><br>
                         <div class="card-body border">
                <form action="{{route('admin.store_product_image',$product->product_id)}}" method="POST" 
                         enctype="multipart/form-data">
                   @csrf
                   <div class="media-heading">
                           <h5><strong>Add Product Image</strong></h5>
                        </div><br>
            <div class="row">
               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Image</label>
                      <input type="hidden" class="form-control" 
                       name="product_id"  value="{{ old('product_id',$product->product_id) }}" >

                       <input type="file" class="form-control" 
                       name="product_image"  value="{{ old('product_image') }}" placeholder="Product Image">

                  </div>
               </div>
             </div>
                <div class="col-md-12">
              <div class="form-group">
                           <center>
                            <label></label>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">Reset</button>
                         
                           </center>
                        
                </div>
              </div>

                   </form>
                </div>
                <br>
              
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>
                                    <th class="wd-15p">{{ __('Image Status') }}</th>
                                  <th  class="wd-20p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$product_images->isEmpty())
                                 @foreach ($product_images as $product_image)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td><img src="{{asset('/assets/uploads/products/base_product/feature_image/'.$product_image->product_image)}}"  width="50" ></td>
                                     <td>

                                   <form action="{{route('admin.status_product_image',$product_image->product_image_id)}} " method="POST">
                                          
                                          @csrf
                                          @method('POST')
                                          <button  @if($product_image->image_flag == 0) type="button" @else type="submit" onclick="return confirm('Do you want to Change Default Image?');" @endif  class="btn btn-sm
                                          @if($product_image->image_flag == 0) btn-danger @else btn-success @endif"> @if($product_image->image_flag == 0)
                                          Default
                                          @else
                                          Thumb
                                          @endif</button>

                                       </form>
                            </td>
                                     <td>
                               <form action="{{route('admin.destroy_product_image',$product_image->product_image_id)}}" method="POST">
                       
                              @csrf
                             @method('POST')

                       
                            <button @if($product_image->image_flag == 0) type="button" @else type="submit" onclick="return confirm('Do you want to Delete Image?');" @endif  class="btn btn-sm btn-danger">Delete</button>
                         </form> 

                        </td>
                                  
                                 </tr>
                                 @endforeach
                                 @else
                                 <tr>
                                <td colspan="5"><center> No data available in the table</center></td>
                                  </tr>
                                  @endif
                              </tbody>   
                           </table>
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_product') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
               <div class="tab-pane" id="tab-71">
                     
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Attributes</strong></h5>
                        </div><br>

       
                        <div class="table-responsive">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Attribute Group') }}</th>
                                    <th class="wd-15p">{{ __('Attribute Value') }}</th>
                                    {{-- <th  class="wd-20p">{{__('Action')}}</th>  --}}
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                 @if(!$attr_varient_products->isEmpty()) 
                                 @foreach ($attr_varient_products as $attr_varient_product)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{@$attr_varient_product->attr_group['group_name']}}</td>
                                    <td>{{@$attr_varient_product->attr_value['group_value']}}</td>
                                 {{--   <td>
                                    <form action="{{route('admin.destroy_attribute',$attr_group->attr_group_id)}}" method="POST">
                       
                                    @csrf
                                    @method('POST')
                                    <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                    </form>  
                                    </td>--}}
                                 </tr>
                                 @endforeach
                                  @else
                                 <tr>
                                <td colspan="4"><center> No data available in the table</center></td>
                                  </tr>
                                  @endif 
                              </tbody>   
                           </table>
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_product') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
               
               
                  </div>
             

</div>
</div>
</div>
<script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
<script>CKEDITOR.replace('product_description');</script>
<script>CKEDITOR.replace('product_specification');</script>
<script>CKEDITOR.replace('product_delivery_info');</script>
<script>CKEDITOR.replace('product_shipping_info');</script>

@endsection

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script type="text/javascript">


$(document).ready(function() {
   var wrapper      = $(".BaseFeatureArea"); //Fields wrapper
  var add_button      = $(".addBaseFeatureImage"); //Add button ID
  
  var x = 1; //initlal text box count
  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div>  <input type="file" class="form-control" name="product_image[]"  multiple="" value="{{old('product_image')}}" placeholder="Base Product Feature Image" /> <a href="#" class="remove_field btn btn-primary">Remove</a></div>'); //add input box
    
  });
  
  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});




 $(document).ready(function() {
       $('.attr_group').change(function(){
       // alert("hi");
       // alert("dd");
        var attr_group_id = $(this).val();
       
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('admin/product/ajax/get_attr_value') }}?attr_group_id="+attr_group_id,
         
         
          success:function(res){
            //alert(data);
            if(res){
            $('.attr_value').prop("diabled",false);
            $('.attr_value').empty();
            $('.attr_value').append('<option value="">Select Attribute Value</option>');
            $.each(res,function(attr_value_id,group_value)
            {
              $('.attr_value').append('<option value="'+attr_value_id+'">'+group_value+'</option>');
            });

            }else
            {
              $('.attr_value').empty();

            }
            }

        });
      });
       
    });

    $(document).ready(function() {
       $('#business_type').change(function(){
       // alert("dd");
        var business_type_id = $(this).val();
       //alert(business_type_id);
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('store/product/ajax/get_category') }}?business_type_id="+business_type_id,
         
         
          success:function(res){
           // alert(data);
            if(res){
            $('#category').prop("diabled",false);
            $('#category').empty();
            $('#category').append('<option value="">Select Product Category</option>');
            $.each(res,function(category_id,category_name)
            {
              $('#category').append('<option value="'+category_id+'">'+category_name+'</option>');
            });

            }else
            {
              $('#category').empty();

            }
            }

        });
      });
       
    });
    
</script>