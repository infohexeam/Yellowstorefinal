@extends('admin.layouts.app')
@section('content')
<div class="row" id="user-profile" style="min-height: 70vh;">
   <div class="col-lg-12">
      <div class="card" >
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
                           data-toggle="tab">Profile</a></li>
                        <!--<li><a href="#tab-61" data-toggle="tab" class="">Documents</a></li>-->
                        <li><a href="#tab-71" data-toggle="tab" class="">Images</a></li>
                       <li><a href="#tab-81" data-toggle="tab" class="">Agencies</a></li>
                        <li><a href="#tab-91" data-toggle="tab" class="">Products</a></li>
                        <li><a href="#tab-92" data-toggle="tab" class="">Delivery Boys</a></li>

                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="store_id" value="{{$store->store_id}}">
      <div class="card" style="min-height:70vh;">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Store Information</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                    <td><strong>Store Name:</strong> {{ @$store->store_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Name:</strong> {{ @$store->store_contact_person_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Number:</strong> {{ @$store->store_contact_person_phone_number}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Store Mobile Number:</strong> {{ @$store->store_mobile}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Website Link:</strong> <a target="_balnk" href="{{ @$store->store_website_link}}" >{{ $store->store_website_link}}</a></td>
                                 </tr>
                                    <tr>
                                    <!-- <td><strong> Pincode:</strong> {{ @$store->store_pincode}}</td> -->
                                    <td><strong> Registered GSTIN:</strong> {{ @$store->gst }}</td>
                                 </tr>
                                 </tr>
                                <tr>
                                    <td><strong> Business Type:</strong> {{ @$store->business_type['business_type_name']}}</td>
                                 </tr>

                                   <tr>
                                    <td><strong> Store Commission Amount (Monthly):</strong> {{ @$store->store_commision_amount}}</td>
                                 </tr>
                                  <tr>
                                    <td><strong> Store Commission Percentage (Per Order):</strong> {{ @$store->store_commision_percentage}}</td>
                                 </tr>

                                  {{-- <tr>
                                    <td><strong> Store Commision Percentage Per Order:</strong> {{ $store->store_commision_percentage}}%</td>
                                 </tr>

                                  <tr>
                                    <td><strong> Store Commision Amount (Monthly) :</strong> {{ $store->store_commision_amount}}</td>
                                 </tr> --}}



                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                     <td><strong>Email:</strong> {{ @$store->email}}</td>
                                 </tr>
                                 <tr>
                                     <td><strong>Registered Date:</strong> {{ \Carbon\Carbon::parse($store->created_at)->format('d-m-Y')}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Country:</strong>{{ @$store->country['country_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>State:</strong> {{ @$store->state['state_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>District:</strong> {{ @$store->district['district_name']}}</td>
                                 </tr>
                                  <tr>
                                    <td><strong>Pincode:</strong> {{ @$store->town['town_name']}}</td>
                                 </tr>
                                  <tr>
                                    <td><strong>Place:</strong> {{ @$store->place}}</td>
                                 </tr>
                                 {{-- <tr>
                                    <td><strong>Store Commision Percentage :</strong> {{ $store->store_commision_percentage}}</td>
                                 </tr> --}}

                                 <tr>
                                    <td><strong>Username :</strong> {{ $store->store_username}}</td>
                                 </tr>
                                   <tr>
                                     <td><strong>Address :</strong>
                                     <span style="display: inline-flex;" > <?php
                                        echo nl2br(@$store->store_primary_address);
                                        ?> </span>
                                     </td>

                                 </tr>
                              </tbody>
                           </table>


                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                 </div>
              <div class="tab-pane" id="tab-61">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Documents</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table   class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-20p">{{__('Document Name')}}</th>
                                    <th class="wd-20p">{{__('file')}}</th>
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$store_documents->isEmpty())
                                 @foreach ($store_documents as $document)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{ $document->store_document_file_head}} </td>
                                    <td> {{-- <a  href="{{url('admin/store/view_document/'.Crypt::encryptString($document->store_document_id))}}">{{ $document->store_document_other_file}}</a>  --}}
                                  <a href="{{  URL::to('assets/uploads/store_document/files/'.$document->store_document_other_file)}}" enctype="multipart/form-data" target="_blank">{{$document->store_document_other_file}}</a>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>

                <div class="tab-pane" id="tab-71">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Images</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table  class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>

                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$store_images->isEmpty())
                                 @foreach ($store_images as $image)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td><img data-toggle="modal" data-target="#viewModal{{$image->store_image_id}}" src="{{asset('/assets/uploads/store_images/images/'.$image->store_image)}}"  width="50" ></td>
                                 </tr>
                                 @endforeach
                                 @else
                                 <tr>
                                <td colspan="2"><center> No data available in the table</center></td>
                                  </tr>
                                  @endif
                              </tbody>
                           </table>
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>


                <div class="tab-pane" id="tab-81">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Agencies</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table  class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Contact person') }}</th>
                                    <th class="wd-15p">{{ __('Mobile') }}</th>
                                    <th class="wd-15p">{{ __('Email') }}</th>
                                    <th class="wd-15p">{{ __('Action') }}</th>
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$agencies->isEmpty())
                                 @foreach ($agencies as $agency)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{@$agency->agency['agency_name']}}</td>
                                    <td>{{@$agency->agency['agency_contact_person_name']}}</td>
                                    <td>{{@$agency->agency['agency_contact_person_phone_number']}}</td>
                                    <td>{{@$agency->agency['agency_email_address']}}</td>
                                    <td>
                                      <form action="{{route('admin.destroy_store_agency',$agency->link_id )}}" method="POST">

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
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>


                <div class="tab-pane" id="tab-91">
                    <div class="card-body">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Products</strong></h5>
                        </div>
                          <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                                   <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                   <th class="wd-15p">{{ __('Product Code') }}</th>
                                    <th class="wd-15p">{{ __('Category') }}</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Price') }}</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>

                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$store_products->isEmpty())
                                 @foreach ($store_products as $product)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$product->product_code}}</td>
                                    <td>{{@$product->categories->category_name}}</td>
                                    <td>{{$product->product_name}}</td>
                                    <td>{{$product->product_price}}</td>

                                    <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="50" ></td>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                           </div>
                        </div>
                     </div>
                  </div>


                   <div class="tab-pane" id="tab-92">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Delivery Boys</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table   class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Mobile') }}</th>
                                    {{-- <th class="wd-15p">{{ __('Action') }}</th> --}}
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$delivery_boys->isEmpty())
                                 @foreach ($delivery_boys as $dboy)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{@$dboy->delivery_boy['delivery_boy_name']}}</td>
                                    <td>{{@$dboy->delivery_boy['delivery_boy_mobile']}}</td>
                                    {{-- <td>
                                      <form action="{{route('admin.destroy_store_agency',$dboy->store_link_delivery_boy_id )}}" method="POST">

                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                       </form>
                                    </td> --}}
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>

                </div>

{{-- </div>
</div> --}}
</div>
</div>
</div>




  @foreach($store_images as $image)
  <div class="modal fade" id="viewModal{{$image->store_image_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
     <div class="modal-dialog" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           <div class="modal-body">

            <img  src="{{asset('/assets/uploads/store_images/images/'.$image->store_image)}}"  width="600" >

           </div>
           <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
           </div>
        </div>
     </div>
  </div>
  @endforeach


@endsection
<script type="text/javascript">
            function openTab(th)
            {
                window.open(th.name,'_blank');
            }
        </script>
