@extends('admin.layouts.app')
@section('content')
<style>

  .password-show {
    position: relative;
  }
  .password-show input {
    padding-right: 2.5rem;
  }
  .password-show__toggle {
    position: absolute;
    top: 5px;
    right: 0;
    bottom: 0;
    width: 2.5rem;
  }
  .password-show_toggleshow-icon, .password-showtoggle_hide-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: #555;
  }
  .password-show_toggle_show-icon {
    display: block;
  }
  .password-show.show .password-show_toggle_show-icon {
    display: none;
  }
  .password-show_toggle_hide-icon {
    display: none;
  }
  .password-show.show .password-show_toggle_hide-icon {
    display: block;
  }
  </style>
<div class="row" id="user-profile" style="min-height: 70vh;">
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
                           data-toggle="tab">Store Information</a></li>
                       <li><a href="#tab-42" data-toggle="tab" class="">Change Password</a></li>
                        <!--<li><a href="#tab-61" data-toggle="tab" class="">Documents</a></li>-->
                        <li><a href="#tab-71" data-toggle="tab" class="">Images</a></li>
                       <li><a href="#tab-81" data-toggle="tab" class="">Agencies</a></li>
                        <li><a id="dboyz" href="#tab-91" data-toggle="tab" class="">Delivery Boys</a></li>
                       <li><a href="#tab-41" data-toggle="tab" class="">Products</a></li>

                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="store_id" value="{{$store->store_id}}">
      <div class="card">
       @if ($message = Session::get('status'))
                  <div class="alert alert-success">
                     <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                  </div>
                  @if($message == "Delivery Boy Assigned successfully.")
                            <script>
                            $(document).ready(function() {
                                $(function () {
                                    $("#dboyz").trigger('click');

                                });

                                });
                            </script>
                  @endif


                  @endif
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Store Information</strong></h5>
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
               <form  action="{{route('admin.update_store',$store->store_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Store Name</label>

                           <input type="text" readonly="" class="form-control" name="store_name" value="{{old('store_name',$store->store_name)}}"  placeholder="Store Name">
                        </div>
                      </div>

                        <div class="col-md-6">
                            <div class="form-group">
                            <label class="form-label">Store Mobile Number</label>
                                <input type="text" readonly="" maxlength="10" name="store_mobile" class="form-control"  value="{{old('store_mobile',$store->store_mobile)}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  placeholder="Store Mobile Number">
                            </div>
                        </div>

                      <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Contact Person Name</label>
                            <input type="text" required name="store_contact_person_name" class="form-control"  value="{{old('store_contact_person_name',$store->store_contact_person_name)}}" placeholder="Contact Person Name">
                           </div>
                        </div>

                        <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Contact Person Number*</label>
                            <input type="text"  required  maxlength="10" name="store_contact_person_phone_number" id='txtcontact'  onpaste="return false" class="form-control" value="{{old('store_contact_person_phone_number',$store->store_contact_person_phone_number)}}"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  placeholder="Contact Person Number ">
                           </div>
                        </div>
                              <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Store Email</label>
                          <input type="email" id="email_" name="email" class="form-control" placeholder="Store Email" value="{{old('email',$store->email)}}"  >
                          <span id="error_email"></span>

                        </div>
                         </div>



                        <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Country *</label>
                            <select name="store_country_id" required="" class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{old('store_country_id',$store->store_country_id) == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                           </div>
                        </div>

                         <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">State *</label>
                           @php
                               @$states_data = \DB::table('sys_states')->where('country_id',@$store->store_country_id)->get();
                               //dd($store->store_state_id);
                           @endphp
                            <select name="store_state_id" required="" class="form-control" id="state" >
                             @foreach ($states_data as $value)
                          
                                <option  @if($store->store_state_id)@if ($store->store_state_id == $value->state_id)  selected  @endif @endif  value="{{@$value->state_id}}">  {{@$value->state_name}}</option>
                             @endforeach
                            </select>
                           </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District *</label>
                          @php
                               @$district_data = \DB::table('mst_districts')->where('state_id',@$store->store_state_id)->get();
                           @endphp
                           <select name="store_district_id" required="" class="form-control" id="city">
                           @foreach (@$district_data as $value)
                                <option @if($store->store_district_id) @if ($store->store_district_id == $value->district_id)  selected  @endif @endif  value="{{@$value->district_id}}">  {{@$value->district_name}}</option>

                             @endforeach

                          </select>
                        </div>
                         </div>
                         <div class="col-md-6">
                         <div class="form-group">
                              <label class="form-label">Pincode *</label>
                              <select name="store_town" required="" class="form-control" id="town">
                                 <option  selected="" value="{{$store->town_id}}">  {{@$store->town['town_name']}}</option>
                              </select>
                           </div>
                        </div>


                         <div class="col-md-6">
                           <div class="form-group">
                              <label class="form-label">Store Place *</label>
                              <input required type="text" class="form-control" name="store_place" id="store_place" value="{{old('store_place',$store->place)}}" placeholder="Store Place">
                           </div>
                     </div>

                       {{-- <div class="col-md-6" style="display:none;">
                            <div class="form-group">
                            <label class="form-label">Store Pincode</label>
                            <input type="text" class="form-control" name="store_pincode" value="{{old('store_pincode',$store->store_pincode)}}" placeholder="Store Pincode" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
                            </div>
                         </div> --}}

                      <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Website Link</label>
                                <input type="url" class="form-control" name="store_website_link" value="{{old('store_website_link',$store->store_website_link)}}" placeholder="Website Link">
                            </div>
                        </div>
 @if (auth()->user()->user_role_id == 0)
                  <div class="col-md-6">
                    <div class="form-group">
                          <label class="form-label">Store Commission Amount (Monthly)</label>
                          <input type="number" min="0"   name="store_commision_amount" step="0.1" class="form-control" placeholder="Store Commision Amount (Monthly) "  value="{{old('store_commision_amount',$store->store_commision_amount )}}">
                    </div>
                     </div>

                   <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Store Commission Percentage (Per Order)</label>
                          <input type="number" min="0"   name="store_commision_percentage"  step="0.1" class="form-control" value="{{old('store_commision_percentage',$store->store_commision_percentage)}}" placeholder="Store Commission Percentage (Per Order)" >
                        </div>
                     </div>
@endif

 
        @if (auth()->user()->user_role_id == 0)
                         <div class="col-md-6">

                        <div class="form-group">
                           <label class="form-label">Sub Admin</label>
                             <select required name="subadmin_id"  class="form-control"  >
                                  <option value=""> Select Sub Admin</option>
                                 @foreach($subadmins as $key)
                                 <option {{old('subadmin_id',$store->subadmin_id) == $key->id ? 'selected':''}} value="{{$key->id}}"> {{$key->name }} </option>
                                 @endforeach
                               </select>
                           </div>
                           </div>
                     @endif

                    <!--<div class="col-md-6">-->
                    <!--    <div class="form-group">-->
                    <!--       <label class="form-label">Store License</label>-->
                    <!--       <input type="text" class="form-control" name="store_document_license" value="{{old('store_document_license',@$store->store_doc->store_document_license)}}" placeholder="Store License">-->
                    <!--    </div>-->
                    <!-- </div>-->


                     <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Registered GSTIN</label>
                           <input type="text" class="form-control"
                           name="store_document_gstin" value="{{old('gst',@$store->gst)}}" placeholder="Registered GSTIN">
                        </div>
                     </div>

                         <div class="col-md-6">

                             <div class="form-group">
                           <label class="form-label"> User Name *</label>
                          <input type="text" id="username" readonly="" required name="store_username" class="form-control" placeholder="Store UserName" value="{{old('store_username',$store->store_username)}}">
                          <span id="error_username"></span>
                       </div>

                     </div>
                      <div class="col-md-6">
                           <div class="form-group">
                    <label class="form-label">Business Type *</label>
                    <select name="business_type_id" required="" class="form-control" >
                                 <option value=""> Select Business Type</option>
                                @foreach($business_types as $key)
                                <option {{old('business_type_id',$store->business_type_id) == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                                @endforeach
                              </select>
                  </div>

                      </div>

                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label"> Address</label>
                           <textarea class="form-control"  name="store_primary_address"
                           rows="4" placeholder="Primary Address">{{old('store_primary_address',$store->store_primary_address)}}</textarea>
                        </div>

                     </div>
                       <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Expiration Date</label>
                          <input type="date" id="expdate"  required name="expiry_date" class="form-control" placeholder="Expiration Date" value="{{old('expiry_date',@$store_admin->expiry_date)}}">
                        </div>

                     </div>



                     <!--<div class="col-md-6">-->

                     <!--</div-->

                  </div>

                 <br>

                 <!--    <div class="card-body border">-->
                 <!--      <div class="card-header">-->
                 <!-- <h3 class="mb-0 card-title">Add Store Documents</h3>-->
                 <!--   </div>-->

                 <!--  <div class="card-body">-->
                 <!-- <div class="row">-->

                 <!--       <div id="doc_area">-->



                 <!--   <div class="col-md-10">-->
                 <!--       <div class="form-group">-->
                 <!--          <label class="form-label">Document Title </label>-->
                 <!--          <input type="text" class="form-control docTitle" id="docTitle_0" -->
                 <!--          name="store_document_other_file_head[]"  placeholder="Document Title">-->
                 <!--     </div>-->
                 <!--    </div>-->

                 <!--    <div class="col-md-10">-->
                 <!--       <div class="form-group">-->
                 <!--          <label class="form-label"> Other File [in pdf,doc,docx or txt] </label>-->
                 <!--          <input type="file" class="form-control" accept=".pdf,.docx,.txt,.doc" id="docFile_0"-->
                 <!--          name="store_document_other_file[]"  placeholder="Store Document  File">-->
                 <!--     </div>-->
                 <!--    </div>-->


                 <!--       </div>-->


                 <!--     <div class="col-md-2">-->
                 <!--       <div class="form-group">-->
                 <!--          <label class="form-label">Add more</label>-->
                 <!--           <button type="button" id="addDoc" class="btn btn-raised btn-success">-->
                 <!--     Add More</button>-->
                 <!--       </div>-->
                 <!--       </div>-->
                 <!-- </div>-->
                 <!--  </div>-->
                 <!--</div>-->

                   <br>
                    <div class="card-body border">
                      <div class="card-header">
                  <h3 class="mb-0 card-title">Add Store Images</h3>
                    </div>
                   <div class="card-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <div id="teamArea">
                           <label class="form-label">Images (1000*800 above)</label>
                           <input type="file" class="form-control" accept="image/x-png,image/jpg,image/jpeg" multiple="" name="store_image[]"  placeholder="Images">
                        </div>
                     </div>
                    </div>

                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Add more</label>
                            <button type="button" id="addImage" class="btn btn-raised btn-success"> Add More</button>
                        </div>
                        </div>

                      </div>
                      </div>
                    </div>
                    <br>

                     <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                        </div>
                      </div>
                  </form>
                  
              

                     </div>
                 </div>
              <div class="tab-pane" id="tab-61">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Documents</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    {{-- <th class="wd-15p">{{ __('License') }}</th> --}}
                                    <th class="wd-20p">{{__('Document Title')}}</th>
                                    <th class="wd-20p">{{__('file')}}</th>
                                    <th  class="wd-20p">{{__('Action')}}</th>
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
                                    {{-- <td>{{ $document->store_document_license}}</td>
                                    <td>{{ $document->store_document_gstin}} </td> --}}
                                    <td>{{ $document->store_document_file_head}} </td>
                                    <td>
                                  <a href="{{  URL::to('assets/uploads/store_document/files/'.$document->store_document_other_file)}}" enctype="multipart/form-data" target="_blank">{{$document->store_document_other_file}}</a>
                                        </td>
                                    <td>

                                    <form action="{{route('admin.destroy_store_doc',$document->store_document_id)}}" method="POST">

                                    @csrf
                                    @method('POST')
                                    <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                    </td>
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
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Image') }}</th>
                                    <th class="wd-15p">{{ __('Default Status') }}</th>
                                    <th class="wd-15p">{{ __('Action') }}</th>

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

                                    <td><input type="checkbox" class="csatatus" @if (@$image->default_image == 1) checked

                                    @endif value="1" onchange="myFunction({{$image->store_image_id}})"  name="default_status" id="default_status{{$image->store_image_id}}"></td>

                                     <td>

                                    <form action="{{route('admin.destroy_store_image',$image->store_image_id)}}" method="POST">

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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>


                <div class="tab-pane" id="tab-81">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Agencies</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>

                  <div class="tab-pane" id="tab-91">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Delivery Boys</strong></h5>
                        </div><br>
                           <a  style="color:white;" data-toggle="modal" data-target="#AssignModal"  class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Assign Delivery Boys
                            </a>
                        </br>

                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Mobile') }}</th>
                                    <th class="wd-15p">{{ __('Action') }}</th>
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                   $all_assigned_dboys[]  = 0;
                                 @endphp
                                @if(!$delivery_boys->isEmpty())
                                 @foreach ($delivery_boys as $delivery_boy)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{ @$delivery_boy->delivery_boy['delivery_boy_name']}}</td>
                                    <td>{{ @$delivery_boy->delivery_boy['delivery_boy_mobile']}}</td>
                                   <td>

                                    <form action="{{route('admin.store_link_delivery_boy',$delivery_boy->store_link_delivery_boy_id )}}" method="POST">

                                    @csrf
                                    @method('POST')
                                    <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                    </td>
                                 </tr>
                                 @php
                                 $all_assigned_dboys[] = @$delivery_boy->delivery_boy['delivery_boy_id'];
                                 @endphp
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



                   <div class="tab-pane" id="tab-41">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Products</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="exampletable" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">Product Name </th>
                                    <th class="wd-15p">Product Code</th>
                                    <th class="wd-15p">Product Offer Price</th>
                                    <th class="wd-15p">Image</th>
                                    <th class="wd-15p">{{ __('Action') }}</th>
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                // dd($products);
                                 @endphp
                                @if(!$products->isEmpty())
                                 @foreach ($products as $product)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{$product->product_name}}</td>
                                    <td>{{$product->product_code}}</td> 
                                    <td>{{$product->product_price_offer}}</td>
                                    <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.$product->product_base_image)}}"  width="50" >&nbsp;</td>
                       

                                    <td>
                                        
                                        @if(!isset($product->deleted_at))
                                          <form action="{{route('admin.convert_to_global_product',$product->product_id )}}" method="POST">
    
                                              @csrf
                                              @method('POST')
                                                  @if((!isset($product->global_product_id)) || ($product->global_product_id == 0) )
                                                  @php
                                                    $prductDataCount = \DB::table('mst__global_products')
                                                    ->where('isConvertedFromProducts',@$product->product_id)->count();

                                                  @endphp
                                                  @if($prductDataCount <= 0)
                                                  <button type="submit" onclick="return confirm('Are you sure?');"  class="btn btn-sm btn-info">Convert to Global</button>
                                                    @else
                                                  <button type="submit" disabled onclick="return confirm('Are you sure?');"  class="btn btn-sm btn-info">Convert to Global</button>

                                                    @endif
                                                  @endif
                                              <br>
                                              <a href="{{url('admin/product/home-screen/'.$product->product_id)}}" onclick="return confirm('Are you sure?');"  class="mt-2 btn btn-sm @if($product->show_in_home_screen == 0) btn-green @else btn-warning   @endif">
                                              @if($product->show_in_home_screen == 0)
                                              Show in Home Screen
                                              @else
                                              Remove From Home Screen
                                              @endif
                                              </a>
                                          </form>
                                        @else
                                        <form action="{{route('admin.restore_product',$product->product_id)}}" method="POST">
                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to restore this item?');"  class="btn btn-sm btn-warning">Restore</button>
                                       </form>
                                        @endif

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
                  
                  
                  
                     <div class="tab-pane" id="tab-42">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Change Password</strong></h5>
                        </div><br>
                        <div class="card">
                            
                                 <form  id="myForm" onsubmit="return validateForm()" action="{{ route('admin.update_password_store',$store->store_id) }}" method="POST" enctype="multipart/form-data" >
                        @csrf
                    <div class="card-body border">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                 <label id="passlabel" class="form-label">Password </label>
                                 <div class="password-show">
                                      <input required type="Password" onkeyup="validatePassLength()" oninput="checkPasswordComplexity(this.value)"  id="password"  name="password" class="form-control" placeholder=" Password" value="{{old('password')}}">
                                       <div class="password-show__toggle">
                              <i class="fa fa-eye password-show_toggle_show-icon"></i>
                              <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                            </div>
                            </div>
                                    <p id="showpassmessage"><p>
                                    <p id="showpassmessage2"><p>
                                </div>
                               </div>
                               <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Confirm Password </label>
                                         <div class="password-show">
                                        <input required type="password"  class="form-control" onkeyup="validatePass()"
                                        name="password_confirmation" id="confirm_password" value="{{old('password_confirmation')}}"  placeholder="Confirm Password">
                                         <div class="password-show__toggle">
                              <i class="fa fa-eye password-show_toggle_show-icon"></i>
                              <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                            </div>
                            </div>
                                            <p id="showmessage"><p>
                                    </div>
                                </div>
                                </div>
                        </div>
                        <div class="form-group">
                           <center>
                         <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_store') }}">Cancel</a>
                           </center>
                           </div>
                           
                           
                        </form>
                          
                           
                           
                        </div>
                     </div>
                  </div>

