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
                 <form action="{{route('admin.store_attribute_value')}}"  method="POST" 
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-12">
                        <div class="form-group">
                           <label class="form-label">Attribute Group</label>
                           <select class="form-control attribute_group" name="attribute_group_id" required >
                           <option value="">Select Attribute Group</option>
                         @foreach ($attributegroups as $key)
                           <option {{old('attribute_group_id') == $key->attr_group_id ? 'selected':''}} value=" {{ $key->attr_group_id}} "> {{ $key->group_name }}</option>
                           
                           @endforeach 
                           {{-- <option value="1">size</option>
                           <option value="2">color</option> --}}
                        </select>
                          
                        </div>
                      </div>
                      </div>

                         <div class="row color_code" style="display: none">
                        
                          <div class="col-md-6">
                        <div class="form-group">
                        <label class="form-label">Color Code</label>
                           <input type="text" class="form-control" name="Hexvalue[]" value="{{old('Hexvalue')}}" placeholder="Color Code">
                           </div>
                         </div>
                        <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Add more</label>
                            <button type="button" id="addNewColor" class="btn btn-raised btn-success">
                      Add More</button>
                        </div>
                        </div>
                          </div>
 
                        
                           <div class="row"  id="teamColorArea">
                            <div class="col-md-6">
                            <div class="form-group">
                           <div id="teamArea">
                           <label class="form-label">Attribute Value</label>
                           <input type="text" required class="form-control" name="group_value[]" multiple="" value="{{old('group_value')}}" placeholder="Attribute Value">
                           </div>
                         </div>
                        </div>
                           <div class="col-md-4" id="addNewTeam">
                        <div class="form-group">
                           <label class="form-label">Add More</label>
                            <button type="button"  class="btn btn-raised btn-success">
                      Add More</button>
                        </div>
                        </div>
                      </div>
                     
                    
                
                     <center>
                        <div class="col-md-12">
                         <div class="form-group">
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Add</button>
                           <button type="reset" class="btn btn-raised btn-success">
                      Reset</button>
                       <a class="btn btn-danger" href="{{ route('admin.list_attribute_value') }}">Cancel</a>
                        </div>
                        </div>
                        </center> 
               </form> 
            </div>
         </div>
      </div>
   </div>

@endsection
 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
     $('.attribute_group').change(function(){
      //alert('dsd');
      var attribute_group_id = $(this).val();
     // alert(attribute_group_id);

        if($(this).val() == 2)

        {
          $('.color_code').show();
         
           $('#addNewTeam').hide();
           


        } 
       
        else
        {
          $('.color_code').hide();
          /*$('#addNewColor').hide();*/
            $('#addNewTeam').show();


             
        }
     });
   });


$(document).ready(function() {
   var wrapper      = $("#teamArea"); //Fields wrapper
  var add_button      = $("#addNewTeam"); //Add button ID
  
  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div> <br> <input type="text" class="form-control" name="group_value[]" value="{{old('group_value')}}" placeholder="Attribute Value" /> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box
    
  });

  
  
  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});

$(document).ready(function() {
   var wrapper      = $("#teamColorArea"); //Fields wrapper
  var add_button      = $("#addNewColor"); //Add button ID
  
  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<br><div class="row"> <div class="col-md-6"><div class="form-group"><input type="text" class="form-control" name="Hexvalue[]" value="{{old('Hexvalue')}}" placeholder="Color Code"></div></div><div class="col-md-6"><div class="form-group"><input type="text" class="form-control" name="group_value[]"  value="{{old('group_value')}}" placeholder="Attribute Value"></div></div><a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box
    
  });
 
  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});

</script>
</script>