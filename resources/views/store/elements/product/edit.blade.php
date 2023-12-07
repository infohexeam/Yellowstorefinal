@extends('store.layouts.app')
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
                           data-toggle="tab">Product Information</a></li>
                        <li><a href="#tab-61" data-toggle="tab" class="">Images</a></li>
                                                @if($product->service_type != 1)

                        <li><a href="#tab-71" data-toggle="tab" class="">Product Variants</a></li>
                                                @endif

                         <li><a href="#tab-72" data-toggle="tab" class="">Product Videos</a></li>



                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="product_id" id="productId" value="{{$product->product_id}}">
      <div class="card" style="min-height:80vh;">
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
            <div class="alert alert-danger">
              <p>{{ $message1 }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
            @if ($message22 = Session::get('status-empty-field'))
            <div class="alert alert-danger">
              <p>{{ $message22 }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
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
        <form action="{{route('store.update_product',$product->product_id)}}" method="POST" enctype="multipart/form-data">
                  @csrf
            <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Product Name *</label>
                           <input type="text" class="form-control" required
                              name="product_name" value="{{old('product_name',$product->product_name)}}" placeholder="Product Name">
                        </div>
                     </div>
                     @php
                     $taglessBody = strip_tags($product->product_description);
                     @endphp
                      <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" >Product Description *</label>
                            <textarea class="form-control" id="product_description" required
                                name="product_description" rows="4" placeholder="Product Description">{{old('product_description',$taglessBody)}}</textarea>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">MRP *</label>
                            <input type="number" step="0.01" class="form-control" required 
                             name="regular_price"   id="regular_price" value="{{old('regular_price',$product->product_price)}}" placeholder="MRP" oninput="regularPriceChange()">
                             <span style="color:red" id="mrp_priceMsg"> </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Sale Price *</label>
                            <input type="number" step="0.01" class="form-control" required 
                             name="sale_price"  id="sale_price" value="{{old('sale_price',$product->product_price_offer)}}" placeholder="Sale Price" oninput="salePriceChange()">
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
                       <label class="form-label">Product Type *</label>
                        <select onchange="proTypeChanged(this.value)" name="product_type" required id="product_type" class="form-control"  >
                              <option value="1" > Product</option>
                              <option value="2" {{old('product_type',$product->product_type) == '2' ? 'selected':''}} > Service</option>
                           </select>
                     </div>
                  </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Product Code *</label>
                            <input type="text" required class="form-control" oninput="isCodeAvailable(this.value)" name="product_code" id="product_code" value="{{old('product_code',$product->product_code)}}" placeholder="Product Code">
                                <p id="productCodeMsg" style="color:red"></p>

                        </div>
                    </div>
                    <div class="col-md-6" id="minStockDiv">
                        <div class="form-group">
                       
                          <label class="form-label">Min Stock *</label>
                          <input type="text" required onkeypress="preventNonNumericalInput(event)" class="form-control"  name="min_stock" id="min_stock" value="{{old('min_stock',$product->stock_count )}}" placeholder="Min Stock">               
                        </div>
                    </div>
                  <input type="hidden" name="mStock" id="mStock" value="{{$product->stock_count??0}}">
                  
                   <div class="col-md-6">
                     
                      
                          <label class="form-label">Is time slot based product?</label>
                          <div class="form-group">
                          <label class="form-label" for="tsb_product_yes">Yes:</label>
                            <input type="radio" required class="tsb_product" name="timeslot_based_product" @if($product->is_timeslot_based_product=="1")  checked @endif id="tsb_product_yes" onclick="isTSBProduct(this.value)"  value="1" >
                            
                           </div>
                             <div class="form-group">
                               <label class="form-label" for="tsb_product_no">No:</label>
                               @php
                               //dd($product->is_timeslot_based_product);
                               @endphp
                            <input type="radio" required class="tsb_product" @if($product->is_timeslot_based_product=="0" ||$product->is_timeslot_based_product==NULL)  checked @endif	 name="timeslot_based_product" id="tsb_product_no" onclick="isTSBProduct(this.value)"  value="0">
                           
                            </div>
                       
                        </div>
                         <div class="col-md-6">
                         <div id="tslot">
                       
                          <label class="form-label">Time slot</label>
                          <div class="form-group">
                           <label class="form-label">start Time:</label> <input type="time" required class="tsb_product_ts form-control" name="timeslot_start_time" id="tsb_product_st"  onclick="checkTimeSlot()" @if($product->timeslot_start_time) value="{{$product->timeslot_start_time}}" @endif>
                            <span style="color:red" id="st_msg"> </span>
                           </div>
                             <div class="form-group">
                            <label class="form-label">End time:</label> <input type="time" required class="tsb_product_ts form-control" name="timeslot_end_time" id="tsb_product_et" onclick="checkTimeSlot()" @if($product->timeslot_end_time) value="{{$product->timeslot_end_time}}" @endif>
                             <span style="color:red" id="et_msg"> </span>
                            </div>
                            </div>
                        </div>
               

                  <div id="service_type_id" class="col-md-12">
                     <div class="form-group">
                       <label class="form-label">Service Type *</label>
                        <select id="service_type_input" name="service_type"   onchange="servTypeChanged(this.value)"  class="form-control"  >
                         <option value="" > Service Type </option>
                         <option value="1" {{old('service_type',$product->service_type) == '1' ? 'selected':''}} > Booking Only</option>
                         <option value="2" {{old('service_type',$product->service_type) == '2' ? 'selected':''}} > Purchase</option>
                         </select>
                     </div>
                  </div> 
                       
                  

                  
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
                            <label class="form-label" >Product Subcategory  </label>
                            <select name="sub_category_id"  id="sub_category_id" class="form-control" required  >
                                 <option value="">Product Subcategory</option>
                                 @foreach ($subcategories as $key)
                                 <option {{old('sub_category_id',$product->sub_category_id) == $key->sub_category_id ? 'selected':''}} value="{{ @$key->sub_category_id }}">{{ @$key->sub_category_name }}</option>
                                 @endforeach
                               
                                  <option value="0"  @if($product->sub_category_id==0) selected @endif>Others</option>

                               
  
                            </select>
                        </div>
                     </div>
                         @php
                             $i = 0;
                             $k = 0;
                             $usedAttr = array();
                         @endphp
                     @if(count($product_base_varient_attrs) > 0 )

                     <div class="col-md-12">

                        <div class="row">
                           <div class="table-responsive ">
                           <table id="attrTable"   class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                 <th class="wd-15p">SL.No</th>
                                 <th class="wd-15p">{{ __('Attr Group') }}</th>
                                 <th class="wd-15p">{{ __('Attr Val') }}</th>

                                    <th  class="wd-20p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody class="col-lg-12 col-xl-12 p-1">
                                
                                    @foreach ($product_base_varient_attrs as $val)
                                    @php
                                    $k++;
                                    @endphp
                                    @endforeach
                              </tbody>

                                    @foreach ($product_base_varient_attrs as $val)
                                       @php
                                       $i++;
                                       $attr_grp_name = \DB::table('mst_attribute_groups')->where('attr_group_id',$val->attr_group_id)->pluck('group_name');
                                       $attr_val_name = \DB::table('mst_attribute_values')->where('attr_value_id',$val->attr_value_id)->pluck('group_value');
                                      $usedAttr[] = $val->attr_group_id;
                                      @endphp
                                       <tr id="trId{{$val->variant_attribute_id}}">
                                          <td>{{$i}}</td>
                                          <td>{{@$attr_grp_name[0]}}
                                              <input type="hidden" class="attrGroup500" value="{{$val->attr_group_id}}" />
                                          </td>
                                          <td>{{@$attr_val_name[0]}}</td>
                                          <td>
                                             <!--<form id="removeVarAttr" action="{{route('store.destroy_product_var_attr',$val->variant_attribute_id)}}" method="POST">-->
                                                @csrf
                                                @method('POST')
                                                <a type="submit" onclick="removeTableRowValidate({{$val->product_varient_id}},{{$val->variant_attribute_id}})"  
                                                    class="btn btn-sm btn-danger text-white">Delete</a>
                                                <!--@if(($i == 1) && ($k == 1))-->
                                                <!--@else-->
                                                <!--    onclick="removeTableRowTr({{$val->variant_attribute_id}})"  -->
                                                <!--@endif-->
                                                
                                             <!--</form>-->
                                          </td>
                                       </tr>
                                    @endforeach
                              </tbody>
                           </table>
                           </div>
                        </div>
                     </div>
                     @endif


                     <div id="attHalfRow500a" class="container"> 
                        <div  id="attHalfSec500a" class="section">
                          <div  class=" row">

                              <div class="col-md-6">
                                <div class="form-group">
                                  <label class="form-label">Attribute </label>
                                  <select name="attr_group_id[500][]" onchange="findValue('500a0')"  id="attr_group500a0" class="attr_group form-control attrGroup500 " >
                                    <option value="">Attribute</option>
                                    @foreach($attr_groups as $key)
                                       @if(!in_array($key->attr_group_id, $usedAttr))
                                          <option value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                       @endif
                                    @endforeach
                                  </select>
                                </div> 
                              </div>
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label class="form-label">Value </label>
                                      <select name="attr_value_id[500][]"   id="attr_value500a0" class="attr_value form-control " >
                                        <option value="">Value</option>
                                      </select>
                                  </div>
                              </div>
                          </div>
                          
                        </div>

                        <div class="col-md-2">
                          <div class="form-group">
                              <a  id="addVariantAttr500" onclick="addAttributes('500a',500)" class="text-white mt-2 btn btn-sm btn-secondary">Add More</a>
                          </div>
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
                     <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Product Brand </label>
                            <input type="text"  class="form-control" name="product_brand" id="product_brand" value="{{old('product_brand',$product->product_brand)}}" placeholder="Product Brand">
                        </div>
                    </div>
                     <div class="col-md-6">
                      <div class="form-group">
                   <label class="form-group custom-switch">
                   Is it just a product listing?
                         
														<input type="checkbox" name="is_product_listed" @if($product->is_product_listed_by_product==1) checked   @endif class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description"></span>
													</label>
                          </div>
                          </div>
   

                      <div class="col-md-12">
                   <div class="form-group">
                    <div class="BaseFeatureArea">
                     <label class="form-label">Upload Images *</label>
                     <input type="file" accept="image/png, image/jpeg, image/jpg" class="form-control imgValidation" name="product_image[]">
                        <br>
                     </div>
                     </div>
                 </div>
            </div>

            <section style="display:none;" id="varClass"> 

            <div id="attSec" class="container"> 
               <p class="h5 ml-2">Add Product Variants </p>
               <div  id="attRow" class="section">


                 <div style="border: 1px solid #0008ff42;"  class="mt-2 row">

                   <div class="col-md-12">
                     <div class="form-group">
                         <label class="form-label">Variant Name </label>
                         <input  type="text" class="form-control proVariant"  name="variant_name[]"  oninput="variantNameFilled(this.value,0)"  id="variant_name0" placeholder="Variant Name">
                     </div>
                 </div>

                   <div id="attHalfRow0a" class="container"> 
                     <div  id="attHalfSec0a" class="section">
                       <div  class=" row">

                           <div class="col-md-6">
                             <div class="form-group">
                               <label class="form-label">Attribute  </label>
                               <select  name="attr_group_id[0][]" onchange="findValue('0a0')"  id="attr_group0a0" class="attr_group form-control proVariant attrGroup0" >
                                 <option value="">Attribute</option>
                                 @foreach($attr_groups as $key)
                                 {{-- @if(!in_array($key->attr_group_id, $usedAttr)) --}}
                                 <option value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                 {{-- @endif --}}
                                       @endforeach
                               </select>
                             </div>
                           </div>
                           <div class="col-md-6">
                               <div class="form-group">
                                   <label class="form-label">Value  </label>
                                   <select  name="attr_value_id[0][]"   id="attr_value0a0" class="attr_value form-control proVariant" >
                                     <option value="">Value</option>
                                   </select>
                               </div>
                           </div>
                       </div>
                       
                     </div>

                     <div class="col-md-2">
                       <div class="form-group">
                           <a  id="addVariantAttr0" onclick="addAttributes('0a',0)" class="text-white mt-2 btn btn-sm btn-secondary ">Add More</a>
                       </div>
                     </div>
                   </div>



                   <div class="col-md-6">
                     <div class="form-group">
                         <label class="form-label">MRP </label>
                         <input step="0.01"  type="number" class="form-control proVariant"  oninput="regularPriceChangeVar(0)"
                         name="var_regular_price[]"   id="var_regular_price0" value="" placeholder="MRP">
                     </div>
                 </div>
                   <div class="col-md-6">
                       <div class="form-group">
                           <label class="form-label">Sale Price </label>
                           <input step="0.01" type="number" class="form-control proVariant"  oninput="salePriceChangeVar(0)"
                           name="var_sale_price[]"   id="var_sale_price0" value="" placeholder="Sale Price">
                                <span style="color:red" id="sale_priceMsg0"> </span>

                       </div>
                   </div>
                   <input type="hidden" id="cval0" value="0">

                   <div class="col-md-12">
                     <div class="form-group">
                         <label class="form-label">Images </label>
                         <input type="file"  accept="image/png, image/jpeg, image/jpg" class="form-control proVariant imgValidation" name="var_images[0][]" >
                     </div>
                 </div>
              </div>

              
            </div>
            <div class="col-md-2">
             <div class="form-group">
                 <a  id="addVariant" class="text-white mt-2 btn btn-raised btn-success">Add More</a>
             </div>
             </div>
           </div>
            </section>

            <a class="btn btn-primary btn-raised text-white mt-2" id="btnAddVar" onclick="showVariant()"> Add Variant </a>

            <div class="row">
                      <div class="col-md-12">
                     <div class="form-group">
                     <center>
                            <button  type="submit" id="submit" class="btn btn-raised btn-info">
                           Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('store.list_product') }}">Cancel</a>
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

                         <form action="{{route('store.update_product_images',$product->product_id)}}" method="POST" enctype="multipart/form-data">
                                 @csrf
                           <div class="row">

                                 <div class="col-md-12">
                                       <div class="form-group">
                                          <label class="form-label">Product Name</label>
                                             <select name="product_varient_id"   class=" form-control  " >
                                                <option value="{{ @$product_base_varient->product_varient_id }}">{{ @$product_base_varient->variant_name }}</option>
                                                @foreach($product_varients as $key)
                                                <option value="{{$key->product_varient_id}}"> {{$key->variant_name}} </option>
                                                      @endforeach
                                             </select>
                                           </div>
                                    </div>

                              <div class="col-md-12">
                                    <div class="form-group">
                                       <label class="form-label">Images(1000*800 or more) *</label>
                                       <input type="file" accept="image/png, image/jpeg, image/jpg"  class="form-control proVariant imgValidation" name="var_image[]" >
                                    </div>
                              </div>

                              <div class="col-md-12">
                                 <div class="form-group">
                                    <center>
                                          <button  type="submit" id="submit" class="btn btn-raised btn-info">Update</button>
                                    </center>
                                 </div>
                              </div>

                                 
                           </div>
                         </form>

                        <div class="table-responsive ">
                           <table   class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">SL.No</th>
                                   <th class="wd-15p">{{ __('Image') }}</th>
                                   <th class="wd-15p">{{ __('Variant') }}</th>
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
                                 
                                 
                                   @if($product_image->product_varient_id != 0)
                                    @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.$product_image->product_image)}}"  width="50" ></td>
                                    <td>
                                         @if(@$product_image->variant->is_base_variant != 1)
                                         @else
                                        @endif
                                        {{@$product_image->variant->variant_name}}
                                       
                                        
                                    </td>
                                    <td>
                                        @if($product_image->image_flag != 1)
                                          <a href="{{ url('admin/change-img-status/'.$product_image->product_image_id) }}"  onclick="return confirm('Do you want to change status?');" class="btn btn-sm
                                          @if($product_image->image_flag != 1) btn-danger @else btn-success @endif "   > @if($product_image->image_flag != 1)
                                            Not Default
                                          @else
                                            Base Image
                                          @endif</a>
                                          
                                          @else
                                             <a href="#"  class="btn btn-sm btn-success "   > Base Image   </a>
                                          @endif
                                   
                                    </td>

                                    <td>
                                        <form action="{{route('store.destroy_product_image',$product_image->product_image_id)}}" method="POST">
                                            @csrf
                                            @method('POST')

                                            <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                 </tr>
                                 
                                  @endif

                                 @endforeach
                                 @else
                                 <tr>
                                <td colspan="3"><center> No data available in the table</center></td>
                                  </tr>
                                  @endif
                              </tbody>
                           </table>
                           <center>
                           <a class="btn btn-cyan" href="{{ route('store.list_product') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
      



                  <div class="tab-pane" id="tab-71">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Product Variants</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                 <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Variant Name') }}</th>
                                    <th class="wd-15p">{{ __('MRP') }}</th>
                                    <th class="wd-15p">{{ __('Sale Price') }}</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>
                                    <th class="wd-15p">{{ __('Stock Count') }}</th>
                                    <th class="wd-15p">{{ __('Status') }}</th>
                                    <th  class="wd-20p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                              @if(!$product_varients->isEmpty())
                                 @foreach ($product_varients as $value)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$value->variant_name}}</td>
                                    <td>{{$value->product_varient_price}}</td>
                                    <td>{{$value->product_varient_offer_price}}</td>
                                    <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.$value->product_varient_base_image)}}"  width="50" ></td>
                                    <td>{{$value->stock_count}}</td>
                                    <td>
                                    @php

                                     $productStatus = $value->variant_status;
                                    @endphp
                                     <form action="{{route('store.status_product_variant',$value->product_varient_id)}}" method="POST">      
                                       @csrf
                                       @method('POST')
                                       <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                       @if($productStatus == 0) btn-danger @else btn-success @endif"> @if($productStatus == 0)
                                       Inactive
                                       @else
                                       Active
                                       @endif</button>
                                    </form>
                                    </td>

                                    <td>
                                       <form action="{{route('store.destroy_product_variant',$value->product_varient_id)}}" method="POST">
                                          @csrf
                                          @method('POST')
                                          <a  data-toggle="modal" data-target="#AttrModal{{$value->product_varient_id}}" class="text-white btn btn-sm btn-indigo">Attributes</a>

                                          <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                       </form>
                                       <a  data-toggle="modal" data-target="#AddAttrModal{{$value->product_varient_id}}" class="mt-2  text-white btn btn-sm btn-yellow">Add Attributes</a>

                                    </td>
                                 </tr>
                                 @endforeach
                                 @else
                                 <tr>
                              <td colspan="6"><center> No data available in the table</center></td>
                                 </tr>
                                 @endif
                              </tbody>
                           </table>
                           <center>
                           <a class="btn btn-cyan" href="{{ route('store.list_product') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
                  
                  
                     <div class="tab-pane" id="tab-72">

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
                                   
                                    <td  >
                                    <div class="exam"> {!!$value->link!!} </div>
                                    </td>
                                     

                                    <td>
                                       <form action="{{route('store.destroy_product_video',$value->product_video_id)}}" method="POST">
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
                           <a class="btn btn-cyan" href="{{ route('store.list_product') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>
                  
                  
            </div>


