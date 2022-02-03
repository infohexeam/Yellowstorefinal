@extends('store.layouts.app')
@section('content')
<div class="row" id="user-profile">
   <div class="col-lg-12">
      <div class="card">
         <div class="card-body">
            <div class="wideget-user">
               <h4>{{$pageTitle}}</h4>
            </div>
         </div>

         <div class="border-top">
            <div class="wideget-user-tab">
               <div class="tab-menu-heading">
                  <div class="tabs-menu1">
                     <ul class="nav">
                        <li class=""><a href="#tab-61" class="active show"
                           data-toggle="tab">Delivery Boys</a></li>
                        {{-- <li><a href="#tab-61" class="active show" data-toggle="tab" class=""></a></li> --}}
                        <li><a href="#tab-71" data-toggle="tab" class="">Assigned</a></li>
                        <li><a href="#tab-81" data-toggle="tab" class="">Inprogress</a></li>
                        <li><a href="#tab-91" data-toggle="tab" class="">Completed</a></li>



                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="card">
         <div class="card-body">
            <div class="border-0">
               <div class="tab-content">

              <div class="tab-pane  active show" id="tab-61">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Delivery Boys</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example1" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">SL.No</th>
                                   <th class="wd-15p">{{ __('Name') }}</th>
                                   <th class="wd-15p">{{ __('Mobile') }}</th>
                                     <th class="wd-15p">{{ __('Town') }}</th>

                                     <th  class="wd-20p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($delivery_boys as $delivery_boy)
                                <tr>
                                  <td>{{ ++$i }}</td>
                                  <td>{{$delivery_boy->delivery_boy_name}}</td>
                                  <td>{{$delivery_boy->delivery_boy_mobile}}</td>
                                    @php
                                    $towns =  \DB::table('mst_towns')->where('town_id', @$delivery_boy->town_id)->first();
                                    // dd($towns);
                                    @endphp
                                  <td>{{@$towns->town_name}}</td>
                                   <td>
                                    <a class="btn btn-sm btn-success text-white" href="tel:{{ $delivery_boy->delivery_boy_mobile }}"><i class="fa fa-phone text-white"></i> Call</a>
                                    <a class="btn btn-sm btn-primary text-white" href="{{ url('store/delivery-boys/location/'.$delivery_boy->delivery_boy_id)}}">Got to Map</a>

                                  </td>
          
                                </tr>
                                @endforeach
                              </tbody>
                           </table>
                           <center>
                           <!--<a class="btn btn-cyan text-white" href="{{ route('store.list_boys') }}">Cancel</a>-->
                           </center>
                        </div>
                     </div>
                  </div>
      



                  <div class="tab-pane" id="tab-71">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Assigned</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example1" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">SL.No</th>
                                   <th class="wd-15p">{{ __('Name') }}</th>
                                   <th class="wd-15p">{{ __('Mobile') }}</th>
                                   <th class="wd-15p">{{ __('Customer') }}</th>
                                   <th class="wd-15p">{{ __('Order Number') }}</th>

                                     <th  class="wd-20p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($assigned_delivery_boys as $delivery_boy)
                                <tr>
                                  <td>{{ ++$i }}</td>
                                  <td>{{$delivery_boy->delivery_boy_name}}</td>
                                  <td>{{$delivery_boy->delivery_boy_mobile}}</td>
                                    @php
                                    $towns =  \DB::table('mst_towns')->where('town_id', @$delivery_boy->town_id)->first();
                                    // dd($towns);
                                    @endphp
                                  <td>{{$delivery_boy->customer}}</td>
                                  <td>{{$delivery_boy->order_number}}</td>
                                  <td>
                                    <a class="btn btn-sm btn-success text-white" href="tel:{{ $delivery_boy->delivery_boy_mobile }}"><i class="fa fa-phone text-white"></i> Call</a>
                <a class="btn btn-sm btn-blue"  href="{{url('store/delivery-order/view/'.Crypt::encryptString($delivery_boy->order_id))}}">View Order</a>
                <a class="btn btn-sm btn-primary text-white" href="{{ url('store/delivery-boys/location/'.$delivery_boy->delivery_boy_id)}}">Got to Map</a>

                                  </td>
          
                                </tr>
                                @endforeach
                              </tbody>
                           </table>
                           <center>
                           <!--<a class="btn btn-cyan text-white" href="{{ route('store.list_boys') }}">Cancel</a>-->
                           </center>
                        </div>
                     </div>
                  </div>


                  <div class="tab-pane" id="tab-81">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Inprogress</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example1" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">SL.No</th>
                                   <th class="wd-15p">{{ __('Name') }}</th>
                                   <th class="wd-15p">{{ __('Mobile') }}</th>
                                   <th class="wd-15p">{{ __('Customer') }}</th>
                                   <th class="wd-15p">{{ __('Order Number') }}</th>

                                     <th  class="wd-20p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($inprogress_delivery_boys as $delivery_boy)
                                <tr>
                                  <td>{{ ++$i }}</td>
                                  <td>{{$delivery_boy->delivery_boy_name}}</td>
                                  <td>{{$delivery_boy->delivery_boy_mobile}}</td>
                                    @php
                                    $towns =  \DB::table('mst_towns')->where('town_id', @$delivery_boy->town_id)->first();
                                    // dd($towns);
                                    @endphp
                                  <td>{{$delivery_boy->customer}}</td>
                                  <td>{{$delivery_boy->order_number}}</td>
                                  <td>
                                    <a class="btn btn-sm btn-success text-white" href="tel:{{ $delivery_boy->delivery_boy_mobile }}"><i class="fa fa-phone text-white"></i> Call</a>
                <a class="btn btn-sm btn-blue"  href="{{url('store/delivery-order/view/'.Crypt::encryptString($delivery_boy->order_id))}}">View Order</a>
                                    <a class="btn btn-sm btn-primary text-white" href="{{ url('store/delivery-boys/location/'.$delivery_boy->delivery_boy_id)}}">Got to Map</a>

                                  </td>
          
                                </tr>
                                @endforeach
                              </tbody>
                           </table>
                           <center>
                           <!--<a class="btn btn-cyan text-white" href="{{ route('store.list_boys') }}">Cancel</a>-->
                           </center>
                        </div>
                     </div>
                  </div>


                  <div class="tab-pane" id="tab-91">

                     <div id="profile-log-switch">
                        <div class="media-heading">
                           <h5><strong>Completed</strong></h5>
                        </div><br>
                        <div class="table-responsive ">
                           <table  id="example1" class="table table-striped table-bordered">
                              <thead>
                                 <tr>
                                   <th class="wd-15p">SL.No</th>
                                   <th class="wd-15p">{{ __('Name') }}</th>
                                   <th class="wd-15p">{{ __('Mobile') }}</th>
                                   <th class="wd-15p">{{ __('Customer') }}</th>
                                   <th class="wd-15p">{{ __('Order Number') }}</th>

                                     <th  class="wd-20p">{{__('Action')}}</th>
                                 </tr>
                              </thead>
                              <tbody>
                                @php
                                $i = 0;
                                @endphp
                                @foreach ($completed_delivery_boys as $delivery_boy)
                                <tr>
                                  <td>{{ ++$i }}</td>
                                  <td>{{$delivery_boy->delivery_boy_name}}</td>
                                  <td>{{$delivery_boy->delivery_boy_mobile}}</td>
                                    @php
                                    $towns =  \DB::table('mst_towns')->where('town_id', @$delivery_boy->town_id)->first();
                                    // dd($towns);
                                    @endphp
                                  <td>{{$delivery_boy->customer}}</td>
                                  <td>{{$delivery_boy->order_number}}</td>
                                  <td>
                                    <a class="btn btn-sm btn-success text-white" href="tel:{{ $delivery_boy->delivery_boy_mobile }}"><i class="fa fa-phone text-white"></i> Call</a>
                                                      <a class="btn btn-sm btn-blue"  href="{{url('store/delivery-order/view/'.Crypt::encryptString($delivery_boy->order_id))}}">View Order</a>


                                  </td>
          
                                </tr>
                                @endforeach
                              </tbody>
                           </table>
                           <center>
                           <!--<a class="btn btn-cyan text-white" href="{{ route('store.list_boys') }}">Cancel</a>-->
                           </center>
                        </div>
                     </div>
                  </div>
            </div>


</div>
</div>
</div>


@endsection
