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
                        <li><a href="#tab-61" data-toggle="tab" class="">Documents</a></li>
                        <li><a href="#tab-71" data-toggle="tab" class="">Images</a></li>
                       <li><a href="#tab-81" data-toggle="tab" class="">Agencies</a></li>
                        <li><a href="#tab-91" data-toggle="tab" class="">Delivery Boys</a></li>
                       
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <input type="hidden" name="store_link_subadmin_id" value="{{$store->store_id}}">
      <div class="card">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">
                  <div class="tab-pane active show" id="tab-51">
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Store Information</strong></h5>
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
               <form action="{{route('admin.update_store_subadmin',$store->store_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Name</label>
                         
                           <input type="text" class="form-control" name="store_name" value="{{old('store_name',$store->store_name)}}"  placeholder="Store Name">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Contact Person Name</label>
                            <input type="text" maxlength="10" name="store_contact_person_name" class="form-control"  value="{{old('store_contact_person_name',$store->store_contact_person_name)}}" placeholder="Contact Person Name">
                           </div>
                        </div>
                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Contact Person Number</label>
                            <input type="text" maxlength="10" name="store_contact_person_phone_number" class="form-control"  value="{{old('store_contact_person_phone_number',$store->store_contact_person_phone_number)}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  placeholder="Contact Person Number">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Contact Number 2</label>
                            <input type="text" maxlength="10" name="store_contact_number_2" id='txtcontact'  onpaste="return false" class="form-control" value="{{old('store_contact_number_2',$store->store_contact_number_2)}}"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  placeholder="Contact Number 2">
                           </div>
                        </div>
                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Website Link</label>
                           <input type="url" class="form-control" name="store_website_link" value="{{old('store_website_link',$store->store_website_link)}}" placeholder="Website Link">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Country</label>
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
                           <label class="form-label">Pincode</label>
                           <input type="text" class="form-control" name="store_pincode" value="{{old('store_pincode',$store->store_pincode)}}" placeholder="Store Pincode">
                        </div>
                         <div class="form-group">
                           <label class="form-label">State</label>
                            <select name="store_state_id" required="" class="form-control" id="state" >
                             <option  selected="" value="{{$store->store_state_id}}">  {{$store->state->state_name}}</option>
                                
                              </select>
                           </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District</label>
                          <select name="store_district_id" required="" class="form-control" id="city">
                          <option  selected="" value="{{$store->store_district_id}}"> {{$store->district->district_name}} </option>
                             
                          </select>
                        </div>
                        <div class="form-group">
                           <label class="form-label"> User Name</label>
                          <input type="text" id="username" name="store_username" class="form-control" placeholder="Store UserName" value="{{old('store_username',$store->store_username)}}">
                          <span id="error_username"></span>
                       </div>
                        </div>
                          <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Email</label>
                          <input type="text" id="email" required name="store_email_address" class="form-control" placeholder="Email" value="{{old('store_email_address',$store->store_email_address)}}"  >
                          <span id="error_email"></span>
                          
                        </div>
                          <div class="form-group">
                            <label class="form-label">Store Password</label>
                          <input type="Password"  name="password" class="form-control" placeholder="Password" value="{{old('password')}}">
                        </div>
                     </div>
                  <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password"  class="form-control"
                    name="password_confirmation"  placeholder="Confirm Password">
                    
                  </div>
                    
                  </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label"> Address</label>
                           <textarea class="form-control"  name="store_primary_address" 
                           rows="4" placeholder="Primary Address">{{old('store_primary_address',$store->store_primary_address)}}</textarea>
                        </div>
                       
                     </div>
                  </div>
                
                 <br>
               
                     <div class="card-body border">
                       <div class="card-header">
                  <h3 class="mb-0 card-title">Add Store Documents</h3>
                    </div>
              
                   <div class="card-body">
                  <div class="row">
        
                     <div class="col-md-6">
                        <div class="form-group">
                         <div id="doc_area">
                           <label class="form-label"> Other File</label>
                           <input type="file" class="form-control"
                           name="store_document_other_file[]" value="{{old('store_document_other_file')}}" placeholder="Store Document Other File">
                        </div>
                      </div>
                      </div>
                      <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Add more</label>
                            <button type="button" id="addDoc" class="btn btn-raised btn-success">
                      Add More</button>
                        </div>
                        </div>   
                  </div>
                   </div>
                 </div>

                   <br>
                    <div class="card-body border">
                      <div class="card-header">
                  <h3 class="mb-0 card-title">Add Banner Images</h3>
                    </div>
                   <div class="card-body">
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <div id="teamArea">
                           <label class="form-label">Images </label>
                           <input type="file" class="form-control" multiple="" name="store_image[]" value="{{old('store_image')}}" placeholder="Images">
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
                           <a class="btn btn-danger" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
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
                                    <th class="wd-15p">{{ __('License') }}</th>
                                    <th class="wd-20p">{{__('GSTIN')}}</th>
                                    <th class="wd-20p">{{__('file')}}</th>
                                    <th  class="wd-20p">{{__('Action')}}></th>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
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
                                    <td><img src="{{asset('/assets/uploads/store_images/images/'.$image->store_image)}}"  width="50" ></td>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
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
                           <a class="btn btn-cyan" href="{{ route('admin.list_store_subadmin') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>

                  <div class="tab-pane" id="tab-91">
                     
                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Delivery Boys</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example5" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Action') }}</th>
                                 </tr>
                              </thead>
                               <tbody class="col-lg-12 col-xl-6 p-0">
                                 @php
                                 $i = 0;
                                 @endphp
                                @if(!$delivery_boys->isEmpty())
                                 @foreach ($delivery_boys as $delivery_boy)
                                 @php
                                 $i++;
                                 @endphp
                                 <tr>
                                    <td>{{$i}}</td>
                                    <td>{{ $delivery_boy->delivery_boy['delivery_boy_name']}}</td>
                                   <td>
                                     
                                    <form action="{{route('admin.store_link_delivery_boy',$delivery_boy->store_link_delivery_boy_id )}}" method="POST">
                       
                                    @csrf
                                    @method('POST')
                                    <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                    </td>
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
             
{{-- </div>
</div> --}}
</div>
</div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
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
      $(wrapper).append('<div> <br>  <input type="file" class="form-control" multiple="" name="store_image[]" value="{{old('store_image')}}" placeholder="Images"> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box
    
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
      $(wrapper).append('<div> <br>  <input type="file" class="form-control" name="store_document_other_file[]" value="{{old('store_document_other_file')}}" placeholder="Store Document Other File"> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box
    
  });

  
  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});

 $(document).ready(function(){
  $("#email").blur(function(){
   var error_email = '';
  var store_email_address = $(this).val();
  //alert(store_email_address);
  var _token = $('input[name="_token"]').val();
 var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!filter.test(store_email_address))
  {    
   $('#error_email').html('<label class="text-danger">Invalid Email</label>');
   $('#email').addClass('has-error');
   $('#submit').attr('disabled', 'disabled');
  }
     $.ajax({
    url:"{{ route('admin.unique_email') }}",
    method:"POST",
    data:{store_email_address:store_email_address, _token:_token},
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
  //alert(store_email_address);
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