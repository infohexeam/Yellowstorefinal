@extends('admin.layouts.app')
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
                        <div class="card-body">
                            {{-- <a  data-toggle="modal" data-target="#StockModal01" class="btn btn-block btn-info"> --}}
                                <a href="{{ route('admin.add_taxes') }}"  class="btn btn-block btn-info">
                                    <i class="fa fa-plus"></i> Add Tax </a>
                               @if(auth()->user()->user_role_id == 0)  
                                 <a href=" {{ url('admin/tax/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i> Restore Tax </a>
                           @endif
                                <br>
                            <div class="table-responsive">
                            <table id="example2" class="table table-striped table-bordered  text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Tax Name')}}</th>
                                        <th class="wd-15p">{{__('Tax Value')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($taxes as $tax)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $tax->tax_name}}</td>
                                        <td>{{ $tax->tax_value}}</td>

                                        <td>
                                            <form action="{{route('admin.destroy_tax',$tax->tax_id)}}" method="POST">
                                                @csrf
                                                    <a class="btn btn-sm btn-cyan text-white"  data-toggle="modal" data-target="#StockModal{{$tax->tax_id}}" ><i class="fa fa-eye" aria-hidden="true"></i> View</a>
                                                        <a href="{{ url('admin/tax/edit/'.$tax->tax_id )}} " class="btn btn-sm btn-cyan" ><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Edit</a>
                                                        @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach

                                </tbody>

                            </table>

                            {{-- table responsive end --}}
                            </div>
                        {{-- Card body end --}}
                        </div>
                    {{-- col 12 end --}}
                </div>
            {{-- row end --}}
            </div>
        {{-- card --}}



        </div>
        {{-- row justify end --}}
    </div>
{{-- container end --}}
</div>


      <div class="modal fade" id="StockModal01" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Add Tax</h5>
                        <button type="button" class="close" data-dismiss="modal"  onclick="clearTax()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_tax') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                   <label class="form-label">Tax Name</label>
                    <input type="text" required  id="tax_name"  class="form-control" value="" placeholder="Tax Name" name="tax_name"  >


                     <label class="form-label">Tax Value</label>
                    <input type="number" id="tax_value" required class="form-control" onchange="if (this.value < 0) this.value = '';" placeholder="Tax Value" name="tax_value" >

  	{{-- <label class="custom-switch">
                                                        <input type="hidden" name="isActive" value=0 />
														<input type="checkbox" name="isActive"  checked value=1 class="custom-switch-input">
														<span class="custom-switch-indicator"></span>
														<span class="custom-switch-description">Active Status</span>
													</label> --}}
                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Add</button>
                        <button type="button" class="btn btn-secondary" onclick="clearTax()" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>



@foreach ($taxes as $tax)

              <div class="modal fade" id="StockModal{{$tax->tax_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">View Tax</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.update_tax',$tax->tax_id) }}" method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">
                

                  <table class="table ">
                      <tbody>
                          <tr>
                              <td>Tax Name: {{ @$tax->tax_name}}</td>
                             
                          </tr>
                          <tr>
                            <td>Tax Value: {{ @$tax->tax_value}}</td>
                           
                        </tr>
                      </tbody>
                  </table>

                  <h6>Tax Split Ups</h6>

                 
                  <div class="table-responsive">
                  <table class="table">
                      <thead>
                        <tr>
                            <th>Tax name</th>
                            <th>Tax value</th>
                        </tr>
                      </thead>
                    <tbody>
                        
                       
                        @foreach ($tax_splits as $tax_split)
                        @if($tax_split->tax_id == $tax->tax_id)
                        <tr>
                            <td>{{ $tax_split->split_tax_name }}</td> <td>{{ $tax_split->split_tax_value }}</td>
                        </tr>
                        @endif
                        @endforeach
                        
                       
                    </tbody>
                </table>
            </div>

                     <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>

            </div>
<!-- MESSAGE MODAL CLOSED -->


<script>
function clearTax()
{
      $('#tax_value').val('');
      $('#tax_name').val('');

}
</script>

  <script>

             
</script>

                                    @endforeach

@endsection