{{-- </div>
</div> --}}
</div>
</div>
</div>


 <div class="modal fade" id="AssignModal" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" onclick="clearTax()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.assign_delivery_boy') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">


                    <input type="hidden" name="store_id" value="{{$store->store_id}}">
                    <input type="hidden" name="store_name" value="{{$store->store_name}}">

                         <div id="store">
                           <label class="form-label">Delivery Boy</label>
                           <select name="delivery_boy_id[]" required="" class="form-control" >
                                 <option value=""> Select Delivery Boy</option>
                                @foreach($all_delivery_boys as $key)
 @if (!in_array($key->delivery_boy_id, $all_assigned_dboys))
                                 <option value="{{$key->delivery_boy_id}}"> {{$key->delivery_boy_name }} </option>
 @endif

                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                           <label class="form-label">Add more</label>
                            <button type="button" id="addStore" class="btn btn-raised btn-success"> Add More</button>
                        </div>
                      </div>


                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="button" onclick="clearTax()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
<!-- MESSAGE MODAL CLOSED -->


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


<script type="text/javascript">
   function initialize() {
      var input3 = document.getElementById('store_place'); // replace textbox id here
      var autocomplete3 = new google.maps.places.Autocomplete(input3);

   }
   google.maps.event.addDomListener(window, 'load', initialize);
