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
               <form id="myForm" action="{{route('admin.update_company',$company->company_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Company Name</label>
                           <input type="hidden" name="company_id" value="{{$company->company_id}}">
                           <input type="text" class="form-control" name="company_name" value="{{old('company_name',$company->company_name)}}"  placeholder="Company Name">
                        </div>
                          </div>
                      <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Contact Person Name</label>
                            <input type="text"  name="company_contact_person_name" class="form-control"  value="{{old('company_contact_person_name',$company->company_contact_person_name)}}" placeholder="Contact Person Name">
                           </div>
                        </div>
                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Contact Person Number</label>
                            <input type="text" name="company_contact_person_phone_number" class="form-control"  value="{{old('company_contact_person_phone_number',$company->company_contact_person_phone_number)}}" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  placeholder="Contact Person Number">
                        </div>
                          </div>
                      <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Contact Number 2</label>
                            <input type="text" name="company_contact_number_2" id='txtcontact'  onpaste="return false" class="form-control" value="{{old('company_contact_number_2',$company->company_contact_number_2)}}"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  placeholder="Contact Number 2">
                           </div>
                        </div>

                      <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Country</label>
                            <select name="country_id" required="" class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{old('country_id',$company->country_id) == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                           </div>
                        </div>

                      <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">State</label>
                            <select name="state_id" required="" class="form-control" id="state" >
                                <option  selected="" value="{{$company->state_id}}">  {{$company->state->state_name}}</option>

                              </select>
                           </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District</label>
                          <select name="district_id" required="" class="form-control" id="city">
                            <option  selected="" value="{{$company->district_id}}">  {{$company->district->district_name}}</option>

                          </select>
                        </div>
                          </div>

                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Pincode</label>
                           <input type="number" class="form-control" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="company_pincode" value="{{old('company_pincode',$company->company_pincode)}}" placeholder="company Pincode">
                        </div>
                          </div>

                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Website Link</label>
                           <input type="url" class="form-control" name="company_website_link" value="{{old('company_website_link',$company->company_website_link)}}" placeholder="Website Link">
                        </div>
                          </div>

                           <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Company Logo [in png, jpeg or jpg]</label>
                    <input type="file"  class="form-control" name="company_logo" accept="image/x-png,image/jpg,image/jpeg"  value="{{old('company,$company->company_logo')}}" placeholder="company Logo">

                  </div>
                </div>

                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Username</label>
                          <input type="text" id="username" name="company_username" class="form-control" placeholder="Username" value="{{old('company_username',$company->company_username)}}">
                          <span id="error_username"></span>
                       </div>
                        </div>
                          <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Company Email</label>
                          <input type="text" id="email_" name="company_email_address" class="form-control" placeholder="Company Email" value="{{old('company_email_address',$company->company_email_address)}}"  >
                          <span id="error_email"></span>

                        </div>
                          </div>
                      <div class="col-md-6">
                          <div class="form-group">
                            <label class="form-label">Password </label>
                            <input type="Password" onkeyup="validatePassLength()" oninput="checkPasswordComplexity(this.value)" id="password"  name="company_password" class="form-control" placeholder="Password" value="{{old('company_password')}}">
                                                <p id="showpassmessage"><p>
                                                <p id="showpassmessage2"><p>

                        </div>
                     </div>
                  <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Confirm Password </label>
                    <input type="password"  class="form-control"  id="confirm_password"
                   onkeyup="validatePass()" name="password_confirmation" value="{{old('password_confirmation')}}"  placeholder="Confirm Password">
                                                    <p id="showmessage"><p>


                    </div>
                     </div>
                      <div class="col-md-6">
                     <div class="form-group">
                    <label class="form-label">Business Type</label>
                    <select name="business_type_id" required="" class="form-control" >
                      <option value=""> Select Business Type</option>
                      @foreach($business_types as $key)
                       <option {{old('business_type_id',$company->business_type_id) == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id}}"> {{$key->business_type_name }} </option>
                        @endforeach
                      </select>
                  </div>
                  </div>

                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Address</label>
                           <textarea class="form-control"  name="company_primary_address"
                           rows="4" placeholder="Address">{{old('company_primary_address',$company->company_primary_address)}}</textarea>
                        </div>

                     </div>
                  </div>

                         <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_company') }}">Cancel</a>
                           </center>
                        </div>
                  </form>
                </div>

         {{--    </div>
         </div> --}}
      </div>
   </div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
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
           // document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
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

<script type="text/javascript">
    $(document).ready(function() {
        var coc = 0;
       $('#country').change(function(){
           if(coc != 0)
           {


       /* $('#city').empty();
         $('#city').append('<option value="">Select City</option>');*/
        var country_id = $(this).val();

        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_state') }}?country_id="+country_id,


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

        }
           else
           {
               coc++;
           }
      });

    });
    $(document).ready(function() {

  var sc =0;

       $('#state').change(function(){
            if(sc != 0)
           {

        var state_id = $(this).val();
        //alert(state_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_city') }}?state_id="+state_id ,

          success:function(res){

            if(res){
            $('#city').prop("diabled",false);
            $('#city').empty();

            $('#city').append('<option value="">Select District</option>');
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

        }
           else
           {
               sc++;
           }

      });

    });


 $(document).ready(function(){
  $("#email").blur(function(){
   var error_email = '';
  var company_email_address = $(this).val();
  //alert(company_email_address);
  var _token = $('input[name="_token"]').val();
 var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
  if(!filter.test(company_email_address))
  {
   $('#error_email').html('<label class="text-danger">Invalid Email</label>');
   $('#email').addClass('has-error');
   $('#submit').attr('disabled', 'disabled');
  }
     $.ajax({
    url:"{{ route('admin.unique_email_company') }}",
    method:"POST",
    data:{company_email_address:company_email_address, _token:_token},
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
  var company_username = $(this).val();
  //alert(company_email_address);
  var _token = $('input[name="_token"]').val();
  $.ajax({
    url:"{{ route('admin.unique_username_company') }}",
    method:"POST",
    data:{company_username:company_username, _token:_token},
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