</div>
</div>
</div>



@foreach($product_varients as $value)
<div class="modal fade" id="AttrModal{{$value->product_varient_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="example-Modal3">Variant Attributes</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         @php
            $var_atts = \DB::table('trn__product_variant_attributes')
            ->where('product_varient_id',$value->product_varient_id)
            ->orderBy('variant_attribute_id','DESC')
            ->get();
         @endphp

         <div class="modal-body">
            <table class="table table-striped table-bordered">
               <thead>
                  <tr>
                  <th class="wd-15p">SL.No</th>
                     <th class="wd-15p">{{ __('Group Name') }}</th>
                     <th class="wd-15p">{{ __('Value Name') }}</th>
                     <th  class="wd-20p">{{__('Action')}}</th>
                  </tr>
               </thead>
               <tbody class="col-lg-12 col-xl-6 p-0">
                  @php
                  $i = 0;
                  @endphp
                  @if(!$var_atts->isEmpty())
                  @foreach ($var_atts as $val)
                     @php
                     $i++;
                     $attr_grp_name = \DB::table('mst_attribute_groups')->where('attr_group_id',$val->attr_group_id)->pluck('group_name');
                     $attr_val_name = \DB::table('mst_attribute_values')->where('attr_value_id',$val->attr_value_id)->pluck('group_value');
                     @endphp
                     <tr>
                        <td>{{$i}}</td>
                        <td>{{@$attr_grp_name[0]}}</td>
                        <td>{{@$attr_val_name[0]}}</td>
                        <td>
                           <form action="{{route('store.destroy_product_var_attr',$val->variant_attribute_id)}}" method="POST">
                              @csrf
                              @method('POST')
                              <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                           </form>
                        </td>
                     </tr>
                  @endforeach
                  @else
                  <tr>
               <td colspan="6"><center> No data available in the table</center></td>
                  </tr>
                  @endif
               </tbody>
            </table>
         </div>
               
    
      </div>
   </div>
