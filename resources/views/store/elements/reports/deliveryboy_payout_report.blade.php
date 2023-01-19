@extends('store.layouts.app')
@section('content')


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
                                <form action="{{route('store.delivery_boy_payout_reports')}}" method="GET" enctype="multipart/form-data">
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
                                         
                                         
                                         
                                        
                                      
                                         
                                         <div class="col-md-12">
                                            <div class="form-group">
                                                <center>
                                                   <button type="submit" class="btn btn-raised btn-primary"><i class="fa fa-check-square-o"></i> Filter</button>
                                                   {{-- <button type="reset" id="reset" class="btn btn-raised btn-success">Reset</button> --}}
                                                   <a href="{{route('store.delivery_boy_payout_reports')}}"  class="btn btn-info">Cancel</a>
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
                                            <th class="wd-15p">Order Number</th>
                                            
                                           
                                            <th class="wd-15p">Delivery Boy</th>
                                           
                                            <th class="wd-15p">Subadmin</th>
                                             <th class="wd-15p">Subadmin Phone</th>
                                            
                                            
                                           
                                            <th class="wd-15p">Total Amount</th>
                                            <th class="wd-15p">Delivery Charge</th>
                                            <th class="wd-15p">Packing Charge</th>
                                            <th class="wd-15p">Commision/Month</th>
                                            <th class="wd-15p">Commision/Order</th>
                                             <th class="wd-15p">Previous Commision</th>
                                            <th class="wd-15p">Commisiion After order</th>
                                           
                                            
                                           
                                            
                                           
                                            
                                  
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

                                            <td>{{ $d->order_number }}</td>
                                            
                                           
                                               <td>
                                                @if(isset($d->delivery_boy_name))
                                                 {{ $d->delivery_boy_name }}
                                                 @else
                                                 ---
                                                 @endif
                                            </td>
                                            <td>{{ (new \App\Helpers\Helper)->subAdminName($d->subadmin_id) }}</td>
                                            <td>{{$d->subadmindetail->phone??'---'}}
                                            <td>{{ $d->product_total_amount }}</td>
                                            <td>{{ number_format(@$d->delivery_charge,2)??0.00 }}</td>
                                             <td>{{ number_format(@$d->packing_charge,2)??0.00 }}</td>
                                            <td>{{ number_format(@$d->c_month,2)??0.00 }}</td>
                                            <td>{{ number_format(@$d->c_order,2)??0.00 }}</td>
                                            <td>{{ number_format(@$d->previous_amount+@$d->c_month)??0.00 }}</td>
                                            <td>{{ number_format(@$d->new_amount+@$d->c_month)??0.00 }}</td>


                                          
                                            
                                           

                                           
                                           
                                           

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
                title: 'Delivery Boy Payout Report',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9,10,11,12]
                 },
                 orientation : 'landscape',
                pageSize : 'A4',
            },
            {
                extend: 'excel',
                title: 'Delivery Boy Payout Report',
                footer: true,
                exportOptions: {
                     columns: [0,1,2,3,4,5,6,7,8,9,10,11,12]
                 }
            }
         ]
    } );

} );
</script>


<script>


    $(document).ready(function() {
        
        $("#subadminId").on('change', function(){    
            
         let subadminId = $('#subadminId').val();
         
         var _token= $('input[name="_token"]').val();
            $.ajax({
              type:"GET",
              url:"{{ url('admin/store-name-list') }}?subadmin_id="+subadminId,
    
              success:function(res){
                    if(res){
                       // console.log(res);
                        $('#storeId').prop("diabled",false);
                        $('#storeId').empty();
                        $('#storeId').append('<option value="">Store</option>');
                        $.each(res,function(store_id,store_name)
                        {
                          $('#storeId').append('<option value="'+store_id+'">'+store_name+'</option>');
                        });
                    }else
                    {
                      $('#storeId').empty();
                    }
                }
    
            });
        });
    });
    
    
    $(document).ready(function() {
 
     let subadminId = $('#subadminId').val();
      if ( typeof subadminId === "undefined") {
          subadminId = '';
      }
     let storeId = $('#storeId').val();
     
     var _token= $('input[name="_token"]').val();
        $.ajax({
          type:"GET",
          url:"{{ url('admin/product-name-list') }}?subadmin_id="+subadminId+'&store_id'+storeId,

          success:function(res){
                if(res){
                   // console.log(res);
                    $('#productId').prop("diabled",false);
                    $('#productId').empty();
                    $('#productId').append('<option value="">Product</option>');
                    $.each(res,function(product_id,product_name)
                    {
                      $('#productId').append('<option value="'+product_id+'">'+product_name+'</option>');
                    });
                    
                    let productId = getUrlParameter('product_id');
                    if ( typeof productId !== "undefined" && productId) {
                        $("#productId option").each(function(){
                            if($(this).val()==productId){ 
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
    
    
    $(document).ready(function() {
        
        $("#categoryId").on('change', function(){    
            
        let categoryId = $('#categoryId').val();
        
       // console.log(categoryId);

        var _token= $('input[name="_token"]').val();
        
            $.ajax({
              type:"GET",
              url:"{{ url('admin/sub-category-list') }}?category_id="+categoryId,
    
              success:function(res){
                    if(res){
                       // console.log(res);
                        $('#subCategoryId').prop("diabled",false);
                        $('#subCategoryId').empty();
                        $('#subCategoryId').append('<option value="">Sub Category</option>');
                        $.each(res,function(sub_category_id,sub_category_name)
                        {
                          $('#subCategoryId').append('<option value="'+sub_category_id+'">'+sub_category_name+'</option>');
                        });
                        
                        let subCategoryId = getUrlParameter('sub_category_id');
                        if ( typeof subCategoryId !== "undefined" && subCategoryId) {
                            $("#subCategoryId option").each(function(){
                                if($(this).val()==subCategoryId){ 
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

