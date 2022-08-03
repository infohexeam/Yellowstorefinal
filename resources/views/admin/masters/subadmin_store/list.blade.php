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
                 <form action="{{route('admin.list_store_subadmin')}}" method="GET"
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">
               <div class="col-md-3">
                  <div class="form-group">
                     <label class="form-label">Country</label>
                       <select name="store_country_id"  class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{request()->input('store_country_id') == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                  </div>
               </div>
                <div class="col-md-3">
                  <div class="form-group">
                     <label class="form-label">State</label>
                      <select name="store_state_id"  class="form-control" id="state" >
                       <option {{request()->input('store_state_id')}} value=""> Select State</option>
                               @if (request()->input('store_state_id'))
                                    @foreach(@$states as $key)
                                    <option {{request()->input('store_state_id') == @$key->state_id ? 'selected':''}} value="{{@$key->state_id}}"> {{@$key->state_name }} </option>
                                @endforeach
                               @endif
                       </select>
                  </div>
               </div>
                <div class="col-md-3">
                  <div class="form-group">
                     <label class="form-label">District</label>
                     <select name="store_district_id" class="form-control" id="city">
                             <option value="">Select District</option>
                               @if (request()->input('store_district_id'))
                                  @foreach(@$districts as $key)
                                    <option {{request()->input('store_district_id') == @$key->district_id ? 'selected':''}} value="{{@$key->district_id}}"> {{@$key->district_name }} </option>
                                    @endforeach
                                @endif
                          </select>
                  </div>
               </div>

                <div class="col-md-3">
                  <div class="form-group">
                     <label class="form-label">Town</label>
                     <select name="store_town_id" class="form-control" id="town">
                             <option value="">Select Town</option>
                               @if (request()->input('store_town_id'))
                                 @foreach(@$town as $key)
                                    <option {{request()->input('store_town_id') == @$key->town_id ? 'selected':''}} value="{{@$key->town_id}}"> {{@$key->town_name }} </option>
                                @endforeach
                            @endif
                        </select>
                  </div>
               </div>


             </div>
             <div class="row">
               <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label"> Name</label>
                    <input type="text" class="form-control" name="store_name" value="{{request()->input('store_name')}}" placeholder="Store Name">
                        </div>
                      </div>
                  <div class="col-md-4">
                   <div class="form-group">
                    <label class="form-label"> Email</label>
                    <input type="email" class="form-control" name="store_email_address" value="{{request()->input('store_email_address')}}" placeholder="Store Email">
                        </div>
                      </div>

                <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label"> Mobile</label>
                  <input type="text" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" maxlength="10" name="store_contact_person_phone_number" class="form-control"  value="{{request()->input('store_contact_person_phone_number')}}" placeholder="Contact Person Number">
                        </div>
                      </div>
                    </div>
                        <div class="row">
               <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label"> Status</label>
                    <select name="store_account_status" id="store_account_status"  class="form-control" >
                 <option value="" >Select Status</option>
                 <option {{request()->input('store_account_status') == '1' ? 'selected':''}} value="1" >Active</option>
                 <option {{request()->input('store_account_status') == '0' ? 'selected':''}} value="0" >InActive</option>
                 </select>
                        </div>
                      </div>

               {{--
                <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label"> Mobile</label>
                  <input type="text" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" name="store_contact_person_phone_number" class="form-control"  value="{{old('store_contact_person_phone_number')}}" placeholder="Contact Person Number">
                        </div>
                      </div> --}}
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('admin.list_store')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                   </form>
                </div>

                    <div class="card-body">
                        <a href="  {{route('admin.create_store')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create store
                        </a>
                        </br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Name') }}</th>
                                    <th class="wd-15p">{{ __('Contact Person') }}</th>
                                    <th class="wd-20p">{{__('Email')}}</th>

                                    <th class="wd-20p">{{__('Status')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($stores as $store)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ @$store->store['store_name']}}</td>
                                    <td>{{@$store->store['store_contact_person_name']}} </td>
                                    <td>{{ @$store->store['email']}} </td>

                                 <td>
                                    @php
                                            $storeData = App\Models\admin\Mst_store::find($store->store_id);
                                            $storeAdmData = App\Models\admin\Trn_StoreAdmin::where('store_id',$store->store_id)->where('role_id',0)->first();
                                             $today = Carbon\Carbon::now()->addDays(3);
                                                $now = Carbon\Carbon::now();
                                                $dateExp = Carbon\Carbon::parse(@$storeAdmData->expiry_date);
                                                $diff = $dateExp->diffInDays($now) + 1; //14
                                                
                                                $todayDate =  Carbon\Carbon::now()->toDateString();

                                                if(@$diff == 1){
                                                    $dayString = 'day';
                                                }else{
                                                    $dayString = 'days';
                                                }
                                        @endphp

                                    
                                       <form action="{{route('admin.status_store_subadmin',$store->store_id)}}" method="POST">
                                           

                                          @csrf
                                          @method('POST')
                                          <button type="submit"  onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                          @if(@$adminData->store_account_status == 0) btn-danger @else @if($todayDate > @$storeAdmData->expiry_date) btn-danger @else  btn-success @endif  @endif"> @if(@$adminData->store_account_status == 0)
                                          InActive
                                          @else
                                           @if($todayDate > @$storeAdmData->expiry_date)
                                           Expired
                                          @else
                                          Active
                                          @endif
                                          @endif</button>
                                       </form>

                                    </td>
                                    <td>
                                <form action="{{route('admin.destroy_store_subadmin',$store->store_link_subadmin_id)}}" method="POST">
                           <a class="btn btn-sm btn-cyan" href="{{url('admin/store/edit/subadmin/'.Crypt::encryptString($store->store_link_subadmin_id) )}}">Edit</a>
                             <a class="btn btn-sm btn-info" href="{{url('admin/store/assign_agency/subsdmin/'.Crypt::encryptString($store->store_link_subadmin_id))}}"> Agency</a>
                              <a class="btn btn-sm btn-info" href="{{url('admin/store/assign_delivery_boy/subadmin/'.Crypt::encryptString( $store->store_link_subadmin_id))}}">Delivery Boy</a>
                                 <a class="btn btn-sm btn-primary" href="{{url('admin/store/view/subadmin/'.Crypt::encryptString($store->store_link_subadmin_id))}}">View</a>
                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
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

            <!-- MESSAGE MODAL CLOSED -->

                 <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js">
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


