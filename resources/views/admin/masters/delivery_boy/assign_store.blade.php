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

            <div class="table-responsive">
               <table  class="table table-bordered text-nowrap w-80">
                 <thead>
                                 <tr>
                                   <th class="wd-15p">S.No</th>
                                    <th class="wd-15p">{{ __('Store') }}</th>
                                    <th class="wd-15p">{{ __('Phone') }}</th>
                                    <th class="wd-15p">{{ __('Action') }}</th>

                                 </tr>
                </thead>
                  <tbody>
                     @php
                     $i = 0;
                     $all_assigned_store[]  = 0;
                      @endphp
                      @foreach ($dboy_linked_stores as $data)
                       @php
                                $store_data = \DB::table('mst_stores')->where('store_id',$data->store_id)->first();
                            @endphp
                     @if($store_data)
                        <tr>
                           <td>{{ ++$i }}</td>
                           <td>{{$store_data->store_name}}</td>
                           <td>{{$store_data->store_contact_person_phone_number}}</td>
                           @php
                           $store_data = "";
                                 $all_assigned_store[] = $data->store_id;
                           @endphp
                           <td>
                               @php
                                   $stores__ids = \DB::table('mst_store_link_delivery_boys')
                                ->join('mst_stores','mst_stores.store_id','=','mst_store_link_delivery_boys.store_id')
                                ->where('mst_store_link_delivery_boys.delivery_boy_id','=',$delivery_boy->delivery_boy_id)
                                ->select('mst_stores.*')
                                ->where('mst_stores.subadmin_id',auth()->user()->id)
                                ->pluck('store_id')->toArray();
                                $thisStore = $data->store_id;
                               @endphp
                              <a class="btn btn-small btn-danger"     href="{{ url('admin/link/destroy/delivery_boy_store/'.$data->store_link_delivery_boy_id) }}">Delete</a>
                              <!--<a class="btn btn-small btn-danger @if(!in_array( $thisStore ,$stores__ids)) disabled @endif"     href="{{ url('admin/link/destroy/delivery_boy_store/'.$data->store_link_delivery_boy_id) }}">Delete</a>-->
                           
                           </td>
                        </tr>
                     @endif
                      @endforeach
                  </tbody>
               </table>
            </div>
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
               <form action="{{route('admin.add_assign_store',$delivery_boy->delivery_boy_id)}}" method="POST"
                  enctype="multipart/form-data">
                  @csrf
                  <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">

                          <input type="hidden" name="delivery_boy_id" value="{{$delivery_boy->delivery_boy_id}}">
                         <div id="store">
                           <label class="form-label">Store </label>
                           <select name="store_id[]" required="" class="form-control" >
                                 <option value=""> Select Store</option>
                                @foreach($store as $key)
                                    @if (!in_array($key->store_id, $all_assigned_store))
                                        <option {{old('store_id') == $key->store_id ? 'selected':''}} value="{{$key->store_id}}"> {{$key->store_name }} </option>
                                     @endif
                                @endforeach
                              </select>
                        </div>
                     </div>
                     </div>

                     <div class="col-md-2">
                        <div class="form-group">
                           <label class="form-label">Add more</label>
                            <button type="button" id="addstore" class="btn btn-raised btn-success"> Add More</button>
                        </div>
                        </div>
                        </div>


                     <div class="form-group">
                           <center>
                           <button type="submit"  class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Submit</button>
                           <button type="reset" class="btn btn-raised btn-success">
                           Reset</button>
                           <a class="btn btn-danger" href="{{ route('admin.list_delivery_boy') }}">Cancel</a>
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

$(document).ready(function() {
   var wrapper      = $("#store"); //Fields wrapper
  var add_button      = $("#addstore"); //Add button ID

  var x = 1; //initlal text box count


  $(add_button).click(function(e){ //on add input button click
    e.preventDefault();
    //max input box allowed
      x++; //text box increment
      $(wrapper).append('<div> <br> <label class="form-label">Stores </label> <select name="store_id[]" required="" class="form-control"  ><option value=""> Select store</option> @foreach($store as $key) @if(!in_array($key->store_id,$all_assigned_store)) <option {{old('store_id') == $key->store_id ? 'selected':''}} value="{{$key->store_id}}"> {{$key->store_name }} </option> @endif @endforeach </select><a href="#" class="remove_field btn btn-info btn btn-sm">Remove</a></div>'); //add input box

  });



  $(wrapper).on("click",".remove_field", function(e){ //user click on remove text
    e.preventDefault(); $(this).parent('div').remove(); x--;
  })
});


</script>
