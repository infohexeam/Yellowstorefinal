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
    
    @php
    use App\Models\admin\Mst_store;
    use App\Models\admin\Trn_StoreAdmin;
    $storeProfilePic= Mst_store::find(Auth::guard('store')->user()->store_id)->profile_image;
    @endphp

    @if(Auth::check())

    <ul class="side-menu">
         <div class="app-sidebar__user">
      <div class="dropdown user-pro-body text-center">
        <div class="user-pic pt-4">
        
     @if($storeProfilePic==NULL)
          <img src="{{URL::to('/assets/uploads/admin.png')}}" alt="user-img" class="avatar-xl rounded-circle">
      @else
      <img src="{{asset('/assets/uploads/store_images/images/'.$storeProfilePic)}}" alt="user-img" class="avatar-xl rounded-circle">
      @endif
        </div>


        <div class="user-info">

         
                
                @if(Auth::guard('store')->user()->role_id != 0)
               
                
                    @php
                    $storeName=   Mst_store::find(Auth::guard('store')->user()->store_id)->store_name;
                      $storeMobile =   Mst_store::find(Auth::guard('store')->user()->store_id)->store_mobile;
                      $admin_name=Trn_StoreAdmin::find(Auth::guard('store')->user()->store_admin_id)->admin_name;
                      $admin_mobile=Trn_StoreAdmin::find(Auth::guard('store')->user()->store_admin_id)->store_mobile;
                    @endphp
                  <h6 class=" mb-0 text-dark">{{$admin_name}}</h6>
                  <span class="text-muted app-sidebar__user-name text-sm"> {{ @$storeName }}</span><br>
                  <span class="text-muted app-sidebar__user-name text-sm"> {{ @$admin_mobile }}</span>
              @else
               @php
                    $storeName=   Mst_store::find(Auth::guard('store')->user()->store_id)->store_name;
                      $storeMobile =   Mst_store::find(Auth::guard('store')->user()->store_id)->store_mobile;
                      $admin_name=Trn_StoreAdmin::find(Auth::guard('store')->user()->store_admin_id)->admin_name;
                    @endphp
                  <h6 class=" mb-0 text-dark">{{$storeName}}</h6>
                  <span class="text-muted app-sidebar__user-name text-sm"> {{ @$storeMobile }}</span>

              @endif
              
        </div>
      </div>
    </div>
     <!-- <li><h3>Main</h3></li>-->
     
      <li><h3>Main</h3></li>
      <li class="slide">
        <a class="side-menu__item" href="{{url('store/home')}}"><i class="side-menu__icon ti-shield"></i><span class="side-menu__label"> Dashboard</span></a>
      </li>
      
       {{-- <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-panel"></i><span class="side-menu__label">{{ __('Masters') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">

        <li><a class="slide-item" href="{{route('store.list_attribute_group')}}">{{ __('Attribute Group') }}</a></li>
        <li><a class="slide-item" href="{{route('store.list_attribute_value')}}">{{ __('Attribute Value') }}</a></li>

       <li><a class="slide-item" href="{{route('store.list_agency')}}">{{ __('Agency') }}</a></li>
        </ul>
      </li> --}}


    <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_product')}}">
          <i class="side-menu__icon fa fa-shopping-cart"></i>
          <span class="side-menu__label"> {{ __('Products') }}</span>
        </a>
      </li>
      
         <li class="slide">
        <a class="side-menu__item" href="{{route('store.global_products')}}">
          <i class="side-menu__icon ti-world"></i>
          <span class="side-menu__label"> {{ __('Global Products') }}</span>
        </a>
      </li>

       <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_inventory')}}">
          <i class="side-menu__icon ti-pencil-alt"></i>
          <span class="side-menu__label"> {{ __('Inventory Management') }}</span>
        </a>
      </li>

       <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_pos')}}">
          <i class="side-menu__icon ti-receipt"></i>
          <span class="side-menu__label"> {{ __('Point of Sale') }}</span>
        </a>
      </li>

      {{-- <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_pos')}}">
          <i class="side-menu__icon ti-receipt"></i>
          <span class="side-menu__label"> {{ __('Point of Sale') }}</span>
        </a>
      </li> --}}

      <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_order')}}">
          <i class="side-menu__icon ti-layers"></i>
          <span class="side-menu__label"> {{ __('Orders') }}</span>
        </a>
      </li>
      <li class="slide">
       <a class="side-menu__item" href="{{route('store.list_enquiries')}}" >
     <i class="side-menu__icon ti ti ti-user"></i>
      <span class="side-menu__label"> {{ __('Enquiries') }}</span>
     </a>
      </li>
      
      <!--  <li class="slide">-->
      <!--  <a class="side-menu__item" href="" >-->
      <!--    <i class="side-menu__icon ti ti ti-import"></i>-->
      <!--    <span class="side-menu__label"> {{ __('Payments') }}</span>-->
      <!--  </a>-->
      <!--</li>-->
      
      
          <li class="slide">
 <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti ti ti-import"></i><span class="side-menu__label">{{ __('Payments') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
              <li><a class="slide-item" href="{{url('store/payment_settlments/')}}">{{ __('Payment Settlements') }}</a></li>
              {{-- <li><a class="slide-item" href="{{url('store/incoming-payments')}}">{{ __('Incoming Payments') }}</a></li> --}}
             
            </ul>
        </li>

      
       <li class="slide">
 <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti ti-file"></i><span class="side-menu__label">{{ __('Reports') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
              <li><a class="slide-item" href="{{route('store.overall_product_reports')}}">{{ __('Overall Product Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.show_reports')}}">{{ __('Product Wise Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.delivery_boy_payout_reports')}}">{{ __('Delivery Boy Payout Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.store_visit_reports')}}">{{ __('Store Visit Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.sales_reports')}}">{{ __('Sales Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.inventory_reports')}}">{{ __('Inventory Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.out_of_stock_reports')}}">{{ __('Out of Stock Reports') }}</a></li>

              <li><a class="slide-item" href="{{route('store.online_sales_reports')}}">{{ __('Online Sales Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.offline_sales_reports')}}">{{ __('Offline Sales Reports') }}</a></li>

              <li><a class="slide-item" href="{{route('store.payment_reports')}}">{{ __('Payment Reports') }}</a></li>
              <li><a class="slide-item" href="{{route('store.delivery_reports')}}">{{ __('Delivery Reports') }}</a></li>
              <li><a class="slide-item" href="{{url('store/incoming-payments')}}">{{ __('Incoming Payments Reports') }}</a></li>
              <li><a class="slide-item" href="{{url('store/refund-reports')}}">{{ __('Refund Reports') }}</a></li>
              <li><a class="slide-item" href="{{url('store/wallet-redeem-reports')}}">{{ __('Wallet Redeem Reports') }}</a></li>
              <li><a class="slide-item" href="{{url('store/enquiries-report')}}">{{ __('Enquiry Reports') }}</a></li>
            </ul>
        </li>
      @if(Auth::guard('store')->user()->role_id == 0)


       <li class="slide">
        <a class="side-menu__item" href="{{route('store.store_admin')}}">
          <i class="side-menu__icon ti-user"></i>
          <span class="side-menu__label"> {{ __('Store Admin') }}</span>
        </a>
      </li>
      @endif

      <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_disputes')}}">
          <i class="side-menu__icon ti-comments"></i>
          <span class="side-menu__label"> {{ __('Disputes') }}</span>
        </a>
      </li>

      <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_coupon')}}">
          <i class="side-menu__icon ti-gift"></i>
          <span class="side-menu__label"> {{ __('Coupon') }}</span>
        </a>
      </li>
      <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon fa fa-heart"></i><span class="side-menu__label">{{ __('Loyalty Programs') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">
          <li><a class="slide-item" href="{{route('store.create_configure_points')}}">{{ __('Configure Points') }}</a></li>
         <li><a class="slide-item" href="{{route('store.customer_reward.list')}}">{{ __('Customer Rewards') }}</a></li>
          {{--<li><a class="slide-item" href="{{route('admin.list_points_to_customer')}}">Reward Points of<br> Non-existing Customer</a></li>--}}
        </ul>
      </li>
      

    

      <li class="slide">
        <a class="side-menu__item" href="{{route('store.list_boys')}}">
          <i class="side-menu__icon ti ti-truck"></i>
          <span class="side-menu__label"> {{ __('Delivery Boys') }}</span>
        </a>
      </li>
 <li class="slide">
       <a class="side-menu__item" href="{{route('store.list_referrals')}}" >
     <i class="side-menu__icon ti ti ti-share"></i>
      <span class="side-menu__label"> {{ __('Referrals') }}</span>
     </a>
      </li>
    <li class="slide">
       <a class="side-menu__item" href="{{route('store.add_youtube_videos')}}" >
     <i class="side-menu__icon ti fa fa-youtube"></i>
      <span class="side-menu__label"> {{ __('Youtube Promotional Links') }}</span>
     </a>
      </li>
     

      <li><h3>General</h3></li>

       <li class="slide">
        <a class="side-menu__item"  data-toggle="slide" href="#"><i class="side-menu__icon ti-settings"></i><span class="side-menu__label">{{ __('Settings') }}</span><i class="angle fa fa-angle-right"></i></a>
        <ul class="slide-menu">

         <li><a class="slide-item" href="{{route('store.settings')}}">{{ __('Settings') }}</a></li>
         <li><a class="slide-item" href="{{route('store.time_slots')}}">{{ __('Working Days') }}</a></li>
         <li><a class="slide-item" href="{{route('store.delivery_time_slots')}}">{{ __('Time Slots') }}</a></li>
         <li><a class="slide-item" href="{{route('store.password')}}">{{ __('Change Password') }}</a></li>

        </ul>
      </li>

    </ul>

    @endif

  
  </aside>
  <!--/APP-SIDEBAR-->
