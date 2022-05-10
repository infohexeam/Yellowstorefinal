@extends('store.layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height: 70vh;" >
      <div class="col-md-12">
         <div class="card" >
            <div class="card-header">
               <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
            </div>
            <div class="card-body" >
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
               <form action="{{route('store.store_store_admin')}}" id="myForm" method="POST"  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Name *</label>
                                <input type="text" required class="form-control" name="admin_name" value="{{old('admin_name')}}" placeholder="Name">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Phone *</label>
                                <input type="text" required class="form-control"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="phone" value="{{old('phone')}}" placeholder="Phone">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Email</label>
                                <input type="email" class="form-control" name="email" value="{{old('email')}}" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="form-label">Username *</label>
                                <input type="text" required=""  name="username" class="form-control"  value="{{old('username')}}" placeholder="Username">
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="form-label">Role *</label>
                                 <select required=""  name="role_id"  class="form-control"  >
                                    <option  value="">Role</option>
                                    <option  {{old('role_id') == '2' ? 'selected':''}} value="2">Admin</option>
                                    <option {{old('role_id') == '3' ? 'selected':''}} value="3">Manager</option>
                                    <option {{old('role_id') == '4' ? 'selected':''}} value="4">Staff</option>
                                 </select>
                              </div>
                        </div>

               <div class="col-md-6">
                  <div class="form-group">
                     <label class="form-label">Password *</label>
                          <input type="Password" required="" name="password" oninput="checkPasswordComplexity(this.value)" onkeyup="validatePassLength()"  id="password" class="form-control" placeholder=" Password" value="{{old('password')}}">
 <p id="showpassmessage"><p>
                            <p id="showpassmessage2"><p>
                  </div>

                  </div>
                   <div class="col-md-6">
                  <div class="form-group">

                    <label class="form-label">Confirm Password *</label>
                    <input type="password"  class="form-control"
                    name="password_confirmation" required id="confirm_password" onkeyup="validatePass()" placeholder="Confirm Password">
                            <p id="showmessage"><p>

                  </div>
                  </div>
                                    <div class="col-md-2">
                                   <br> <br>
                                	<label class="custom-switch">
                                                        <input type="hidden" name="status" value=0 />
														<input type="checkbox" name="status"  checked value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Active Status</span>
													</label>
                            </div>
                  </div>
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('store.store_admin') }}">Cancel</a>
                           </center>
                        </div>
               </form>

         </div>
      </div>
   </div>
</div>



<script type="text/javascript">


function checkPasswordComplexity(pwd) {
 var re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/

    if(pwd != '')  
    {
        
      if(re.test(pwd) == false)
      {
           document.getElementById('showpassmessage2').style.color = 'red';
          //  document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
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


 @endsection
