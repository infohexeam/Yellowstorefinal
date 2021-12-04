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
                <form action="{{route('admin.list_company')}}" method="GET"
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">
               <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">Country</label>
                       <select name="country_id" required="" class="form-control" id="country" >
                                 <option value=""> Select Country</option>
                                @foreach($countries as $key)
                                <option {{request()->input('country_id') == $key->country_id ? 'selected':''}} value="{{$key->country_id}}"> {{$key->country_name }} </option>
                                @endforeach
                              </select>
                  </div>
               </div>
                   <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">State</label>
                      <select name="state_id" required="" class="form-control" id="state" >
                       <option {{request()->input('state_id')}} value=""> Select State</option>

                       </select>
                  </div>
               </div>
                <div class="col-md-4">
                  <div class="form-group">
                     <label class="form-label">District</label>
                     <select name="district_id" required="" class="form-control" id="city">
                             <option value="">Select City</option>
                          </select>
                  </div>
               </div>
                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button>
                          <a href="{{route('admin.list_company')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>
                </div>
                   </form>
                </div>

                    <div class="card-body">
                        <a href="  {{route('admin.create_company')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Company
                        </a>
                        @if(auth()->user()->user_role_id == 0)
                          <a href=" {{ url('admin/company/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i>
                          Restore Company
                        </a>
                        @endif
                        </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Company Name') }}</th>
                                    <th class="wd-15p">{{ __('Contact Person') }}</th>
                                    <th class="wd-20p">{{__('Email')}}</th>
                                    <th class="wd-20p">{{__('Country')}}</th>
                                    <th class="wd-20p">{{__('Status')}}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($companies as $company)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $company->company_name}}</td>
                                    <td>{{$company->company_contact_person_name}} </td>
                                    <td>{{ $company->company_email_address}} </td>
                                    <td>{{ $company->country['country_name']}} </td>

                                 <td>
                                       <form action="{{route('admin.status_company',$company->company_id)}}" method="POST">

                                          @csrf
                                          @method('POST')
                                          <button type="submit" onclick="return confirm('Do you want to Change status?');" class="btn btn-sm
                                          @if($company->company_account_status == 0) btn-danger @else btn-success @endif"> @if($company->company_account_status == 0)
                                          InActive
                                          @else
                                          Active
                                          @endif</button>
                                       </form>
                                    </td>
                                   <td>
                                   <form action="{{route('admin.destroy_company',$company->company_id)}}" method="POST">
                                        <a class="btn btn-sm btn-cyan"
                                             href="{{url('admin/company/edit/'.
                                          $company->company_name_slug)}}">Edit</a>
                                          <a class="btn btn-sm btn-cyan"
                                             href="{{url('admin/company/view/'.
                                          $company->company_name_slug)}}">View</a>
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
            @endsection

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
                title: 'Companies',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5]
                 }
            },
            {
                extend: 'excel',
                title: 'Companies',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5]
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


   });
});

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
            $('#city').prop("diabled",false);
            $('#city').empty();

            $('#city').append('<option value="">Select City</option>');
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
