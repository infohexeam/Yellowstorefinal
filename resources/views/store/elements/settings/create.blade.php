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
                @if ($message = Session::get('error'))
               <div class="alert alert-danger">
                  <p>{{ $message }}</p>
               </div>
               @endif
            </div>
            <div class="col-lg-12">
                
                <!-- @if(!isset($store->store_state_id) || !isset($store->store_district_id) || !isset($store->town_id))-->
                <!--    <div class="alert alert-danger">-->
                <!--        <p> You have to update your-->
                <!--         <a href="{{url('/store-profile/view')}}">profile </a>  -->
                <!--         inorder to add the settings-->
                       
                        <!--<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>-->
                <!--        </p>-->
                <!--    </div>-->
                <!--@endif-->
        
        
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

               <form  id="myForm" action="{{route('store.update_store_settings')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
              <div class="row">



                <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Service Area(km)</label>
                           <input type="number" min="0"  step="0.01"  class="form-control" required  onchange="findKM(this.value)"
                              id="service_area" name="service_area"  value="{{old('service_area',$store->service_area)}}" placeholder="Service Area(km)">
                        </div>
                     </div>


                      {{-- <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Service District *</label>
                           <select name="service_district" required id="city" class="form-control"  >
                                 <option value=""> Service District</option>
                                @foreach($districts as $key)
                                <option {{old('service_district',@$store->store_district_id) == $key->district_id ? 'selected':''}} value="{{$key->district_id }}"> {{$key->district_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div>

                       <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Service Town *</label>
                           <select name="service_town" required id="town" class="form-control"  >
                            <option value="">Service Town</option>
                            @if(old('service_town') || @$store->town_id)
                                @php
                                $towns = \DB::table('mst_towns')->where('district_id',@$store->store_district_id)->get();
                            @endphp
                                @foreach($towns as $key)
                                <option {{old('service_town',@$store->town_id) == $key->town_id ? 'selected':''}} value="{{$key->town_id }}"> {{$key->town_name }} </option>
                                @endforeach

                            @else
                             @endif
                              </select>
                        </div>
                     </div>


                  <div class="col-md-6">
                        <div class="form-group">
                          <label class="form-label">Business Type *</label>
                           <select name="business_type_id" required id="business_type" class="form-control"  >
                                 <option value=""> Select Business Type</option>
                                @foreach($business_types as $key)
                                <option {{old('business_type_id',$store->business_type_id) == $key->business_type_id ? 'selected':''}} value="{{$key->business_type_id }}"> {{$key->business_type_name }} </option>
                                @endforeach
                              </select>
                        </div>
                     </div> --}}

                     <div class="col-md-6">
                      <div class="form-group">
                         <label class="form-label">Order Number Prefix</label>
                         <input type="text"  class="form-control" oninput="checkUnique(this.value)" name="order_number_prefix"  value="{{old('order_number_prefix',$store->order_number_prefix)}}" placeholder="Order Number Prefix">
                        <span style="color:red" id="prefixErr" ></span>
                      </div>
                   </div>
                    <div class="col-md-6">
                      <div class="form-group">
                         <label class="form-label">Product Upload Limit</label>
                         <input type="text" readonly value="{{$store->product_upload_limit}}"   class="form-control"  placeholder="Order Number Prefix">
                        <span style="color:red" id="prefixErr" ></span>
                      </div>
                   </div>
                   <div class="col-md-6">
                      <div class="form-group">
                         <label class="form-label">Total Products Uploaded</label>
                         <input type="text" readonly  value="{{$product_count}} Products Uploaded /{{$store->product_upload_limit}}"   class="form-control"  placeholder="Order Number Prefix">
                        <span style="color:red" id="prefixErr" ></span>
                      </div>
                   </div>
                    <div class="col-md-4">
                      <div class="form-group">
                   <label class="form-group custom-switch">
                   Immediate Delivery
                         
														<input type="checkbox" name="immediate_delivery" @if ($store->delivery_option_immediate == 1) checked @endif    class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description"></span>
													</label>
                          </div>
                          </div>
                             <div class="col-md-4">
                      <div class="form-group">
                   <label class="form-group custom-switch">
                   Slot Delivery(Same Day)
                         
														<input type="checkbox" name="slot_delivery"   @if ($store->delivery_option_slot == 1) checked @endif       class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description"></span>
													</label>
                          </div>
                          </div>
                     <div class="col-md-4">
                      <div class="form-group">
                   <label class="form-group custom-switch">
                   Future Delivery
                         
														<input type="checkbox" name="future_delivery"   @if ($store->delivery_option_future == 1) checked @endif    class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description"></span>
													</label>
                          </div>
                          </div>
                   
                     <div class="col-md-12">
                      <div class="form-group">
                         <label class="form-label">Qrcode</label>
                         <input type="text" readonly class="form-control"  name="store_qrcode"  value="{{old('store_qrcode',$store->store_qrcode)}}" placeholder="">
                      </div>
                   </div>


                     <table id="first" class="table">
                       <thead>
                         <tr>
                           <th>Starting(km)</th>
                           <th></th>
                           <th>Ending(km)</th>
                           <th>Delivery Charges</th>
                           <th>Packing Charges</th>
                         </tr>
                       </thead>
                       <tbody id="table_body">
                       @if ($settingcount > 0)
                      @php
                      $c = 0;
                      @endphp
                           
                          @foreach ($store_settings as $data)
                              <tr class="trClass" id="trSec{{$c}}">
                              <td>
                                <input step="0.1" readonly required type="number" id="start{{$c}}" onchange="endKMChanged(this.id)" value="{{ $data->service_start }}" class="form-control endingKm" name="start[]">
                              </td>
                                <td class="text-center"> -
                                
                                <input type="hidden" class="count"  value="{{ $c }}">

                                </td>
                              <td class="endcls" >
                                <input step="0.1" min="0" required type="number" id="end{{$c}}" oninput="startKMChanged(this.id,{{$c}})" value="{{ $data->service_end }}" class="endkm form-control startingKm"   name="end[]">
                              </td>
                              <td>
                                <input type="number" min="0" step="1" oninput="validity.valid||(value='');" required value="{{ $data->delivery_charge }}" id="delivery_charge0" class="form-control"  name="delivery_charge[]">
                              </td>
                              <td>
                                <input type="number" min="0" step="1" oninput="validity.valid||(value='');" required value="{{ $data->packing_charge }}"  id="packing_charge0" class="form-control"  name="packing_charge[]">
                              </td>
                              @if($loop->last)
                               <td>
                               
                                 <a id="r" onclick="startKMChanged(this.id,{{$c}})" class="remove_field btn btn-warning"><i style="color:red;" class="fa fa-trash"></i></a>
                              </td>
                              @else
                              <td>
                               <a id="r" onclick="startKMChanged(this.id,{{$c}})" class="remove_field btn btn-warning"><i style="color:red;" class="fa fa-trash"></i></a>
                              </td>
                              @endif
                            </tr>
                            @if ($c == count($store_settings))
                             @php
                              echo '<script type="text/javascript">makeValue('.$loop->iterator.')</script>';
                             @endphp
                            @endif
                            @php
                              $c++;
                            @endphp
                          @endforeach

                        @else
                             <tr class="trClass" id="trSec0" >
                              <td>
                                <input step="0.1" required value="0" readonly type="number"  onchange="endKMChanged(this.id)" id="start0"  class="form-control endingKm" name="start[]">
                              </td>
                                <td class="text-center"> 
                                - 
                                </td>
                              <td class="endcls"> 
                                <input step="0.1" min="0" required  type="number"  oninput="startKMChanged(this.id,0)" id="end0" class="endkm form-control startingKm"   name="end[]">
                              </td>
                              <td>
                                <input type="number" min="0" step="1" oninput="validity.valid||(value='');" required  id="delivery_charge0" class="form-control"  name="delivery_charge[]">
                              </td>
                              <td>
                                <input type="number" min="0" step="1" oninput="validity.valid||(value='');" required   id="packing_charge0" class="form-control"  name="packing_charge[]">
                              </td>
                             <td>
                                 <a id="r" onclick="startKMChanged(this.id,0)" class="remove_field btn btn-warning"><i style="color:red;" class="fa fa-trash"></i></a>
                              </td>
                            </tr>
                         @endif

                           
                         
                       </tbody>
                     </table>

                   
                    <div class="col-md-12">
                      <div class="form-group">
                        <center>
                                <a id="addDoc" class="text-white mb-2 btn btn-block btn-gray">Add Row</a>
                    <button type="submit" id="updateBtn" class="mb-2 btn btn-block btn-raised btn-info">Update</button>
                
                              <a href="{{ route('store.time_slots') }}" style="color:white;"  class="btn  btn-block btn-raised btn-info">Working Days & Time</a>
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


<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

<script>

function checkUnique(prefix){
    var _token= $('input[name="_token"]').val();
    $('#updateBtn').prop('disabled',true);

        $.ajax({
          type:"GET",
          url:"{{ url('ajax/stre-order/prefix-unique') }}?prefix="+prefix,
          success:function(res){
            if(res == 'exist'){
                $('#prefixErr').text("Prefix already taken");
                $('#updateBtn').prop('disabled',true);
            }else{
                $('#prefixErr').text("");
                $('#updateBtn').prop('disabled',false);
            }
          }
        });
}

function makeValue(val)
{
  alert(val);
}
   var x = 1;

 //$(document).ready(function() { 
    // if($('#first tr:last td:nth-child(2) input').val())
    // {
 //var x = $('#first tr:last td:nth-child(2) input').val();
    //alert(x);
   // }

 //});



$(document).ready(function() {
    var wrapper      = $("#table_body"); //Fields wrapper
    var add_button      = $("#addDoc"); //Add button ID

    var x = 1; //initlal text box count
    let dummyCount= 0;
    let serviceArea = $('#service_area').val();

  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();

    //  var xVal = $('#first tr:last td:nth-child(2) input').val();
    // //  if((xVal != 0) && (dummyCount == 0) ){
    // //      dummyCount++;
    // //      x = xVal;
    // //  }
    //  if(dummyCount == 0){
    //      dummyCount++;
    //      x = xVal;
    //  }

    x = ($('.trClass').length) - 1;
     //alert($('.trClass').length);
     let lastEndVal = $("#end"+x).val();
     let lastStartVal = $("#start"+x).val();
     if(('.trClass').length==0)
     {
      //alert('hii');
      //lastStartVal = 0;


     }
    // console.log(x);
     
        if(parseFloat(lastEndVal) <= parseFloat(lastStartVal))
        {
                //console.log(lastEndVal + "<"+lastStartVal);
          
            if(lastEndVal != lastStartVal)
             alert("Wrong input entered. " +lastEndVal+ " is lessthan "+lastStartVal);
             else
             alert("Wrong input entered. ");

        }else{
            let lastEndValuePlus = parseFloat($("#end"+x).val()) + 0.1;
            let serviceArea = $("#service_area").val();
          //  console.log(lastEndValuePlus+" <= "+serviceArea);
            // if(lastEndValuePlus <= serviceArea)
            // {
                x++; 
                if(x==0)
                {
                  $(wrapper).append(' <tr class="trClass" id="trSec'+x+'" ><td><input min="0" step="0.1" required readonly onchange="endKMChanged(this.id)"  type="number" id="start'+x+'" value="0.0"  class="form-control endingKm" name="start[]"></td><td class="text-center"> - </td><td class="endcls" ><input step="0.1" min="0" oninput="startKMChanged(this.id,'+x+')" required type="number" value="0.0"  id="end'+x+'" class="endkm form-control startingKm"   name="end[]"></td><td><input type="number" min="0" step="1" oninput="validity.valid||(value=0);" required  id="delivery_charge'+x+'" class="form-control"  name="delivery_charge[]" min="0"></td><td><input type="number" min="0" min="0" step="1" oninput="validity.valid||(value=0);" required   id="packing_charge'+x+'" class="form-control"  name="packing_charge[]"></td><td><a id="r" onclick="startKMChanged(this.id,'+x+')" class="remove_field btn btn-warning"><i style="color:red;" class="fa fa-trash"></i></a></td></tr>'); //add input box

                }
                else
                {
                  $(wrapper).append(' <tr class="trClass" id="trSec'+x+'" ><td><input min="0" step="0.1" required readonly onchange="endKMChanged(this.id)"  type="number" id="start'+x+'" value="'+lastEndValuePlus+'"  class="form-control endingKm" name="start[]"></td><td class="text-center"> - </td><td class="endcls" ><input step="0.1" min="0" oninput="startKMChanged(this.id,'+x+')" required type="number" value="0"  id="end'+x+'" class="endkm form-control startingKm"   name="end[]"></td><td><input type="number" min="0" step="1" oninput="validity.valid||(value=0);" required  id="delivery_charge'+x+'" class="form-control"  name="delivery_charge[]" min="0"></td><td><input type="number" min="0" min="0" step="1" oninput="validity.valid||(value=0);" required   id="packing_charge'+x+'" class="form-control"  name="packing_charge[]"></td><td><a id="r" onclick="startKMChanged(this.id,'+x+')" class="remove_field btn btn-warning"><i style="color:red;" class="fa fa-trash"></i></a></td></tr>'); //add input box

                }
                
            // }else{
            //     alert("Service area already filled..");
            // }
        }

        
    });
    
      $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
        e.preventDefault(); $(this).parent().parent().remove(); x--;
      })
    });


