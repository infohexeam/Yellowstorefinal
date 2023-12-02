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
                   @if ($message = Session::get('error'))
                            <div class="alert alert-danger">
                                <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
                            </div>
                            @endif
                  <div class="col-lg-12">
                     @if ($errors->any())
                     <div class="alert alert-danger">
                        <h6>Whoops!</h6> There were some problems with your input.<br><br>
                        <ul>
                           @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                           @endforeach
                        </ul>
                     </div>
                     @endif
                     <div class="card-header">
                        <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                     </div>

                        <div class="card-body border">
                <form action="{{route('admin.list_subadmin')}}" method="GET"
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">
               <div class="col-md-3">
                  <div class="form-group">
                     <label class="form-label">Country</label>
                       <select name="country_id"  class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{request()->input('country_id') == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                  </div>
               </div>
                <div class="col-md-3">
                  <div class="form-group">
                     <label class="form-label">State</label>
                      <select name="state_id"  class="form-control" id="state" >
                       <option {{request()->input('state_id')}} value=""> Select State</option>
                               @if (request()->input('state_id'))
                                    @foreach(@$states as $key)
                                    <option {{request()->input('state_id') == @$key->state_id ? 'selected':''}} value="{{@$key->state_id}}"> {{@$key->state_name }} </option>
                                @endforeach
                               @endif
                       </select>
                  </div>
               </div>
                <div class="col-md-3">
                  <div class="form-group">
                     <label class="form-label">District</label>
                     <select name="district_id" class="form-control" id="city">
                             <option value="">Select District</option>
                               @if (request()->input('district_id'))
                                  @foreach(@$districts as $key)
                                    <option {{request()->input('district_id') == @$key->district_id ? 'selected':''}} value="{{@$key->district_id}}"> {{@$key->district_name }} </option>
                                    @endforeach
                                @endif
                          </select>
                  </div>
               </div>

                <div class="col-md-3">
                  <div class="form-group">
                     <label class="form-label">Pincode</label>
                     <select name="town_id" class="form-control" id="town">
                             <option value="">Select Pincode</option>
                               @if (request()->input('town_id'))
                                 @foreach(@$town as $key)
                                    <option {{request()->input('town_id') == @$key->town_id ? 'selected':''}} value="{{@$key->town_id}}"> {{@$key->town_name }} </option>
                                @endforeach
                            @endif
                        </select>
                  </div>
               </div>

             </div>


                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                          <a href="{{route('admin.list_subadmin')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                   </form>
                </div>

                     <div class="card-body">
                        <a href=" {{route('admin.create_subadmin')}}" class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Sub Admin
                        </a>
                        @if(auth()->user()->user_role_id == 0)
                         <a href=" {{ url('admin/subadmin/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i>
                          Restore Sub Admin
                        </a>
                        @endif
                        
                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Mobile') }}</th>
                                    <th class="wd-15p">{{ __('Pincode') }}</th>
                                    <th class="wd-15p">{{ __('Commision') }}<br> Amount</th>
                                    <th class="wd-15p">{{ __('Commision') }}<br> Percentage</th>
                                    <th class="wd-15p">{{ __('Assigned') }}<br>Stores</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($subadmins as $subadmin)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                   
                                    <!--<td>{{ @$subadmin->name}}</td>-->
                                    <td>{{ @$subadmin->admin_name}}</td>

                                    <td>{{ @$subadmin->subadmins->phone}}</td>
                                    <td>{{ @$subadmin->subadmins->town['town_name']}}</td>
                                    <td>{{ @$subadmin->subadmins->subadmin_commision_amount}}</td>
                                    <td>{{ @$subadmin->subadmins['subadmin_commision_percentage']}}</td>
                                    @php

                                   // \DB::enableQueryLog();

                                       $store_data = \DB::table('mst_stores')
                                        ->where('subadmin_id',$subadmin->id)
                                        ->get();
                                        
                                         $store_dataC = \DB::table('mst_stores')
                                        ->where('subadmin_id',$subadmin->id)
                                        ->count();
                                //  dd($store_data);
                @endphp


                                        <td>
                                            @if($store_dataC > 0 )
                                                @foreach($store_data as  $store)
                                                {{ @$store->store_name}} <br>
                                                @endforeach
                                            @else
                                                ---
                                            @endif
                                        </td>




                                    <td>
                                       <form action="{{route('admin.destroy_subadmin',$subadmin->id)}}" method="POST">
                                         <a class="btn btn-sm btn-cyan"
                                             href="{{url('admin/subadmin/edit/'.Crypt::encryptString($subadmin->id) )}}">Edit</a>
                                          <a class="btn btn-sm btn-primary"
                                             href="{{url('admin/store/assign_subadmin/'.Crypt::encryptString($subadmin->id) )}}">Assign Store</a>

                                     <a class="btn btn-sm btn-success" data-toggle="modal" data-target="#viewModal{{$subadmin->id}}"
                                             >View</a>

                                          @csrf
                                          @method('POST')
                                          @if(@$subadmin->id!=2)
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                          @endif
                                       </form>
                                    </td>
                                 </tr>
                                 @endforeach
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>



@foreach($subadmins as $subadmin)
            <div class="modal fade" id="viewModal{{$subadmin->id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>
                     <div class="modal-body">

                        <div class="table-responsive ">
                           <table class="table row table-borderless">
                              <tbody class="col-lg-12 col-xl-12 p-0">

                                 <tr>
                                    <td><h6>Name:</td><td>{{ @$subadmin->name}}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Phone:</td><td>{{  @$subadmin->subadmins->phone }}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Pincode:</td><td>{{ @$subadmin->subadmins->town['town_name']}}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Address:</td><td>{{ @$subadmin->subadmins['subadmin_address']}}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Commission Amount:</td><td>{{ @$subadmin->subadmins['subadmin_commision_amount']}}</h6></td>
                                 </tr>
                                  <tr>
                                    <td><h6>Commission Percentage:</td><td>{{ @$subadmin->subadmins['subadmin_commision_percentage']}}</h6></td>
                                 </tr>


                                    @php
                                       // $store_data = \DB::table('mst_store_link_subadmins')
                                       // ->join('mst_stores','mst_stores.store_id','=','mst_store_link_subadmins.store_id')
                                       // ->where('mst_store_link_subadmins.subadmin_id',$subadmin->id)
                                       // ->select('mst_stores.*')
                                       // ->get();

                    $store_data = \DB::table('mst_stores')
                                        ->where('subadmin_id',$subadmin->id)
                                        ->get();
                                    //    echo "<pre>";
                                     //   print_r($store_data);die;
                                    @endphp

                                <tr>
                                    <td><h6>Stores:</h6></td>
                                    <td>
                                      @foreach($store_data as  $store)
                                            {{ @$store->store_name}} <br>
                                     @endforeach
                                    </td>
                                 </tr>

                              </tbody>
                           </table>
                        </div>

                     </div>
                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                  </div>
               </div>
            </div>
            @endforeach

            <!-- MESSAGE MODAL CLOSED -->



              <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
</script>
<script type="text/javascript">


$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: ' Sub Admin',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6]
                 }
            },
            {
                extend: 'excel',
                title: ' Sub Admin',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6]
                 }
            }
         ]
    } );

} );


$(document).ready(function() {
 $('#reset').click(function(){
     $('#country').val('');
     $('#state option:not(:first)').remove();

     $('#city option:not(:first)').remove();

     $('#town option:not(:first)').remove();

   });
});




       $(document).ready(function() {
        var coc = 0;
       $('#country').change(function(){

           if(coc != 0)
           {


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
           else{
               coc++;
           }

      });

    });



//display town

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
          url:"{{ url('admin/ajax/get_town') }}?city_id="+city_id ,

          success:function(res){

           if(res){
             // console.log(res);
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
           else
           {
               cc++;
           }

      });

    });


    $(document).ready(function() {

        var dc = 0;

       $('#state').change(function(){
    if(dc != 0)
    {


        var state_id = $(this).val();
        //alert(state_id);
        var _token= $('input[name="_token"]').val();

        $.ajax({
          type:"GET",
          url:"{{ url('admin/ajax/get_city') }}?state_id="+state_id ,

          success:function(res){
            //alert(res);
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
    else
    {
        dc++;
    }
      });

    });

</script>
            @endsection