</script>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script>
        $(document).ready(function() {
  $(".password-show__toggle").on("click", function(e) {
    console.log("click");
    if (
      !$(this)
        .parent()
        .hasClass("show")
    ) {
      $(this)
        .parent()
        .addClass("show");
      $(this)
        .prev()
        .attr("type", "text");
    } else {
      $(this)
        .parent()
        .removeClass("show");
      $(this)
        .prev()
        .attr("type", "password");
    }
  });
});
   </script>


<script>
   function checkPasswordComplexity(pwd) {
// var re = /^(?=.*\d)(?=.*[a-z])(.{8,50})$/
 var re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/

    if(pwd != '')  
    {
        
      if(re.test(pwd) == false)
      {
           document.getElementById('showpassmessage2').style.color = 'red';
     //       document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
                   document.getElementById('showpassmessage2').innerHTML = 'Password must include at least one upper case letter, lower case letter, number, and special character';
                                $('#submit').attr('disabled', 'disabled');
    
      }
      else
      {
             document.getElementById('showpassmessage2').innerHTML = '';
                        $('#submit').attr('disabled', false);
    
      }
    }
    else
    {
           document.getElementById('showpassmessage2').innerHTML = '';
                        $('#submit').attr('disabled', false);
    }
}



function validatePass() {
  var x = document.forms["myForm"]["password"].value;
  var y = document.forms["myForm"]["confirm_password"].value;
   document.getElementById('showmessage').innerHTML = '';
   if(y != '')
   {
    if (x == y) {
    document.getElementById('password').border.color = 'green';
    document.getElementById('confirm_password').border.color = 'green';


    } else {
        document.getElementById('showmessage').style.color = 'red';
        document.getElementById('showmessage').innerHTML = 'passwords not matching';
    }
   }
   else
   {
               document.getElementById('showmessage').innerHTML = '';

   }
}
</script>

