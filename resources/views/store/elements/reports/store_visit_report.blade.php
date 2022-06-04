@extends('store.layouts.app')
@section('content')

@php
    use App\Models\admin\Trn_RecentlyVisitedStore;
    use App\Models\admin\Trn_store_order;

@endphp

<div class="container">
    <div class="row justify-content-center">
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
                             
                               <div class="card-body border">
                                <form action="{{route('store.store_visit_reports')}}" method="GET" enctype="multipart/form-data">
                                   @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">From Date</label>
                                                   <input type="date" class="form-control"  name="date_from" id="date_fromc"  value="{{@$datefrom}}" placeholder="From Date">
                            
                                             </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">To Date</label>
                                                 <input type="date" class="form-control" name="date_to"   id="date_toc" value="{{@$dateto}}" placeholder="To Date">
                                            </div>
                                         </div>
                                     
                                          {{-- <div class="col-md-6">
                                          <div class="form-group">
                                              <label class="form-label">Customer</label>
                                                  <select name="customer_id" id="customer_id" class="form-control select2-show-search" data-placeholder="Customer" >
                                                       <option value="" >Customer</option>
                                                       @foreach ($customers as $key)
                                                            <option value="{{ $key->customer_id }}" {{request()->input('customer_id') == $key->customer_id ? 'selected':''}} >{{ $key->customer_first_name }} {{ $key->customer_last_name }} - {{ $key->customer_mobile_number }} </option>
                                                       @endforeach
                                                  </select>
                                           </div>
                                          </div> --}}

                                          <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Customer Mobile</label>
                                                <input type="text"  maxlength="10" name="customer_mobile_number" onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" class="form-control" value="{{ request()->input('customer_mobile_number') }}"  placeholder="Customer mobile Number">
                                             </div>
                                            </div>
                                         
                                         <div class="col-md-6">
                                            <div class="form-group">
                                              <label class="form-label">Pincode </label>
                                               <select  name="town_id" id="townId" class="form-control select2-show-search" data-placeholder="Pincode"  >
                                                    <option value="">Pincode</option>
                                                  
                                                  </select>
                                            </div>
                                         </div>
                                         
                                         
                                         
                                         
                                         
                                         
                                         <div class="col-md-12">
                                            <div class="form-group">
                                                <center>
                                                   <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Filter</button>
                                                   {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                                                   <a href="{{route('store.store_visit_reports')}}"  class="btn btn-info">Cancel</a>
                                                </center>
                                            </div>
                                          </div>
                     
                     
                                    </div>
                                </form>
                            </div>
                        
                            <div class="card-body">
                                <div class="table-responsive">
                                   <table id="exampletable" class="table table-striped table-bordered text-nowrap w-100">
                                      <thead>
                                         <tr>
                                            <th class="wd-15p">SL.No</th>
                                            <th class="wd-15p">Date</th>
                                            {{-- <th class="wd-15p">Time</th> --}}
                                            <th class="wd-15p">Customer</th>
                                            <th class="wd-15p">Pincode</th>
                                            <th class="wd-15p">Customer<br>Phone</th>
                                            <th class="wd-15p">Visit<br>Count</th>
                                            <th class="wd-15p">Order<br>Per Visit</th>
                                         </tr>
                                      </thead>
                                      <tbody>
                                          
                                        @php
                                        $i = 0;
                                        @endphp
                                        @foreach ($data as $d)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ \Carbon\Carbon::parse($d->created_at)->format('d-m-Y')}}</td>
                                            {{-- <td>{{ \Carbon\Carbon::parse($d->created_at)->format('H:i:s')}}</td> --}}
                                            <td>{{ $d->customer_first_name }} {{ $d->customer_last_name }}</td>
                                            <td>{{ @$d->customer->town['town_name'] }}</td>
                                            <td>{{ $d->customer_mobile_number }}</td>
                                            @php
                                            
                                                $visitCount = Trn_RecentlyVisitedStore::join('mst_stores','mst_stores.store_id','=','trn__recently_visited_stores.store_id');
                                                
                                               
                                                $visitCount =   $visitCount->where('trn__recently_visited_stores.store_id', $d->store_id);
                                                $visitCount =   $visitCount->where('trn__recently_visited_stores.customer_id', $d->customer_id);
                                                $visitCount =   $visitCount->whereDate('trn__recently_visited_stores.created_at', \Carbon\Carbon::parse($d->created_at)->format('Y-m-d'));
                                                $visitCount =   $visitCount->sum('trn__recently_visited_stores.visit_count');
                                                
                                            @endphp
                                            <td>{{ @$visitCount }}</td>
                                            
                                             @php
                                            
                                                $puchasedCount =  new Trn_store_order;
                                                    
                                                $puchasedCount = $puchasedCount->join('mst_stores','mst_stores.store_id','=','trn_store_orders.store_id');
                                                
                                                $puchasedCount = $puchasedCount->where('trn_store_orders.customer_id', $d->customer_id);
                                                $puchasedCount = $puchasedCount->where('trn_store_orders.store_id', $d->store_id);
                                                $puchasedCount = $puchasedCount->whereDate('trn_store_orders.created_at', \Carbon\Carbon::parse($d->created_at)->format('Y-m-d'));
                                                $puchasedCount = $puchasedCount->count();

                                            @endphp
                                            <td>{{ @$puchasedCount }}</td>
                                            
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
</div>



<script>
    $(function(e) {
	 $('#exampletable').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'pdf',
                title: 'Store Visit Report',
                footer: true,
                orientation : 'landscape',
                pageSize : 'LEGAL',
            },
            {
                extend: 'excel',
                title: 'Store Visit Report',
                footer: true,
              
            }
         ]
    } );

} );
</script>


<script>

 $(document).ready(function() {
 
 
     var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('store/town-name-list') }}",

          success:function(res){
                if(res){
                    console.log(res);
                    $('#townId').prop("diabled",false);
                    $('#townId').empty();
                    $('#townId').append('<option value="">Pincode</option>');
                    $.each(res,function(town_id,town_name)
                    {
                      $('#townId').append('<option value="'+town_id+'">'+town_name+'</option>');
                    });
                    
                    let townId = getUrlParameter('town_id');
                    if ( typeof townId !== "undefined" && townId) {
                        $("#townId option").each(function(){
                            if($(this).val()==townId){ 
                                $(this).attr("selected","selected");    
                            }
                        });
                    } 
    
                }else
                {
                  $('#storeId').empty();
                }
            }

        });

    });
    
    
      var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
        return false;
    };
    
</script>

@endsection

