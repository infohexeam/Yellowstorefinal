@extends('store.layouts.app')
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
               <form action="{{route('store.store_assign_agency')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">

                         <div id="agency">
                           <label class="form-label">Agencies </label>
                           <select name="agency_id[]" required="" class="form-control" id="country" >
                                 <option value=""> Select Agency</option>
                                @foreach($agencies as $key)
                                <option {{old('agency_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id}}"> {{$key->agency_name }} </option>
                                @endforeach
                              </select>
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
                           <a class="btn btn-danger" href="{{ route('store.list_agency') }}">Cancel</a>
                           </center>
                        </div>
                  </form>
               
            
         </div>
      </div>
   </div>
</div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
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
      $(wrapper).append('<div> <br> <label class="form-label">Agencies </label> <select name="agency_id[]" required="" class="form-control"  ><option value=""> Select Agency</option> @foreach($agencies as $key)<option {{old('agency_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id}}"> {{$key->agency_name }} </option> @endforeach </select><a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box
    
  });

  
  
  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});


</script>