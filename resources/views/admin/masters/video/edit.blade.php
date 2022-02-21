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
               <form action="{{route('admin.update_video',$video->video_id)}}" method="POST"  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                      <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Platform *</label>
                                <select name="platform" required=""  class="form-control">
                                 <option value="">Platform</option>
                                 <option {{old('platform',$video->platform) == 'Youtube' ? 'selected':''}} value="Youtube">Youtube</option>
                                 <option {{old('platform',$video->platform) == 'Vimeo' ? 'selected':''}} value="Vimeo">Vimeo</option>
                                </select>
                            </div>
                        </div>
                        
                         <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Visibility *</label>
                                <select name="visibility" required=""  class="form-control">
                                 <option value="">Visibility</option>
                                 <option {{old('visibility',$video->visibility) == '1' ? 'selected':''}} value="1">Store</option>
                                 <option {{old('visibility',$video->visibility) == '2' ? 'selected':''}} value="2">Customer</option>
                                 <option {{old('visibility',$video->visibility) == '3' ? 'selected':''}} value="3">Delivery Boy</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                               <label class="form-label">State </label>
                                <select name="state_id" onchange="findCity(this.value)" class="form-control" id="state" >
                                     <option value=""> Select State</option>
                                    @foreach( @$state as $key)
                                    <option {{old('state_id',$video->state_id) == $key->state_id ? 'selected':''}} value="{{$key->state_id}}"> {{$key->state_name }} </option>
                                    @endforeach
                                  </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                               <label class="form-label">District </label>
                              <select name="district_id" onchange="findTown(this.value)" class="form-control" id="city">
                                <option value="">Select District</option>
                                @foreach( @$district as $key)
                                    <option {{old('district_id',$video->district_id) == $key->district_id ? 'selected':''}} value="{{$key->district_id}}"> {{$key->district_name }} </option>
                                @endforeach
                              </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                               <label class="form-label">Town </label>
                                  <select name="town_id" class="form-control" id="town">
                                    <option value="">Select Town</option>
                                        @foreach( @$town as $key)
                                        <option {{old('town_id',$video->town_id) == $key->town_id ? 'selected':''}} value="{{$key->town_id}}"> {{$key->town_name }} </option>
                                        @endforeach
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
                              <textarea class="form-control"  name="video_code" required rows="3" placeholder="Video link">{{old('video_code',$video->video_code)}}</textarea>                            
                           </div>
                        </div>
                        
                         <div class="col-md-12">
                             <div class="form-group">
                                <label class="form-label">Video Description *</label>
                              <textarea class="form-control"  name="video_discription" required rows="4" placeholder="Video Discription">{{old('video_discription',$video->video_discription)}}</textarea>                            
                           </div>
                        </div>
                        
                        
                        @if(isset($video->video_image))
                        <div class="col-md-12">
                            <img src="{{asset('assets/uploads/video_images/'.$video->video_image)}}"  width="50" >
                        </div>
                        @endif

                        <div class="col-md-2">
                            <br> <br>
                            <label class="custom-switch">
                            <input type="hidden" name="status" value=0 />
                            <input type="checkbox" name="status" @if ($video->status == 1) checked @endif  value=1 class="custom-switch-input">
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description">Status</span>
													</label>
                            </div>


                  </div>
                    <div class="form-group">
                           <center>
                           <button type="submit" id="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
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
   
    $(document).ready(function() {
        var sc = 0;
       $('#state').change(function(){
           if(sc != 0){


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



    //display town

    $(document).ready(function() {
        var cc = 0;
       $('#city').change(function(){
        //   if(cc != 0)
        //   {


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

  //}
        //   else
        //   {
        //       cc++;
        //   }

      });

    });


</script>

 @endsection
