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
                        <div class="card-body">
                                    <a  data-toggle="modal" data-target="#StockModal01" class="btn btn-block btn-info text-white">
                                    <i class="fa fa-plus"></i> Add Town </a>
                        @if(auth()->user()->user_role_id == 0)
                        <a href=" {{ url('admin/town/restore-list') }}" class=" text-white btn btn-block btn-danger">
                                   <i class="fa fa-recycle"></i>
                                  Restore Town
                                </a>
                        @endif
                                <br>
                            <div class="table-responsive">
                            <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Town')}}</th>
                                        <th class="wd-15p">{{__('District')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($towns as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $data->town_name}}</td>
                                        <td>{{ @$data->district['district_name']}}</td>

                                        <td>
                                            <form action="{{route('admin.destroy_town',$data->town_id)}}" method="POST">
                                                @csrf
                                                <a href="{{url('admin/town/edit/'.$data->town_id)}}" class="btn btn-sm btn-cyan"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>

                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
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
                        <h5 class="modal-title" id="example-Modal3">Add New Town</h5>
                        <button type="button" class="close" data-dismiss="modal" onclick="clearTax()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_town') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">



                <label class="form-label">Country</label>
                       <select required name="country_id"  class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{request()->input('country_id') == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>

                     <label class="form-label">State</label>
                      <select required name="state_id"  class="form-control" id="state" >
                       <option {{request()->input('state_id')}} value=""> Select State</option>

                       </select>


                      <label class="form-label">District</label>
                            <select name="district_id" required class="form-control" id="city" >
                            <option value="">Select District</option>

                            </select>

 <label class="form-label">Town Name</label>
                    <input type="text" placeholder="Town Name" id="town" required class="form-control" name="town_name" >



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

 @foreach ($towns as $data)
  <div class="modal fade" id="StockModal{{$data->town_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Edit Town</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action="{{ route('admin.edit_town',$data->town_id) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">


                        <label class="form-label">Town Name</label>
                    <input type="text" placeholder="Town Name" value="{{$data->town_name}}" required class="form-control" name="town_name" >



                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>

  @endforeach


              <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>


<script>
function clearText()
{
      $('#state option:not(:first)').remove();
      $('#city option:not(:first)').remove();

      $('#country').val('');
      $('#town').val('');

}
</script>

                <script>

$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Towns',
                footer: true,
                exportOptions: {
                     columns: [0,1,2]
                 }
                 
            },
            {
                extend: 'excel',
                title: 'Towns',
                footer: true,
                exportOptions: {
                     columns: [0,1,2]
                 }
            }
         ]
    } );

} );
            </script>


<script type="text/javascript">
       $(document).ready(function() {
       $('#country').change(function(){
       /* $('#city').empty();
         $('#city').append('<option value="">Select City</option>');*/
        var country_id = $(this).val();
            //alert(country_id);
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
            // $('#city').prop("diabled",false);
            // $('#city').empty();

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
      });

    });



//display town

    $(document).ready(function() {
       $('#city').change(function(){
        var city_id = $(this).val();
       // alert(city_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_town') }}?city_id="+city_id ,

          success:function(res){

           if(res){
              console.log(res);
           // $('#town').prop("diabled",false);
           // $('#town').empty();

          //  $('#town').append('<option value="">Select Town</option>');
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
      });

    });


    $(document).ready(function() {
       $('#state').change(function(){
        var state_id = $(this).val();
        //alert(state_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_city') }}?state_id="+state_id ,

          success:function(res){
            //alert(res);
            if(res){
           // $('#city').prop("diabled",false);
            //$('#city').empty();

           // $('#city').append('<option value="">Select City</option>');
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
      });

    });

</script>

@endsection
