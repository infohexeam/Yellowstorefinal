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
   <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12">
      <div class="row">
          <div class="row col-lg-6 col-md-12 col-sm-12 col-xl-6">
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
         <div class="col-lg-6 col-md-12 col-sm-12 col-xl-6">
								<div class="card ">
									<div class="card-header">
										<h3 class="card-title mb-0">Recent Orders</h3>
									</div>
									<div class="card-body">
										<div class="grid-margin">
											<div class="">
												<div class="table-responsive">
													<table class="table card-table border table-vcenter text-nowrap align-items-center">
														<thead class="">
															<tr>
																<th>Si.No</th>
																<th>Order Number</th>
																<th>Store</th>
																<th>Total Amount</th>
																<th>Status</th>
															</tr>
														</thead>
														<tbody>
                                                            @php
                                                             $i = 0;
                                                             @endphp
                                                             @foreach ($ordersData as $row)
                                                             <tr>
                                                                <td>{{ ++$i }}</td>	
																<td class="text-sm font-weight-600">{{ $row->order_number }}</td>
																<td>{{ @$row->store->store_name }}</td>
																<td>{{ @$row->product_total_amount }}</td>
																<td>{{ @$row->status->status }}</td>
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
    <div class="col-md-12 col-sm-12 ">
                  <div id="container2"></div>
        </div>
        <?php 
          $returnNum = array_map('intval', $return);
        ?>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script type="text/javascript">
    var users =  <?php echo json_encode($returnNum) ?>;

    Highcharts.chart('container2', {
        title: {
            text: 'Registered Stores <?php echo date("Y"); ?> '
        },
        // subtitle: {
        //     text: 'Source'
        // },
         xAxis: {
            categories: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August',  'September', 'October', 'November', 'December'],
            title: {
                text: 'Months'
            } ,

        //     labels: {
        //   formatter: function() {
        //     return Highcharts.dateFormat('%M %Y',users);
        //   }
        // }   
        },

        
        yAxis: {
            title: {
                text: 'Number of New Registered Stores'
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },
        plotOptions: {
            series: {
                allowPointSelect: true
            }
        },
        series: [{
            name: 'New Stores',
            //data: [1,2,0,1,1,2,4,2,0,1,1,2]
            data: users
        }],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }
});
</script>

   <!-- COL END -->
</div>
<!-- ROW-1 END -->
</div>
</div>
<!-- CONTAINER END -->
</div>
@endsection
