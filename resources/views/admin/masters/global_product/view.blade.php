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
                           data-toggle="tab">Product Information</a></li>
                        <li><a href="#tab-61" data-toggle="tab" class="">Images</a></li>
                        <li><a href="#tab-21" data-toggle="tab" class="">Videos</a></li>

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
                                    <td><strong> Category:</strong> {{@$product->product_cat['category_name']}}</td>
                                 </tr>

                                  <tr>
                                    <td><strong> Sub Category:</strong> {{@$product->product_subcat['sub_category_name']}}</td>
                                 </tr>
                                
                                <tr>
                                    <td><strong>Regular Price:</strong> {{ $product->regular_price}}</td>
                                 </tr>
                               <tr>
                                    <td><strong>Sale Price:</strong> {{ $product->sale_price}}</td>
                                </tr>
                                
                                 <tr>
                                    <td><strong>Brand:</strong> {{ $product->product_brand}}</td>
                                </tr>
                                {{-- <tr>
                                     <td><strong>Commision Rate :</strong>{{ $product->product_commision_rate}}</td>
                                </tr> --}}
                                
                                  <tr>
                                    <td><strong>Min. Stock:</strong> {{ $product->min_stock}}</td>
                                 </tr>
                               


                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">

                                    <tr>
                                    <td><strong>Tax:</strong> {{ @$product->tax['tax_name']}} ({{ @$product->tax['tax_value']}})</td>
                                </tr>
                               
                                 <tr>
                                    <td><strong>Vendor:</strong> {{ @$product->vendor['agency_name']}}</td>
                                </tr>

                                 <tr>
                                     <td><strong>Description:</strong> </td><td> {!! @$product->product_description!!}</td>
                                 </tr>
                                 {{-- <tr>
                                    <td><strong>Specification :</strong>{!! $product->product_specification!!}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Delivery Information :</strong> {{ $product->product_delivery_info}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Shipping Information :</strong> {{ $product->product_shipping_info}}</td>
                                 </tr> --}}
                                 <tr>
                                    <td><strong>Image:</strong> <img src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="50" ></td>
                                 </tr>
                                 
                               
                                 
                                 

                              </tbody>
                           </table>


                           <center>
                       <a class="btn btn-cyan" href="{{route('admin.global_products') }}">Cancel</a>
                           </center>
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
                                   <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>

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
                                    @if($product_image->image_name)
                                    <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.@$product_image->image_name)}}"  width="50" ></td>
                                    @endif
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


                  <div class="tab-pane" id="tab-21">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Product Images</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Platform') }}</th>
                                    <th class="wd-15p">{{ __('Video') }}</th>
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$product_images->isEmpty())
                                 @foreach ($product_videos as $value)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $value->platform}}</td>
                                    @php
                                       $vid = $value->video_code;
                                    @endphp
                                    <td  >
                                    <div class="exam"> {!!$value->video_code!!} </div>
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
@endsection