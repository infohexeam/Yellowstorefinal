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
                           data-toggle="tab">Profile</a></li>
                        <li><a href="#tab-61" data-toggle="tab" class="">Documents</a></li>
                        <li><a href="#tab-71" data-toggle="tab" class="">Images</a></li>
                       <li><a href="#tab-81" data-toggle="tab" class="">Agencies</a></li>
                        <li><a href="#tab-91" data-toggle="tab" class="">Products</a></li>
                       
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="store_id" value="{{$store->store_id}}">
      <div class="card">
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
                                    <td><strong>Store Name :</strong> {{ $store->store_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Name :</strong>{{ $store->store_contact_person_name}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Number :</strong> {{ $store->store_contact_person_phone_number}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Contact Person Number 2:</strong> {{ $store->store_contact_number_2}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Website Link :</strong> {{ $store->store_website_link}}</td>
                                 </tr>
                                    <tr>
                                    <td><strong> PinCode :</strong> {{ $store->store_pincode}}</td>
                                 </tr>
                                  </tr>
                                    <tr>
                                   <td><strong> Business Type :</strong> {{ $store->business_type['business_type_name']}}</td>
                                 </tr>
   
                              </tbody>
                              <tbody class="col-lg-12 col-xl-6 p-0">
                                 <tr>
                                     <td><strong>Email :</strong> {{ $store->email}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Country :</strong>{{ $store->country['country_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>State :</strong> {{ $store->state['state_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>District :</strong> {{ $store->district['district_name']}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Store Commision :</strong> {{ $store->store_commision_percentage}}</td>
                                 </tr>
                                 <tr>
                                    <td><strong>Username :</strong> {{ $store->store_username}}</td>  
                                 </tr>
                                   <tr>
                                     <td><strong>Address :</strong>{{ $store->store_primary_address}}</td>
   
                                 </tr>
                              </tbody>
                           </table>
                    
                           
                           <center>
                           <a class="btn btn-cyan" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
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
                           <table  id="example" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('License') }}</th>
                                    <th class="wd-20p">{{__('GSTIN')}}</th>
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
                                    <td>{{ $document->store_document_license}}</td>
                                    <td>{{ $document->store_document_gstin}} </td>
                                    <td>{{ $document->store_document_other_file}} </td>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
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
                           <table  id="example" class="table table-striped table-bordered">
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
                                    <td><img src="{{asset('/assets/uploads/store_images/images/'.$image->store_image)}}"  width="50" ></td>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
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
                           <table  id="example" class="table table-striped table-bordered">
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
                                    <td>{{$agency->agency['agency_name']}}</td>
                                    <td>{{$agency->agency['agency_contact_person_name']}}</td>
                                    <td>{{$agency->agency['agency_contact_person_phone_number']}}</td>
                                    <td>{{$agency->agency['agency_email_address']}}</td>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>


                <div class="tab-pane" id="tab-91">
                     
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Products</strong></h5>
                        </div>
                        <div class="table-responsive ">
                           <table  id="example" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                   <th class="wd-15p">{{ __('Code') }}</th>
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
                                    <td>{{$product->product_name}}</td>
                                    <td>{{$product->product_price}}</td>
                                   
                                    <td><img src="{{asset('/assets/uploads/products/images/'.$product->product_base_image)}}"  width="50" ></td>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
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
@endsection