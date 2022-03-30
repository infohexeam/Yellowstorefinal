<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar">
  <div class="side-header" style="background-color: #9e9e9e;">
    <a class="header-brand1 " style="display: flex;align-items: center;" href="{{route('home')}}">
    <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img desktop-logo " alt="logo">
      <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img toggle-logo" alt="logo">
      <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img light-logo" alt="logo">
      <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img light-logo1" style="margin-left: 50%;" alt="logo">
      </a><!-- LOGO -->
      <a aria-label="Hide Sidebar" class="app-sidebar__toggle ml-auto" data-toggle="sidebar" href="#"></a><!-- sidebar-toggle-->
    </div>


   <!--  <div class="app-sidebar__user">
      <div class="dropdown user-pro-body text-center">
        <div class="user-pic">
          <img src="{{URL::to('/assets/uploads/admin.png')}}" alt="user-img" class="avatar-xl rounded-circle">
        </div>



        <div class="user-info">
          <h6 class=" mb-0 text-dark">{{Auth::user()->name}}</h6>
          <span class="text-muted app-sidebar__user-name text-sm">{{Auth::user()->email}}</span>
        </div>
      </div>
    </div>
   <div class="sidebar-navs">
      <ul class="nav  nav-pills-circle" style="justify-content: center;">
        {{-- <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Followers">
          <a class="nav-link text-center m-2">
            <i class="fe fe-user"></i>
          </a>
        </li> --}}
        <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Logout">
          <a class="nav-link text-center m-2" href="{{ route('logout') }}" onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            <i class="fe fe-power"></i>
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>
         <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Profile Edit">
          <a class="nav-link text-center m-2" href=" {{ route('admin.profile') }} " >
            <i class="fe fe-edit"></i>
          </a>

        </li>
      </ul>
    </div>-->
    

    @if(Auth::check() && Auth::user()->user_role_id == 0 )
    <ul class="side-menu">
         <div class="app-sidebar__user">
      <div class="dropdown user-pro-body text-center">
        <div class="user-pic pt-4">
          <img src="{{URL::to('/assets/uploads/admin.png')}}" alt="user-img" class="avatar-xl rounded-circle">
        </div>



        <div class="user-info">
          <h6 class=" mb-0 text-dark">{{Auth::user()->admin_name}}</h6>
          <!---<span class="text-muted app-sidebar__user-name text-sm">{{Auth::user()->email}}</span>-->
        </div>
      </div>
    </div>
     <!-- <li><h3>Main</h3></li>-->
      <li class="slide">
        <a class="side-menu__item" href="{{route('home')}}">
          <i class="side-menu__icon ti-shield"></i>
          <span class="side-menu__label"> Dashboard</span>
        </a>
      </li>

      <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-panel"></i><span class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
                      <li><a class="slide-item" href="{{route('admin.list_business_type')}}">{{ __('Business Types') }}</a></li>
                      <li><a class="slide-item" href="{{route('admin.list_category')}}">{{ __('Product Category') }}</a></li>
                      <li><a class="slide-item" href="{{route('admin.sub_category')}}">{{ __('Product Sub Category') }}</a></li>
                      {{-- <li><a class="slide-item" href="{{route('admin.list_districts')}}">{{ __('Districts') }}</a></li> --}}
                      <li><a class="slide-item" href="{{route('admin.list_towns')}}">{{ __('Pincodes') }}</a></li>
                        <li><a class="slide-item" href="{{route('admin.list_vihicle_types')}}">{{ __('Vehicle Types') }}</a></li>
                      <li><a class="slide-item" href="{{route('admin.list_taxes')}}">{{ __('Tax') }}</a></li>
                      <li><a class="slide-item" href="{{route('admin.videos')}}">{{ __('Video') }}</a></li>
                      
   <li><a class="slide-item" href="{{route('admin.list_attribute_group')}}">{{ __('Attribute Group') }}</a></li>
     <li><a class="slide-item" href="{{route('admin.list_attribute_value')}}">{{ __('Attribute Value') }}</a></li>
                      <li><a class="slide-item" href="{{route('admin.list_feedback_questions')}}">{{ __('Feedback Questions') }}</a></li>


                <!--<li><a class="slide-item" href="{{route('admin.list_product')}}">{{ __('Product') }}</a></li>      -->
                <!--<li><a class="slide-item" href="{{route('admin.list_payment_settlment')}}">{{ __('Store Payment Settlment') }}</a></li> -->
                <!--<li><a class="slide-item" href="{{route('admin.list_delivery_boy_payment_settlment')}}">{{ __('Delivery Boy Payment Settlment') }}</a></li> -->
                <!--<li><a class="slide-item" href="{{route('admin.list_delivery_boy_order')}}">{{ __('Delivery Boy Order') }}</a></li> -->
                <!--<li><a class="slide-item" href="{{route('admin.list_reward_transaction_type')}}">{{ __('Reward Transaction Type') }}</a></li> -->
                <!--<li><a class="slide-item" href="{{route('admin.list_customer_reward')}}">{{ __('Customer Rewards') }}</a></li> -->
        </ul>
      </li>

      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_subadmin')}}">
          <i class="side-menu__icon ti-user"></i>
          <span class="side-menu__label"> {{ __('Sub Admin') }}</span>
        </a>
      </li>
      
      
       

       <li class="slide">
        <a class="side-menu__item" href="{{route('admin.global_products')}}">
          <i class="side-menu__icon ti-world"></i>
          <span class="side-menu__label"> {{ __('Global Products') }}</span>
        </a>
      </li>

      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_store')}}">
          <i class="side-menu__icon ti-truck"></i>
          <span class="side-menu__label"> {{ __('Stores') }}</span>
        </a>
      </li>


      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_agency')}}">
          <i class="side-menu__icon ti-agenda"></i>
          <span class="side-menu__label"> {{ __('Agencies') }}</span>
        </a>
      </li>

      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_company')}}">
          <i class="side-menu__icon ti-receipt"></i>
          <span class="side-menu__label"> {{ __('Companies') }}</span>
        </a>
      </li>

      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_customer')}}">
          <i class="side-menu__icon ti-face-smile"></i>
          <span class="side-menu__label"> {{ __('Customers') }}</span>
        </a>
      </li>
      
    
        <li class="slide">
 <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti ti-file"></i><span class="side-menu__label">{{ __('Reports') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
              <li><a class="slide-item" href="{{route('admin.show_reports')}}">{{ __('Product Wise Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.product_visit_reports')}}">{{ __('Product Visit Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.store_visit_reports')}}">{{ __('Store Visit Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.sales_reports')}}">{{ __('Sales Reports') }}</a></li>

      
              <li><a class="slide-item" href="{{route('admin.online_sales_reports')}}">{{ __('Online Sales Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.offline_sales_reports')}}">{{ __('Offline Sales Reports') }}</a></li>

              <li><a class="slide-item" href="{{route('admin.payment_reports')}}">{{ __('Payment Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.delivery_reports')}}">{{ __('Delivery Reports') }}</a></li>
              
                <li><a class="slide-item" href="{{route('admin.inventory_reports')}}">{{ __('Inventory Reports') }}</a></li>
                  <li><a class="slide-item" href="{{route('admin.out_of_stock_reports')}}">{{ __('Out of Stock Reports') }}</a></li>
                  <li><a class="slide-item" href="{{route('admin.referal_reports')}}">{{ __('Referral Reports') }}</a></li>

              
            </ul>
        </li>
      
       <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_delivery_boy')}}">
          <i class="side-menu__icon ti ti-truck"></i>
          <span class="side-menu__label"> {{ __('Delivery Boys') }}</span>
        </a>
      </li>
      

   

      <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon
