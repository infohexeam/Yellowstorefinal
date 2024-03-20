 @extends('store.layouts.app')
@section('content')
@php
$date = Carbon\Carbon::now();
@endphp
<div class="container">
   <div class="row justify-content-center" style="min-height: 70vh;">
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
                 <form action="{{route('store.list_references')}}" method="GET"
                         enctype="multipart/form-data">
                   @csrf
            <div class="row">

                


             


                  {{-- @php
                   if(!@$datefrom)
                   {
                        $datefrom = $date->toDateString();
                   }

                    if(!@$dateto)
                   {
                        $dateto = $date->toDateString();
                   }
               @endphp --}}

              {{--   <div class="col-md-6">
                 <div class="form-group">
                    <label class="form-label">From Date</label>
                    <div id="date_froml"></div>
                     <input type="date" class="form-control" name="date_from" id="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

                  </div>
               </div>
                 <div class="col-md-6">
               <div class="form-group">
                    <label class="form-label">To Date</label>
                    <div id="date_tol"></div>
                     <input type="date" class="form-control"  name="date_to"  id="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

                  </div>
               </div>
           </div>
            <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                       {{--   <a href="{{route('store.list_references')}}"  class="btn btn-info">Cancel</a>
                           </center>
                        </div>
                  </div>--}}
                   </form>
               </div>

                    <div class="card-body">
                       
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                     <th class="wd-15p">Enquiry Number</th>
                                    <th class="wd-15p">Product Name</th>
                                    <th class="wd-15p">Customer Name</th>
                                    <th class="wd-15p">Customer Mobile</th>
                                    <th class="wd-15p">Enquiry Date & Time</th>
    
                                    
                                    
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 @endphp
                                 @foreach ($enquiries as $enquiry)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>ENQ-{{$enquiry->enquiry_id}}</td>
                                    <td>{{$enquiry->variant_name}}</td>
                                    <td>{{$enquiry->customer_first_name}} {{$enquiry->customer_last_name}}</td>
                                    <td>{{$enquiry->customer_mobile_number}}</td>
                                    <td>
                                 
                                    {{ date('M d Y,h:i A', strtotime(@$enquiry->created_at)) }}
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
</div>
               </div>
            </div>
            <!-- MESSAGE MODAL CLOSED -->





<!-- MESSAGE MODAL CLOSED -->

<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.3.0/firebase-messaging.js"></script>
                      <script>
 $(document).ready(function() {
$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Customer Enquiries',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4]
                 }
            },
            {
                extend: 'excel',
                title: 'Customer Enquiries',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4]
                 }
            }
         ]
    } );

} );
} );
</script>
<script>
  var firebaseConfig = {
     apiKey: "AIzaSyABJjLKVYHKL020Zdi8pbHsNS2ZLQ1Ka4Q",
    authDomain: "yellowstore-web-application.firebaseapp.com",
    projectId: "yellowstore-web-application",
    storageBucket: "yellowstore-web-application.appspot.com",
    messagingSenderId: "444886856017",
    appId: "1:444886856017:web:935481722416346323e370",
    measurementId: "G-VX5SKTNN3F"
  };
      
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
  
  document.addEventListener("DOMContentLoaded", function(){

            messaging
            .requestPermission()
            .then(function () {
                console.log("working");
                return messaging.getToken({ vapidKey: 'BP_JpvDrfy-521nigpqvOv5WTXBK5i5AMa2g8CvI-nXK_mxHAQFHHrtwJc-kLAWNBtM-AJjqEARZ_49e6Wph5e8' });
            })
            .then(function(token) {
                console.log("token : "+token);
     var _token = $('input[name="_token"]').val();
     console.log("token_2 : "+_token);

      $.ajax({
            url:"{{ route('store.saveBrowserToken') }}",
            method:"POST",
            data:{token:token, _token:_token},
            success:function(result)
            {
                console.log(result);
            }
       })         
  
            }).catch(function (err) {
                console.log('User Chat Token Error'+ err);
            });
  });      
    messaging.onMessage(function(payload) {
        const noteTitle = payload.notification.title;
        const noteOptions = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        // Play the alarm sound when a notification is received
        const alarmSound = new Audio('https://hexprojects.in/Yellowstore/assets/alaram.wav');
        alarmSound.play();
        new Notification(noteTitle, noteOptions);
    });
   
</script>




@endsection


