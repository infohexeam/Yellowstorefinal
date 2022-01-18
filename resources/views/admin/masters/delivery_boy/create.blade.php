@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;">
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
               <form id="myForm" action="{{route('admin.store_delivery_boy')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Delivery Boy Name *</label>
                           <input type="text" class="form-control" required name="delivery_boy_name" value="{{old('delivery_boy_name')}}" placeholder="Delivery Boy Name">
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Mobile Number *</label>
                            <input type="text" required=""  name="delivery_boy_mobile" class="form-control"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  value="{{old('delivery_boy_mobile')}}" placeholder="Mobile Number">
                           </div>
                        </div>
                    <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Email </label>
                            <input type="email"  name="delivery_boy_email" class="form-control"  value="{{old('delivery_boy_email')}}" placeholder="Email">
                        </div>
                     </div>
                    <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Vehicle Number *</label>
                            <input type="text" required  name="vehicle_number" class="form-control" value="{{old('vehicle_number')}}" placeholder="Vehicle Number">
                           </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Store </label>
                            <select name="store_id"  class="form-control" >
                                 <option value=""> Select Store</option>
                                @foreach($stores as $key)
                                <option {{old('store_id') == $key->store_id ? 'selected':''}} value="{{$key->store_id}}"> {{$key->store_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div>


                    <div class="col-md-6">
                        <div class="form-group">
                         <label class="form-label">Country *</label>
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
                            <label class="form-label">State *</label>
                            <select name="state_id" required="" class="form-control" id="state" >
                                 <option  value=""> Select State</option>
                                @if(old('state_id'))

                                @php
                                    $states = \DB::table('sys_states')->where('country_id',old('country_id'))->get();
                                @endphp

                                @foreach ($states as $key)
                                        <option {{ old('state_id') == $key->state_id ? 'selected':''}} value="{{ $key->state_id }}">{{ $key->state_name }}</option>
                                @endforeach

                                @endif


                              </select>
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District *</label>
                          <select name="district_id" required="" class="form-control" id="city">
                             <option value="">Select District</option>
                             @if(old('district_id'))

                                @php
                                    $districts = \DB::table('mst_districts')->where('state_id',old('state_id'))->get();
                                @endphp

                                @foreach ($districts as $key)
                                        <option {{ old('district_id') == $key->district_id ? 'selected':''}} value="{{ $key->district_id }}">{{ $key->district_name }}</option>
                                @endforeach

                                @endif
                          </select>

                       </div>
                     </div>

                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="form-label">Town *</label>
                              <select name="town_id" required="" class="form-control" id="town">
                                 <option value="">Select Town</option>
                                  @if(old('town_id'))

                                @php
                                    $towns = \DB::table('mst_towns')->where('district_id',old('district_id'))->get();
                                @endphp

                                @foreach ($towns as $key)
                                        <option {{ old('town_id') == $key->town_id ? 'selected':''}} value="{{ $key->town_id }}">{{ $key->town_name }}</option>
                                @endforeach

                                @endif
                              </select>
                           </div>
                        </div>


                       <div class="col-md-6">
                        <div class="form-group">
                              <label class="form-label">Username *</label>
                          <input type="text"  required id="username" name="delivery_boy_username" class="form-control" placeholder="Username" value="{{old('delivery_boy_username')}}">
                           <span id="error_username"></span>

                       </div>
                     </div>


                       <div class="col-md-6">
                        <div class="form-group">
                    <label class="form-label">Vehicle Type *</label>
                                <select name="vehicle_type_id" required="" class="form-control"  >
                                <option value=""> Select Vehicle Type</option>
                                @foreach($vehicle_types as $key)
                                <option {{old('vehicle_type_id') == $key->vehicle_type_id  ? 'selected':''}} value="{{$key->vehicle_type_id }}"> {{$key->vehicle_type_name }} </option>
                                @endforeach
                              </select>

                       </div>
                     </div>


                       <div class="col-md-6">
                        <div class="form-group">
                      <label class="form-label">Image [1000*800] [in png, jpeg or jpg]</label>
                          <input type="file"  accept="image/x-png,image/jpg,image/jpeg" name="delivery_boy_image" class="form-control" placeholder="delivery_boy Logo" value="{{old('delivery_boy_image')}}">

                       </div>
                     </div>




                       <div class="col-md-6">
                   <label class="form-label"> Password *</label>
                            <input class="form-control" type="password" oninput="checkPasswordComplexity(this.value)" onkeyup="validatePassLength()" name="delivery_boy_password" value="{{old('delivery_boy_password')}}" placeholder="Password" id="password" type="password" required autocomplete="current-password">
                            <p id="showpassmessage"><p>
                            <p id="showpassmessage2"><p>
                       </div>

                        <div class="col-md-6">
                      <label class="form-label">Confirm Password *</label>
                            <input class="form-control" type="password" onkeyup="validatePass()" name="password_confirmation" placeholder="Confirm Password" value="{{old('password_confirmation')}}" id="confirm_password" type="password" required autocomplete="current-password">
                            <p id="showmessage"><p>
                       </div>

                           <div class="col-md-6">
                        <div class="form-group">
                  <label class="form-label">Commission Amount (Monthly) *</label>
                      <input type="number" step="0.1" required="" name="delivery_boy_commision" class="form-control" placeholder="Commission Amount (Monthly)" value="{{old('delivery_boy_commision')}}">

                         </div>
                     </div>

                          <div class="col-md-6">
                        <div class="form-group">
   <label class="form-label">Commission Amount (Per Order) *</label>
                         <input type="number" step="0.1" required="" name="delivery_boy_commision_amount" class="form-control" placeholder="Commission Amount (Per Order)" value="{{old('delivery_boy_commision_amount')}}">

                         </div>
                     </div>


                          <div class="col-md-6">
                        <div class="form-group">
     <label class="form-label">Availabilities *</label>

                           @foreach($availabilities as $key)
                           <input type="checkbox" name="delivery_boy_availability_id[{{ $loop->iteration}}]"
                             {{ (is_array(old('delivery_boy_availability_id')) && in_array($key->availability_id, old('delivery_boy_availability_id'))) ? ' checked' : '' }} name="delivery_boy_availability_id[{{$loop->iteration}}]" value="{{$key->availability_id}}" > {{$key->availabilable_days}}
                           @endforeach
                         </div>
                     </div>



                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label"> Address *</label>
                           <textarea class="form-control"  name="delivery_boy_address" required
                           rows="4" placeholder=" Address">{{old('delivery_boy_address')}}</textarea>
                        </div>
                     </div>
                </div>



                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_delivery_boy') }}">Cancel</a>
                           </center>
                        </div>
               </form>
           {{--  </div>
         </div> --}}
      </div>
   </div>
   </div>
</div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

@endsection





<script type="text/javascript">


function checkPasswordComplexity(pwd) {
 //var re = /^(?=.*\d)(?=.*[a-z])(.{8,50})$/
 var re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/

    if(pwd != '')  
    {
        
      if(re.test(pwd) == false)
      {
           document.getElementById('showpassmessage2').style.color = 'red';
           // document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
                   document.getElementById('showpassmessage2').innerHTML = 'Password must include at least one upper case letter, lower case letter, number, and special character';
                         $('#submit').attr('disabled', 'disabled');
   validatePass();
      }
      else
      {
             document.getElementById('showpassmessage2').innerHTML = '';
                        $('#submit').attr('disabled', false);
    validatePass();
      }
    }
    else
    {
           document.getElementById('showpassmessage2').innerHTML = '';
                        $('#submit').attr('disabled', false);
      validatePass();

    }
}



	function mobileValidation(){
	var number= document.forms["myForm"]["store_mobile_"].value;///get id with value
	var numberpattern=/^\+?([0-9]{2})\)?[-. ]?([0-9]{4})[-. ]?([0-9]{4})$/;////Regular expression
	if(numberpattern.test(number))
	   {
		// document.forms["myForm"]["store_mobile"].style.backgroundColor='yellow';
                 var _token= $('input[name="_token"]').val();
            $.ajax({
                url:"{{ route('store.unique_store_mobile') }}",
                method:"POST",
                data:{number:number, _token:_token},
                success:function(result)
                {
                //alert("dszczs");
                if(result == 'unique')
                {
                                $('#error_store_mobile').empty();
                                       $('#submit').attr('disabled', false);

                }
                else
                {
                $('#error_store_mobile').html('<label class="text-danger">Mobile Number Already Exist </label>');
            // $('#email').addClass('has-error');
                $('#submit').attr('disabled', 'disabled');

            }
            }
        });

       }
    else
      {
    	//document.forms["myForm"]["store_mobile"].style.backgroundColor='red';
      }
	}

</script>




<script>
function validatePass() {
  var x = document.forms["myForm"]["password"].value;
  var y = document.forms["myForm"]["confirm_password"].value;
   document.getElementById('showmessage').innerHTML = '';
   if(y != '')
   {
       
 
    if (x == y) {
   // document.getElementById('password').border.color = 'green';
    //document.getElementById('confirm_password').border.color = 'green';
                                $('#submit').attr('disabled',false);


    } else {
        document.getElementById('showmessage').style.color = 'red';
        document.getElementById('showmessage').innerHTML = 'Passwords not matching';
                                    $('#submit').attr('disabled', 'disabled');

        
    }
   }
}
</script>

<script>
function validatePassLength() {
  var x = document.forms["myForm"]["password"].value;
   if(x != '')
{
    if(x.length < 8)
   {
     document.getElementById('showpassmessage').style.color = 'red';
            document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 digits!';
   }
   else
   {
                   document.getElementById('showpassmessage').innerHTML = '';

                              //  $('#submit').attr('disabled',false);
   }
}
else
{
                       document.getElementById('showpassmessage').innerHTML = '';

}

}
</script>



<script>
function validateForm() {
  var x = document.forms["myForm"]["password"].value;
  var y = document.forms["myForm"]["confirm_password"].value;
  if(y != '')
  {
   if(x.length >= 8)
    {
        if (x != y) {
            document.getElementById('showmessage').style.color = 'red';
            document.getElementById('showmessage').innerHTML = 'Passwords not matching';
            var elmnt = document.getElementById("passlabel");
            elmnt.scrollIntoView();
            return false;
                                       $('#submit').attr('disabled', 'disabled');

        }
    }
    else
    {
           document.getElementById('showpassmessage').style.color = 'red';
            document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 digits!';
            var elmnt = document.getElementById("passlabel");
            elmnt.scrollIntoView();
            return false;
    }
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
        var sc = 0;
       $('#state').change(function(){
           if(sc != 0){


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
  }else
           {
                sc++;
           }

      });

    });



    //display town

    $(document).ready(function() {
        var cc = 0;
       $('#city').change(function(){
           if(cc != 0)
           {


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

            $('#town').append('<option value="">Select Town</option>');
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

  }
           else
           {
               cc++;
           }

      });

    });

</script>