<script>
function validatePassLength() {
  var x = document.forms["myForm"]["password"].value;
//document.forms["myForm"]["confirm_password"].required = true;
   if(x.length < 8)
   {
     document.getElementById('showpassmessage').style.color = 'red';
           if(x.length < 1 )
        {
                   document.getElementById('showpassmessage').innerHTML = '';
//document.forms["myForm"]["confirm_password"].required = false;

                 //   $('#submit').attr('disabled', false);

        }
        else
        {
            document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 characters!';
        }
                  //  $('#submit').attr('disabled', 'disabled');
   }
   else
   {
                   document.getElementById('showpassmessage').innerHTML = '';
                 //   $('#submit').attr('disabled', false);

   }

}
</script>


<script type="text/javascript">

$(document).ready(function() {
   var wrapper      = $("#store"); //Fields wrapper
  var add_button      = $("#addStore"); //Add button ID

  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div>  <br>   <label class="form-label">Delivery Boy</label><select name="delivery_boy_id[]" required="" class="form-control" >  <option value=""> Select Delivery Boy</option>@foreach($all_delivery_boys as $key) @if (!in_array($key->delivery_boy_id, $all_assigned_dboys))<option {{old('delivery_boy_id') == $key->delivery_boy_id ? 'selected':''}} value="{{$key->delivery_boy_id}}"> {{$key->delivery_boy_name }} </option> @endif @endforeach</select><a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box

  });



  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});