ti ti-shopping-cart"></i><span class="side-menu__label">{{ __('Orders') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
          <li><a class="slide-item" href="{{route('admin.list_order')}}">{{ __('Store Order') }}</a></li>
          <li><a class="slide-item" href="{{route('admin.list_delivery_boy_order')}}">{{ __('Delivery Boy Orders') }}</a></li>
        </ul>
      </li>


      <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon
    ti ti-desktop"></i><span class="side-menu__label">{{ __('Banners') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
          <li><a class="slide-item" href="{{route('admin.list_customer_app_banners')}}">{{ __('Customer App Banners') }}</a></li>
          <li><a class="slide-item" href="{{route('admin.list_store_app_banners')}}">{{ __('Store App Banners') }}</a></li>
        </ul>
      </li>



      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_payment')}}">
          <i class="side-menu__icon ti ti ti-import"></i>
          <span class="side-menu__label"> {{ __('Incoming Payments') }}</span>
        </a>
      </li>

      <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti ti-export"></i><span class="side-menu__label">{{ __('Payouts') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
          <li><a class="slide-item" href="{{route('admin.list_subadmin_payment_settlments')}}">{{ __('Sub Admin Payouts') }}</a></li>
          <li><a class="slide-item" href="{{route('admin.list_payment_settlments')}}">{{ __('Store Payouts') }}</a></li>
          <li><a class="slide-item" href="{{route('admin.list_delivery_boys_payment_settlment')}}">{{ __('Delivery Boy Payouts') }}</a></li>
        </ul>
      </li>

      <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon fa fa-heart"></i><span class="side-menu__label">{{ __('Loyalty Programs') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
          <li><a class="slide-item" href="{{route('admin.list_configure_points')}}">{{ __('Configure Points') }}</a></li>
          <li><a class="slide-item" href="{{route('admin.list_customer_reward')}}">{{ __('Customer Rewards') }}</a></li>
          <li><a class="slide-item" href="{{route('admin.list_points_to_customer')}}">Reward Points of<br> Non-existing Customer</a></li>
        </ul>
      </li>

       <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti ti-comments"></i><span class="side-menu__label">{{ __('Disputes') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
          <li><a class="slide-item" href="{{route('admin.list_disputes')}}">{{ __('Disputes') }}</a></li>
          <li><a class="slide-item" href="{{route('admin.list_issues')}}">{{ __('Issues') }}</a></li>
        </ul>
      </li>

      {{-- <li class="slide">
        <a class="side-menu__item" href="">
          <i class="side-menu__icon ti ti-comments"></i>
          <span class="side-menu__label"> {{ __('Disputes') }}</span>
        </a>
      </li> --}}
      
          <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_reviews')}}">
          <i class="side-menu__icon fe fe-square"></i>
          <span class="side-menu__label"> {{ __('Reviews') }}</span>
        </a>
      </li>



      <!-- <li class="slide">-->
      <!--  <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-panel"></i><span class="side-menu__label">{{ __('DashBoard') }}</span><i class="angle fa fa-angle-right"></i></a>-->
      <!--  <ul class="slide-menu">-->

      <!--    <li><a class="slide-item" href="#">{{ __('Settings') }}</a></li>-->

      <!--  </ul>-->
      <!--</li>-->


      <!--<li class="slide">-->
      <!--  <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-panel"></i><span class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>-->
      <!--  <ul class="slide-menu">-->

      <!--    <li><a class="slide-item" href="{{route('admin.list_delivery_boy')}}">{{ __('Delivery Boys') }}</a></li>     -->


      <!--  {{-- <li><a class="slide-item" href="{{route('admin.list_category')}}">{{ __('Category') }}</a></li>  --}}-->

      <!--  {{-- <li><a class="slide-item" href="{{route('admin.list_business_type')}}">{{ __('Business Type') }}</a></li>  --}}-->

      <!--  {{-- <li><a class="slide-item" href="{{route('admin.list_store')}}">{{ __('Stores') }}</a></li> -->
      <!--  <li><a class="slide-item" href="{{route('admin.list_agency')}}">{{ __('Agencies') }}</a></li> -->
      <!--  <li><a class="slide-item" href="{{route('admin.list_company')}}">{{ __('Companies') }}</a></li> -->
      <!--  <li><a class="slide-item" href="{{route('admin.list_customer')}}">{{ __('Customers') }}</a></li>-->
      <!--  <li><a class="slide-item" href="{{route('admin.list_subadmin')}}">{{ __('Sub Admin') }}</a></li>-->

      <!--  <li><a class="slide-item" href="{{route('admin.list_delivery_boy')}}">{{ __('Delivery Boys') }}</a></li>     -->

      <!--  <li><a class="slide-item" href="{{route('admin.list_order')}}">{{ __('Order') }}</a></li>     -->

      <!--  <li><a class="slide-item" href="{{route('admin.list_payment')}}">{{ __('Payments') }}</a></li> -->


      <!--  <li><a class="slide-item" href="{{route('admin.list_attribute_group')}}">{{ __('Attribute Group') }}</a></li>  --}}-->
      <!--  {{-- <li><a class="slide-item" href="{{route('admin.list_attribute_value')}}">{{ __('Attribute Value') }}</a></li>  --}}-->

        <!--<li><a class="slide-item" href="{{route('admin.list_product')}}">{{ __('Product') }}</a></li>      -->

        <!-- <li><a class="slide-item" href="{{route('admin.list_payment_settlment')}}">{{ __('Store Payment Settlment') }}</a></li> -->
        <!--  <li><a class="slide-item" href="{{route('admin.list_delivery_boy_payment_settlment')}}">{{ __('Delivery Boy Payment Settlment') }}</a></li> -->
        <!-- <li><a class="slide-item" href="{{route('admin.list_delivery_boy_order')}}">{{ __('Delivery Boy Order') }}</a></li> -->

        <!--  <li><a class="slide-item" href="{{route('admin.list_reward_transaction_type')}}">{{ __('Reward Transaction Type') }}</a></li> -->
        <!--  <li><a class="slide-item" href="{{route('admin.list_customer_reward')}}">{{ __('Customer Rewards') }}</a></li> -->

      <!--  </ul>-->
      <!--</li>-->

      <!--<li><h3>General</h3></li>-->

       <li class="slide sid-m" >
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-settings"></i><span class="side-menu__label">{{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">

           <li><a class="slide-item" href="{{route('admin.password')}}">{{ __('Change Password') }}</a></li>
           <li><a class="slide-item" href="{{route('admin.edit_terms')}}">{{ __('Store Terms & Conditions') }}</a></li>
           <li><a class="slide-item" href="{{route('admin.edit_terms_customer')}}">{{ __('Customer Terms & Conditions') }}</a></li>

        </ul>
      </li>


    </ul>

    @else

         <ul class="side-menu" style="min-height: 90vh;">
         <div class="app-sidebar__user">
      <div class="dropdown user-pro-body text-center">
        <div class="user-pic pt-4">
          <img src="{{URL::to('/assets/uploads/admin.png')}}" alt="user-img" class="avatar-xl rounded-circle">
        </div>



        <div class="user-info">
          <h6 class=" mb-0 text-dark">{{Auth::user()->name}}</h6>
          <!---<span class="text-muted app-sidebar__user-name text-sm">{{Auth::user()->email}}</span>-->
        </div>
      </div>
    </div>






     <!-- <li><h3>Main</h3></li>-->
      <li class="slide">
        <a class="side-menu__item" href="{{route('home')}}"><i class="side-menu__icon ti ti-shield"></i><span class="side-menu__label"> Dashboard</span></a>


      </li>

       <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_store_subadmin')}}">
          <i class="side-menu__icon ti ti-archive"></i>
          <span class="side-menu__label"> {{ __('Stores') }}</span>
        </a>
      </li>


      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_delivery_boy')}}">
          <i class="side-menu__icon ti ti-truck"></i>
          <span class="side-menu__label"> {{ __('Delivery Boys') }}</span>
        </a>
      </li>

      <li class="slide">
        <a class="side-menu__item" href="{{route('admin.list_order')}}">
          <i class="side-menu__icon ti ti-shopping-cart-full"></i>
          <span class="side-menu__label"> {{ __('Order') }}</span>
        </a>
      </li>
      
      

      {{-- @php
    use Hash;
    @endphp --}}

     <li class="slide">
        <a class="side-menu__item" href="{{url('admin/subadmin/payment_settlment/list/'.auth()->user()->name.'/'.Crypt::encryptString(auth()->user()->id))}}" >
          <i class="side-menu__icon ti ti ti-import"></i>
          <span class="side-menu__label"> {{ __('Payments') }}</span>
        </a>
      </li>


 <li class="slide">
 <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti ti-file"></i><span class="side-menu__label">{{ __('Reports') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
              <li><a class="slide-item" href="{{route('admin.show_reports')}}">{{ __('Product Wise Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.product_visit_reports')}}">{{ __('Product Visit Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.store_visit_reports')}}">{{ __('Store Visit Reports') }}</a></li>
              
                            <li><a class="slide-item" href="{{route('admin.sales_reports')}}">{{ __('Sales Reports') }}</a></li>

              
              <li><a class="slide-item" href="{{route('admin.online_sales_reports')}}">{{ __('Online Sales Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.offline_sales_reports')}}">{{ __('Offline Sales Reports') }}</a></li>

              <li><a class="slide-item" href="{{route('admin.payment_reports')}}">{{ __('Payment Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('admin.delivery_reports')}}">{{ __('Delivery Reports') }}</a></li>
              
                <li><a class="slide-item" href="{{route('admin.inventory_reports')}}">{{ __('Inventory Reports') }}</a></li>
                  <li><a class="slide-item" href="{{route('admin.out_of_stock_reports')}}">{{ __('Out of Stock Reports') }}</a></li> 
              
            </ul>
        </li>

      <!-- <li class="slide">-->
      <!--  <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-panel"></i><span class="side-menu__label">{{ __('DashBoard') }}</span><i class="angle fa fa-angle-right"></i></a>-->
      <!--  <ul class="slide-menu">-->

      <!--    <li><a class="slide-item" href="#">{{ __('Settings') }}</a></li>-->

      <!--  </ul>-->
      <!--</li>-->


      <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-panel"></i><span class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">

              <li><a class="slide-item" href="{{route('admin.list_category')}}">{{ __('Product Category') }}</a></li>

       <li><a class="slide-item" href="{{route('admin.list_attribute_group')}}">{{ __('Attribute Group') }}</a></li>
     <li><a class="slide-item" href="{{route('admin.list_attribute_value')}}">{{ __('Attribute Value') }}</a></li>
                        <li><a class="slide-item" href="{{route('admin.list_vihicle_types')}}">{{ __('Vehicle Types') }}</a></li>
  <!--<li><a class="slide-item" href="{{route('admin.videos')}}">{{ __('Video') }}</a></li>-->
                      <li><a class="slide-item" href="{{route('admin.sub_category')}}">{{ __('Product Sub Category') }}</a></li>


        </ul>
      </li>
       <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-settings"></i><span class="side-menu__label">{{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">

           <li><a class="slide-item" href="{{route('admin.password')}}">{{ __('Change Password') }}</a></li>

        </ul>
      </li>

    </ul>

    @endif
  </aside>
  <!--/APP-SIDEBAR-->
