<!-- Mobile Header -->
<div class="mobile-header">
   <div class="container-fluid">
      <div class="d-flex">
         <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-toggle="sidebar" href="#"></a><!-- sidebar-toggle-->
         <a class="header-brand" href="index.html">
         <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img desktop-logo" alt="logo">
         <img src="{{URL::to('/assets/Yellow-Store-logo.png')}}" class="header-brand-img desktop-logo mobile-light" alt="logo">
         </a>
         <div class="d-flex order-lg-2 ml-auto header-right-icons">
            <button class="navbar-toggler navresponsive-toggler d-md-none" type="button" data-toggle="collapse" data-target="#navbarSupportedContent-4"
               aria-controls="navbarSupportedContent-4" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon fe fe-more-vertical text-white"></span>
            </button>
            <div class="dropdown profile-1">
               <a href="#" data-toggle="dropdown" class="nav-link pr-2 leading-none d-flex">
               <span>
               <img src="{{URL::to('/assets/images/users/10.jpg')}}" alt="profile-user" class="avatar  profile-user brround cover-image">
               </span>
               </a>
               <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                  <div class="drop-heading">
                     <div class="text-center">
                        <h5 class="text-dark mb-0">{{Auth::user()->name}}</h5>
                        <small class="text-muted">{{Auth::user()->email}} </small>
                     </div>
                  </div>
                  <div class="dropdown-divider m-0"></div>
                  <a class="dropdown-item" href="#">
                  <i class="dropdown-icon mdi mdi-account-outline"></i> {{ __('Profile') }}
                  </a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="login.html">
                  <i class="dropdown-icon mdi  mdi-logout-variant"></i>{{ __('Sign out')}} 
                  </a>
               </div>
            </div>
            <div class="dropdown d-md-flex header-settings">
               <a href="#" class="nav-link icon " data-toggle="sidebar-right" data-target=".sidebar-right">
               <i class="fe fe-align-right"></i>
               </a>
            </div>
            <!-- SIDE-MENU -->
         </div>
      </div>
   </div>
</div>
<div class="mb-1 navbar navbar-expand-lg  responsive-navbar navbar-dark d-md-none bg-white">
   <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
      <div class="d-flex order-lg-2 ml-auto">
     
      </div>
   </div>
</div>
<!-- Mobile Header -->
<!--app-content open-->
<div class="app-content">
<div class="side-app">
<!-- PAGE-HEADER -->
<div class="page-header" style="margin: 0rem -2rem 1.5rem -2rem;">
   <a aria-label="Hide Sidebar" class="app-sidebar__toggle close-toggle" data-toggle="sidebar" href="#"></a><!-- sidebar-toggle-->
   <div>
      <h1 class="page-title">{{ __('YellowStore') }}</h1>
      <ol style="    background-color: #fef200;" class="breadcrumb">
         <li class="breadcrumb-item"><a href="#">Yellowstore</a></li>
         <li class="breadcrumb-item active" aria-current="page">{{$pageTitle}}</li>
      </ol>
   </div>
   <div class="d-flex  ml-auto header-right-icons header-search-icon">
         <div class="sidebar-navs">
      <ul class="nav  nav-pills-circle" style="justify-content: center;">
        {{-- <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Followers">
          <a class="nav-link text-center m-2">
            <i class="fe fe-user"></i>
          </a>
        </li> --}}
        
        
         

        @if (Auth::guard('store')->user()->role_id == 0)

         @php
         $store_id =   Auth::guard('store')->user()->store_id;
         $storeData = App\Models\admin\Mst_store::find($store_id);
         $storeAdmData = App\Models\admin\Trn_StoreAdmin::where('store_id',$store_id)->where('role_id',0)->first();
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

      @if(@$storeAdmData->expiry_date == $todayDate)
      <li class="nav-item" >
         <a class=" text-center m-2" >
            Store expires today
         </a> 
      </li>
      @elseif($todayDate > @$storeAdmData->expiry_date)
      <li class="nav-item" >
         <a class=" text-center m-2" >
            Store expired on <br> <b style="font-size:11px"> {{ @$storeAdmData->expiry_date}} </b><br> ({{@$diff}} days before)
         </a> 
      </li>
      @else
         @if (@$diff <= 3)
         <li class="nav-item" >
            <a class=" text-center m-2" >
               This account expires in <b style="font-size:11px">{{@$diff}}</b> {{@$dayString}}
            </a> 
         </li>
            
         @endif
      @endif
            
            
            @if(($storeAdmData->store_account_status == 0) && ($today > $storeAdmData->expiry_date) )
                <li class="nav-item" >
                   <a class=" text-center m-2" >
                    Account expires in {{@$diff}} {{$dayString}} 
                    </a> 
                </li>
            @endif
             
             <li class="nav-item" data-toggle="tooltip" data-placement="top" title=" @if ($storeData->online_status == 1) Go Offline @else Go Online @endif">
               <a class="nav-link text-center m-2" href="{{ route('store.switchStatus') }}" >
                  @if ($storeData->online_status == 1)
                  <span class="ml-1"><span class="dot-label bg-success mr-2"></span>Online</span>
                  @else
                  <span class="ml-1"><span class="dot-label bg-danger mr-2"></span>Offline</span>
                  @endif  
             </a> 
            </li>

        @endif
        
        <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Videos">
          <a class="nav-link text-center m-2" href=" {{ route('store.video_gallery') }} " >
            <i class="fa fa-film"></i>
          </a>
        </li>
        

        <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Logout">
          <a class="nav-link text-center m-2" href="{{ route('logout') }}" onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">
            <i class="fe fe-power"></i>
          </a>
          <form id="logout-form" action="{{ route('store.logout') }}" method="POST" style="display: none;">
            @csrf
          </form>
        </li>
        <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Profile Edit">
          <a class="nav-link text-center m-2" href=" {{ route('store.profile') }} " >
            <i class="fe fe-edit"></i>
          </a>

        </li>
      </ul>
    </div>
   </div>
</div>
<!-- PAGE-HEADER END -->