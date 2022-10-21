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
  top: 15px;
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
			         <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
			      </div>
                    @endif
                </div>
                <div class="col-lg-12">
                 @if ($message = Session::get('errstatus'))
                  <div class="alert alert-danger">
                            <strong>Whoops!</strong> There were some problems with your input.<br><br>
                            <ul>
                                    <li>{{ $message }}</li>
                            </ul>
                        </div>
                 @endif
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

                    <form  id="myForm" onsubmit="return validateForm()" action="{{ route('admin.update_password') }}" method="POST" enctype="multipart/form-data" >
                        @csrf

		      <div class="form-body">
		      	<div class="row">

			<div class="col-md-12">
			<div class="form-group">
				<label class="form-label">Old Password</label>
                <div class="password-show">
					  <input type="password" required class="form-control" name="old_password" id="old_password" value="" placeholder="Old Password">
                      <div class="password-show__toggle">
                        <i class="fa fa-eye password-show_toggle_show-icon"></i>
                        <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                      </div> 
                </div>
               </div>
				   </div>


				<div class="col-md-12">
			<div class="form-group">
				<label class="form-label">New Password</label>
                <div class="password-show">
					  <input type="password" class="form-control" required oninput="checkPasswordComplexity(this.value)" onkeyup="validatePassLength()" name="password" id="password" value="" placeholder="New Password">
                      <div class="password-show__toggle">
                        <i class="fa fa-eye password-show_toggle_show-icon"></i>
                        <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                      </div>
                </div> 
                        <span id="showpassmessage"></span> <br>
                        <span id="showpassmessage2"></span>
                        
               </div>
				   </div>
				  <div class="col-md-12">
				 <div class="form-group">
						<label class="form-label">Password Confirmation</label>
                        <div class="password-show">
					  <input onkeyup="validatePass()" id="confirm_password" required type="password" class="form-control" name="password_confirmation" value="" placeholder="Confirm Password">
                      <div class="password-show__toggle">
                        <i class="fa fa-eye password-show_toggle_show-icon"></i>
                        <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                      </div> 
                        </div>
				                        <span id="showmessage"></span>

                </div>
      </div>

				</div>


			<div class="col-md-12">
 			   <div  class="form-group">
 					<center>
              	<button type="submit" id="submit" class="btn btn-raised btn-primary">
				          <i class="fa fa-check-square-o"></i> Update</button>
				          <button type="reset" class="btn btn-raised btn-success">
				         Reset</button>
				         <a class="btn btn-danger" href="{{ route('home') }}">Cancel</a>
					</center>
               </div>

            </div>
         </div>


    </form>
</div>

                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>
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
    //        document.getElementById('showpassmessage2').innerHTML = 'passwords must be in alphanumeric format';
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


<script>
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
    }}
}
</script>


<script>
function validateForm() {
  var x = document.forms["myForm"]["password"].value;
  var y = document.forms["myForm"]["confirm_password"].value;
   if(x.length >= 8)
    {
        if (x != y) {
            document.getElementById('showmessage').style.color = 'red';
            document.getElementById('showmessage').innerHTML = 'passwords not matching';
            var elmnt = document.getElementById("passlabel");
            elmnt.scrollIntoView();
            return false;
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
</script>


@endsection

