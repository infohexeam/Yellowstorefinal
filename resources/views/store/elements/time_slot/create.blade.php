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

               <form action="{{route('store.update_time_slot')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
              <div class="row">
                     <table class="table">
                       <thead>
                         <tr>
                           <th>Working Days</th>
                           <th>Opening Time</th>
                           <th>Closing Time</th>
                         </tr>
                       </thead>
                       <tbody id="table_body">
                      
                       @if ($time_slots_count > 0)
                       @php
                         @$i = 0;
                       @endphp
                           
                          @foreach ($time_slots as $data)
                            <tr id="{{@$i}}">
                              <td>
                                <input readonly type="text"  value="{{ $data->day }}" class="form-control" name="day[]">
                              </td>
                              <td>
                                <span id="ss{{@$i}}"></span>
                                <input type="time" id="s{{@$i}}"   value="{{ $data->time_start }}"  class="form-control"   name="start[]">
                              </td>
                              <td>
                                <span id="se{{@$i}}"></span>
                                <input type="time" id="e{{@$i}}"   value="{{ $data->time_end }}"   class="form-control"  name="end[]">
                              </td>
                              <td>
                                <a id="r" onclick="clearfields({{@$i}})" class="btn btn-warning">Clear</a>
                              </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                          @endforeach

                        @else

                        @php
                          $days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                        @endphp
                         @php
                         $i = 0;
                       @endphp
                           

                        @foreach ($days as $data)
                           <tr id="{{@$i}}">
                              <td >
                                <input readonly type="text"  value="{{ $data }}" class="form-control" name="day[]">
                              </td>
                              <td>
                                <span id="ss{{@$i}}"></span>
                                <input type="time" id="s{{@$i}}"   class="form-control"   name="start[]">
                              </td>
                              <td>
                                <span id="se{{@$i}}"></span>
                                <input type="time" id="e{{@$i}}"    class="form-control"  name="end[]">
                              </td>
                                <td>
                                <a id="r" onclick="clearfields({{@$i}})" class="btn btn-warning">Clear</a>
                              </td>
                            </tr>
                              @php
                                $i++;
                            @endphp
                        @endforeach

                        @endif

                           

                         
                       </tbody>
                     </table>

                   
                    <div class="col-md-12">
                      <div class="form-group">
                        <center>
                              <button type="submit" class="btn btn-block btn-raised btn-info">Update</button>
                        </center>
                      </div>
                    </div>

                     

                  </div>
                <br>
             
       </form>
           
      </div>
   </div>
</div>
</div>
@endsection



{{-- .varient {
  background-color: #EDEDFD;
  border: 1px  grey;
} --}}

 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>

<script>
function clearfields(id)
{
 // var id = this.id;
  //$('#'+id).remove();
  $('#s'+id).remove();
  $('#ss'+id).append('<input type="time" id="s'+id+'"   class="form-control"   name="start[]">');
  $('#e'+id).remove();
  $('#se'+id).append('<input type="time" id="e'+id+'"   class="form-control"   name="end[]">');
 // alert(id);
}


