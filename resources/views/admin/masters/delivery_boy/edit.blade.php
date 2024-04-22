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
               <form id="myForm" action="{{route('admin.update_delivery_boy',$delivery_boy->delivery_boy_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Delivery Boy Name</label>
                           <input type="hidden" name="delivery_boy_id" value="{{$delivery_boy->delivery_boy_id}}">
                           <input type="text" required class="form-control" name="delivery_boy_name" value="{{old('delivery_boy_name',$delivery_boy->delivery_boy_name)}}" placeholder="Delivery Boy Name">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Mobile Number</label>
                            <input type="text" required=""  name="delivery_boy_mobile" class="form-control" readonly=""  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')"  value="{{old('delivery_boy_mobile',$delivery_boy->delivery_boy_mobile)}}" placeholder="Mobile Number">
                           </div>
                        </div>
                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Email</label>
                            <input type="email"  name="delivery_boy_email" class="form-control"  value="{{old('delivery_boy_email',$delivery_boy->delivery_boy_email)}}" placeholder="Email">
                        </div>
                         <div class="form-group">
                           <label class="form-label">Vehicle Number</label>
                            <input type="text" required name="vehicle_number" class="form-control" value="{{old('vehicle_number',$delivery_boy->vehicle_number)}}" placeholder="Vehicle Number">
                           </div>
                        </div>
                           <div class="col-md-6">
                        {{-- <div class="form-group">
                           <label class="form-label">Store</label>
                            <select name="store_id" required="" class="form-control" >
                                 <option value=""> Select Store</option>
                                @foreach($stores as $key)
                                <option {{old('store_id',$delivery_boy->store_id) == $key->store_id ? 'selected':''}} value="{{$key->store_id}}"> {{$key->store_name }} </option>
                                @endforeach
                              </select>
                        </div> --}}
                         <div class="form-group">
                           <label class="form-label">Country</label>
                            <select name="country_id" required="" class="form-control select2-show-search" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{old('country_id',@$delivery_boy->country_id) == @$key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                           </div>
                        </div>


                      <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">State</label>
                            <select name="state_id" required="" class="form-control select2-show-search" id="state" >
                            @foreach($states as $state)
                             <option   @if($state->state_id==$delivery_boy->state_id) selected="" @endif value="{{$state->state_id}}" >  {{@$state->state_name}}</option>
                             @endforeach

                              </select>

                           </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District</label>
                    <select name="district_id" required="" class="form-control select2-show-search" id="city">
                    @foreach($districts as $district)
                            <option   @if($district->district_id==$delivery_boy->district_id) selected=""  @endif value="{{$district->district_id}}">  {{@$district->district_name}}</option>
                    @endforeach
                          </select>
                        </div>
</div>
                         <div class="col-md-6">

  <div class="form-group">
                              <label class="form-label">Pincode</label>
                              <select name="town_id"  class="form-control select2-show-search" id="town">
                                @foreach($towns as $town)
                                 <option  @if($town->town_id==$delivery_boy->town_id) selected=""  @endif  value="{{@$town->town_id}}">  {{@$town->town_name}}</option>
                                 @endforeach
                              </select>
                           </div>
</div>
    <div class="col-md-6">
                        <div class="form-group">
                       <label class="form-label">Image [1000*800] [in png, jpeg or jpg]</label>
                          <input type="file"  name="delivery_boy_image"  accept="image/x-png,image/jpg,image/jpeg"  class="form-control" placeholder="delivery_boy Logo" value="{{old('delivery_boy_image',@$delivery_boy->delivery_boy_image)}}">
                        </div>
                        </div>
                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Username</label>
                          <input type="text" readonly=""  required id="username" name="delivery_boy_username" class="form-control" placeholder="Username" value="{{old('delivery_boy_username',@$delivery_boy->delivery_boy_username)}}">
                           <img src="{{asset('/assets/uploads/delivery_boy/images/'.@$delivery_boy->delivery_boy_image)}}"  width="100" style="height:60px" "width :50px">
                           <span id="error_username"></span>
                       </div>
                        </div>
                           <div class="col-md-6">
                          <div class="form-group">
                             <label class="form-label">Vehicle Type</label>
                    <select name="vehicle_type_id" required="" class="form-control"  >
                    <option value=""> Select Vehicle Type</option>
                       @foreach($vehicle_types as $key)
                       <option {{old('vehicle_type_id',@$delivery_boy->vehicle_type_id) == @$key->vehicle_type_id  ? 'selected':''}} value="{{$key->vehicle_type_id }}"> {{$key->vehicle_type_name }} </option>
                                @endforeach
                              </select>

                        </div>
</div>


                       <div class="col-md-6">
                   <label class="form-label"> Password </label>
                   <div class="password-show">
                            <input class="form-control" type="password" onkeyup="validatePassLength()" oninput="checkPasswordComplexity(this.value)" name="password" value="{{old('password')}}" placeholder="Password" id="password" type="password"  autocomplete="current-password">
                             <div class="password-show__toggle">
                              <i class="fa fa-eye password-show_toggle_show-icon"></i>
                              <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                            </div>
                            </div>
                            <p id="showpassmessage"><p>
                            <p id="showpassmessage2"><p>
                       </div>

                        <div class="col-md-6">
                      <label class="form-label">Confirm Password</label>
                      <div class="password-show">
                            <input class="form-control" type="password" onkeyup="validatePass()" name="password_confirmation" placeholder="Confirm Password" value="{{old('password_confirmation')}}" id="confirm_password" type="password"  autocomplete="current-password">
                            <div class="password-show__toggle">
                              <i class="fa fa-eye password-show_toggle_show-icon"></i>
                              <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                            </div>
                            </div>
                            <p id="showmessage"><p>
                       </div>


              <div class="col-md-6">
                  <div class="form-group">
                    <label class="form-label">Commission Amount (Monthly)*</label>
                      <input type="decimal" min="0" step="0.1"  name="delivery_boy_commision" class="form-control" placeholder="Commision Amount (Monthly)" value="{{old('delivery_boy_commision',@$delivery_boy->delivery_boy_commision)}}">
                    </div>

                  </div>
                     <div class="col-md-6">
                      <div class="form-group">
                       <label class="form-label">Commission Amount (Per Order)*</label>
                         <input type="number" min="0" step="0.1"  name="delivery_boy_commision_amount" class="form-control" placeholder="Commission Amount (Per Order)" value="{{old('delivery_boy_commision_amount',@$delivery_boy->delivery_boy_commision_amount)}}">
                       </div>
                     </div>
                     </div>
                  {{-- <div class="col-md-10">
                     <div class="form-group">
                        <label class="form-label">Availabilities</label>
                      @php
                          $available_arrat =    explode(',',$delivery_boy->delivery_boy_availability_id);

                      @endphp
                           @foreach($availabilities as $key)
                           <input type="checkbox"
                           @if (in_array($key->availability_id,$available_arrat))
                               checked
                           @endif

                           name="delivery_boy_availability_id[{{ $loop->iteration}}]"
                             {{ (is_array(old('delivery_boy_availability_id')) && in_array($key->availability_id, old('delivery_boy_availability_id'))) ? ' checked' : '' }} name="delivery_boy_availability_id[{{$loop->iteration}}]" value="{{$key->availability_id}}" > {{$key->availabilable_days}}

                           @endforeach

                     </div>
               </div> --}}
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label"> Address</label>
                           <textarea class="form-control"  name="delivery_boy_address"  rows="4" placeholder=" Address">{{old('delivery_boy_address',$delivery_boy->delivery_boy_address)}}</textarea>
                        </div>

                     </div>
                  </div>

                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
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
@endsection
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


function validatePass() {
  var x = document.forms["myForm"]["password"].value;
  var y = document.forms["myForm"]["confirm_password"].value;
   document.getElementById('showmessage').innerHTML = '';
   
   if(y != '')
   {
       
  
   
    if (x == y) {
   // document.getElementById('password').border.color = 'green';
    //document.getElementById('confirm_password').border.color = 'green';


    } else {
        document.getElementById('showmessage').style.color = 'red';
        document.getElementById('showmessage').innerHTML = 'Passwords not matching';
    }
   }
}
</script>

<script>
function validatePassLength() {
  var x = document.forms["myForm"]["password"].value;
  if(x != '')
  {
      //alert(x);
   if(x.length < 8)
   {
     document.getElementById('showpassmessage').style.color = 'red';
            document.getElementById('showpassmessage').innerHTML = 'You have to enter at least 8 characters!';
   }
   else
   {
                   document.getElementById('showpassmessage').innerHTML = '';

   }
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
      //////////////////////////////////////////////
       $('#city').change(function(){
        var city_id = $(this).val();
       //alert(city_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_town') }}?city_id="+city_id ,

          success:function(res){

           if(res){
              console.log(res);
            $('#town').prop("disabled",false);
            $('#town').empty();
            $('#city').val(city_id);

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
    

      ///////////////////////////////////////////////


    });
       });



    //display town

    
      


</script>
