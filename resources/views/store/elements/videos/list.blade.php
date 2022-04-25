@extends('store.layouts.app')
@section('content')
<style>
    .responsive-iframe {
    position: relative;
    padding-bottom: 56.25%; /*16:9*/
    height: 0;
    overflow: hidden;
    iframe {
        position: absolute;
        top:0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
li .active{
        background-image: linear-gradient( 315deg , #fbb034 0%, #fef200 74%);
    }
}

</style>
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
                                <h5>Whoops!</h5> There were some problems with your input.<br><br>
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
                               
                                
									<div class="card-body p-6">
										 <div class="tab_wrapper second_tab">
											<ul class="tab_list">
											    @foreach($videos as $v)
												    <li class="  @if($loop->iteration == 1) active @endif"  style="@if($loop->iteration == 1)  background-image: linear-gradient( 315deg , #fbb034 0%, #fef200 74%); @endif" >
												        @if(isset($v->video_image))
												            <img  width="30px" src="/assets/uploads/video_images/{{$v->video_image}}" />
												        @else
												            <img width="30px" src="{{ (new \App\Helpers\Helper)->default_video_image() }}" />
												        @endif
												        
												        <p>{{ $v->video_discription }}</p>
												    </li>
												@endforeach
											</ul>
											
											@php
											    // dd($videos);
											@endphp
											
											

											<div class="content_wrapper">
											    @foreach($videos as $v)
											        @if($v->platform == 'Vimeo' )
											            @php
											                 $revLink = strrev($v->video_code);
                                                             $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                                                             $linkCode = strrev($revLinkCode);
											            @endphp
        												<div class="tab_content @if($loop->iteration == 1) active @endif">
        													<!--<iframe src="http://player.vimeo.com/video/{{@$linkCode}}" width="500px" height="500px" frameborder="0" allowfullscreen></iframe>-->
        											<div class="responsive-iframe">
        											<iframe src="http://player.vimeo.com/video/{{@$linkCode}}" width="500px" height="360px"  frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>

        											</div>	</div>
                                                    @endif
                                                    
                                                     @if($v->platform == 'Youtube' )
											            @php
											                 $revLink = strrev($v->video_code);
                                                             $revLinkCode = substr($revLink, 0, strpos($revLink, '/'));
                                                             $linkCode = strrev($revLinkCode);
                                                             dd($linkCode);
											            @endphp
        												<div class="tab_content @if($loop->iteration == 1) active @endif">
                                                          <div class="responsive-iframe">
                                                                <iframe  src="//www.youtube.com/embed/{!!$linkCode!!}" width="500px" height="360px" frameborder="0" allow="autoplay; fullscreen" allowfullscreen ></iframe>
        												</div>
        												</div>
                                                    @endif
												@endforeach
											</div>
										</div>
									</div>

                            </div>
                        </div>
                   </div>
                </div>
            </div>
        </div>
    </div>
</div>


            <!-- MESSAGE MODAL CLOSED -->
            @endsection