</div>
@endforeach



@foreach($product_varients as $value)
<div class="modal fade" id="AddAttrModal{{$value->product_varient_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="example-Modal3">Add Attributes</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         @php
            $var_atts = \DB::table('trn__product_variant_attributes')
            ->where('product_varient_id',$value->product_varient_id)
            ->orderBy('variant_attribute_id','DESC')
            ->get();
         @endphp

         <div class="modal-body">
          <table class="table table-striped table-bordered">
               <thead>
                  <tr>
                  <th class="wd-15p">SL.No</th>
                     <th class="wd-15p">{{ __('Group Name') }}</th>
                     <th class="wd-15p">{{ __('Value Name') }}</th>
                  </tr>
               </thead>
               <tbody class="col-lg-12 col-xl-6 p-0">
                  @php
                  $i = 0;
                  @endphp
                  @if(!$var_atts->isEmpty())
                  @foreach ($var_atts as $val)
                     @php
                     $i++;
                     $attr_grp_name = \DB::table('mst_attribute_groups')->where('attr_group_id',$val->attr_group_id)->pluck('group_name');
                     $attr_val_name = \DB::table('mst_attribute_values')->where('attr_value_id',$val->attr_value_id)->pluck('group_value');
                     @endphp
                     <tr>
                        <td>{{$i}}</td>
                        <td>{{@$attr_grp_name[0]}}</td>
                        <td>{{@$attr_val_name[0]}}</td>
                       
                     </tr>
                  @endforeach
                  @else
                  <tr>
               <td colspan="6"><center> No data available in the table</center></td>
                  </tr>
                  @endif
               </tbody>
            </table>
         </div>

            <form action="{{ route('store.add_attr_to_variant') }} " method="POST" enctype="multipart/form-data" >
               @csrf
                <div class="modal-body">
                   <input type="hidden" name="product_varient_id" value="{{$value->product_varient_id}}">

                        <div  class=" row">
                              <div class="col-md-6">
                              <div class="form-group">
                                 <label class="form-label">Attribute </label>
                                 <select required name="attr_grp_id"   class="attr_groupz form-control" >
                                    <option value="">Attribute</option>
                                    @foreach($attr_groups as $key)
                                       {{-- @if(!in_array($key->attr_group_id, $usedAttr)) --}}
                                       <option value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                       {{-- @endif      --}}
                                    @endforeach
                                 </select>
                              </div>
                              </div>
                              <div class="col-md-6">
                                 <div class="form-group">
                                    <label class="form-label">Value </label>
                                    <select required name="attr_val_id" class="attr_valuez form-control" >
                                       <option value="">Value</option>
                                    </select>
                                 </div>
                              </div>
                        </div>
               
                     
                </div>
                   <div class="modal-footer">
                     <button type="submit" class="btn btn-raised btn-primary">
                  <i class="fa fa-check-square-o"></i> Add</button>
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                   </div>
            </form>
           
               
      </div>
   </div>
