@extends('admin.layouts.app')
@section('content')
<div class="container">
   <div class="row justify-content-center">
      <div class="col-md-12 col-lg-12">
         <div class="card" >
            <div class="row"  style="min-height: 70vh;">
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
                 <div class="card-body border" >
       <form action="" method="GET"
                enctype="multipart/form-data">
          @csrf
            <div class="row">

               {{-- <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Select Year</label>
                     <div id="date_fromly"></div>
  	                <select required name="year" id="year" class="form-control custom-select">
                        <option value="">Select Year</option>
                      @for ($y=2010; $y<=2040;  $y++)
							<option {{ request()->input('year') == $y ? 'selected':''}} value="{{$y}}">{{$y}}</option>
                      @endfor

						</select>

                  </div>
               </div> --}}

             {{-- <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Select Month</label>
                     <div id="date_fromlm"></div>
  	                <select required name="month" id="month" class="form-control custom-select">
    <option  value="">Select Month</option>
    <option {{ request()->input('month') == '01' ? 'selected':''}} value="01">January</option>
    <option {{ request()->input('month') == '02' ? 'selected':''}} value="02">February</option>
    <option {{ request()->input('month') == '03' ? 'selected':''}}  value="03">March</option>
    <option {{ request()->input('month') == '04' ? 'selected':''}} value="04">April</option>
    <option {{ request()->input('month') == '05' ? 'selected':''}} value="05">May</option>
    <option {{ request()->input('month') == '06' ? 'selected':''}} value="06">June</option>
    <option {{ request()->input('month') == '07' ? 'selected':''}} value="07">July</option>
    <option {{ request()->input('month') == '08' ? 'selected':''}} value="08">August</option>
    <option {{ request()->input('month') == '09' ? 'selected':''}} value="09">September</option>
    <option {{ request()->input('month') == '10' ? 'selected':''}} value="10">October</option>
    <option {{ request()->input('month') == '11' ? 'selected':''}} value="11">November</option>
    <option {{ request()->input('month') == '12' ? 'selected':''}} value="12">December</option>
						</select>

                  </div>
               </div> --}}

               <!--  <div class="col-md-6">-->
               <!-- <div class="form-group">-->
               <!--     <label class="form-label">To Date</label>-->
               <!--       <div id="date_tol"></div>-->
               <!--       <input type="month" class="form-control" id="date_to" name="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">-->

               <!--   </div>-->
               <!--</div>-->


               
         <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">From Date</label>
                 <input type="date" class="form-control" name="date_from"  value="{{ request()->input('date_from') }}" placeholder="From Date">

              </div>
           </div>
             <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">To Date</label>
                 <input type="date" class="form-control" name="date_to" value="{{ request()->input('date_to') }}" placeholder="To Date">

              </div>
           </div>
          

                     <div class="col-md-12">
                     <div class="form-group">
                           <center>
                           <button type="submit" class="btn btn-raised btn-primary">
                           <i class="fa fa-check-square-o"></i> Filter</button>
                           <button type="reset"  id="reset" class="btn btn-raised btn-success">Reset</button>
                           </center>
                        </div>
                  </div>
    </div>
       </form>
    </div>
    

         
                     <div class="card-body">


                         </br>
                        <div class="table-responsive">
                           <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                              <thead>
                                 <tr>
                                    <th class="wd-15p">SL.No</th>
                                    <th class="wd-15p">{{ __('Order Number') }}</th>
                                    <th class="wd-15p">{{ __('Order Date') }}</th>
                                    <th class="wd-15p">{{ __('Total Order Amount') }}</th>
                                    <th class="wd-15p">{{ __('Admin Commission Amount') }}</th>
                                    <th class="wd-15p">{{ __('Store Commission Amount') }}</th>
                                  
                                 </tr>
                              </thead>
                              <tbody>
                                 @php
                                 $i = 0;
                                 $total_store_amount = 0;
                                 $total_admin_amount = 0;
                                 $total_amount = 0;
                                 @endphp
                                 @foreach ($store_payments as $store_payment)
                                 <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ @$store_payment->order_number}}</td>
                                        <td>{!! date('d-M-Y', strtotime(@$store_payment->created_at)) !!}</td>
                                    <td>{{ @$store_payment->orderAmount }}</td>
                                    <td>{{ @$store_payment->orderAmount - @$store_payment->settlementAmount }}</td>
                                    <td>{{ @$store_payment->settlementAmount }}</td>
                                    

                                 </tr>
                                 @php
                                    $total_store_amount += $store_payment->settlementAmount;
                                    $total_admin_amount += (@$store_payment->orderAmount - @$store_payment->settlementAmount);
                                    $total_amount += $store_payment->orderAmount; 
                                 @endphp
                                 @endforeach
                              </tbody>
                           </table>
                        </div><br>

                        <div class="row">
                           <div class="col-6" >
                              <table class="table table-bordered text-nowrap">
                                 <tr>
                                    <td>
                                       Total Order Amount : 
                                    </td>
                                    <td>
                                        <b>{{ @$total_amount }}</b>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       Admin Commission Amount : 
                                    </td>
                                    <td>
                                       <b>{{ @$total_admin_amount }}</b>
                                    </td>
                                 </tr>

                                 <tr>
                                    <td>
                                       Store Commission Amount : 
                                    </td>
                                    <td>
                                       <b>{{ @$total_store_amount }}</b>
                                    </td>
                                 </tr>
                              </table>
                           </div>
                        </div>

                            
                            <div class="row">
                              <div class="col-4" >
                                    {{-- <a data-toggle="modal" data-target="#StockModal{{$store_id}}" class="btn btn-small btn-success">
                                        <i class="fa fa-tick"></i>
                                        Pay
                                    </a> --}}
                                    <a data-toggle="modal" data-target="#PaymentsModal" class="btn btn-small btn-primary">
                                       <i class="fa fa-tick"></i>
                                       Previous Payments
                                   </a>
                                </div>
                            </div>

                     </div>

                  </div>
               </div>
            </div>
            <div class="modal fade" id="StockModal{{$store_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                  <form action=" {{ url('admin/store/pay/'.Crypt::encryptString($store_id)) }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                   <div class="modal-body">
  @if(request()->input('date_from'))
                        <input type="hidden" name="date_of_payment" value="{{request()->input('date_from')}}" >
                    @endif
                    <label class="form-label">Pay Amount*</label>
                    <input type="text" name="commision_paid" placeholder="Pay Amount" class="form-control" required="" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')">
                    <label class="form-label">Note</label>
                    <textarea type="text" name="payment_note" placeholder="Payment Note" class="form-control"></textarea>
                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Submit</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
            <!-- MESSAGE MODAL CLOSED -->
            <div class="modal fade" id="PaymentsModal" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">{{$pageTitle}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                     <div class="table-responsive">
                        <table  class="table table-striped table-bordered text-nowrap w-100">
                           <thead>
                              <tr>
                                 <th class="wd-15p">Sl.No</th>
                                 <td>Date</td>
                                 <td>Amount</td>
                                 <td>Description</td>
                              </tr>
                           </thead>
                           <tbody>
                              @php
                              $i = 0;
                              @endphp
                              @foreach ($payments_datas as $payments_data)
                                 <tr>
                                 <td>{{ ++$i }}</td>
                                 <td>{{date('M, Y', strtotime($payments_data->date_of_payment))}}</td>
                                 <td>Rs. {{$payments_data->commision_paid}}</td>
                                 <td>{{$payments_data->payment_note}}</td>
                                 </tr>
                              @endforeach
                           </tbody>
                        </table>
                     </div>

                  </div>
               </div>
            </div>


                           <script>

$(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Store Payments',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6]
                 }
            },
            {
                extend: 'excel',
                title: 'Store Payments',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6]
                 }
            }
         ]
    } );

} );




            </script>

            @endsection
