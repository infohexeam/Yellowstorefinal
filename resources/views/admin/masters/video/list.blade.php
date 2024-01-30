@extends('admin.layouts.app')
@section('content')
<style>
.exam{
     text-align:left;
  width:100%;
}



iframe{
  width: 40% ! important;
  height: 40% ! important;
}
</style>
<style>
    .buttons-html5 {
   display : none !important; /* overrides the red color */
}     
</style>
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
                        <h5>Whoops!</h5> There were some problems with your input.<br><br>
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
                    {{-- <div class="card-body border">

                </div> --}}

                    <div class="card-body">
                        <a href="  {{route('admin.create_video')}} " class="btn btn-block btn-info">
                           <i class="fa fa-plus"></i>
                           Create Video
                        </a>
                        @if(auth()->user()->user_role_id == 0)
                          <a href=" {{ url('admin/video/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i> Restore Video </a>
                         @endif  
                        </br>
                        <div class="table-responsive">
                           <table id="example" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Platform') }}</th>
                                    <th class="wd-15p">{{ __('Video Link') }}</th>
                                    <th class="wd-15p">{{ __('Visibility') }}</th>
                                    <th class="wd-15p">{{ __('Pincode') }}</th>
                                    <th class="wd-15p">{{ __('Description') }}</th>
                                    <th class="wd-15p">{{ __('Status') }}</th>
                                    <th class="wd-15p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($video as $value)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $value->platform}}</td>
                                    @php
                                       $vid = $value->video_code;
                                    @endphp
                                    <td  >
                                    <div class="exam"> 
                                    
                                    <a target="_blank" href="{!!$value->video_code!!}">{!!$value->video_code!!} </a>
                                    </div>
                                    </td>
                                    <td>@if($value->visibility == 1)
                                       Store
                                   @elseif($value->visibility == 2)
                                       Customer
                                   @elseif($value->visibility == 3)
                                       Delivery Boy
                                   @else
                                       --
                                   @endif</td>
                                   <td>{{ @$value->town->town_name }} </td>
                                   <td style="white-space: normal;">{{ $value->video_discription }} </td>
                                      <td> 
                                       @if ($value->status == 1)
                                          Active
                                       @else
                                          InActive
                                       @endif
                                      </td>

                                    <td>
                                       <form action="{{route('admin.destroy_video',$value->video_id)}}" method="POST">
                                         @csrf
                                          @method('POST')
                                          <a class="btn btn-sm btn-cyan"  href="{{url('admin/video/edit/'.Crypt::encryptString($value->video_id))}}">Edit</a>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#viewModal{{$value->video_id}}" > View</button>

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
            
            
            @foreach($video as $value)
            <div class="modal fade" id="viewModal{{$value->video_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
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
                                    <td><h6>Platform: </td><td>  {{ $value->platform }} </h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Video Link: </td><td>  {{ $value->video_code }} </h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Video Description: </td><td>  {{ $value->video_discription }} </h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Status: </td><td> 
                                        @if($value->status == 1)
                                            Active
                                        @else
                                            Inactive
                                        @endif
                                    </h6></td>
                                 </tr>
                                 
                                 <tr>
                                    <td><h6>Visibility: </td>
                                    <td>  
                                        @if($value->visibility == 1)
                                            Store
                                        @elseif($value->visibility == 2)
                                            Customer
                                        @elseif($value->visibility == 3)
                                            Delivery Boy
                                        @else
                                            --
                                        @endif
                                    </h6></td>
                                 </tr>
                                 @if( @$value->state->state_name)
                                 <tr>
                                    <td><h6>State: </td><td>  {{ @$value->state->state_name }} </h6></td>
                                 </tr>
                                 @endif
                                 
                                 @if(@$value->district->district_name)
                                 <tr>
                                    <td><h6>District: </td><td>  {{ @$value->district->district_name }} </h6></td>
                                 </tr>
                                 @endif
                                @if(@$value->town->town_name)
                                <tr>
                                    <td><h6>Pincode: </td><td>  {{ @$value->town->town_name }} </h6></td>
                                </tr>
                                @endif
                                
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

             
            @endsection