function findKM(km)
{
  //if((km % 5) != 0)
 // { 
  //  $('#service_area').val(0);
  //}
 // {
    var km_count =  km / 5;
     $('#table_body').empty();
    var v1 = 0.1;

    var i = 1;
    for(i = 1; i <= km_count; i++)
    {
     // var html = "";
     var v2 = i * 5;
     var v1 = (v2 - 4.9);
    
      $('#table_body').append('<tr><td><input type="number" step="0.1" id="start'+i+'" value="'+v1.toFixed(1)+'" class="form-control" name="start[]"></td><td class="text-center"> - </td><td><input type="number" id="end'+i+'" step="0.1"  value="'+v2+'"  class="form-control"   name="end[]"></td><td><input type="number" id="delivery_charge'+i+'" class="form-control"  name="delivery_charge[]"></td><td><input type="number" id="packing_charge'+i+'" class="form-control"  name="packing_charge[]"></td></tr>');
    }

    if((km % 5) > 0)
    {
      var v4 = km % 5;
      if(km > 5)
      {
        v1 = v1 + 5;
        v4 = v4 + v2;

      }

      $('#table_body').append('<tr><td><input type="number" step="0.1" id="start'+i+'" value="'+v1.toFixed(1)+'" class="form-control" name="start[]"></td><td class="text-center"> - </td><td><input type="number" id="end'+i+'" step="0.1"  value="'+v4+'"  class="form-control"   name="end[]"></td><td><input type="number" id="delivery_charge'+i+'" class="form-control"  name="delivery_charge[]"></td><td><input type="number" id="packing_charge'+i+'" class="form-control"  name="packing_charge[]"></td></tr>');
    }
  //}
}



$(document).ready(function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
      var files = e.target.files,
        filesLength = files.length;
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          $("<span class=\"pip\">" +
            "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
            "<br/><span class=\"remove\">Remove image</span>" +
            "</span>").insertAfter("#files");
          $(".remove").click(function(){
            $(this).parent(".pip").remove();
          });

          // Old code here
          /*$("<img></img>", {
            class: "imageThumb",
            src: e.target.result,
            title: file.name + " | Click to remove"
          }).insertAfter("#files").click(function(){$(this).remove();});*/

        });
        fileReader.readAsDataURL(f);
      }
      console.log(files);
    });
  } else {
    alert("Your browser doesn't support to File API")
  }
});
</script>


<script type="text/javascript">


$(document).ready(function() {
   var wrapper      = $(".BaseFeatureArea"); //Fields wrapper
  var add_button      = $(".addBaseFeatureImage"); //Add button ID

  var x = 1; //initlal text box count
  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div>  <input type="file" class="form-control" name="product_image[]"  value="{{old('product_image')}}" placeholder="Base Product Feature Image" /> <a href="#" class="remove_field btn btn-small btn-danger">Remove</a></div>'); //add input box

  });

  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});




 $(document).ready(function() {

var agc = 0;

       $('.attr_group').change(function(){
       // alert("hi");
       if(agc != 0)
       { 
       // alert("dd");
        var attr_group_id = $(this).val();

        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('store/product/ajax/get_attr_value') }}?attr_group_id="+attr_group_id,


          success:function(res){
            //alert(data);
            if(res){
            $('.attr_value').prop("diabled",false);
            $('.attr_value').empty();
            $('.attr_value').append('<option value="">Value</option>');
            $.each(res,function(attr_value_id,group_value)
            {
              $('.attr_value').append('<option value="'+attr_value_id+'">'+group_value+'</option>');
            });

            }else
            {
              $('.attr_value').empty();

            }
            }

        });
       }
       else
       {
         agc++;
       }
      });

    });
  $(document).ready(function() {
    var pcc = 0;
      //  alert("dd");
       $('#business_type').change(function(){
         if(pcc != 0)
         { 
        var business_type_id = $(this).val();
       //alert(business_type_id);
        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('store/product/ajax/get_category') }}?business_type_id="+business_type_id,


          success:function(res){
           // alert(data);
            if(res){
            $('#category').prop("diabled",false);
            $('#category').empty();
            $('#category').append('<option value="">Product Category</option>');
            $.each(res,function(category_id,category_name)
            {
              $('#category').append('<option value="'+category_id+'">'+category_name+'</option>');
            });

            }else
            {
              $('#category').empty();

            }
            }

        });
         }else{
           pcc++;
         }
      });

    });


    $(document).ready(function() {
    var cc = 0;


       $('#city').change(function(){
          if(cc != 0)
         { 

        var city_id = $(this).val();
       // alert(city_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('store/ajax/get_town') }}?city_id="+city_id ,

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
         }
         else
         {
           cc++;
         }
      });

    });

</script>
