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
               <form id="myForm" action="{{route('admin.update_agency',$agency->agency_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Agency Name *</label>
                           <input type="hidden" required name="agency_id" value="{{$agency->agency_id}}">
                           <input type="text" id="agency_name"  class="form-control" name="agency_name" value="{{old('agency_name',$agency->agency_name)}}"  placeholder="Agency Name">
                        </div>
                         </div>
                           <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Contact Person Name *</label>
                            <input type="text" id="agency_contact_person_name" required name="agency_contact_person_name" class="form-control"  value="{{old('agency_contact_person_name',$agency->agency_contact_person_name)}}" placeholder="Contact Person Name">
                           </div>
                        </div>
                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Contact Person Number *</label>
                            <input type="text" required id="agency_contact_person_phone_number" name="agency_contact_person_phone_number" class="form-control" maxlength="10"  value="{{old('agency_contact_person_phone_number',$agency->agency_contact_person_phone_number)}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  placeholder="Contact Person Number">
                        </div>
                         </div>
                         <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Contact Number 2</label>
                            <input type="text" maxlength="10" name="agency_contact_number_2" id='txtcontact'  onpaste="return false" class="form-control" value="{{old('agency_contact_number_2',$agency->agency_contact_number_2)}}"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  placeholder="Contact Number 2">
                           </div>
                        </div>
                             <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Agecy Email*</label>
                          <input type="text" id="email" required name="agency_email_address" class="form-control" placeholder="Agecy Email" value="{{old('agency_email_address',$agency->agency_email_address)}}"  >
                          <span id="error_email"></span>

                        </div>
                         </div>



                           <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Country *</label>
                            <select name="country_id" required="" class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{old('country_id',$agency->country_id) == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                           </div>
                        </div>

                           <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">State *</label>
                             @php
                               @$states_data = \DB::table('sys_states')->where('country_id',@$agency->country_id)->get();
                           @endphp
                            <select name="state_id" required="" class="form-control" id="state" >
                             @foreach ($states_data as $value)
                                <option @if ($agency->state_id == $value->state_id)  selected  @endif  value="{{$value->state_id}}">  {{$value->state_name}}</option>
                             @endforeach
                              </select>
                           </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District *</label>
                           @php
                               @$district_data = \DB::table('mst_districts')->where('state_id',@$agency->state_id)->get();
                           @endphp
                        <select name="district_id" required="" class="form-control" id="city">
                           @foreach (@$district_data as $value)
                            <option  @if ($agency->district_id == $value->district_id)  selected  @endif value="{{$value->district_id}}">  {{$value->district_name}}</option>
                            @endforeach
                             </select>
                        </div>
                         </div>

                          <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Pincode *</label>
                           <input type="text" class="form-control" id="agency_pincode" name="agency_pincode" value="{{old('agency_pincode',$agency->agency_pincode)}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" placeholder="Agency Pincode">
                        </div>
                         </div>

                        <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Website Link</label>
                           <input type="url" class="form-control" id="agency_website_link" name="agency_website_link" value="{{old('agency_website_link',$agency->agency_website_link)}}" placeholder="Website Link">
                        </div>
                         </div>

                           <div class="col-md-6">
                                        <div class="form-group">
                    <label class="form-label">Business Type *</label>
                    <select name="business_type_id" required="" id="business_type_id" class="form-control" >
                      <option value=""> Select Business Type</option>
                      @foreach($business_types as $key)
                       <option {{old('business_type_id',$agency->business_type_id) == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                        @endforeach
                      </select>
                  </div>
                  </div>

                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Username *</label>
                          <input type="text" id="username" name="agency_username" required class="form-control" placeholder="Username" value="{{old('agency_username',$agency->agency_username)}}">
                          <span id="error_username"></span>
                       </div>
                        </div>


                           <div class="col-md-6">
                          <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="Password" onkeyup="validatePassLength()"  oninput="checkPasswordComplexity(this.value)" id="password"  name="agency_password" class="form-control" placeholder="Password" value="{{old('agency_password')}}">
                                                <p id="showpassmessage"><p>
                                                <p id="showpassmessage2"><p>

                        </div>
                     </div>
                  <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" class="form-control"  id="confirm_password"
                   onkeyup="validatePass()" name="password_confirmation"  placeholder="Confirm Password">
                                                    <p id="showmessage"><p>


                    </div>
                     </div>

                      <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Agency Logo [in png,jpeg or jpg]</label>
                    <input type="file" id="agency_logo" accept="image/x-png,image/jpg,image/jpeg"  class="form-control" name="agency_logo"  value="{{old('agency,$agency->agency_logo')}}" placeholder="Agency Logo">

                  </div>
                </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Address</label>
                           <textarea class="form-control" id="agency_primary_address" name="agency_primary_address"
                           rows="4" placeholder="Address">{{old('agency_primary_address',$agency->agency_primary_address)}}</textarea>
                        </div>

                     </div>
                  </div>

                         <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_agency') }}">Cancel</a>
                           </center>
                        </div>
                  </form>
                </div>



      </div>
   </div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>



<script>

  function checkPasswordComplexity(pwd) {
//  var re = /^(?=.*\d)(?=.*[a-z])(.{8,50})$/
 var re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/

    if(pwd != '')  
    {
        
      if(re.test(pwd) == false)
      {
           document.getElementById('showpassmessage2').style.color = 'red';
         //   document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
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
    if (x == y) {

    } else {
        document.getElementById('showmessage').style.color = 'red';
        if(x.length < 1  || y.length < 1 )
        {
            document.getElementById('showmessage').innerHTML = '';
        }
        else
        {
            document.getElementById('showmessage').innerHTML = 'Passwords not matching';
        }
                  //  $('#submit').attr('disabled', 'disabled');

    }
}
</script>

<script>
function validatePassLength() {
  var x = document.forms["myForm"]["password"].value;
document.forms["myForm"]["confirm_password"].required = true;
   if(x.length < 8)
   {
     document.getElementById('showpassmessage').style.color = 'red';
           if(x.length < 1 )
        {
                   document.getElementById('showpassmessage').innerHTML = '';
document.forms["myForm"]["confirm_password"].required = false;

        }
        else
        {
            document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 digits!';
        }
                   // $('#submit').attr('disabled', 'disabled');
   }
   else
   {
                   document.getElementById('showpassmessage').innerHTML = '';
                  //  $('#submit').attr('disabled', false);

   }

}
</script>


<script>


$(document).ready(function() {
 $('#reset').click(function(){

     $('#country').val('');

     $('#state option:not(:first)').remove();

     $('#city option:not(:first)').remove();

     $('#town option:not(:first)').remove();


 $('#agency_primary_address').val('');
$('#agency_logo').val('');
$('#password').val('');
$('#confirm_password').val('');
$('#business_type_id').val('');
$('#username').val('');
$('#agency_pincode').val('');
$('#agency_website_link').val('');
$('#email').val('');
$('#txtcontact').val('');
$('#agency_name').val('');
$('#agency_contact_person_phone_number').val('');
$('#agency_contact_person_name').val('');

$('#error_username').contents().unwrap();
$('#showpassmessage').contents().unwrap();
$('#showmessage').contents().unwrap();

   });
});


function validatePass() {
  var x = document.forms["myForm"]["password"].value;
  var y = document.forms["myForm"]["confirm_password"].value;
   document.getElementById('showmessage').innerHTML = '';
    if ((x == y) && (x != '') &&  (y != '')) {
    document.getElementById('password').border.color = 'green';
    document.getElementById('confirm_password').border.color = 'green';


    } else {
        if((x != '') &&  (y != ''))
        {
        document.getElementById('showmessage').style.color = 'red';
        document.getElementById('showmessage').innerHTML = 'passwords not matching';
        }
    }
}
</script>

<script>
function validatePassLength() {
  var x = document.forms["myForm"]["password"].value;
   if(x.length < 8 && (x != ''))
   {
     document.getElementById('showpassmessage').style.color = 'red';
            document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 digits!';
   }
   else
   {
                   document.getElementById('showpassmessage').innerHTML = '';

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
    url:"{{ route('admin.unique_email_agency') }}",
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
    url:"{{ route('admin.unique_username_agency') }}",
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
