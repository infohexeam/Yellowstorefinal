@extends('store.layouts.app')
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
               <form action="{{route('store.store_agency')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Name</label>
                           <input type="text" class="form-control" name="agency_name" value="{{old('agency_name')}}" placeholder="Agency Name">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Contact Person Name</label>
                            <input type="text" required=""  name="agency_contact_person_name" class="form-control"  value="{{old('agency_contact_person_name')}}" placeholder="Contact Person Name">
                           </div>
                        </div>
                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Contact Person Number</label>
                            <input type="text" required="" maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="agency_contact_person_phone_number" class="form-control"  value="{{old('agency_contact_person_phone_number')}}" placeholder="Contact Person Number">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Contact Number 2</label>
                            <input type="text" maxlength="10" name="agency_contact_number_2" id='txtcontact'  onpaste="return false" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" class="form-control" value="{{old('agency_contact_number_2')}}" placeholder="Contact Number 2">
                           </div>
                        </div>
                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Website Link</label>
                           <input type="url" required="" class="form-control" name="agency_website_link" value="{{old('agency_website_link')}}" placeholder="Website Link">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Country</label>
                            <select name="country_id" required="" class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{old('country_id') == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                           </div>
                        </div>
                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Pincode</label>
                           <input type="text" required class="form-control" name="agency_pincode" value="{{old('agency_pincode')}}" placeholder="Pincode">
                        </div>
                         <div class="form-group">
                           <label class="form-label">State</label>
                            <select name="state_id" required="" class="form-control" id="state" >
                                 <option  value=""> Select State</option>
                                
                              </select>
                           </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District</label>
                          <select name="district_id" required="" class="form-control" id="city">
                             <option value="">Select City</option>
                          </select>
                        </div>
                        <div class="form-group">
                           <label class="form-label"> User Name</label>
                          <input type="text"  required id="username" name="agency_username" class="form-control" placeholder="UserName" value="{{old('agency_username')}}">
                           <span id="error_username"></span>
                       </div>
                        </div>
                          <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Email</label>
                          <input type="email" id="email" required name="agency_email_address" class="form-control" placeholder="Email" value="{{old('agency_email_address')}}">
                           <span id="error_email"></span>
                          
                        </div>
                          <div class="form-group">
                            <label class="form-label"> Password</label>
                          <input type="Password" required="" name="agency_password" class="form-control" placeholder="Password" value="{{old('agency_password')}}">
                        </div>
                     </div>
                  <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" required  class="form-control"
                    name="password_confirmation"  placeholder="Confirm Password">
                    </div>
                  
                  
                     <div class="form-group">
                       <label class="form-label">Agency Logo</label>
                          <input type="file" required="" name="agency_logo" class="form-control" placeholder="Agency Logo" value="{{old('agency_logo')}}">
                        </div>
                    </div>
                    <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label"> Business Type</label>
                    <select name="business_type_id" required="" class="form-control" >
                    <option value=""> Select Business Type</option>
                       @foreach($business_types as $key)
                       <option {{old('business_type_id') == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                                @endforeach
                              </select>
                    </div>
                  </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label"> Address</label>
                           <textarea class="form-control"  name="agency_primary_address" 
                           rows="4" placeholder="Primary Address">{{old('agency_primary_address')}}</textarea>
                        </div>
                        
                     </div>
                 
                  <div class="col-md-12">
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('store.list_agency') }}">Cancel</a>
                           </center>
                        </div>
                      </div>
               </form>
            </div>
         </div>
      </div>
   </div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script type="text/javascript">
    $(document).ready(function() {
       $('#country').change(function(){
       /* $('#city').empty();
         $('#city').append('<option value="">Select City</option>');*/
        var country_id = $(this).val();
            
        var _token= $('input[name="_token"]').val();
       
        $.ajax({
          type:"GET",
          url:"{{ url('store/ajax/get_state') }}?country_id="+country_id,
         
         
          success:function(res){
          
            if(res){
            $('#state').prop("diabled",false);
            $('#state').empty();
            // $('#city').prop("diabled",false);
            // $('#city').empty();
            
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
       
    });
    $(document).ready(function() {
       $('#state').change(function(){
        var state_id = $(this).val();
        //alert(state_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('store/ajax/get_city') }}?state_id="+state_id ,
              
          success:function(res){
           
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


$(document).ready(function(){
  $("#email").blur(function(){
   var error_email = '';
  var agency_email_address = $(this).val();
  //alert(agency_email_address);
  var _token = $('input[name="_token"]').val();
 var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!filter.test(agency_email_address))
  {    
   $('#error_email').html('<label class="text-danger">Invalid Email</label>');
   $('#email').addClass('has-error');
   $('#submit').attr('disabled', 'disabled');
  }
     $.ajax({
    url:"{{ route('store.unique_email_agency') }}",
    method:"POST",
    data:{agency_email_address:agency_email_address, _token:_token},
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
  var agency_username = $(this).val();
  //alert(agency_email_address);
  var _token = $('input[name="_token"]').val();
  $.ajax({
    url:"{{ route('store.unique_username_agency') }}",
    method:"POST",
    data:{agency_username:agency_username, _token:_token},
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
 