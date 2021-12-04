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
                                    <a  data-toggle="modal" data-target="#StockModal01" class="text-white btn btn-block btn-info">
                                    <i class="fa fa-plus"></i> Add District </a>
                        @if(auth()->user()->user_role_id == 0)        
                        <a href=" {{ url('admin/district/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i>
                          Restore District
                        </a>
                        @endif
                                <br>
                            <div class="table-responsive">
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('District')}}</th>
                                        <th class="wd-15p">{{__('State')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($districts as $data)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $data->district_name}}</td>
                                        <td>{{ $data->state['state_name']}}</td>

                                        <td>
                                            <form action="{{route('admin.destroy_district',$data->district_id)}}" method="POST">
                                                @csrf
                <a href="{{url('admin/district/edit/'.$data->district_id)}}" class="btn btn-sm btn-cyan">Edit</a>

                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>
                                        {{-- {{ $districts->links() }} --}}

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

                 <form action=" {{ route('admin.create_district') }} " method="POST" enctype="multipart/form-data" >
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

                    <label class="form-label">District Name</label>
                    <input type="text" required class="form-control" placeholder="District Name" id="city" name="district_name" >





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
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>

<script>
function clearTax()
{
      $('#state option:not(:first)').remove();

      $('#country').val('');
      $('#city').val('');

}
</script>

<script type="text/javascript">

       $(document).ready(function() {
       $('#country').change(function(){

        var country_id = $(this).val();
        var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_state') }}?country_id="+country_id,


          success:function(res){
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
      });

    });




       $(document).ready(function() {
        var  ccc = 0;
       $('.ecountry').change(function(){
            if(ccc != 0 ){


        var country_id = $(this).val();
        var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_state') }}?country_id="+country_id,


          success:function(res){
            if(res){
            $('.estate').prop("diabled",false);
            $('.estate').empty();
            $('.estate').append('<option value="">Select State</option>');
            $.each(res,function(state_id,state_name)
            {
              $('.estate').append('<option value="'+state_id+'">'+state_name+'</option>');
            });

            }else
            {
              $('.estate').empty();

            }
            }

        });
        }
            else
            {
                ccc++;
            }
      });

    });




</script>
@endsection
