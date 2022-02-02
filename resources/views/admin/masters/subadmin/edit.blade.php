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
               <form id="myForm" action="{{route('admin.update_subadmin',$subadmin->id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">

                      <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Name *</label>
                           <input type="text" required class="form-control" name="admin_name" value="{{old('admin_name',$subadmin->admin_name)}}" placeholder=" Name">
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> Username *</label>
                           <input type="text" class="form-control" required name="name" value="{{old('name',$subadmin->name)}}" placeholder=" Name">
                        </div>
                     </div>
                     <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Email</label>
                           <input type="email" class="form-control"
                           name="email" value="{{old('email',$subadmin->email)}}" placeholder="Email">
                        </div>
                     </div>
                        <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Phone *</label>
                           <input type="number" class="form-control"
                           name="phone" value="{{old('phone',@$subadmin->subadmins['phone'])}}" required placeholder="Phone">
                        </div>
                     </div>

                     <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label"> Commission Percentage (Per Order) *</label>
                          <input type="number" step="0.1"  required name="subadmin_commision_percentage" class="form-control" placeholder="Commission Percentage (Per Order)" value="{{old('subadmin_commision_percentage',@$subadmin_details->subadmin_commision_percentage)}}">

                        </div>
                      </div>

                      <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Commission Amount (Monthly) *</label>
                          <input type="number" step="0.1"  required name="subadmin_commision_amount"  class="form-control" placeholder="Commission Amount (Monthly)" value="{{old('subadmin_commision_amount',@$subadmin_details->subadmin_commision_amount)}}">
                        </div>
                      </div>


                      <div class="col-md-6">
                       <div class="form-group">
                           <label class="form-label">Country *</label>
                            <select name="country_id" required="" class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{old('country_id',@$subadmin->subadmins['country_id']) == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                        </div>
                      </div>

                       <div class="col-md-6">
                       <div class="form-group">
                           <label class="form-label">State *</label>
                           @php
                               @$states_data = \DB::table('sys_states')->where('country_id',@$store->subadmins['country_id'])->get();
                           @endphp
                            <select name="state_id" required="" class="form-control" id="state" >
                                <option  value="{{@$subadmin->subadmins['state_id']}}">  {{@$subadmin->subadmins->state['state_name']}} </option>
                             {{-- @foreach ($states_data as $value)
                                <option  @if ($subadmin->subadmins['state_id'] == $value->state_id)  selected  @endif  value="{{@$value->state_id}}">  {{@$value->state_name}}</option>
                             @endforeach --}}
                            </select>
                           </div>
                      </div>
{{-- {{@$subadmin->subadmins}} --}}
                       <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District *</label>
                          @php
                               @$district_data = \DB::table('mst_districts')->where('state_id',@$subadmin->subadmins['state_id'])->get();
                           @endphp
                           <select name="district_id" required="" class="form-control" id="city">
                                <option value="{{@$subadmin->subadmins['district_id']}}">  {{@$subadmin->subadmins->district['district_name']}}</option>

                           {{-- @foreach (@$district_data as $value)
                                <option @if ($subadmin->subadmins['district_id'] == $value->district_id)  selected  @endif  value="{{@$value->district_id}}">  {{@$value->district_name}}</option>

                             @endforeach --}}

                          </select>
                        </div>
                      </div>

                       <div class="col-md-6">
                        <div class="form-group">
                              <label class="form-label">Town *</label>
                              <select name="town_id" required="" class="form-control" id="town">
                                 <option  selected="" value="{{@$subadmin->subadmins['town_id']}}"> {{@$subadmin->subadmins->town['town_name']}}</option>
                              </select>
                           </div>
                      </div>


                        <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Password </label>
                           <input type="password" oninput="checkPasswordComplexity(this.value)" class="form-control" onkeyup="validatePassLength()"  id="password"
                           name="password" value="{{old('password')}}" placeholder="Password">
                        <p id="showpassmessage"><p>
                        <p id="showpassmessage2"><p>
                        </div>
                     </div>
                       <div class="col-md-6">
                          <div class="form-group">

                           <label class="form-label">Confirm Password </label>
                           <input type="password" class="form-control"   onkeyup="validatePass()"  id="confirm_password"
                           name="password_confirmation" value="{{old('password_confirmation')}}" placeholder="Confirm Password">
                                <p id="showmessage"><p>
                        </div>
                     </div>

                        <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label"> Address *</label>
                           <textarea class="form-control"  name="subadmin_address" required
                           rows="4" placeholder=" Address">{{old('subadmin_address',@$subadmin_details->subadmin_address)}}</textarea>
                        </div>
                     </div>
                  </div>


                        <div class="form-group">
                           <center>
                           <button type="submit"  id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_subadmin') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                  </div>

               </form>
         {{--    </div>
         </div> --}}
      </div>
   </div>
</div>


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

            $('#state').append('<option  value=""> Select State </option>');
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
    if(sc != 0)
    {
        var state_id = $(this).val();
        //alert(state_id);
        var _token= $('input[name="_token"]').val();
   //$('#city').prop("diabled",false);
         //    $('#city').empty();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_city') }}?state_id="+state_id ,

          success:function(res){

            if(res){
            $('#city').prop("diabled",false);
            $('#city').empty();

            $('#city').append('<option value=""> Select District </option>');
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

            $('#town').append('<option  selected="" value=""> Select Town </option>');
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
@endsection
