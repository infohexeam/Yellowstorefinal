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
    top: 15px;
    right: 0;
    bottom: 0;
    width: 2.5rem;
  }
  .password-show_toggle_show-icon, .password-show_toggle_hide-icon {
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

                    <form id="myForm" action="{{ route('store.update_password') }}" method="POST" enctype="multipart/form-data">
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
                                            <input type="password" class="form-control" oninput="checkPasswordComplexity(this.value)" onkeyup="validatePassLength()" name="password" id="password" value="" placeholder="New Password">
                                            <div class="password-show__toggle">
                                                <i class="fa fa-eye password-show_toggle_show-icon"></i>
                                                <i class="fa fa-eye-slash password-show_toggle_hide-icon"></i>
                                            </div>
                                            <span id="showpassmessage"></span>
                                            <span id="showpassmessage2"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="form-label">Password Confirmation</label>
                                        <div class="password-show">
                                            <input onkeyup="validatePassChange()" id="confirm_password" type="password" class="form-control" name="password_confirmation" value="" placeholder="Confirm Password">
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
                                <div class="form-group">
                                    <center>
                                        <button type="submit" class="btn btn-raised btn-primary" id="submit" disabled>
                                            <i class="fa fa-check-square-o"></i> Update
                                        </button>
                                        <a class="btn btn-danger" href="{{ url('store/home') }}">Cancel</a>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $(".password-show__toggle").on("click", function(e) {
            var $parent = $(this).parent();
            var $input = $parent.find("input");

            $parent.toggleClass("show");
            $input.attr("type", function(index, attr) {
                return attr === "password" ? "text" : "password";
            });
        });
    });

    function checkPasswordComplexity(pwd) {
        var re = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/;

        if (pwd !== '') {
            if (!re.test(pwd)) {
                $('#showpassmessage2').css('color', 'red').html('Password must include at least one upper case letter, lower case letter, number, and special character');
                validatePass();
                $('#submit').prop('disabled', true);
            } else {
                $('#showpassmessage2').html('');
                validatePass();
            }
        } else {
            $('#showpassmessage2').html('');
            validatePass();
        }
    }

    function validatePassLength() {
        var x = $('#password').val();
        if (x !== '') {
            if (x.length < 8) {
                $('#showpassmessage').css('color', 'red').html('You have to enter at least 8 digits!');
            } else {
                $('#showpassmessage').html('');
            }
        } else {
            $('#showpassmessage').html('');
        }
    }

    function validatePassChange() {
        var x = $('#password').val();
        var y = $('#confirm_password').val();

        $('#showmessage').html('');
        if (y !== '') {
            if (x === y) {
                $('#password, #confirm_password').css('border-color', 'green');
                validatePass();
            } else {
                $('#showmessage').css('color', 'red').html('Passwords not matching');
                $('#submit').prop('disabled', true); // Added line
            }
        } else {
            validatePass();
        }
    }

    function validatePass() {
        var x = $('#password').val();
        var y = $('#confirm_password').val();

        $('#showmessage').html('');
        if (y !== '') {
            if (x === y) {
                $('#password, #confirm_password').css('border-color', 'green');
                $('#submit').prop('disabled', false);
            } else {
                $('#showmessage').css('color', 'red').html('Passwords not matching');
                $('#submit').prop('disabled', true);
            }
        } else {
            $('#submit').prop('disabled', true);
        }
    }

    function validateForm() {
        var x = $('#password').val();
        var y = $('#confirm_password').val();
        if (x.length >= 8) {
            if (x !== y) {
                $('#showmessage').css('color', 'red').html('Passwords not matching');
                return false;
            }
        } else {
            $('#showpassmessage').css('color', 'red').html('You have to enter at least 8 digits!');
            return false;
        }
    }
</script>


@endsection
