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
               <table id="example" class="table table-striped table-bordered text-nowrap w-80">
                  <tbody>
                     @php
                     $i = 0;
                     @endphp
                      @foreach ($linked_agencies as $data) 
                      @php
                      $agency_data = \DB::table('mst_store_agencies')
                      ->where('agency_id',$data->agency_id) 
                      ->where('agency_account_status',1)
                      ->first();
                      @$linked_agency_ids[] = $agency_data->agency_id;

                        @endphp

                        @if(!empty($agency_data))
                        <tr>
                           <td>{{ ++$i }} </td>
                           
                           <td>{{$agency_data->agency_name}}</td>
                           @php
                           //$agency_data = "";   
                           @endphp     
                           <td>
                              <a class="btn btn-small btn-danger" href="{{ url('admin/link/destroy/agency_store/'.$data->link_id) }}">Remove</a>
                           </td>                    
                        </tr>
                        @endif

                      @endforeach 
                  </tbody>
               </table>
            </div>
                        @if (empty($linked_agency_ids))
                             @php
                                 $linked_agency_ids[] = 0;
                             @endphp 
                          @endif
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
               <form action="{{route('admin.add_store_agency',$store->store_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-10">
                        <div class="form-group">
                          
                          <input type="hidden" name="store_id" value="{{$store->store_id}}">
                         <div id="agency">
                           <label class="form-label">Agencies </label>
                           <select name="agency_id[]" required="" class="form-control" id="country" >
                                 <option value=""> Select Agency</option>
                                @foreach($agencies as $key)
                                 @if(!in_array($key->agency_id,$linked_agency_ids))
                                 <option {{old('agency_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id}}"> {{$key->agency_name }} </option>
                                 @endif
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
                           <a class="btn btn-danger" href="{{ route('admin.list_store') }}">Cancel</a>
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

/*$(document).ready(function() {
   var wrapper      = $("#agency"); //Fields wrapper
  var add_button      = $("#addAgency"); //Add button ID
  
  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div> <br> <label class="form-label">Agencies </label> <select name="agency_id[]" required="" class="form-control"  ><option value=""> Select Agency</option> @foreach($agencies as $key)  @if(!in_array($key->agency_id,$linked_agency_ids)) <option {{old('agency_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id}}"> {{$key->agency_name }} </option> @endif @endforeach </select><a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box
    
  });

  
  
  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});*/
/*$(document).ready(function() {
    var wrapper = $("#agency"); // Fields wrapper
    var add_button = $("#addAgency"); // Add button ID
    var x = 1; // Initial text box count

    // Function to get array of already selected agency IDs
    function getSelectedAgencyIds() {
        var selectedAgencyIds = [];
        $('select[name="agency_id[]"]').each(function() {
            var selectedAgencyId = $(this).val();
            if (selectedAgencyId != '') {
                selectedAgencyIds.push(parseInt(selectedAgencyId));
            }
        });
        return selectedAgencyIds;
    }

    $(add_button).click(function(e) { // On add input button click
        e.preventDefault();
        // Max input box allowed
        x++; // Text box increment
        var options = '<option value=""> Select Agency</option>';
        // Generate options excluding already linked and already selected agencies
        var linkedAgencyIds = {!! json_encode($linked_agency_ids) !!};
        var selectedAgencyIds = getSelectedAgencyIds();
        var excludedAgencyIds = linkedAgencyIds.concat(selectedAgencyIds);

        @foreach($agencies as $key)
            if (!excludedAgencyIds.includes({{$key->agency_id}})) {
                options += '<option {{old('agency_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id}}">{{$key->agency_name}}</option>';
            }
        @endforeach
        // Append input box
        $(wrapper).append('<div> <br> <label class="form-label">Agencies </label> <select name="agency_id[]" required="" class="form-control"  >' + options + '</select><a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>');
    });

    $(wrapper).on("click", ".remove_field", function(e) { // User click on remove text
        e.preventDefault();
        $(this).parent('div').remove();
        x--;
    });
}); 
*/
$(document).ready(function() {
    var wrapper = $("#agency"); // Fields wrapper
    var add_button = $("#addAgency"); // Add button ID
    var x = 1; // Initial text box count

    // Function to get array of already selected agency IDs
    function getSelectedAgencyIds() {
        var selectedAgencyIds = [];
        $('select[name="agency_id[]"]').each(function() {
            var selectedAgencyId = $(this).val();
            if (selectedAgencyId != '') {
                selectedAgencyIds.push(parseInt(selectedAgencyId));
            }
        });
        return selectedAgencyIds;
    }

    // Update the first dropdown to show only the selected value
    function updateFirstDropdown() {
        var selectedAgencyId = $('select[name="agency_id[]"]').eq(0).val();
        var options = '<option value=""> Select Agency</option>';
        options += '<option selected value="' + selectedAgencyId + '">' + $('select[name="agency_id[]"]').eq(0).find(':selected').text() + '</option>';
        $('select[name="agency_id[]"]').eq(0).html(options);
    }

    $(add_button).click(function(e) { // On add input button click
        e.preventDefault();
        // Max input box allowed
        x++; // Text box increment
        var options = '<option value=""> Select Agency</option>';
        // Generate options excluding already linked and already selected agencies
        var linkedAgencyIds = {!! json_encode($linked_agency_ids) !!};
        var selectedAgencyIds = getSelectedAgencyIds();
        var excludedAgencyIds = linkedAgencyIds.concat(selectedAgencyIds);

        @foreach($agencies as $key)
            if (!excludedAgencyIds.includes({{$key->agency_id}})) {
               @if(!in_array($key->agency_id,$linked_agency_ids))
                options += '<option {{old('agency_id') == $key->agency_id ? 'selected':''}} value="{{$key->agency_id}}">{{$key->agency_name}}</option>';
               @endif
            }
        @endforeach
        // Append input box
        var newDropdown = '<div> <br> <label class="form-label">Agencies </label> <select name="agency_id[]" required="" class="form-control"  >' + options + '</select><a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>';
        $(wrapper).append(newDropdown);

        // Update the first dropdown to show only the selected value
        updateFirstDropdown();
    });

    $(wrapper).on("click", ".remove_field", function(e) { // User click on remove text
        e.preventDefault();
        $(this).parent('div').remove();
        x--;

        // Update the first dropdown to show only the selected value
        updateFirstDropdown();
    });
});








</script>