@extends('store.layouts.app')
@section('content')
<div class="row" id="user-profile">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <div class="wideget-user">
          <h4>{{$pageTitle}}</h4>
                     <div class="row">
                  <div class="col-lg-6 col-md-12">
                     <div class="wideget-user-desc d-sm-flex">
                        <div class="wideget-user-img">
                           <input type="hidden" class="form-control" name="product_id" value="{{$product->product_id}}" >


                        </div>

                     </div>
                  </div>
               </div>
            </div>
         </div>

         <div class="border-top">
            <div class="wideget-user-tab">
               <div class="tab-menu-heading">
                  <div class="tabs-menu1">
                     <ul class="nav">
                        <li class=""><a href="#tab-51" class="active show"
                           data-toggle="tab">Profile</a></li>
                        <li><a href="#tab-61" data-toggle="tab" class="">Images</a></li>
                        <li><a href="#tab-71" data-toggle="tab" class="">Product Variants</a></li>

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
                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Product Name:</strong> {{ $product->product_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Product Code:</strong> {{ $product->product_code}}</td>
                                 </tr>
                                  <tr>
                                    <td><strong> Category:</strong> {{@$product->categories['category_name']}}</td>
                                 </tr>
                                 
                                  <tr>
                                    <td><strong> Sub Category:</strong> {{@$product->sub_category['sub_category_name']}}</td>
                                 </tr>
                                 
                                 <tr>
                                    <td><strong> Type:</strong> 
                                       
                                       @if(@$product->product_type == 1)
                                       Product
                                       @else
                                       Service
                                       @endif

                                    </td>
                                 </tr>
                                @if(@$product->product_type == 2)
                                   <tr>
                                    <td><strong>Service Type:</strong> 
                                       
                                       @if(@$product->service_type == 1)
                                       Booking Only
                                       @else
                                       Purchase
                                       @endif

                                    </td>
                                 </tr>
                                @endif

                                 
                                 <tr>
                                    <td><strong>Product Brand:</strong> {{ @$product->product_brand}}</td>
                                 </tr>

                                 {{-- <tr>
                                    <td><strong>Offer From Date :</strong> {{ $product->product_offer_from_date}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Offer To Date :</strong> {{ $product->product_offer_to_date}}</td>
                                 </tr> --}}
                               
                                {{-- <tr>
                                     <td><strong>Commision Rate :</strong>{{ $product->product_commision_rate}}</td>
                                </tr> --}}
                                
                                <tr>
                                  <td><strong>Store:</strong> {{ @$product->store['store_name']}}</td>
                                </tr>
                               


                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                  
                                   <tr>
                                    <td><strong>MRP:</strong> {{ $product->product_price}}</td>
                                 </tr>
                               <tr>
                                    <td><strong>Sale Price:</strong> {{ $product->product_price_offer}}</td>
                                </tr>
                                
                                  <tr>
                                    <td><strong>Tax:</strong> {{ @$product->tax['tax_name']}} ({{ @$product->tax['tax_value']}})</td>
                                </tr>

                                 <tr>
                                    <td><strong>Vendor:</strong> {{ @$product->agency['agency_name']}}</td>
                                </tr>

                                 <tr>
                                     <td><strong>Description:</strong> {!! @$product->product_description !!}</td>
                                 </tr>
                               
                                 <tr>
                                    <td><strong>Image:</strong> <img data-toggle="modal" data-target="#viewSingleProduct" src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="50" ></td>
                                 </tr>

                                
                                <tr>
                                     <td><strong>Minimum Stock Count:</strong> {{ @$product->stock_count}}</td>
                                </tr>
                              </tbody>
                           </table>


                          
                        </div>
                        
                        <br>
                        
                             @php
                             $i = 0;
                             $k = 0;
                             $usedAttr = array();
                         @endphp
                     @if(count($product_base_varient_attrs) > 0 )
                        <h4>Attributes</h4>
                     <div class="col-md-12">

                        <div class="row">
                           <div class="table-responsive ">
                           <table id="attrTable"   class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                 <th class="wd-15p">SL.No</th>
                                 <th class="wd-15p">{{ __('Attr Group') }}</th>
                                 <th class="wd-15p">{{ __('Attr Val') }}</th>

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
                                          <td>{{@$attr_grp_name[0]}}</td>
                                          <td>{{@$attr_val_name[0]}}</td>
                                         
                                       </tr>
                                    @endforeach
                              </tbody>
                           </table>
                           </div>
                        </div>
                     </div>
                     @endif
                     
                      <center>
                       <a class="btn btn-cyan" href="{{route('store.list_product') }}">Cancel</a>
                           </center>
                           
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
                                   <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>
                                    <th class="wd-15p">{{ __('Product Variant') }}</th>
                                   <th class="wd-15p">{{ __('Base Image') }}</th>

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
                                    @if($product_image->product_image)
                                    <td><img data-toggle="modal" data-target="#viewModal{{$product_image->product_image_id}}" src="{{asset('/assets/uploads/products/base_product/base_image/'.@$product_image->product_image)}}"  width="50" ></td>
                                    <td>
                                        @if(@$product_image->variant->is_base_variant != 1)
                                         @else
                                        @endif
                                        {{@$product_image->variant->variant_name}}
                                       
                                                                                </td>
                                     <td><input type="checkbox"  @if (@$product_image->image_flag == 1) checked @endif disabled "></td>
                                    @endif
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
                                    <th class="wd-15p">{{ __('Sale Price') }}</th>
                                    <th class="wd-15p">{{ __('Offer Price') }}</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>
                                    <th class="wd-15p">{{ __('Stock Count') }}</th>
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
                                    <td><img  data-toggle="modal" data-target="#viewProductModal{{$value->product_varient_id}}" src="{{asset('/assets/uploads/products/base_product/base_image/'.@$value->product_varient_base_image)}}"  width="50" ></td>
                                    <td>{{$value->stock_count}}</td>

                                    <td>
                                          <a  data-toggle="modal" data-target="#AttrModal{{$value->product_varient_id}}" class="text-white btn btn-sm btn-indigo">Attributes</a>
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


                  </div>

             </div>

</div>
</div>
</div>
</div>


<div class="modal fade" id="viewSingleProduct" tabindex="-1" role="dialog"  aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">

          <img  src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="600" >

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>


@foreach($product_varients as $product)
<div class="modal fade" id="viewProductModal{{$product->product_varient_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">

          <img  src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_varient_base_image)}}"  width="600" >

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
@endforeach


@foreach($product_images as $product)
<div class="modal fade" id="viewModal{{$product->product_image_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">

          <img  src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_image)}}"  width="600" >

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
@endforeach


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
               
    
      </div>
   </div>
</div>
@endforeach

@endsection