$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Products',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3]
                 }
            },
            {
                extend: 'excel',
                title: 'Products',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3]
                 }
            }
         ]
    } );

} );



</script>

<script>

function myFunction(id)
         {
            if($('#default_status'+id).prop('checked'))
            {

                    $('.csatatus').prop('checked', false);
                    $('#default_status'+id).prop('checked', true);

                var _token= $('input[name="_token"]').val();
                //alert(_token);
                $.ajax({
                type:"GET",
                url:"{{ url('admin/ajax/set_default_image')}}?table_id="+id,


            success:function(res){
            // alert(data);
                if(res){
                   // alert("default image setup completed");
                      //  location.reload();
                }else
                {
                    alert("error");
                }
                }

        });
    }
    else{


                var _token= $('input[name="_token"]').val();
                //alert(_token);
                $.ajax({
                type:"GET",
                url:"{{ url('admin/ajax/change_default_image')}}?table_id="+id,


            success:function(res){
            // alert(data);
                if(res){
                   // alert("default image setup completed");
                      //  location.reload();
                    alert("please select a default image");

                }else
                {
                    alert("error");
                }
                }

        });

    }

         }

</script>


<script type="text/javascript">





      $(document).ready(function() {
      $(function () {
       $('#country').change(function(){
       // alert("dd");
        $('#city').empty();
         $('#city').append('<option value="">Select City</option>');
        var country_id = $(this).val();
            //alert(country_id);
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_state') }}?country_id="+country_id,


          success:function(res){
           // alert(data);
            if(res){
            $('#state').prop("diabled",false);
            $('#state').empty();

            $('#state').append('<option value="">Select State</option>');
            $.each(res,function(state_id,state_name)
            {
              $('#state').append('<option value="'+state_id+'">'+state_name+'</option>');
            });

            }else
            {
              $('#state').empty();

            }
            }

        });
      });
        $('#state').change(function(){
       // alert("dd");
        var state_id = $(this).val();
       //alert(product_cat_id);
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_city') }}?state_id="+state_id ,


          success:function(res){
           // alert(data);
            if(res){
            $('#city').prop("diabled",false);
            $('#city').empty();
            $('#city').append('<option value="">Select City</option>');
            $.each(res,function(district_id,district_name)
            {
              $('#city').append('<option value="'+district_id+'">'+district_name+'</option>');
            });

            }else
            {
              $('#city').empty();

            }
            }

        });
      });

       //display town

       $('#city').change(function(){
        var city_id = $(this).val();
       // alert(city_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_town') }}?city_id="+city_id ,

          success:function(res){

           if(res){
              console.log(res);
            $('#town').prop("diabled",false);
            $('#town').empty();

            $('#town').append('<option value="">Select Pincode</option>');
            $.each(res,function(town_id,town_name)
            {
              $('#town').append('<option value="'+town_id+'">'+town_name+'</option>');
            });

            }else
            {
              $('#town').empty();

             }
            }

        });
      });



    });
       });




