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
                                    <td><strong> Type:</strong> {{@$product->business_type['business_type_name']}}</td>
                                 </tr>

                                 {{-- <tr>
                                    <td><strong>Offer From Date :</strong> {{ $product->product_offer_from_date}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Offer To Date :</strong> {{ $product->product_offer_to_date}}</td>
                                 </tr> --}}
                                <tr>
                                    <td><strong>Regular Price:</strong> {{ @$product->regular_price}}</td>
                                 </tr>
                               <tr>
                                    <td><strong>Sale Price:</strong> {{ @$product->sale_price}}</td>
                                </tr>
                                {{-- <tr>
                                     <td><strong>Commision Rate :</strong>{{ $product->product_commision_rate}}</td>
                                </tr> --}}
                               


                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">

                                    <tr>
                                    <td><strong>Tax:</strong> {{ @$product->tax['tax_name']}} ({{ @$product->tax['tax_value']}})</td>
                                </tr>
                                <tr>
                                    <td><strong>Color:</strong> {{ @$product->color['group_value']}}</td>
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
                        <form action="{{route('store.global_product_add_to_store',$product->global_product_id)}}" method="GET">
                                         @csrf
                       <a class="btn btn-cyan" href="{{route('store.global_products') }}">Cancel</a>
                                      
                                          <button type="submit" onclick="return confirm('Do you want to add this item to store products?');"  class="btn btn-secondary">Add To Store</button>
                                       </form>
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
                                    <td><img data-toggle="modal" data-target="#viewModal{{$product_image->global_product_image_id}}"  src="{{asset('/assets/uploads/products/base_product/base_image/'.@$product_image->image_name)}}"  width="50" ></td>
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
                        <form action="{{route('store.global_product_add_to_store',$product->global_product_id)}}" method="GET">
                                         @csrf
                                          @method('GET')
                       <a class="btn btn-cyan" href="{{route('store.global_products') }}">Cancel</a>
                                      
                                          <button type="submit" onclick="return confirm('Do you want to add this item to store products?');"  class="btn btn-secondary">Add To Store</button>
                                       </form>
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




@foreach($product_images as $product)
<div class="modal fade" id="viewModal{{$product->global_product_image_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
         </div>
         <div class="modal-body">

          <img  src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->image_name)}}"  width="600" >

         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
         </div>
      </div>
   </div>
</div>
@endforeach
@endsection
