@extends('store.layouts.app')
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12 col-lg-12">
      <div class="card">
        <div class="row">
          <div class="col-12">
            
            
            @if ($message = Session::get('status'))
            <div class="alert alert-success">
              <p>{{ $message }}<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></p>
            </div>
            @endif
            <div class="col-lg-12">
              @if ($errors->any())
              <div class="alert alert-danger">
                <strong>Whoops!</strong>
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
                   
             
               <div class="card-body">
                <a class="btn btn-cyan btn-raised float-right mb-2" href="{{ url('/store/product/list') }}"><i class="fa fa-arrow-left"> Back</i></a>

                <div class="table-responsive">
                    <table  id="example" class="table table-striped table-bordered">
                        <thead>
                           <tr>
                           <th class="wd-15p">SL.No</th>
                              <th class="wd-15p">{{ __('Variant Name') }}</th>
                              <th class="wd-15p">{{ __('MRP') }}</th>
                              <th class="wd-15p">{{ __('Sale Price') }}</th>
                              <th class="wd-15p">{{ __('Image') }}</th>
                              <th class="wd-15p">{{ __('Stock Count') }}</th>
                              <th  class="wd-20p">{{__('Action')}}</th>
                           </tr>
                        </thead>
                        <tbody class="col-lg-12 col-xl-6 p-0">
                           @php
                           $i = 0;
                           @endphp
                        @if(!$product_variants->isEmpty())
                           @foreach ($product_variants as $value)
                           @php
                           $i++;
                           @endphp
                           <tr>
                              <td>{{$i}}</td>
                              <td>{{$value->variant_name}}</td>
                              <td>{{$value->product_varient_price}}</td>
                              <td>{{$value->product_varient_offer_price}}</td>
                              <td><img src="{{asset('/assets/uploads/products/base_product/base_image/'.$value->product_varient_base_image)}}"  width="50" ></td>
                              <td>{{$value->stock_count}}</td>

                              <td>
                                 <form action="{{route('store.destroy_product_variant',$value->product_varient_id)}}" method="POST">
                                    @csrf
                                    @method('POST')
                                    <a  data-toggle="modal" data-target="#AttrModal{{$value->product_varient_id}}" class="text-white btn btn-sm btn-indigo">Attributes</a>

                                    <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                                 </form>
                                 <a href="{{ url('store/product/variant/edit/'.$value->product_varient_id) }}" class="mt-2  text-white btn btn-sm btn-azure">Edit</a>
                                 <a  data-toggle="modal" data-target="#AddAttrModal{{$value->product_varient_id}}" class="mt-2  text-white btn btn-sm btn-yellow">Add Attributes</a>

                              </td>
                           </tr>
                           @endforeach
                           @else
                           <tr>
                        <td colspan="6"><center> No data available in the table</center></td>
                           </tr>
                           @endif
                        </tbody>
                     </table>
              </div>
            </div>
          </div>
        </div>
    
  </div>




  @foreach($product_variants as $value)
  <div class="modal fade" id="AttrModal{{$value->product_varient_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
     <div class="modal-dialog" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="example-Modal3">Variant Attributes</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           @php
              $var_atts = \DB::table('trn__product_variant_attributes')
              ->where('product_varient_id',$value->product_varient_id)
              ->orderBy('variant_attribute_id','DESC')
              ->get();
           @endphp
  
           <div class="modal-body">
              <table class="table table-striped table-bordered">
                 <thead>
                    <tr>
                    <th class="wd-15p">SL.No</th>
                       <th class="wd-15p">{{ __('Group Name') }}</th>
                       <th class="wd-15p">{{ __('Value Name') }}</th>
                       <th  class="wd-20p">{{__('Action')}}</th>
                    </tr>
                 </thead>
                 <tbody class="col-lg-12 col-xl-6 p-0">
                    @php
                    $i = 0;
                    @endphp
                    @if(!$var_atts->isEmpty())
                    @foreach ($var_atts as $val)
                       @php
                       $i++;
                       $attr_grp_name = \DB::table('mst_attribute_groups')->where('attr_group_id',$val->attr_group_id)->pluck('group_name');
                       $attr_val_name = \DB::table('mst_attribute_values')->where('attr_value_id',$val->attr_value_id)->pluck('group_value');
                       @endphp
                       <tr>
                          <td>{{$i}}</td>
                          <td>{{@$attr_grp_name[0]}}</td>
                          <td>{{@$attr_val_name[0]}}</td>
                          <td>
                             <form action="{{route('store.destroy_product_var_attr',$val->variant_attribute_id)}}" method="POST">
                                @csrf
                                @method('POST')
                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
                             </form>
                          </td>
                       </tr>
                    @endforeach
                    @else
                    <tr>
                 <td colspan="6"><center> No data available in the table</center></td>
                    </tr>
                    @endif
                 </tbody>
              </table>
           </div>
                 
      
        </div>
     </div>
  </div>
  @endforeach
  
  
  
  @foreach($product_variants as $value)
  <div class="modal fade" id="AddAttrModal{{$value->product_varient_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
     <div class="modal-dialog" role="document">
        <div class="modal-content">
           <div class="modal-header">
              <h5 class="modal-title" id="example-Modal3">Add Attributes</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
           </div>
           @php
              $var_atts = \DB::table('trn__product_variant_attributes')
              ->where('product_varient_id',$value->product_varient_id)
              ->orderBy('variant_attribute_id','DESC')
              ->get();
           @endphp
  
           <div class="modal-body">
            <table class="table table-striped table-bordered">
                 <thead>
                    <tr>
                    <th class="wd-15p">SL.No</th>
                       <th class="wd-15p">{{ __('Group Name') }}</th>
                       <th class="wd-15p">{{ __('Value Name') }}</th>
                    </tr>
                 </thead>
                 <tbody class="col-lg-12 col-xl-6 p-0">
                    @php
                    $i = 0;
                    @endphp
                    @if(!$var_atts->isEmpty())
                    @foreach ($var_atts as $val)
                       @php
                       $i++;
                       $attr_grp_name = \DB::table('mst_attribute_groups')->where('attr_group_id',$val->attr_group_id)->pluck('group_name');
                       $attr_val_name = \DB::table('mst_attribute_values')->where('attr_value_id',$val->attr_value_id)->pluck('group_value');
                       @endphp
                       <tr>
                          <td>{{$i}}</td>
                          <td>{{@$attr_grp_name[0]}}</td>
                          <td>{{@$attr_val_name[0]}}</td>
                         
                       </tr>
                    @endforeach
                    @else
                    <tr>
                 <td colspan="6"><center> No data available in the table</center></td>
                    </tr>
                    @endif
                 </tbody>
              </table>
           </div>
  
              <form action="{{ route('store.add_attr_to_variant') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
                     <input type="hidden" name="product_varient_id" value="{{$value->product_varient_id}}">
  
                          <div  class=" row">
                                <div class="col-md-6">
                                <div class="form-group">
                                   <label class="form-label">Attribute </label>
                                   <select name="attr_grp_id"   class="attr_groupz form-control" >
                                      <option value="">Attribute</option>
                                      @foreach($attr_groups as $key)
                                      <option value="{{$key->attr_group_id}}"> {{$key->group_name}} </option>
                                            @endforeach
                                   </select>
                                </div>
                                </div>
                                <div class="col-md-6">
                                   <div class="form-group">
                                      <label class="form-label">Value </label>
                                      <select name="attr_val_id" class="attr_valuez form-control" >
                                         <option value="">Value</option>
                                      </select>
                                   </div>
                                </div>
                          </div>
                 
                       
                  </div>
                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
              </form>
             
                 
        </div>
     </div>
  </div>
  @endforeach
  
<script>
    


 $(document).ready(function() {
     var ac = 0;

       $('.attr_groupz').change(function(){
if(ac != 0)
{
       // alert("hi");
       // alert("dd");
        var attr_group_id = $(this).val();

        var _token= $('input[name="_token"]').val();
        //alert(_token);
        $.ajax({
          type:"GET",
          url:"{{ url('store/product/ajax/get_attr_value') }}?attr_group_id="+attr_group_id,


          success:function(res){
            //alert(data);
            if(res){
            $('.attr_valuez').prop("diabled",false);
            $('.attr_valuez').empty();
            $('.attr_valuez').append('<option value="">Value</option>');
            $.each(res,function(attr_value_id,group_value)
            {
              $('.attr_valuez').append('<option value="'+attr_value_id+'">'+group_value+'</option>');
            });

            }else
            {
              $('.attr_valuez').empty();

            }
            }

        });
}
else
{
ac = ac + 1;
}
      });

    });

</script>

@endsection