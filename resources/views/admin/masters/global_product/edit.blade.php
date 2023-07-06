@extends('admin.layouts.app')
@section('content')

<style>
.exam{
     text-align:left;
  width:100%;
}



iframe{
  width: 40% ! important;
  height: 40% ! important;
}
</style>

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
                        <li><a href="#tab-71" data-toggle="tab" class="">Videos</a></li>



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
                           <h5><strong>Product Information</strong></h5>
                        </div>
              @if ($message = Session::get('status'))
            <div class="alert alert-success">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif

            @if ($message1 = Session::get('status-error'))
            <div class="alert alert-success">
              <p>{{ $message1 }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
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
        <form action="{{route('admin.update_global_product',$product->global_product_id)}}" method="POST" enctype="multipart/form-data">
                  @csrf
            <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Product Name *</label>
                           <input type="text" class="form-control" required
                              name="product_name" value="{{old('product_name',$product->product_name)}}" placeholder="Product Name">
                        </div>
                     </div>
                      <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" >Product Description *</label>
                            <textarea class="form-control" id="product_description" required
                                name="product_description" rows="2" cols="3" placeholder="Product Description">{{old('product_description',$product->product_description)}}</textarea>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">MRP *</label>
                            <input type="number" step="0.01" class="form-control" required 
                             name="regular_price"   id="regular_price" value="{{old('regular_price',$product->regular_price)}}" oninput="regularPriceChange()" placeholder="MRP" >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Sale Price *</label>
                            <input type="number" step="0.01" class="form-control" required 
                             name="sale_price"  id="sale_price" value="{{old('sale_price',$product->sale_price)}}" placeholder="Sale Price" oninput="salePriceChange()" >
                            <span style="color:red" id="sale_priceMsg"> </span>

                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Tax *</label>
                           <select required  name="tax_id" id="tax_id" class="form-control"  >
                                 <option value="">Tax</option>
                                @foreach($tax as $key)
                                <option {{old('tax_id',$product->tax_id) == $key->tax_id ? 'selected':''}} value="{{$key->tax_id }}"> {{$key->tax_name }} ( {{$key->tax_value}} ) </option>
                                @endforeach
                              </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Min Stock *</label>
                            <input type="number" required class="form-control" onkeypress="preventNonNumericalInput(event)" name="min_stock" id="min_stock" value="{{old('min_stock',$product->min_stock)}}" placeholder="Min Stock">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Product Code *</label>
                            <input type="text" required class="form-control" name="product_code" id="product_code" oninput="isCodeAvailable(this.value,{{$product->global_product_id}})" value="{{old('sale_price',$product->product_code)}}" placeholder="Product Code">
                                                <p id="productCodeMsg" style="color:red"></p>

                        </div>
                    </div>

                    {{-- <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Product Type *</label>
                           <select name="business_type_id" required id="business_type" class="form-control"  >
                                 <option value=""> Select Business Type</option>
                                @foreach($business_types as $key)
                                <option {{old('business_type_id',$product->business_type_id) == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id }}"> {{$key->business_type_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Color *</label>
                           <select name="color_id" required id="color_id" class="form-control"  >
                                 <option value="">Color</option>
                                @foreach($colors as $key)
                                <option {{old('color_id',$product->color_id) == $key->attr_value_id ? 'selected':''}} value="{{$key->attr_value_id }}"> {{$key->group_value }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div> --}}

                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Product Brand </label>
                            <input type="text" class="form-control" name="product_brand" id="product_brand" value="{{old('product_brand',$product->product_brand)}}" placeholder="Product Brand">
                        </div>
                    </div>

                     {{-- <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Attribute </label>
                       <select name="attr_group_id"  class="attr_group form-control" >
                         <option value="">Attribute</option>
                          @foreach($attr_groups as $key)
                          <option {{old('attr_group_id',$product->attr_group_id) == $key->attr_group_id ? 'selected':''}} value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                @endforeach
                              </select>
                     </div>
                     </div> --}}

                     {{-- <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Value </label>
                            <select name="attr_value_id"   class="attr_value form-control" >
                                @if($product->attr_value_id == 0)
                                                         <option value="">Value</option>
                                @else
                            <option value="{{$product->attr_value_id}}">{{@$product->attr_value['group_value']}}</option> 
                            @endif
                            </select>
                        </div>
                    </div> --}}

                    <div class="col-md-6">
                     <div class="form-group">
                         <label class="form-label" >Product Category * </label>
                         <select name="product_cat_id" required id="category" class="form-control"  >
                              @foreach($category as $key)
                              <option {{old('product_cat_id',$product->product_cat_id) == $key->category_id ? 'selected':''}} value="{{ @$key->category_id }}">{{ @$key->category_name }}</option>
                              @endforeach
                           </select>
                     </div>
                  </div>

                  <div class="col-md-6">
                     <div class="form-group">
                         <label class="form-label" >Product Sub Category </label>
                         <select name="sub_category_id"  id="sub_category_id" class="form-control"  >
                              <option value="">Product Sub Category</option>
                              @foreach ($subcategories as $key)
                              <option {{old('sub_category_id',$product->sub_category_id) == $key->sub_category_id ? 'selected':''}} value="{{ @$key->sub_category_id }}">{{ @$key->sub_category_name }}</option>
                              @endforeach
                             

                         </select>
                     </div>
                  </div>

                     <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Vendor </label>
                           <select  name="vendor_id" id="vendor_id" class="form-control"  >
                            <option value="">Select Vendor</option>
                                @foreach($agencies as $key)
                                <option {{old('vendor_id',$product->vendor_id) == $key->agency_id ? 'selected':''}} value="{{$key->agency_id }}"> {{$key->agency_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div>

                     
                <div class="col-md-12">
                   <div class="form-group">
                    <div class="BaseFeatureArea">
                        <label class="form-label">Upload Images</label>
                        <input type="file" accept="image/png, image/jpeg, image/jpg"  class="form-control imgValidation" name="product_image[]"   placeholder="Product Feature Image">
                        <br>
                     </div>
                     </div>
                 </div>



                   <h4 class="ml-4">Videos</h4>

                      <div id="teamArea" class="container">
                      <div  class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Platform </label>
                                <select name="platform[]"   class="form-control">
                                 <option value="">Platform</option>
                                 <option {{old('platform.0') == 'Youtube' ? 'selected':''}} value="Youtube">Youtube</option>
                                 <option {{old('platform.0') == 'Vimeo' ? 'selected':''}} value="Vimeo">Vimeo</option>
                                </select>
                            </div>
                         </div>
                          <div class="col-md-12">
                              <div class="form-group">
                                  <label class="form-label">Video Link</label>
                                <textarea class="form-control"  name="video_code[]" rows="4" placeholder="Video Link">{{old('video_code.0')}}</textarea>                            
                            </div>
                          </div>
                        </div>
                    </div>
                
                      <div class="col-md-2">
                        <div class="form-group">
                            <button type="button" id="addImage" class="mt-2 btn btn-raised btn-success"> Add More</button>
                        </div>
                      </div>



            </div>

            <div class="row">
                      <div class="col-md-12">
                     <div class="form-group">
                     <center>
                            <button  type="submit" id="submit" class="btn btn-raised btn-info">
                           Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.global_products') }}">Cancel</a>
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
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>
                                   <th class="wd-15p">{{ __('Base Image') }}</th>

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
                                    <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.$product_image->image_name)}}"  width="50" ></td>
                                      <td>
                                        @if($product_image->image_name != $product->product_base_image)
                                          <a href="{{ url('admin/img-status/'.$product_image->global_product_image_id) }}"   class="btn btn-sm
                                          @if($product_image->image_name != $product->product_base_image) btn-danger @else btn-success @endif "   > @if($product_image->image_name != $product->product_base_image)
                                            Not Default
                                          @else
                                            Base Image
                                          @endif</a>
                                          
                                          @else
                                             <a href="#"  class="btn btn-sm btn-success "   > Base Image   </a>
                                          @endif
                                          
                                    {{--  {{$product_image->image_name }} -
                                          {{$product->product_base_image }}  --}}
                                   
                                    </td>
                                    <td>
                                        <form action="{{route('admin.destroy_global_product_image',$product_image->global_product_image_id)}}" method="POST">
                                            @csrf
                                            @method('POST')

                                            <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                 </tr>
                                 @endforeach
                                 @else
                                 <tr>
                                <td colspan="3"><center> No data available in the table</center></td>
                                  </tr>
                                  @endif
                              </tbody>
                           </table>
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.global_products') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>


                   <div class="tab-pane" id="tab-71">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Product Videos</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Platform') }}</th>
                                    <th class="wd-15p">{{ __('Video') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                   {{--  <th  class="wd-20p">{{__('Action')}}</th> --}}
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$videos->isEmpty())
                                 @foreach ($videos as $value)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $value->platform}}</td>
                                    @php
                                       $vid = $value->video_code;
                                    @endphp
                                    <td  >
                                    <div class="exam"> {!!$value->video_code!!} </div>
                                    </td>
                                     

                                    <td>
                                       <form action="{{route('admin.destroy_global_video',$value->global_product_video_id)}}" method="POST">
                                         @csrf
                                          @method('POST')
                                          <a class="btn btn-sm btn-cyan"  href="{{url('admin/global/product/video/edit/'.Crypt::encryptString($value->global_product_video_id))}}">Edit</a>
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                       </form>
                                    </td>
                                 </tr>
                                 @endforeach
                                 @else
                                 <tr>
                                <td colspan="3"><center> No data available in the table</center></td>
                                  </tr>
                                  @endif
                              </tbody>
                           </table>
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.global_products') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
       </div>
       </div>


       

            </div>
            </div>