function startKMChanged(id,key)
{
   var fullKm = $('#service_area').val();
   var endKm = $('#'+id).val();
        var xVal = $('#first tr:last td:nth-child(2) input').val();
   x = key;

   console.log(fullKm + " : " +xVal + " : "+key + " : "+x );
   for(let i = key+1; i<=500;i++){
      //console.log(i);
      $("#trSec"+i).remove();
   }
  
    // let ending = [];
    // let starting = [];

    // $($(".startingKm").get().reverse()).each(function() {
    //   let end = $(this).val();
    //   ending.push(end);  
    
    // });
    // $($(".endingKm").get().reverse()).each(function() {
    //   let start = $(this).val();
    //   starting.push(start);   
    // });


  


  


}



$(document).ready(function(){
    $('#myForm').on('submit', function(e){
        e.preventDefault();

        var endingKMS = [];
        $($(".startingKm").get().reverse()).each(function() {
          let end = $(this).val();
          endingKMS.push(end);  
        });

        if((jQuery.inArray("0.0", endingKMS) != -1) || (jQuery.inArray("0", endingKMS) != -1))
        {
          alert("wrong input");
          return false;
        }
        else
        {         
        this.submit();
        }

    });
});



</script>



<script>

function findKM_(km)
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
    
      $('#table_body').append('<tr><td><input type="number" readonly step="0.1" id="start'+i+'" value="'+v1.toFixed(1)+'" class="form-control" name="start[]"></td><td class="text-center"> - </td><td><input readonly type="number" id="end'+i+'" step="0.1"  value="'+v2+'"  class="form-control"   name="end[]"></td><td><input type="number" min="0" step="1" oninput="validity.valid||(value=0);" id="delivery_charge'+i+'" class="form-control" required name="delivery_charge[]"></td><td><input type="number" min="0" step="1" oninput="validity.valid||(value=0);" id="packing_charge'+i+'" required class="form-control"  name="packing_charge[]"></td></tr>');
    }

    if((km % 5) > 0)
    {
      var v4 = km % 5;
      if(km > 5)
      {
        v1 = v1 + 5;
        v4 = v4 + v2;

      }

      $('#table_body').append('<tr><td><input readonly type="number" step="0.1" id="start'+i+'" value="'+v1.toFixed(1)+'" class="form-control" name="start[]"></td><td class="text-center"> - </td><td><input readonly type="number" id="end'+i+'" step="0.1"  value="'+v4+'"  class="form-control"   name="end[]"></td><td><input type="number" min="0" step="1" oninput="validity.valid||(value=0);" id="delivery_charge'+i+'" class="form-control" required  name="delivery_charge[]"></td><td><input type="number" min="0" step="1" oninput="validity.valid||(value=0);" required id="packing_charge'+i+'" class="form-control"  name="packing_charge[]"></td></tr>');
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
  //    console.log(files);
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
          //    console.log(res);
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

@endsection



