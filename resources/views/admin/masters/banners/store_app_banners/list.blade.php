@extends('admin.layouts.app')
@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-lg-12">


        <div class="card">
                <div class="row">
                    <div class="col-12" >
                            @if ($message = Session::get('status'))
                            <div class="alert alert-success">
                                <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                            </div>
                            @endif
                        <div class="card-body">
                                    <a style="color:white;"  data-toggle="modal" data-target="#StockModal01" class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i> Add Store App Banner </a>
                              @if(auth()->user()->user_role_id == 0)
                              <a href=" {{ url('admin/store-app-banner/restore-list') }}" class=" text-white btn btn-block btn-danger">
                                   <i class="fa fa-recycle"></i>
                                  Restore Store App Banner
                                </a>
                                @endif
                                
                                <br>
                            <div class="table-responsive">
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
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Pincode')}}</th>
                                        <th class="wd-15p">{{__('Banner Image')}}</th>
                                        <th class="wd-15p">{{__('Status')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                 <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($banners as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>

                                        <td>{{ @$data->town['town_name'] }}</td>
                                        
                                        
                                    
                                    <td><img src="{{asset('assets/uploads/store_banner/'.$data->image)}}"  width="50" ></td>
                                     <td>
                                        <form action="{{route('admin.status_store_banner',$data->banner_id)}}" method="POST"> 
                                            @csrf 
                                            @method('POST')
                                            <button type="submit" onclick="return confirm('Are you sure?');"  class="btn btn-sm @if($data->status == 0) btn-danger @else btn-success @endif"> 
                                                @if($data->status == 0)
                                                    Inactive
                                                @else
                                                    Active
                                                @endif
                                            </button>
                                         </form> 
                                    </td>
                                        <td>
                                            <form action="{{route('admin.destroy_store_app_banner',$data->banner_id)}}" method="POST">
                                                @csrf
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>

                            {{-- table responsive end --}}
                            </div>
                        {{-- Card body end --}}
                        </div>
                    {{-- col 12 end --}}
                </div>
            {{-- row end --}}
            </div>
        {{-- card --}}


        </div>
        {{-- row justify end --}}
    </div>
{{-- container end --}}
</div>






      <div class="modal fade" id="StockModal01" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" onclick="clearTax()" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_store_app_banner') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">



                <div class="card-body">


                  <div class="row">

                   <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">Country </label>
                            <select name="country_id" onchange="findCountry(this.value)" class="form-control" id="country" >
                                <option value=""> Select Country</option>
                                    @foreach( @$countries as $key)
                                    <option value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                    @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">State </label>
                            <select name="state_id"  onchange="findCity(this.value)"  class="form-control" id="state" >
                                <option  value=""> Select State</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                           <label class="form-label">District </label>
                            <select name="district_id"  onchange="findTown(this.value)" class="form-control" id="city">
                                <option value="">Select District</option>
                            </select>
                        </div>
                    </div>

                      <div class="col-md-6">
                          <div class="form-group">
                              <label class="form-label">Pincode </label>
                              <select name="town_id"  class="form-control" id="town">
                                <option value="">Select Pincode</option>
                              </select>
                           </div>
                        </div>

                     <div class="col-md-10">
                        <div class="form-group">
                           <div id="teamArea">
                           <label class="form-label">Images * [620*290] [in png,jpeg or jpg] </label>
                           <input type="file"  class="form-control" id="imgs" required accept="image/x-png,image/jpg,image/jpeg" name="images[]"  placeholder="Images">
                        </div>
                     </div>
  </div>
                        <div class="col-md-4">
                                <div class="form-group">
                                <label class="form-label"></label>
                                    <button type="button" id="addImage" class="btn btn-raised btn-success">
                            Add More</button>
                                </div>
                         </div>
                        <label class="custom-switch">
                            <input type="hidden" name="status" value=0 />
							<input type="checkbox" name="status"  checked value=1 class="custom-switch-input">
							<span class="custom-switch-indicator"></span>
							<span class="custom-switch-description">Active Status</span>
						</label>
                    </div>
                </div>

                      

                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="button" onclick="clearTax()" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
<!-- MESSAGE MODAL CLOSED -->


<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>


<script>

function clearTax()
{
    $('#country').val('');
     $('#state option:not(:first)').remove();

     $('#city option:not(:first)').remove();

     $('#town option:not(:first)').remove();

     $('#imgs').val('');
}



$(document).ready(function() {
   var wrapper      = $("#teamArea"); //Fields wrapper
  var add_button      = $("#addImage"); //Add button ID

  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div> <br>  <input type="file" accept="image/x-png,image/jpg,image/jpeg" class="form-control" multiple="" name="images[]"  placeholder="Images"> <a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box

  });



  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});
</script>




<script type="text/javascript">

function findCountry(country_id)
{
 $('#city').empty();
         $('#city').append('<option value="">Select City</option>');

        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_state') }}?country_id="+country_id,


          success:function(res){
           // alert(data);
            if(res){
            $('#state').prop("diabled",false);
            $('#state').empty();

            $('#state').append('<option value="">Select State</option>');
            $.each(res,function(state_id,state_name)
            {
              $('#state').append('<option value="'+state_id+'">'+state_name+'</option>');
            });

            }else
            {
              $('#state').empty();

            }
            }

        });
}

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
