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
               <form action="{{route('admin.update_customer',$customer->customer_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">First Name</label>
                           <input type="hidden" name="customer_id" value="{{$customer->customer_id}}">
                           <input type="text" required class="form-control" name="customer_first_name"  value="{{old('customer_first_name',$customer->customer_first_name)}}" placeholder="Customer Name">
                        </div>
                        </div>

                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Last Name</label>
                            <input type="text" value="{{old('customer_last_name',$customer->customer_last_name)}}"  name="customer_last_name" class="form-control" placeholder="Last Name">

                        </div>
                        </div>

                     <div class="col-md-6">
                         <div class="form-group">
                             <label class="form-label">Bank Account</label>
                           <input type="text" class="form-control" name="customer_bank_account" value="{{old('customer_bank_account',$customer->customer_bank_account)}}"  placeholder="Bank Account">
                           </div>
                        </div>


                           <div class="col-md-6">
                         <div class="form-group">
                             <label class="form-label">Customer Mobile</label>
                            <input type="text" readonly maxlength="10" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="customer_mobile_number" class="form-control" value="{{old('customer_mobile_number',$customer->customer_mobile_number)}}" placeholder="Customer Mobile">
                           </div>
                        </div>

 <div class="col-md-6">
                         <div class="form-group">
                             <label class="form-label">Customer Email</label>
                           <input type="email" class="form-control" name="customer_email" value="{{old('customer_email',$customer->customer_email)}}"  placeholder="Email">
                           </div>
                        </div>

                           <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Pincode</label>
                           <input type="text"  class="form-control" name="customer_pincode"  value="{{old('customer_pincode',$customer->customer_pincode)}}" placeholder="Pincode">
                        </div>
                        </div>


                              <div class="col-md-6">
                        <div class="form-group">

                           <label class="form-label">Country</label>
                             <select name="country_id" class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{old('country_id',$customer->country_id) == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>

                        </div>
                        </div>


                           <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">State</label>
                            <select name="state_id"  class="form-control" id="state" >
                                <option  selected=""  value="{{$customer->state_id}}">  {{@$customer->state->state_name}}</option>

                              </select>
                           </div>
                        </div>

                              <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District</label>
                           <select name="district_id"  class="form-control" id="city">

                                <option  selected=""  value="{{$customer->district_id}}">  {{@$customer->district->district_name}}</option>

                          </select>
                        </div>
                      </div>

                       <div class="col-md-6">
                        <div class="form-group">
                              <label class="form-label">Town</label>
                              <select name="town_id" class="form-control" id="town">
                                <option  selected=""  value="{{$customer->town_id}}">  {{@$customer->town->town_name}}</option>
                              </select>
                           </div>
                      </div>


                         <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Location</label>
                           <input type="text"  class="form-control" name="customer_location"  value="{{old('customer_location',$customer->customer_location)}}" placeholder="Location">

                       </div>
                        </div>
                          <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label"> User Name</label>
                          <input type="text"   id="username" name="customer_username" class="form-control" placeholder="UserName" value="{{old('customer_username',$customer->customer_username)}}">
                           <span id="error_username"></span>


                        </div>
                        </div>
                          <div class="col-md-6">
                          <div class="form-group">
                            <label class="form-label">Email</label>
                          <input type="email" id="email"  name="customer_email" class="form-control" placeholder="Email" value="{{old('customer_email',$customer->customer_email)}}">
                           <span id="error_email"></span>

                        </div>
                     </div>
                  <div class="col-md-6">
                  <div class="form-group">
                       <label class="form-label"> Password</label>
                          <input type="Password" name="password" class="form-control" placeholder="Password" value="{{old('password')}}">

                    </div>
                    </div>
                  <div class="col-md-6">
                   <div class="form-group">
                     <label class="form-label">Confirm Password</label>
                    <input type="password"   class="form-control"
                    name="password_confirmation"  placeholder="Confirm Password">

                        </div>

                    </div>
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label"> Address</label>
                           <textarea class="form-control"  name="customer_address"
                           rows="4" placeholder="Primary Address">{{old('customer_address',$customer->customer_address)}}</textarea>
                        </div>

                     </div>
                  </div>

                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_customer') }}">Cancel</a>
                           </center>
                        </div>
               </form>
           {{--  </div>
         </div> --}}
      </div>
   </div>
   </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script type="text/javascript">
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
      });


</script>
@endsection