$(document).ready(function() {
   var wrapper      = $("#teamArea"); //Fields wrapper
  var add_button      = $("#addImage"); //Add button ID

  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div> <br>  <input type="file" class="form-control" accept="image/x-png,image/jpg,image/jpeg" multiple="" name="store_image[]" placeholder="Images"> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box

  });



  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});


$(document).ready(function() {
   var wrapper      = $("#doc_area"); //Fields wrapper
  var add_button      = $("#addDoc"); //Add button ID

  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      //$(wrapper).append('<div> <br>  <input type="file" class="form-control" name="store_document_other_file[]" value="{{old('store_document_other_file')}}" placeholder="Store Document Other File"> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box
$(wrapper).append('<div class="border border-primary mb=2"> <div class="col-md-10"><div class="form-group"><label class="form-label"> Document Title </label><input type="text" required class="form-control"name="store_document_other_file_head[]" placeholder="Store Document File Title"></div></div><div class="col-md-10"><div class="form-group"><label class="form-label"> Other File [in pdf,doc,docx or txt] </label><input type="file" class="form-control" required accept=".pdf,.docx,.txt,.doc" name="store_document_other_file[]"  placeholder="Store Document Other File"></div></div><a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div><br>'); //add input box

  });


  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});



$(document).ready(function(){

    $('.docTitle').on('input', function() {
        
        if(this.value == '')
        {
          $("#docFile_0").val('');
        $("#docFile_0").prop('required',false);
        }
        else
        {
           $("#docFile_0").prop('required',true);
        }

    });

});

 $(document).ready(function(){
  $("#email").blur(function(){
   var error_email = '';
  var email = $(this).val();
  //alert(email);
  var _token = $('input[name="_token"]').val();
 var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!filter.test(email))
  {
   $('#error_email').html('<label class="text-danger">Invalid Email</label>');
   $('#email').addClass('has-error');
   $('#submit').attr('disabled', 'disabled');
  }
     $.ajax({
    url:"{{ route('admin.unique_email') }}",
    method:"POST",
    data:{email:email, _token:_token},
    success:function(result)
    {
     if(result == 'unique')
     {
      $('#error_email').html('<label class="text-success">Email Available</label>');
      $('#email').removeClass('has-error');
       $('#submit').attr('disabled', false);
     }
     else
     {
      $('#error_email').html('<label class="text-danger">Email Already Exist </label>');
      $('#email').addClass('has-error');
       $('#submit').attr('disabled', 'disabled');

     }
    }
   })
  });
});


$(document).ready(function(){
  $("#username").blur(function(){
   var error_username = '';
  var store_username = $(this).val();
  //alert(email);
  var _token = $('input[name="_token"]').val();
  $.ajax({
    url:"{{ route('admin.unique_username') }}",
    method:"POST",
    data:{store_username:store_username, _token:_token},
    success:function(result)
    {
     if(result == 'unique')
     {
      $('#error_username').html('<label class="text-success">Username Available</label>');
      $('#username').removeClass('has-error');
       $('#submit').attr('disabled', false);
     }
     else
     {
      $('#error_username').html('<label class="text-danger">Username Already Exist </label>');
      $('#username').addClass('has-error');
       $('#submit').attr('disabled', 'disabled');

     }
    }
   })
  });
});
</script>
@endsection
