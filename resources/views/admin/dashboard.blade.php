@extends('admin.layouts.app')
@section('content')
{{-- @php
use App\Models\admin\category\Category;
use App\Models\admin\insurance\Insurance;
use App\Models\admin\template\Template;
@endphp --}}
<!-- ROW-1 -->
<div class="row" style="min-height: 70vh;">
   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-6">
      <div class="row">
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-primary mb-0 box-primary-shadow">
                     <i class="fe fe-trending-up text-white"></i>
                  </div>
                  <h6 class="mt-4 mb-1">{{ __('Templates') }}</h6>
                  <h2 class="mb-2 number-font">40</h2>
                  <p class="text-muted">{{ __('Registered Templates ') }}</p>
               </div>
            </div>
         </div>
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-secondary mb-0 box-secondary-shadow">
                     <i class="fe fe-codepen text-white"></i>
                  </div>
                  <h6 class="mt-4 mb-1">{{ __('Category') }}</h6>
                  <h2 class="mb-2 number-font">50</h2>
                  <p class="text-muted">{{ __('Total Category') }}</p>
               </div>
            </div>
         </div>
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-success mb-0 box-success-shadow">
                     <i class="fe fe-aperture text-white"></i>
                  </div>
                  <h6 class="mt-4 mb-1">{{ __('Insurance') }}</h6>
                  <h2 class="mb-2  number-font">50</h2>
                  <p class="text-muted">{{ __('Total Insurance') }}</p>
               </div>
            </div>
         </div>
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
            <div class="card">
               <div class="card-body text-center statistics-info">
                  <div class="counter-icon bg-info mb-0 box-info-shadow">
                     <i class="fe fe-briefcase text-white"></i>
                  </div>
                  <h6 class="mt-4 mb-1">{{ __('Jobs') }}</h6>
                  <h2 class="mb-2  number-font">30</h2>
                  <p class="text-muted">{{ __('Total Jobs') }}</p>
               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="col-sm-12 col-md-12 col-lg-12 col-xl-6">
      <div class="card">
         <div class="card-header">
            <h3 class="card-title">{{ __('Registered Job seekers') }}</h3>
         </div>
         <div class="card-body">
            <div id="container2"></div>
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