</div>
</div>
</div>
<script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
<script>CKEDITOR.replace('product_description');</script>


<script>


        
  function preventNonNumericalInput(e) {
    e = e || window.event;
    var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
    var charStr = String.fromCharCode(charCode);
  
    if (!charStr.match(/^[0-9]+$/))
      e.preventDefault();
  }
      </script>
  
  
 

<script type="text/javascript">


function isCodeAvailable(value,global_product_id)
{
            var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('g-product/ajax/is-code-available') }}?product_code="+value+"&global_product_id="+global_product_id,


          success:function(res){
                if(res == 1)
                {
                   $('#productCodeMsg').text('Product code exists'); 
                   $('#submit').hide();
                }
                else{
                   $('#productCodeMsg').text(''); 
                   $('#submit').show();

                }
          }

        });
}


$(document).ready(function() {
    salePriceChange();
});

function regularPriceChange(){
    salePriceChange();
}

function salePriceChange()
{
    let salePrice = $('#sale_price').val();
    let regularPrice = $('#regular_price').val();
    
    if(parseFloat(salePrice) < 0)
    {
            $('#sale_price').val(0);
    }
    
    if(parseFloat(regularPrice) < 0)
    {
            $('#regular_price').val(0);
    }
    
    if(salePrice !== "")
    {
        if(regularPrice !== "")
        {
            console.log(regularPrice + " " + salePrice);
            if( parseFloat(salePrice) <= parseFloat(regularPrice))
            {
                $('#sale_priceMsg').html('');
                $("#submit").attr("disabled", false);

            }
            else
            {
                $('#sale_priceMsg').html('Sale price should be less than or equal to MRP');
                $("#submit").attr("disabled", true);
            }
        }
        else
        {
             $('#sale_priceMsg').html('MRP is empty');
             $("#submit").attr("disabled", true);

        }
    }
    else
    {
        $('#sale_priceMsg').html('');
        $("#submit").attr("disabled", false);

    }
}



