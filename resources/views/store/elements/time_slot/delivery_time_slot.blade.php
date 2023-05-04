@extends('store.layouts.app')
@section('content')


<div class="container">
   <div class="row" style="min-height:70vh;" >
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

               <form action="{{route('store.update_delivery_time_slots')}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
              <div class="row">
                     <table class="table">
                       <thead>
                         <tr>
                           <th>From Time</th>
                           <th>To Time</th>
                           <th> </th>
                         </tr>
                       </thead>


                       <tbody id="doc_area">

                        @if ($time_slots_count > 0)
                        @php
                            @$i = 0;
                        @endphp
                           
                          @foreach ($time_slots as $data)

                           <tr id="{{@$i}}">
                              <td>
                                <span id="ss"></span>
                                <input type="time" id="s"  required class="form-control" value="{{ $data->time_start }}" name="start[]">
                              </td>
                              <td>
                                <span id="se"></span>
                                <input type="time" id="e"   required  class="form-control" value="{{ $data->time_end }}"  name="end[]">
                              </td>
                                <td>
                                 <a id="r" class="remove_field btn btn-warning"><i style="color:red;" class="fa fa-trash"></i></a>
                              </td>
                            </tr>

                          @endforeach
                        @endif
                      
                      
                       
                         @php
                         $i = 0;
                       @endphp
                            @if ($time_slots_count < 0)
                           <tr id="{{@$i}}">
                              <td>
                                <span id="ss"></span>
                                <input type="time" id="s"  required class="form-control"   name="start[]">
                              </td>
                              <td>
                                <span id="se{{@$i}}"></span>
                                <input type="time" id="e"   required  class="form-control"  name="end[]">
                              </td>
                                <td>
                                 <a id="r" class="remove_field btn btn-warning"><i style="color:red;" class="fa fa-trash"></i></a>
                              </td>
                            </tr>
                              @endif
                              @php
                                $i++;
                            @endphp


                           

                         
                       </tbody>
                     </table>
<div class="col-md-12">
                      <div class="form-group">
                        <center>
    <a id="addDoc" style="background-color: #d2cccc;" class="mb-2 btn btn-block  btn-gray">Add Slot</a>

                        </center>
                      </div>
                    </div>

                   
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

<script>
$(document).ready(function() {
   var wrapper      = $("#doc_area"); //Fields wrapper
  var add_button      = $("#addDoc"); //Add button ID

  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
x++; //text box increment
$(wrapper).append(' <tr id=""><td><span id="ss"></span><input required type="time" id="s"   class="form-control"   name="start[]"></td><td><span id="se{{@$i}}"></span><input type="time" id="e"  required   class="form-control"  name="end[]"></td><td><a id="r" class="remove_field btn btn-warning"><i style="color:red;" class="fa fa-trash"></i></a></td></tr>'); //add input box

  });

  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent().parent().remove(); x--;
  })
});
</script>
@endsection



