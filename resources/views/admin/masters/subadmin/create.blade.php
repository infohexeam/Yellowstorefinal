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
               <form id="myForm" action="{{route('admin.store_subadmin')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Name *</label>
                           <input type="text" required class="form-control" name="admin_name" value="{{old('admin_name')}}" placeholder=" Name">
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Username *</label>
                           <input type="text" required class="form-control" name="name" value="{{old('name')}}" placeholder=" Username">
                        </div>
                     </div>



                     <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Email</label>
                           <input type="email" class="form-control"
                           name="email" value="{{old('email')}}" placeholder="Email">
                        </div>
                     </div>

                       <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Phone *</label>
                           <input type="number" class="form-control" required
                           name="phone" value="{{old('phone')}}" placeholder="Phone">
                        </div>
                     </div>



                     <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label"> Commission Percentage (Per Order) *</label>
                          <input type="number" step="0.1"  required name="subadmin_commision_percentage" class="form-control" placeholder="Commission Percentage (Per Order)" value="{{old('subadmin_commision_percentage')}}">

                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label"> Commission Amount (Monthly) *</label>
                          <input type="number" step="0.1"  required name="subadmin_commision_amount" class="form-control" placeholder="Commission Amount (Monthly)" value="{{old('subadmin_commision_amount')}}">
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

                              </select>
                           </div>
                      </div>

                       <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District *</label>
                          <select name="district_id" required="" class="form-control" id="city">
                             <option value="">Select District</option>
                          </select>
                        </div>
                      </div>

                       <div class="col-md-6">
                        <div class="form-group">
                              <label class="form-label">Pincode *</label>
                              <select name="town_id" required="" class="form-control" id="town">
                                 <option value="">Select Pincode</option>
                              </select>
                           </div>
                      </div>


                     <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Password *</label>
                           <input type="password" oninput="checkPasswordComplexity(this.value)" class="form-control" required onkeyup="validatePassLength()"  id="password"
                           name="password" value="{{old('password')}}" placeholder="Password">
                        <p id="showpassmessage"><p>
                        <p id="showpassmessage2"><p>
                        </div>
                     </div>
                       <div class="col-md-6">
                          <div class="form-group">

                           <label class="form-label">Confirm Password *</label>
                           <input type="password" class="form-control" required  onkeyup="validatePass()"  id="confirm_password"
                           name="password_confirmation" value="{{old('password_confirmation')}}" placeholder="Confirm Password">
                                <p id="showmessage"><p>
                        </div>
                     </div>
                  </div>
                  <div class="col-md-12">
                     <div class="form-group">
                        <label class="form-label"> Address *</label>
                        <textarea class="form-control" required  name="subadmin_address"
                        rows="4" placeholder=" Address">{{old('subadmin_address')}}</textarea>
                     </div>
                  </div>

                        <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_subadmin') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>

               </form>
         {{-- {{--  </div> --}}
         </div>
      </div>
   </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
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
  //          document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
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
       $('#country').change(function(){
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
      });

    });
    $(document).ready(function() {
       $('#state').change(function(){
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


    //display town

    $(document).ready(function() {
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

</script>
@endsection
