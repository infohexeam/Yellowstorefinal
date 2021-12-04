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
                              <div class="card-header">
                        <h3 class="mb-0 card-title">{{$pageTitle}}</h3>
                     </div>
                        <div class="card-body">
                                    <a  data-toggle="modal" data-target="#StockModal01" class="btn btn-block btn-info text-white">
                                    <i class="fa fa-plus"></i> Add Issue </a>
                                @if(auth()->user()->user_role_id == 0)
                          <a href=" {{ url('admin/issues/restore-list') }}" class=" text-white btn btn-block btn-danger">
                           <i class="fa fa-recycle"></i> Restore Issue </a>
                         @endif  
                         
                                <br>
                            <div class="table-responsive">
                            <table id="example" class="table table-striped table-bdataed text-nowrap w-100">
                                <thead>
                                    <tr>
                                        <th class="wd-15p">SL.No</th>
                                        <th class="wd-15p">{{__('Issue Type')}}</th>
                                        <th class="wd-15p">{{__('Issue Name')}}</th>
                                        <th class="wd-15p">{{__('Action')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 0;
                                    @endphp
                                    @foreach ($issues as $issue)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ @$issue->issue_type->issue_type}}</td>
                                        <td>{{ $issue->issue}}</td>

                                        <td>
                                            <form action="{{route('admin.destroy_issue',$issue->issue_id)}}" method="POST">
                                                @csrf
                                                    <a class="btn btn-sm btn-cyan"  data-toggle="modal" data-target="#StockModal{{$issue->issue_id}}"
                                            >Edit</a>
                                                @method('POST')
                                                <button type="submit" onclick="return confirm('Do you want to delete this item?');"  class="btn btn-sm btn-danger">Delete</button>
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
                        <h5 class="modal-title" id="example-Modal3">Add Issue</h5>
                        <button type="button" class="close" data-dismiss="modal"  onclick="clearTax()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.create_issue') }} " method="POST" enctype="multipart/form-data" >
                 @csrf
                  <div class="modal-body">

                          <label class="form-label">Issue Type</label>
                           <select required  name="issue_type_id" id="issue_type_id" class="form-control"  >
                                 <option value="">Issue Type</option>
                                @foreach($issue_types as $key)
                                <option {{old('issue_type_id') == $key->issue_type_id ? 'selected':''}} value="{{$key->issue_type_id }}">  {{$key->issue_type}} </option>
                                @endforeach
                              </select>

                   <label class="form-label">Issue Name</label>
                    <input type="text" required  id="issue"  class="form-control" value="" placeholder="Issue Name" name="issue"  >


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



@foreach ($issues as $issue)

              <div class="modal fade" id="StockModal{{$issue->issue_id}}" tabindex="-1" role="dialog"  aria-hidden="true">
               <div class="modal-dialog" role="document">
                  <div class="modal-content">
                     <div class="modal-header">
                        <h5 class="modal-title" id="example-Modal3">Edit Issue</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                     </div>

                 <form action=" {{ route('admin.update_issue',$issue->issue_id) }}" method="POST" enctype="multipart/form-data" >
                 @csrf


                   
                  <div class="modal-body">
                   
                    <label class="form-label">Issue Type</label>
                     <select required  name="issue_type_id" id="issue_type_id" class="form-control"  >
                           <option value="">Issue Type</option>
                          @foreach($issue_types as $key)
                          <option {{old('issue_type_id',@$issue->issue_type_id) == $key->issue_type_id ? 'selected':''}} value="{{$key->issue_type_id }}"> {{$key->issue_type}} </option>
                          @endforeach
                        </select>

                    <label class="form-label">Issue Name</label>
                    <input type="text" id="issue" required class="form-control" value="{{@$issue->issue}}" placeholder="Issue Name" name="issue"  >

                  </div>

                     <div class="modal-footer">
                       <button type="submit" class="btn btn-raised btn-primary">
                    <i class="fa fa-check-square-o"></i> Update</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     </div>
                      </form>
                  </div>
               </div>
            </div>
<!-- MESSAGE MODAL CLOSED -->

<script>
function clearTax()
{
      $('#issue').val('');

}
</script>
                                    @endforeach

@endsection
