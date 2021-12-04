@extends('admin.layouts.app')
@section('content')
 @php
use App\Models\admin\Mst_store_companies;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Mst_store;
@endphp
<!-- ROW-1 -->
<div class="row" style="min-height: 70vh;">
   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-6">
      <div class="row">
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('admin.list_customer') }}">
                     <div class="card-body text-center statistics-info">
                        <div class="counter-icon bg-primary mb-0 box-primary-shadow">
                           <i class="fe fe-users text-white"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Customers') }}</h6>
                        <h2 class="mb-2 number-font">{{Trn_store_customer::count()}}</h2>
                        <p class="text-muted">{{ __('Registered Customers ') }}</p>
                     </div>
               </a>
            </div>
         </div>
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('admin.list_order') }}">
                  <div class="card-body text-center statistics-info">
                     <div class="counter-icon bg-secondary mb-0 box-secondary-shadow">
                           <i class="fe fe-shopping-cart text-white"></i>
                        </div>
                        <h6 class="mt-4 mb-1">{{ __('Orders') }}</h6>
                        <h2 class="mb-2 number-font">{{Trn_store_order::count()}}</h2>
                        <p class="text-muted">{{ __('Total Orders') }}</p>
                     </div>
               </a>
               </div>
         </div>
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('admin.list_company') }}">
                  <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-success mb-0 box-success-shadow">
                     <i class="fa fa-building-o text-white"></i>
                  </div>
                  <h6 class="mt-4 mb-1">{{ __('Companies') }}</h6>
                  <h2 class="mb-2  number-font">{{Mst_store_companies::count()}}</h2>
                  <p class="text-muted">{{ __('Total Companies') }}</p>
               </div>
               </a>
            </div>
         </div>
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <a href="{{ route('admin.list_store') }}">

               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-info mb-0 box-info-shadow">
                     <i class="fe fe-briefcase text-white"></i>
                  </div>
                  <h6 class="mt-4 mb-1">{{ __('Stores') }}</h6>
                  <h2 class="mb-2  number-font">{{Mst_store::count()}}</h2>
                  <p class="text-muted">{{ __('Total Stores') }}</p>
               </div>
               </a>
            </div>
         </div>
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
