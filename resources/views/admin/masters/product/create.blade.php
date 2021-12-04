@extends('admin.layouts.app')
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

               <form action="{{route('admin.store_product')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf

                    <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Product Name</label>
                           <input type="text" class="form-control"
                              name="product_name" value="{{old('product_name')}}" placeholder="Product Name">
                        </div>
                        <div class="form-group">
                            <label class="form-label" >Product Category </label>
                          <select name="product_cat_id" id="category" class="form-control"  >
                                 <option value=""> Select Product Category</option>

                              </select>

                        </div>
                     </div>
                       <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Business Type</label>
                           <select name="business_type_id" id="business_type" class="form-control"  >
                                 <option value=""> Select Business Type</option>
                                @foreach($business_types as $key)
                                <option {{old('business_type_id') == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id }}"> {{$key->business_type_name }} </option>
                                @endforeach
                              </select>
                        </div>
                       <div class="form-group">

                       <label class="form-label">Stock Count </label>
                        <input type="number" class="form-control" name="stock_count" value="{{old('stock_count')}}" placeholder="Stock Count">

                     </div>
                     </div>

                         <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Attribut Group </label>
                       <select name="attr_group_id" class="attr_group form-control" >
                         <option value=""> Select Attribute Group</option>
                          @foreach($attr_groups as $key)
                          <option {{old('attr_group_id') == $key->attr_group_id ? 'selected':''}} value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                @endforeach
                              </select>
                     </div>
                   </div>

                 <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Attribute Value</label>
                        <select name="attr_value_id"  class="attr_value form-control" >
                         <option value=""> Select Attribute Value</option>

                              </select>

                     </div>
                 </div>


                     <div class="col-md-6">
                     <div class="form-group">
                        <label class="form-label">Product Price </label>
                        <input type="text" class="form-control" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="product_price" value="{{old('product_price')}}" placeholder="Product Price">
                     </div>


                     <div class="form-group">
                        <label class="form-label">Product Offer From Date</label>
                        <input type="date" class="form-control" name="product_offer_from_date" value="{{old('product_offer_from_date')}}" placeholder="Product Offer From Date">

                     </div>
                 </div>

                <div class="col-md-6">
                     <div class="form-group">
                         <label class="form-label">Product Offer Price</label>
                        <input type="text" class="form-control" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="product_price_offer" value="{{old('product_price_offer')}}" placeholder="Product Offer Price">
                     </div>

                     <div class="form-group">
                        <label class="form-label">Product Offer To Date </label>
                        <input type="date" class="form-control" name="product_offer_to_date" value="{{old('product_offer_to_date')}}" placeholder="Product Offer To Date">
                     </div>
                </div>

                 <div class="col-md-6">
                      <div class="form-group">

                       <label class="form-label">Product Base Image </label>
                        <input type="file" accept="image/x-png,image/jpg,image/jpeg" class="form-control" name="product_base_image" value="{{old('product_base_image')}}" placeholder="Product Base Image">

                     </div>

                 </div>
                  <div class="col-md-4">
                   <div class="form-group">
                    <div class="BaseFeatureArea">
                        <label class="form-label">Product Feature Images </label>
                        <input type="file" class="form-control" name="product_image[]" multiple="" value="{{old('product_image')}}" placeholder="Product Feature Image">

                     </div>
                     </div>

                 </div>

                  <div class="col-md-2">
                   <div class="form-group">
                         <label class="form-label">Add More</label>
                            <button type="button" class="addBaseFeatureImage btn btn-raised btn-success">
                      Add More</button>

                     </div>
                </div>

                    </div>
            <div class="row">
                <div class="col-md-6">
                   <div class="form-group">
                     <label class="form-label">Product Delivery Information </label>

                         <textarea class="form-control" id="product_delivery_info"
                              name="product_delivery_info" rows="2" cols="3" placeholder="Product Delivery Information">{{old('product_delivery_info')}}</textarea>
                       </div>
                     </div>

                    <div class="col-md-6">
                      <div class="form-group">
                         <label class="form-label">Product Shipping Information </label>
                          <textarea class="form-control" id="product_shipping_info"
                              name="product_shipping_info" rows="2" cols="3" placeholder="Product Shipping Information">{{old('product_shipping_info')}}</textarea>


                     </div>
                 </div>





                   <div class="col-md-6">
                    <div class="form-group">
                           <label class="form-label" >Product Description</label>
                           <textarea class="form-control" id="product_description"
                              name="product_description" rows="2" cols="3" placeholder="Product Description">{{old('product_description')}}</textarea>
                        </div>
                     </div>
                     <div class="col-md-6">

                        <div class="form-group">
                           <label class="form-label">Product Specification </label>
                           <textarea class="form-control" id="product_specification"
                              name="product_specification" rows="2" cols="3" placeholder="Product Specification">{{old('product_specification')}}</textarea>
                        </div>
                     </div>
                  </div>
                <br>
              <div class="row">
                      <div class="col-md-12">
                     <div class="form-group">
                     <center>
                            <button type="submit" class="btn btn-raised btn-info">
                           Submit</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_product') }}">Cancel</a>
                       </center>
                     </div>
                  </div>
                </div>
       </form>
            <script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
            <script>CKEDITOR.replace('product_description');</script>
            <script>CKEDITOR.replace('product_specification');</script>
            <script>CKEDITOR.replace('product_delivery_info');</script>
            <script>CKEDITOR.replace('product_shipping_info');</script>


      </div>
   </div>
</div>
</div>
@endsection

{{-- .varient {
  background-color: #EDEDFD;
  border: 1px  grey;
} --}}

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
          url:"{{ url('admin/product/ajax/get_category') }}?business_type_id="+business_type_id,


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
