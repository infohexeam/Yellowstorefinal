@extends('admin.layouts.app')
@section('content')

@php
use App\Models\admin\Trn_store_order;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Mst_store;

use App\Models\admin\Trn_subadmin_payments_tracker;
use App\Models\admin\Trn_sub_admin_payment_settlment;
   //dd(auth()->user()->id);
    $store_data = DB::table('mst_stores')->select("store_id")->where('subadmin_id', '=',auth()->user()->id)->where('subadmin_id', '!=',NULL)->get();
	$store_array[] = 0;
	foreach ($store_data as $val) {
		$store_array[] = $val->store_id;
	}
    $orders_count = Trn_store_order::where('subadmin_id',auth()->user()->id)->whereIn('store_id',$store_array)->count();

   //  $storesSubadmins = Mst_store::where('subadmin_id', auth()->user()->id)->groupBy('subadmin_id')->pluck('store_id');

   // $boys_count =  $delivery_boys = \DB::table('mst_delivery_boys')
	// 			->join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')
	// 			->whereIn('mst_store_link_delivery_boys.store_id', $storesSubadmins)
	// 			->count();

   $storesSubadmins = Mst_store::where('subadmin_id', auth()->user()->id)->pluck('store_id');
			
	$deliveryBoys = \DB::table('mst_delivery_boys')
				->join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')
				->whereIn('mst_store_link_delivery_boys.store_id', $storesSubadmins)
				->orderBy('mst_delivery_boys.delivery_boy_id', 'DESC')
				->groupBy('mst_store_link_delivery_boys.delivery_boy_id')
				->get();
      $boys_count = count($deliveryBoys);
           

     @$payments_datas = Trn_subadmin_payments_tracker::where('subadmin_id',auth()->user()->id)->get();
     @$payments = Trn_sub_admin_payment_settlment::where('subadmin_id',auth()->user()->id)->get();
 foreach (@$payments as $payment)
 {
    @$sub_comm = (@$payment->commision_percentage / 100) * @$payment->order['product_total_amount'];

    @$commision_pay =  $commision_pay + $sub_comm;


 }

  @$sub_admin = \DB::table('mst_subadmin_details')->where('subadmin_id',auth()->user()->id)->first();

    @$balance = \DB::table('trn_subadmin_payments_tracker')->where('subadmin_id',auth()->user()->id)->sum('commision_paid');
  @$balance =  (@$sub_admin->subadmin_commision_amount + @$commision_pay) - @$balance;

    @$received = \DB::table('trn_subadmin_payments_tracker')->where('subadmin_id',auth()->user()->id)->sum('commision_paid');


@endphp
<!-- ROW-1 -->
<div class="row" style="min-height: 70vh;">
   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-6">
      <div class="row">

          <div class="col-lg-6 ">
            <div class="card">
               <a href="{{ route('admin.list_order') }}">
                  <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-secondary mb-0 box-secondary-shadow">
                           <i class="fe fe-shopping-cart text-white"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Orders') }}</h6>
                        <h2 class="mb-2 number-font">{{@$orders_count}}</h2>
                        <p class="text-muted">{{ __('Total Orders') }}</p>
                     </div>
               </a>
               </div>
         </div>

          <div class="col-lg-6 ">
            <div class="card">
               <a href="{{ route('admin.list_delivery_boy') }}">
                     <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-primary mb-0 box-primary-shadow">
                           <i class="ti ti-truck text-white"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Delivery Boys') }}</h6>
                        <h2 class="mb-2 number-font">{{@$boys_count}}</h2>
                        <p class="text-muted">{{ __('Total Delivery Boys  ') }}</p>
                     </div>
               </a>
            </div>
         </div>

 <div class="col-lg-6 ">
            <div class="card">
               <a  href="{{url('admin/subadmin/payment_settlment/list/'.auth()->user()->name.'/'.Crypt::encryptString(auth()->user()->id))}}" >
                     <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-warning mb-0 box-primary-shadow">
                          &nbsp;  <i class="fa fa-inr text-white"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Payment Receivable') }}</h6>
                        <h2 class="mb-2 number-font">{{@$balance}}</h2>
                        <p class="text-muted">{{ __('Total Payment Receivable  ') }}</p>
                     </div>
               </a>
            </div>
         </div>



 <div class="col-lg-6 ">
            <div class="card">
               <a href="{{url('admin/subadmin/payment_settlment/list/'.auth()->user()->name.'/'.Crypt::encryptString(auth()->user()->id))}}" >
                     <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-success mb-0 box-primary-shadow">
                           &nbsp;  <i class="fa fa-inr text-white"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Payment Received') }}</h6>
                        <h2 class="mb-2 number-font">{{@$received}}</h2>
                        <p class="text-muted">{{ __('Total Payment Received  ') }}</p>
                     </div>
               </a>
            </div>
         </div>


         </div>
         </div>
         <div class="col-lg-12 col-md-12 col-sm-12 col-xl-6">
         <div class="row">

