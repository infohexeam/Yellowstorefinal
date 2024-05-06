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
               <form action="{{route('admin.update_tax',$tax->tax_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Tax Name</label>
                            <input type="text"   id="tax_name"  class="form-control" value="{{$tax->tax_name}}" placeholder="Tax Name" name="tax_name"  >
        
                        </div>
                     </div> 

                     <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Tax Value</label>
                            <input oninput="valueChanged(this.id)" onkeyup="valueChanged(this.id)" value="{{$tax->tax_value}}"  id="tax_value"  class="form-control" oninput="if (this.value < 0) this.value = '';" placeholder="Tax Value" name="tax_value" >
                        </div>
                     </div>

                    

                     <div class="card">
                        <div class="card-header">
                            <h5>Tax Split Ups</h5>
                        </div>
                        <div class="card-body">
                                <table class="table table-bordered  ">
                                    <tbody class="bodyClass">
                                        @foreach ($tax_splits as $split)
                                        
                                        <tr>
                                            <td>
                                                <input  type="text"  name="split_tax_name[]" id="split_tax_name0" value="{{ $split->split_tax_name }}"  class=".split_name form-control" placeholder="Tax Name" >
                                            </td>
                                            <td>
                                                <input step="0.01"   oninput="valueChanged(this.id)" value="{{ $split->split_tax_value }}" name="split_tax_value[]" id="split_tax_name0" class="split_value form-control" placeholder="Tax Value(%)" >
                                            </td>
                                            <td><a href="#" id="remove_field" onclick="valueChanged(this.id)" class="remove_field btn btn-small btn-danger"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                            
                                        @endforeach
                                      

                                        
                                    </tbody>
                                </table>
                            <a id="add_row" href="#" tabindex="0" class="add_row mt-2 ml-2 btn btn-cyan text-white"><i class="fa fa-plus"></i> Add row</a>

                        </div>
                     </div>
                    

                     <div class="col-md-12">
                        <div class="form-group">
                           <center>
                           <button type="submit" id="submitAdd" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Update</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_taxes') }}">Cancel</a>
                           </center>
                        </div>
                     </div>
                     
                  </div>
                  <script src="{{ asset('vendor\unisharp\laravel-ckeditor/ckeditor.js')}}"></script>
                  <script>CKEDITOR.replace('sub_category_description');</script>
               </form>

      </div>
   </div>
</div>
 </div>
</div>


<script type="text/javascript">


   $(document).ready(function() {
      var wrapper      = $(".bodyClass"); //Fields wrapper
     var add_button      = $(".add_row"); //Add button ID
   
     var x = 1; //initlal text box count
     $(add_button).click(function(e){ //on add input button click
       e.preventDefault();
       //max input box allowed
         x++; //text box increment
      //   alert(x);
         $(wrapper).append('<tr><td><input  type="text" name="split_tax_name[]" id="split_tax_name'+x+'"  class=".split_name form-control" placeholder="Tax Name" ></td><td><input   name="split_tax_value[]" oninput="valueChanged(this.id)" id="split_tax_name'+x+'" class="split_value form-control" placeholder="Tax Value(%)"></td><td><a href="#" class="remove_field btn btn-small btn-danger"><i class="fa fa-trash"></i></a></td></tr>'); //add input box
         
     });
   
     $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
       e.preventDefault(); $(this).parent().parent().remove(); x--;
     })
   });

function valueChanged(id){

   var total_tax = 0;
   $('.split_value').each(function(){
      total_tax += parseFloat(this.value);
   });
  
   var tax_value = $('#tax_value').val()
   if(isNaN(total_tax)) {
      var total_tax = 0;
      }
   if(parseFloat(total_tax) != tax_value){
      //alert(total_tax+ " "+tax_value);
      $('#submitAdd').attr('disabled', true);
   }
   else
   {
      //alert(total_tax+ " "+tax_value);
    //  alert("ggs");
      $("#submitAdd").attr("disabled", false);
   }

}

</script>

@endsection
