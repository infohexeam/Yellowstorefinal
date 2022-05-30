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
               <form action="{{route('admin.store_video')}}" method="POST"  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Platform *</label>
                                <select name="platform" required=""  class="form-control">
                                 <option value="">Platform</option>
                                 <option {{old('platform') == 'Youtube' ? 'selected':''}} value="Youtube">Youtube</option>
                                 <option {{old('platform') == 'Vimeo' ? 'selected':''}} value="Vimeo">Vimeo</option>
                                </select>
                            </div>
                        </div>
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Visibility *</label>
                                <select name="visibility" required=""  class="form-control">
                                 <option value="">Visibility</option>
                                 <option {{old('visibility') == '1' ? 'selected':''}} value="1">Store</option>
                                 <option {{old('visibility') == '2' ? 'selected':''}} value="2">Customer</option>
                                 <option {{old('visibility') == '3' ? 'selected':''}} value="3">Delivery Boy</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                               <label class="form-label">State </label>
                                <select name="state_id" onchange="findCity(this.value)" class="form-control" id="state" >
                                     <option value=""> Select State</option>
                                    @foreach( @$state as $key)
                                    <option {{old('state_id') == $key->state_id ? 'selected':''}} value="{{$key->state_id}}"> {{$key->state_name }} </option>
                                    @endforeach
                                  </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                               <label class="form-label">District </label>
                              <select name="district_id" onchange="findTown(this.value)" class="form-control" id="city">
                                 <option value="">Select District</option>
                              </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                               <label class="form-label">Pincode </label>
                                  <select name="town_id" class="form-control" id="town">
                                     <option value="">Select Pincode</option>
                                  </select>
                            </div>
                        </div>
                        
                         <div class="col-md-6">
                            <div class="form-group">
                               <label class="form-label">Thumbnail image </label>
                               <input type="file" name="video_image" class="form-control" />

                            </div>
                        </div>
                        
                        <div class="col-md-12">
                             <div class="form-group">
                                <label class="form-label">Video link *</label>
                              <textarea class="form-control"  name="video_code" required rows="3" placeholder="Video link">{{old('video_code')}}</textarea>                            
                           </div>
                        </div>
                        
                        <div class="col-md-12">
                             <div class="form-group">
                                <label class="form-label">Video Description *</label>
                              <textarea class="form-control"  name="video_discription" required rows="4" placeholder="Video Discription">{{old('video_discription')}}</textarea>                            
                           </div>
                        </div>

                              <div class="col-md-2">
                                   <br> <br>
                                	<label class="custom-switch">
                                                        <input type="hidden" name="status" value=0 />
														<input type="checkbox" name="status"  checked value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Status</span>
													</label>
                            </div>
                  </div>
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.videos') }}">Cancel</a>
                           </center>
                        </div>
               </form>

         </div>
      </div>
   </div>
</div>

<script>
    function findCity(state_id){


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

}

function findTown(city_id){

        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_town') }}?city_id="+city_id ,

          success:function(res){

           if(res){
            //  console.log(res);
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
}
</script>


 @endsection