@php
        @$stores = Mst_store::where('subadmin_id',auth()->user()->id)->orderBy('store_id','desc')->get();
//print_r($stores);die;

     @endphp
@if(count($stores) > 0)                                <div class="card" style="background:#d43f8d;color:white;">
                                {{-- <div class="card-header">
										<h3 class="card-title">Stores</h3>
									</div> --}}
                                    <h5 style="background:none;" class=" ml-5  mb-2 mt-1 ">Stores Available</h5>

                                	<div class="contacts_body p-0">
										<ul class="contacts mb-0 ">
												<div class="d-flex bd-highlight">
                                                    <table class="table card-table table-vcenter text-nowrap table-secondary">
                                                           <thead class="bg-secondary text-white">
                                                            <tr>
                                                                <td class="text-white">SL. No</td>
                                                                <td class="text-white">Store Name</td>
                                                                <td class="text-white">Store Mobile</td>
                                                                <td class="text-white">Status </td>
                                                            </tr>
                                                </thead>
                                                                 @php
                                                                    $i = 0;
                                                                    @endphp
                                                    @foreach ($stores as $store)
														 <tr>
                                             <td>{{ ++$i }}</td>
                                                            <td>{{ @$store->store_name}}</td>
                                                            <td>{{ @$store->store_mobile}}</td>
                                                            <td>
                                                                 @php
                                                                  $adminData = \DB::table('trn__store_admins')->where('store_id',$store->store_id)
                                                                  ->where('role_id',0)->first();
                                                                  @endphp
                                                                   @php
                                                $storeData = App\Models\admin\Mst_store::find($store->store_id);
                                                $storeAdmData = App\Models\admin\Trn_StoreAdmin::where('store_id',$store->store_id)->where('role_id',0)->first();
                                                $today = Carbon\Carbon::now()->addDays(3);
                                                $now = Carbon\Carbon::now();
                                                $dateExp = Carbon\Carbon::parse(@$storeAdmData->expiry_date);
                                                $diff = $dateExp->diffInDays($now); //14
                                                
                                                $todayDate =  Carbon\Carbon::now()->toDateString();

                                                if(@$diff ==0){
                                                    $dayString = 'day';
                                                }else{
                                                    $dayString = 'days';
                                                     if(@$diff ==1){
                                                       $dayString = 'day';
                                                     }
                                                }
                                        @endphp
                                                            <span class="dot-label @if(@$adminData->store_account_status == 0) btn-danger @else @if($todayDate > @$storeAdmData->expiry_date) btn-danger @else  btn-success @endif  @endif"></span><span class="mr-3">
                                                           @if(@$adminData->store_account_status == 0)
                                          InActive
                                          @else
                                           @if($todayDate > @$storeAdmData->expiry_date)
                                           InActive
                                          @else
                                          Active
                                          @endif
                                          @endif
                                                            </span>
                                                            <br>
                                                             @if(@$storeAdmData->expiry_date == $todayDate)
                                <p style="font-size:9px">Store expires today</p>
                                    @elseif($todayDate > @$storeAdmData->expiry_date)
                                    <p style="font-size:9px">Store expired on <br> <b style="font-size:11px"> {{ @$storeAdmData->expiry_date}} </b><br> ({{@$diff}} {{@$dayString}} before)</p>
                                    @else
                                    @if (@$diff <= 3)
                                    <p style="font-size:9px">This account expires in <b style="font-size:11px">{{@$diff}}</b> {{@$dayString}}</p>
                                    @endif
                                    
                                    @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach

                                                     </table>


											</li>
										</ul>
								</div>
                                </div>
                        @endif

      </div>
   </div>

   <!-- COL END -->
</div>
<!-- ROW-1 END -->
</div>
</div>
<!-- CONTAINER END -->
</div>
@endsection
