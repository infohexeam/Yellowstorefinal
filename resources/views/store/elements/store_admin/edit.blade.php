@extends('store.layouts.app')
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
   <div class="row" style="min-height: 70vh;" >
      <div class="col-md-12">
         <div class="card" >
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
               <form id="myForm" action="{{route('store.update_store_admin',$store_admin->store_admin_id)}}" method="POST"  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                      
                       <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Name *</label>
                                <input type="text" required class="form-control" name="admin_name" value="{{old('admin_name',$store_admin->admin_name)}}" placeholder="Name">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Phone *</label>
                                <input type="text" required class="form-control"  onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="phone" value="{{old('phone',$store_admin->store_mobile)}}" placeholder="Phone">
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"> Email</label>
                                <input type="email" class="form-control" name="email" value="{{old('email',$store_admin->email)}}" placeholder="Email">
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="form-label">Username *</label>
                                <input type="text" required=""  name="username" class="form-control"  value="{{old('username',$store_admin->username)}}" placeholder="Username">
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="form-group">
                                <label class="form-label">Role *</label>
                                 <select required=""  name="role_id"  class="form-control"  >
                                    <option  value="">Role</option>
                                    <option  {{old('role_id',$store_admin->role_id) == '2' ? 'selected':''}} value="2">Admin</option>
                                    <option {{old('role_id',$store_admin->role_id) == '3' ? 'selected':''}} value="3">Manager</option>
                                    <option {{old('role_id',$store_admin->role_id) == '4' ? 'selected':''}} value="4">Staff</option>
                                 </select>
                              </div>
                        </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="password-show">
                            <input type="Password" oninput="checkPasswordComplexity(this.value)" id="password" onkeyup="validatePassLength()" name="password" class="form-control" placeholder=" Password" value="">
                             <div class="password-show__toggle">
                              <i class="fa fa-eye password-show_toggle_show-icon"></i>
                              <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                            </div>
                            </div>
 <p id="showpassmessage"><p>
                            <p id="showpassmessage2"><p>
                    </div>

                  </div>
                   <div class="col-md-6">
                  <div class="form-group">

                    <label class="form-label">Confirm Password</label>
                    <div class="password-show">
                    <input type="password"  class="form-control" id="confirm_password"
                    name="password_confirmation"  onkeyup="validatePass()"  placeholder="Confirm Password">
                     <div class="password-show__toggle">
                              <i class="fa fa-eye password-show_toggle_show-icon"></i>
                              <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                            </div>
                            </div>
                            <p id="showmessage"><p>

                  </div>
                  </div>
                     <div class="col-md-2">
                                   <br> <br>
                                	<label class="custom-switch">
                                                        <input type="hidden" name="status" value=0 />
														<input type="checkbox" name="status" @if ($store_admin->store_account_status == 1) checked @endif  value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Active Status</span>
													</label>
                            </div>
                                
                           
                  </div>
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
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
 @endsection