$(document).ready(function() {
   var wrapper      = $("#teamArea"); //Fields wrapper
  var add_button      = $("#addImage"); //Add button ID

  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div> <br> <div  class="row"><div class="col-md-12"><div class="form-group"><label class="form-label">Platform</label><select name="platform[]"  class="form-control"><option value="">Platform</option><option value="Youtube">Youtube</option><option value="Vimeo">Vimeo</option></select></div></div><div class="col-md-12"><div class="form-group"><label class="form-label">Video Link Code </label><textarea class="form-control"  name="video_code[]"  rows="3" placeholder="Video Link"></textarea></div></div></div><a href="#" class="remove_field mb-2 btn btn-info btn btn-sm">Remove</a></div>'); //add input box
      });



  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});



$(document).ready(function() {
    var pcc = 0;
      //  alert("dd");
       $('#category').change(function(){
         if(pcc != 0)
         { 
        var category_id = $(this).val();
       //alert(business_type_id);
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('admin/product/ajax/get_subcategory') }}?category_id="+category_id,


          success:function(res){
           // alert(data);
            if(res){
            $('#sub_category_id').prop("diabled",false);
            $('#sub_category_id').empty();
            $('#sub_category_id').append('<option value="">Product Sub Category</option>');
            $.each(res,function(sub_category_id,sub_category_name)
            {
              $('#sub_category_id').append('<option value="'+sub_category_id+'">'+sub_category_name+'</option>');
            });

            }else
            {
              $('#sub_category_id').empty();

            }
            }

        });
         }else{
           pcc++;
         }
      });

    });



$(document).ready(function() {
   var wrapper      = $(".BaseFeatureArea"); //Fields wrapper
  var add_button      = $(".addBaseFeatureImage"); //Add button ID

  var x = 1; //initlal text box count
  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div>  <input type="file" class="form-control" name="product_image[]"   value="{{old('product_image')}}" placeholder="Base Product Feature Image" /> <a href="#" class="remove_field btn btn-primary">Remove</a></div>'); //add input box

  });

  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});




 $(document).ready(function() {

var agc = 0;

       $('.attr_group').change(function(){
       // alert("hi");
       if(agc != 0)
       { 
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
            $('.attr_value').append('<option value="">Value</option>');
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
       }
       else
       {
         agc++;
       }
      });

    });
  $(document).ready(function() {
    var pcc = 0;
      //  alert("dd");
       $('#business_type').change(function(){
         if(pcc != 0)
         { 
        var business_type_id = $(this).val();
       //alert(business_type_id);
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('admin/product/ajax/get_category') }}?business_type_id="+business_type_id,
          success:function(res){
           // alert(data);
            if(res){
            $('#category').prop("diabled",false);
            $('#category').empty();
            $('#category').append('<option value="">Product Category</option>');
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
         }else{
           pcc++;
         }
      });

    });

</script>
@endsection
