@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row" style="min-height:70vh;">
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

            <div class="table-responsive">
               <table id="example" class="table table-striped table-bordered text-nowrap w-80 ml-4">
                  <tbody>
                     @php
                     $i = 0;
                     @endphp
                      @foreach ($youtube_links as $link) 
                     

                        @if(!empty($youtube_links))
                        <tr>
                           <td>{{ ++$i }} </td>
                           
                           <td><a href="{{$link->youtube_link}}" target="_blank">{{$link->youtube_link}}</a></td>
                          <td>
                          <img src="{{asset('assets/uploads/video_images/')}}/{{$link->youtube_link_thumbnail}}">
                          </td>
                           <td>
                              <a class="btn btn-small btn-danger" href="{{ url('admin/store/remove_youtube/'.$link->youtube_link_id) }}">Remove</a>
                           </td>                    
                        </tr>
                        @endif

                      @endforeach 
                      
                  </tbody>
               </table>
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
               <form action="{{route('admin.store_youtube_videos',$store->store_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                          
                         
                         <div id="agency">
                           <label class="form-label">Youtube Links * </label>
                           <input type="text" name="youtube_link[]" class="form-control" required>
                         
                        </div>
                     </div>
                     </div>
                     <div class="col-md-4">
                        <div class="form-group">
                          
                         
                         <div id="agency">
                           <label class="form-label">Youtube Thumbnail * </label>
                           <input type="file" name="youtube_thumbnail[]" class="form-control imgValidation" required accept="image/png, image/jpeg, image/jpg">
                         
                        </div>
                     </div>
                     </div>
                
                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Add more</label>
                            <button type="button" id="addAgency" class="btn btn-raised btn-success"> Add More</button>
                        </div>
                        </div>
                      </div>


                     <div class="form-group">
                           <center>
                           <button type="submit"  class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Submit</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ url('admin/store/add_youtube/' . $store->store_name_slug) }}">Cancel</a>
                           </center>
                        </div>
                  </form>
                </div>


           {{--  </div>
         </div> --}}
      </div>
   </div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script type="text/javascript">

$(document).ready(function() {
   var wrapper      = $("#agency"); //Fields wrapper
  var add_button      = $("#addAgency"); //Add button ID
  
  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div class="row col-md-12" > <br> <label class="form-label">Youtube Links * </label><input type="text" required name="youtube_link[]" class="form-control"><label class="form-label">Youtube Thumbnail * </label><input type="file" required name="youtube_thumbnail[]" class="form-control imgValidation"> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box
    
  });

  
  
  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});


</script>