</div>
@endforeach

<script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>




    <script>


        
function preventNonNumericalInput(e) {
  e = e || window.event;
  var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
  var charStr = String.fromCharCode(charCode);

  if (!charStr.match(/^[0-9]+$/))
    e.preventDefault();
}
    </script>


<script>
   
   
   
function isCodeAvailable(value)
{
            var _token= $('input[name="_token"]').val();
            let product_id = $("#productId").val();
        $.ajax({
          type:"GET",
          url:"{{ url('product/ajax/is-code-available') }}?product_code="+value+"&product_id="+product_id,


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
   
    </script>
<script>//CKEDITOR.replace('product_specification');</script>
<script>//CKEDITOR.replace('product_delivery_info');</script>
<script>//CKEDITOR.replace('product_shipping_info');</script>




<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>



<script>
function removeTableRowTr(varAttrId){
// trId
     var _token= $('input[name="_token"]').val();

  $.ajax({
            type:"GET",
            url:"{{ url('ajax/product-variant/attr-remove')}}?variant_attribute_id="+varAttrId,
            success:function(res){
                if(res){
                    if(res == '1'){
                                             console.log(varAttrId,res);
                        $("#trId"+varAttrId).remove();
                    }
                    else{
                        alert('Faied');
                        
                    }
                }else
                {
                    alert("error");
                }
        }
        });
        
    
}
function removeTableRowValidate(varId,varAttrId){

    console.log(varId,varAttrId);


            
            
     var _token= $('input[name="_token"]').val();
        $.ajax({
            type:"GET",
            url:"{{ url('ajax/product-variant/attr-count')}}?product_varient_id="+varId,
            success:function(res){
                if(res){
                   //  console.log(res);
                    if(res == '1'){
                       
                        alert('Only one attribute avilable! Remove all other product variants to delete this attribute');
                        
                    }
                    else{
                        removeTableRowTr(varAttrId);

                    }
                }else
                {
                    alert("error");
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
    
    if(parseFloat(regularPrice) <= 0)
    {
           
            $('#mrp_priceMsg').html('MRP should be greater than 0');
             $('#regular_price').val('');
              $("#submit").attr("disabled", true);
             
    }
    else
    {
       $("#submit").attr("disabled", false);
        $('#mrp_priceMsg').html('');
       

    }
     if(parseFloat(salePrice) <= 0)
    {
      //alert('Sale price should be greater than 0');
            
          $('#sale_priceMsg').html('Sale price should be greater than 0');
          $('#sale_price').val('');
                        
    }
    else
    {
       $("#submit").attr("disabled", false);
       $('#sale_priceMsg').html('');


    }
    
    
    if(salePrice !== "")
    {
        if(regularPrice !== "")
        {
           // console.log(regularPrice + " " + salePrice);
            if( parseFloat(salePrice) <= parseFloat(regularPrice))
            {
                $('#sale_priceMsg').html('');
                $("#submit").attr("disabled", false);
                  if(parseFloat(salePrice) <= 0)
                {
                  //alert('Sale price should be greater than 0');
                        
                      $('#sale_priceMsg').html('Sale price should be greater than 0');
                      $('#sale_price').val('');
                        
                          
                }

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


function regularPriceChangeVar(p){
    salePriceChangeVar(p);
}

function salePriceChangeVar(p)
{
    let salePrice = $('#var_sale_price'+p).val();
    let regularPrice = $('#var_regular_price'+p).val();
    
    if(parseFloat(salePrice) < 0)
    {
            $('#var_sale_price'+p).val(0);
    }
    
    if(parseFloat(regularPrice) < 0)
    {
            $('#var_regular_price'+p).val(0);
    }
    
    
    if(salePrice !== "")
    {
        if(regularPrice !== "")
        {
            if( parseFloat(salePrice) <= parseFloat(regularPrice))
            {
                $('#sale_priceMsg'+p).html('');
                $("#submit").attr("disabled", false);

            }
            else
            {
                $('#sale_priceMsg'+p).html('Sale price should be less than or equal to MRP');
                $("#submit").attr("disabled", true);
            }
        }
        else
        {
             $('#sale_priceMsg'+p).html('MRP is empty');
             $("#submit").attr("disabled", true);

        }
    }
    else
    {
        $('#sale_priceMsg'+p).html('');
        $("#submit").attr("disabled", false);

    }
}



function changeBaseImage(id,varId)
{
    console.log(id+' - '+varId);
            if($('#image_flag'+id).prop('checked'))
            {

                     $('.csatatus'+varId).prop('checked', false);
                     $('#image_flag'+id).prop('checked', true);

                    var _token= $('input[name="_token"]').val();
                    $.ajax({
                        type:"GET",
                        url:"{{ url('store/ajax/product/set_default_image')}}?product_image_id="+id+"&product_varient_id="+varId,
                        success:function(res){
                            if(res){
                            }else
                            {
                                alert("error");
                            }
                    }
                    });
             }
             else
             {
                console.log('someting wen\'t wrong! ');
             }

         }

         $(document).ready(function() {
              tbv=$('input[name="timeslot_based_product"]:checked').val();
               //alert(tbv);
               if(tbv==0)
               {
                  $('#tslot').hide();
                  $(".tsb_product_ts").attr("required", false);
               }
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
          url:"{{ url('store/product/ajax/get_subcategory') }}?category_id="+category_id,


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

function isTSBProduct(v)
    {
        if(v==0)
        {
          $('#tslot').hide();
          $(".tsb_product_ts").attr("required", false);
          $("#submit").prop("disabled", false);
        }
         if(v==1)
        {
          $('#tslot').show();
           $('#tsb_product_st').val("00:00");
           $('#tsb_product_et').val("00:00");
          $(".tsb_product_ts").attr("required", true);
          $('#et_msg').html('');
        }

    }

function showVariant(){

   if($("#varClass").is(":visible"))
   {
         $("#varClass").hide();
         $("#btnAddVar").text('Add Variant');
   }
   else
   {
      let firstAttrGrp = $('#attr_group500a0').val();
      let firstAttrVal = $('#attr_value500a0').val();
      var rowCount = $('#attrTable tr').length;
      if(((firstAttrGrp != '' ) && (firstAttrVal != '')) || (rowCount > 0)){
         $("#varClass").show();
         $("#btnAddVar").text('Hide Variant');
      }else{
         alert("Base attribute and value can't be empty! ");
      }
        
   }

}

</script>




<script>


function variantNameFilled(nameValue, h){
    if ($('#variant_name0').is(':empty')) {
        $("#attr_group0a0").prop('required',false);
        $("#attr_value0a0").prop('required',false);

    }else{
        $("#attr_group0a0").prop('required',true);
        $("#attr_value0a0").prop('required',true);
    }
}



     var xx = 1; 

function addAttributes(att_id_val,mainKey){
    
// alert(mainKey);

var ek = $('.attrGroup'+mainKey).map((_,el) => el.value).get()

// console.log(ek,att_id_val)
    var wrapper      = $("#attHalfSec"+att_id_val); 
    var add_button      = $("#addVariantAttr"+att_id_val); 
   // alert(wrapper);
         var attid = att_id_val + xx;
         var attid_2 = att_id_val + (xx -1);
    
// console.log('#attr_group'+attid_2);
//console.log(att_id_val,mainKey,xx,attid,$('#attr_group'+attid_2).val());
//console.log(mainKey+"a"+xx);
let prAttrValue = $('#attr_value'+attid_2).val();

  let prevAttrVal = $('#attr_group'+attid_2).val();

  var rowCount = $('#attrTable tr').length;
         

  if((prevAttrVal != "" && prAttrValue != '') || (rowCount > 0)){
      
 
           $(".attrGroup"+mainKey).attr('readonly',true);
//   $(".attrGroup"+mainKey).prop('disabled', true);
      // $("#attr_group"+att_id_val+(xx - 1)).prop('disabled', true);

          var id_number = parseInt(att_id_val. replace(/[^0-9. ]/g, ""));
        
        $(wrapper).append('<div class="row  attrRow'+mainKey+'"><div class="col-md-12"><div class="row"><div class="col-md-6"><div class="form-group"><label class="form-label">Attribute *</label><select required name="attr_group_id['+id_number+'][]" onchange="findValue(\''+attid+'\')"id="attr_group'+attid+'" class="attr_group attrGroup'+id_number+' form-control" ><option value="">Attribute</option>@foreach($attr_groups as $key)<option  value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>@endforeach</select></div></div><div class="col-md-6"><div class="form-group"><label class="form-label">Value *</label><select required name="attr_value_id['+id_number+'][]"   id="attr_value'+attid+'" class="attr_value form-control" ><option value="">Value</option></select></div></div><a  onclick="removeAttrRow('+mainKey+','+xx+')" class="remove_field ml-5 btn btn-info btn btn-sm">Remove</a></div></div></div>'); //add input box
         ++xx;

    for(let i=0;i< ek.length; i++){
      $(".attrGroup"+mainKey+" option[value="+ek[i]+"]").hide();
      
            console.log("attrGroup"+mainKey);

    }
  }else{
      alert("Previous attribute empty");
  }
    
    /*$(wrapper).on("click",".remove_field", function(e){ //user click on remove text
      e.preventDefault(); $(this).parent('div').remove(); 
    });*/

}

function removeAttrRow(x,y){
  
     
    $(".attrRow"+x).remove();

   /* console.log(x,y);
     on("click",".remove_field", function(e){ //user click on remove text
      e.preventDefault(); $(this).parent('div').remove(); 
    });*/
  //   console.log(".attrGroup"+x+"a"+(y-1));
            //$("#attr_group"+x+"a"+(y-1)).attr('readonly', false);
            //$("#attr_group"+x+"a"+(y-1)).parent('div').remove();

}

$(document).ready(function() {
   var wrapper      = $("#attRow"); //Fields wrapper
   var add_button      = $("#addVariant"); //Add button ID
   var x = 0; //initlal text box count
   $(add_button).click(function(e){ //on add input button click
     e.preventDefault();
     //max input box allowed
       x++; //text box increment
       var attr_id_div = x+'a0';
       $(wrapper).append('<div style="border: 1px solid #0008ff42;" class="mt-2 row"><div class="col-md-12"><div class="form-group"><label class="form-label">Variant Name </label><input  type="text" class="form-control"  name="variant_name[]"   id="variant_name'+x+'" placeholder="Variant Name"></div></div><div id="attHalfRow'+x+'a" class="container"> <div  id="attHalfSec'+x+'a" class="section"><div  class=" row"><div class="col-md-6"><div class="form-group"><label class="form-label">Attribute * </label><select required name="attr_group_id['+x+'][]" onchange="findValue(\''+attr_id_div+'\')"  id="attr_group'+attr_id_div+'" class="attr_group form-control" ><option value="">Attribute</option>@foreach($attr_groups as $key)  <option value="{{$key->attr_group_id}}"> {{$key->group_name}} </option> @endforeach</select></div></div><div class="col-md-6"><div class="form-group"><label class="form-label">Value *</label><select required name="attr_value_id['+x+'][]"   id="attr_value'+attr_id_div+'" class="attr_value form-control" ><option value="">Value</option></select></div></div></div></div><div class="col-md-2"><div class="form-group"><a  id="addVariantAttr'+x+'" onclick="addAttributes(\''+x+'a\','+x+')" class="text-white mt-2 btn btn-sm btn-secondary">Add More</a></div></div></div> <div class="col-md-6"><div class="form-group"><label class="form-label">MRP </label><input step="0.01" type="number" required class="form-control"   name="var_regular_price[]" id="var_regular_price'+x+'" oninput="regularPriceChangeVar('+x+')"  placeholder="MRP"></div></div><div class="col-md-6"><div class="form-group"><label class="form-label">Sale Price </label><input step="0.01" type="number" class="form-control"  name="var_sale_price[]" id="var_sale_price'+x+'" oninput="salePriceChangeVar('+x+')" placeholder="Sale Price"><span style="color:red" id="sale_priceMsg'+x+'"> </span></div></div><input type="hidden" id="cval'+x+'" value="'+x+'"><div class="col-md-12"><div class="form-group"><label class="form-label">Images </label><input  type="file" class="form-control imgValidation" accept="image/png, image/jpeg, image/jpg" name="var_images['+x+'][]" ></div></div><a href="#" class="remove_field ml-4 mb-2 btn btn-warning btn btn-sm">Remove</a></div>'); //add input box
   });
   
   $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
     e.preventDefault(); $(this).parent('div').remove(); x--;
   })
});




$(document).ready(function () {

    
   var product_type = $('#product_type').val();


   if(product_type == 2){
      $("div#service_type_id").hide();
      $("div#minStockDiv").hide();
      $("#min_stock").prop('required',false); 
      $("#min_stock").val(0); 
      var ser_type = $('#service_type_input').val();
      //alert(ser_type);
      if(ser_type==2)
      {
         //alert(1);
      $("div#minStockDiv").show();
      $("#min_stock").prop('required',true); 

      }
      else
      {
         
      }
   }
   else{
      $("div#service_type_id").hide();
      ("div#minStockDiv").show();
      $("#min_stock").prop('required',true); 
   }
   
});

function proTypeChanged(val)
{
  if(val == 2){
    $("div#service_type_id").show();
   // $(".proVariant").prop('required',false);
   $("div#minStockDiv").hide();
    $("#min_stock").prop('required',false); 
    $("#min_stock").val(0); 
    $("#service_type_input").prop('required',true);
    $("div#attSec").hide();
    //$('#service_type_input').prop('selectedIndex',0);
    var ser_type = $('#service_type_input').val();
      //alert(ser_type);
      if(ser_type==2)
      {
         //alert(1);
      $("div#minStockDiv").show();
      $("#min_stock").prop('required',true); 
       var mStockValue=$('#mStock').val();
    $("#min_stock").val(mStockValue); 

      }
      else
      {
         
      }


  }
  else{
  //  $(".proVariant").prop('required',true);
    $("#service_type_input").prop('required',false);
     $("div#minStockDiv").show();
     $("#min_stock").prop('required',true); 
     var mStockValue=$('#mStock').val();
    $("#min_stock").val(mStockValue); 
    $("div#service_type_id").hide();
    $("div#attSec").show();
        $('#service_type_input').prop('selectedIndex',0);

 }
}
var vc = 1;
function servTypeChanged(v){
  if(vc != 1){
    if(v == 1){
      $("div#attSec").hide();

      $(".proVariant").prop('required',false);
       $("div#minStockDiv").hide();
       $("#min_stock").prop('required',false); 
       $("#min_stock").val(0); 
   

    }
    else{
      $("div#attSec").show();
      $(".proVariant").prop('required',false);
        $("div#minStockDiv").show();
     $("#min_stock").prop('required',true); 
       var mStockValue=$('#mStock').val();
    $("#min_stock").val(mStockValue); 

    }
  }
  vc++;
}


// $(document).ready(function () {
//         $('#variant_name0').on('input', function() {
//             let attVal = $("#variant_name0").val();
//             if(attVal == '')
//             {
//                 $(".proVariant").prop('required',false); 
//             }
//             else
//             {
//                 $(".proVariant").prop('required',true); 
//             }
//         });
//     });


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
      $(wrapper).append('<div>  <input type="file" class="form-control imgValidation" name="product_image[]"  accept="image/png, image/jpeg, image/jpg"  value="{{old('product_image')}}" placeholder="Base Product Feature Image" /> <a href="#" class="remove_field btn btn-primary">Remove</a></div>'); //add input box

  });

  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});


//$(document).ready(function() {

   function findValue(c){

//$('#attr_group'+c).change(function(){
  // alert(c);
   var attr_group_id = $('#attr_group'+c).val();

   var _token= $('input[name="_token"]').val();

   $.ajax({
     type:"GET",
     url:"{{ url('store/product/ajax/get_attr_value') }}?attr_group_id="+attr_group_id,

     success:function(res){
       //alert(data);
       if(res){
        $('#attr_value'+c).prop("diabled",false);
        $('#attr_value'+c).empty();
        $('#attr_value'+c).append('<option value="">Value</option>');
        $.each(res,function(attr_value_id,group_value)
        {
          $('#attr_value'+c).append('<option value="'+attr_value_id+'">'+group_value+'</option>');
        });

        }else
        {
          $('#attr_value'+c).empty();

        }
       }

   });
  
// });


}



//});


 function checkTimeSlot()
  {
    var time1 = $('#tsb_product_st').val();
   
var time2 = $('#tsb_product_et').val();
//alert(time2);

var date1 = new Date("01/01/2023 " + time1);
var date2 = new Date("01/01/2023 " + time2);

 if (date1 >= date2) {
    //alert(time1 + " is later than " + time2);
     $('#et_msg').html('starting time cannot be greater than endtime');
     $("#submit").prop("disabled", true);
}
else
{
  $("#submit").prop("disabled", false);
  $('#et_msg').html('');
   

}

   



  }



 $(document).ready(function() {
     var ac = 0;

       $('.attr_groupz').change(function(){
if(ac != 0)
{
       // alert("hi");
       // alert("dd");
        var attr_group_id = $(this).val();

        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('store/product/ajax/get_attr_value') }}?attr_group_id="+attr_group_id,


          success:function(res){
            //alert(data);
            if(res){
            $('.attr_valuez').prop("diabled",false);
            $('.attr_valuez').empty();
            $('.attr_valuez').append('<option value="">Value</option>');
            $.each(res,function(attr_value_id,group_value)
            {
              $('.attr_valuez').append('<option value="'+attr_value_id+'">'+group_value+'</option>');
            });

            }else
            {
              $('.attr_valuez').empty();

            }
            }

        });
}
else
{
ac = ac + 1;
}
      });

    });

    $(document).ready(function() {
        var btc = 0;
       $('#business_type').change(function(){
       // alert("dd");

       if(btc != 0)
       {
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
       }
       else
       {
           btc++;
       }
      });

    });

</script>
@endsection
