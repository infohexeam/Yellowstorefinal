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
                    <form id="myForm" action="{{route('admin.edit_town',$town_id)}}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                        <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">Country</label>
                            <select name="country_id" required="" class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{@$c_data->country_id == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                           </div>
                        </div>

                        <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">State</label>

                                <select required name="state_id"  class="form-control" id="state" >
                                    <option value=""> Select State</option>
                                @foreach($states as $key)
                                    <option {{ @$s_data->state_id == $key->state_id ? 'selected':''}} value="{{ @$key->state_id }}">{{ $key->state_name }}</option>
                                @endforeach
                                </select>
                           </div>
                        </div>

                        <div class="col-md-6">
                         <div class="form-group">
                           <label class="form-label">District</label>
                                <select required name="district_id"  class="form-control" id="city" >
                                    <option value=""> Select District</option>
                                @foreach($districts as $key)
                                    <option {{ @$town->district_id == $key->district_id ? 'selected':''}} value="{{ @$key->district_id }}">{{ $key->district_name }}</option>
                                @endforeach
                                </select>
                           </div>
                        </div>

                        <div class="col-md-6">
                         <div class="form-group">
                          <label class="form-label">PIN Code</label>
                          <input type="text" placeholder="PIN Code" value="{{$town->town_name}}" required class="form-control" name="town_name" >
                           </div>
                        </div>

                        {{-- <div class="col-md-6">
                          <div class="form-group">
                           <label class="form-label">PIN Code</label>
                           <input type="text" placeholder="PIN Code" id="pin" value="{{@$town->pin}}"  required class="form-control" name="pin" >
       
                            </div>
                         </div> --}}
 




                        </div>

                            <div class="form-group">
                                <center>
                                <button type="submit" id="submit" class="btn btn-raised btn-primary">
                                <i class="fa fa-check-square-o"></i> Update</button>
                                <button type="reset" class="btn btn-raised btn-success">
                                Reset</button>
                                <a class="btn btn-danger" href="{{ route('admin.list_towns') }}">Cancel</a>
                                </center>
                            </div>
                    </form>
            </div>


      </div>
   </div>
</div>
</div>

<script type="text/javascript">

       $(document).ready(function() {
           var coc = 0;
       $('#country').change(function(){
           if(coc != 0){



        var country_id = $(this).val();
        var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_state') }}?country_id="+country_id,


          success:function(res){
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

         }else
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
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_city') }}?state_id="+state_id ,

          success:function(res){
            if(res){
                //alert(res);
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



</script>


@endsection

