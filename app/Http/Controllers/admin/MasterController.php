<?php

namespace App\Http\Controllers\admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Image;
use Hash;
use DB;
use Carbon\Carbon;
use Crypt;

use App\Models\admin\Mst_Video;
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_RewardToCustomer;
use App\Models\admin\Trn_RewardToCustomerTemp;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Mst_FeedbackQuestion;
use App\Models\admin\Trn_customer_reward;

use Auth;
use App\Models\admin\Country;
use App\Models\admin\State;
use App\Models\admin\District;
use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Town;
use App\Models\admin\Trn_CustomerDeviceToken;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB as FacadesDB;

class MasterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    } // commented line
    
     public function addReward()
    {
        $pageTitle = "Add Reward To Existing Customer";
       $customers = Trn_store_customer::select('customer_id','customer_first_name','customer_last_name','customer_mobile_number')->get();
        return view('admin.masters.customer_reward.add',compact('pageTitle','customers'));

    
       // return redirect()->back()->with('status','Reward added successfully.');
    }
    
    
    public function storeReward(Request $request)
    {
      try{ 
          if(isset($request->customer_id))
          {
            $validator = Validator::make(
                $request->all(),
                [
                    'reward_points'          => 'required|numeric|gt:0',
                    
                ]
                
            );
            if (!$validator->fails()) {
            $reward = new Trn_customer_reward;
            $reward->transaction_type_id  	= 0;
            $reward->reward_points_earned  	= $request->reward_points;
            $reward->customer_id  	= $request->customer_id;
            $reward->reward_approved_date 		=  Carbon::now()->format('Y-m-d');
            $reward->reward_point_expire_date 		=  Carbon::now()->format('Y-m-d');
            $reward->reward_point_status  	= 1;
            $reward->discription  	= $request->reward_discription;
            $reward->save(); 
            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $request->customer_id)->get();
           
            foreach ($customerDevice as $cd)
		   {
                $title = 'Rewards points credited';
                //  $body = 'First order points credited successully..';
                $body = $request->reward_points . ' points credited to your wallet from admin';
                $clickAction = "MyWalletFragment";
                $type = "wallet";
                $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
            }
            }
            else
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }
          }else
          {
            return redirect()->back()->withErrors(['Customer not exist!'])->withInput();
          }
          
        } catch (\Exception $e) {
             //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }

        return redirect('admin/customer_reward/list')->with('status','Customer reward added successfully');

    }
  
  
     public function sendPushNotification(Request $request)
    {
       // $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();
          
        $SERVER_API_KEY = 'AAAAZ5VSsVE:APA91bEmc0gaD9tE94DJOaFpQHA0NTZtGMlR-Fx_Tz9wJcwn3rIQKG5YPgxHkbiu-3SrcsHG-IWDWfNhes0krQr4L8jazCQCACFn_nKXMVByZgzeYTMKFKl-1xwC43Wg_g0KHbYWNbjG';
  
        $data = [
            "to" => 'e8YQs12z1TpUkhyZvQnyKE:APA91bHHHvwLMfmAh7Ysfzsh4iokPytCohvzEjh14sPkdcHdiQP_3oA6mHZ_rfiIkNA2V6VHs169OjJDRMzppTt_pHno-SU8HX4oUCuS9wwwXi7TsYE7kLhGzUAgl7hCAFP1Qlm7rdpi',
                        'notification' => array('title' => "hi Title", 'body' => "it\'s working"),

        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
  
        return back();
    }



    
      public function listResioreFeedbackQuestion()
    {
        $pageTitle = "Restore Feedback Questions";
        $questions = Mst_FeedbackQuestion::onlyTrashed()->orderBy('feedback_question_id','DESC')->get();
        return view('admin.masters.feedback.restore',compact('questions','pageTitle'));
    }
    
    public function restoreFeedbackQuestion(Request $request,$feedback_question_id)
    {
        Mst_FeedbackQuestion::onlyTrashed()->where('feedback_question_id',$feedback_question_id)->restore();

        return redirect('admin/feedback-questions/list')->with('status','Feedback questions restore successfully.');
    }
     
    
    public function listFeedbackQuestion()
    {
       // echo "working...";die;
        $pageTitle = "List Feedback Questions";
        $questions = Mst_FeedbackQuestion::orderBy('feedback_question_id','DESC')->get();
      	$categories = Mst_categories::where('category_status', '=', '1')->orderBy('category_name')->get();

        return view('admin.masters.feedback.list',compact('categories','questions','pageTitle'));
    }
    
     public function storeFeedbackQuestion(Request $request,Mst_FeedbackQuestion $fq)
    {
        $data = $request->except('_token');

        $validator = Validator::make($request->all(),
        [
            'feedback_question' => ['required', ],
            'category_id' => ['required', ],
        ],
        [
            'feedback_question.required'         => 'Feedback question required',
            'category_id.required'         => 'Category required',
        ]);
        if(!$validator->fails())
        {
            $fq->feedback_question = $request->feedback_question;
            $fq->category_id = $request->category_id;
            $fq->save();

            return redirect('admin/feedback-questions/list')->with('status','Feedback question added successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

    }
    
    
    public function updateFeedbackQuestion(Request $request, $feedback_question_id)
    {
       // $data = $request->except('_token');

        $validator = Validator::make($request->all(),
        [
            'feedback_question' => ['required', ],
            'category_id' => ['required', ],
        ],
        [
            'feedback_question.required'         => 'Feedback question required',
            'category_id.required'         => 'Category required',
        ]);
        
        if(!$validator->fails())
        {
       // dd($request-//);
            $data['feedback_question'] = $request->feedback_question;
            $data['category_id'] = $request->category_id;
          
            Mst_FeedbackQuestion::where('feedback_question_id',$feedback_question_id)->update($data);


            return redirect('admin/feedback-questions/list')->with('status','Feedback question updated successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }
    
    public function removeFeedbackQuestion(Request $request,$feedback_question_id)
    {
        Mst_FeedbackQuestion::where('feedback_question_id',$feedback_question_id)->delete();

        return redirect()->back()->with('status','Feedback question deleted successfully.');
    }

    

// video

    public function listRestoreVideo()
    {
        $pageTitle = "Restore Video";
        $video = Mst_Video::onlyTrashed()->orderBy('video_id','DESC')->get();
        return view('admin.masters.video.restore',compact('video','pageTitle'));
    }
    
    public function restoreVideo(Request $request,$video_id)
    {
        Mst_Video::onlyTrashed()->where('video_id',$video_id)->restore();

        return redirect('admin/video/list')->with('status','Video restore successfully.');
    }


    public function listVideo()
    {
        $pageTitle = "List Video";
        $video = Mst_Video::orderBy('video_id','DESC')->get();
        return view('admin.masters.video.list',compact('video','pageTitle'));
    }

    public function createVideo()
    {
        $pageTitle = "Create Video";
           $state = State::where('country_id',101)->get();
        return view('admin.masters.video.create',compact('pageTitle','state'));
    }

    public function storeVideo(Request $request,Mst_Video $video)
    {
        $data = $request->except('_token');

        $validator = Validator::make($request->all(),
        [
            'platform' => ['required', ],
            'video_code' => ['required', ],
            'visibility' => ['required', ],
            'video_image' => ['required', ],
            'video_discription' => ['required', ],

        ],
        [
            'platform.required'         => 'Platform required',
            'video_code.required'         => 'Video code required',
            'visibility.required'         => 'Visibility required',
            'video_image.required'         => 'Thumbnail required',
            'video_discription.required'         => 'Discription required',

        ]);
        if(!$validator->fails())
        {
            $video->platform = $request->platform;
            $video->video_code = $request->video_code;
            $video->status = $request->status;

            $video->visibility = $request->visibility;
            $video->state_id = $request->state_id;
            $video->district_id = $request->district_id;
            $video->town_id = $request->town_id;
            $video->video_discription = $request->video_discription;

            if ($request->hasFile('video_image')) {
                        $file = $request->file('video_image');
                        $filename = time()."_".rand().".".$file->getClientOriginalExtension();
                        $file->move('assets/uploads/video_images', $filename);
                        $video->video_image = $filename;

            }



            $video->save();

            return redirect('admin/video/list')->with('status','Video added successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }

    }

    public function editVideo($video_id)
    {
        $video_id  = Crypt::decryptString($video_id);
        $pageTitle = "Edit Video";
        $video = Mst_Video::Find($video_id);
        $state = State::where('country_id',101)->get();
        $district = District::where('state_id',$video->state_id)->get();
        $town = Town::where('district_id',$video->district_id)->get();
        
        
        return view('admin.masters.video.edit',compact('town','state','district','video','video_id','pageTitle'));
    }

    public function updateVideo(Request $request,Mst_Video $video, $video_id)
    {
        $data = $request->except('_token');

       $validator = Validator::make($request->all(),
        [
            'platform' => ['required', ],
            'video_code' => ['required', ],
            'visibility' => ['required', ],
           // 'video_image' => ['required', ],
            'video_discription' => ['required', ],

        ],
        [
            'platform.required'         => 'Platform required',
            'video_code.required'         => 'Video code required',
            'visibility.required'         => 'Visibility required',
            'video_image.required'         => 'Thumbnail required',
            'video_discription.required'         => 'Discription required',

        ]);
        if(!$validator->fails())
        {

            $data['platform'] = $request->platform;
            $data['video_code'] = $request->video_code;
            $data['status'] = $request->status;

            $data['visibility'] = $request->visibility;
            $data['state_id'] = $request->state_id;
            $data['district_id'] = $request->district_id;
            $data['town_id'] = $request->town_id;
            $data['video_discription'] = $request->video_discription;
            
             if ($request->hasFile('video_image')) {
                        $file = $request->file('video_image');
                        $filename = time()."_".rand().".".$file->getClientOriginalExtension();
                        $file->move('assets/uploads/video_images', $filename);
                        $data['video_image'] = @$filename;

            }

    
            Mst_Video::where('video_id',$video_id)->update($data);


            return redirect('admin/video/list')->with('status','Video updated successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeVideo(Request $request,Mst_Video $video,$video_id)
    {
        Mst_Video::where('video_id',$video_id)->delete();

        return redirect()->back()->with('status','Video deleted successfully.');
    }

//
    public function listSubCategory(Request $request)
    {
        if(Auth::user()->user_role_id != 0 )
	    {
	        return redirect('home');
	    }else{
            $pageTitle = "List Product Sub Category";
            $business_types = Mst_business_types::where('business_type_status', 1)->get();
            $categories = Mst_categories::orderBy('mst_store_categories.category_id', 'DESC');
            $sub_category = Mst_SubCategory::orderBy('sub_category_id','DESC');
            if($request->business_type_id)
            {
                $btype_id=$request->business_type_id;
                $sub_category=$sub_category->whereHas('business_type', function (Builder $qry)use($btype_id)  {
                    return $qry->where('business_type_id',$btype_id);
                });
             //$categories=$categories->join('trn__category_business_types', 'trn__category_business_types.category_id', '=', 'mst_store_categories.category_id')->where('trn__category_business_types.business_type_id',$btype_id);

            }
            if($request->product_cat_id)
            {
                $category_id=$request->product_cat_id;
                $sub_category=$sub_category->whereHas('categories', function (Builder $qry)use($category_id)  {
                    return $qry->where('category_id',$category_id);
                });

            }
            if ($request->ajax()) {
                return response()->json([
                    'categories' => $categories,
                ]);
            }

            $sub_category=$sub_category->get();
            $categories=$categories->get();
        //  dd($sub_category);
            return view('admin.masters.sub_category.list',compact('business_types','sub_category','pageTitle','categories'));
        }
        
    }

    public function createSubCategory()
    {

      	$pageTitle = "Create Product Sub Category";
      	$categories = Mst_categories::where('category_status', '=', '1')->get();
		$business_types = Mst_business_types::where('business_type_status','=',1)->get();
		
        return view('admin.masters.sub_category.create',compact('pageTitle','categories','business_types'));

    }

    public function storeSubCategory(Request $request,Mst_SubCategory $sub_category)
    {
        $data = $request->except('_token');

        $validator = Validator::make($request->all(),
		[
		    'category_id'       => 'required',
		    'sub_category_name'       => 'required|unique:mst__sub_categories',
			'sub_category_icon'        => 'dimensions:min_width=150,min_height=150|image|mimes:jpeg,png,jpg|max:1024',
			'sub_category_description' => 'required',
			'business_type_id'		=> 'required',


         ],
		[
		    'category_id.required'         => 'Parent category required',
		    'sub_category_name.required'         => 'Sub category name required',
			'sub_category_icon.required'        => 'Sub category icon required',
			'sub_category_icon.dimensions'        => 'Sub category icon dimensions is invalid',
			'sub_category_description.required'	 => 'Sub category description required',
			'business_type_id.required'	 => 'Business type required',
            'sub_category_icon.max'=>'Maximum file size must not exceeeds 1MB'

		]);

        if(!$validator->fails())
		{

     	$data= $request->except('_token');

		$sub_category->sub_category_name 		= $request->sub_category_name;
		$sub_category->sub_category_name_slug  	= Str::of($request->sub_category_name)->slug('-');
		$sub_category->sub_category_description = $request->sub_category_description;
		$sub_category->business_type_id = $request->business_type_id;
		$sub_category->category_id 		=  $request->category_id;

                if($request->hasFile('sub_category_icon'))
                {

                    $photo = $request->file('sub_category_icon');
                                $filename = time() . '.' . $photo->getClientOriginalExtension();
                                $destinationPath = 'assets/uploads/category/icons';
                                $thumb_img = Image::make($photo->getRealPath());
                                $thumb_img->save($destinationPath . '/' .$filename, 80);

         $sub_category->sub_category_icon = $filename;

                }

                $sub_category->sub_category_status 		= 1;

                $sub_category->save();

                return redirect('/admin/sub/category/list')->with('status','Sub category added successfully.');
            }else
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }

    }

    public function editSubCategory($sub_category_id)
    {
     //   $sub_category_id  = Crypt::decryptString($sub_category_id);
        $pageTitle = "Edit Product Sub Category";
        $sub_category = Mst_SubCategory::where('sub_category_name_slug',$sub_category_id)->first();
        $categories = Mst_categories::where('category_status', '=', '1')->get();
		$business_types = Mst_business_types::where('business_type_status','=',1)->get();
		 return view('admin.masters.sub_category.edit',compact('business_types','categories','sub_category_id','sub_category','pageTitle'));
    }

    public function updateSubCategory(Request $request,Mst_SubCategory $sub_category, $sub_category_id)
    {
        $data = $request->except('_token');

        $validator = Validator::make($request->all(),
		[
		    'category_id'       => 'required',
		    'sub_category_name'       => 'required',
			'sub_category_icon'        => 'dimensions:min_width=150,min_height=150|image|mimes:jpeg,png,jpg|max:1024',
			'sub_category_description' => 'required',
			'business_type_id'		=> 'required',


         ],
		[
		    'category_id.required'         => 'Parent category required',
		    'sub_category_name.required'         => 'Sub category name required',
			'sub_category_icon.dimensions'        => 'Sub category icon dimensions is invalid',
			'sub_category_description.required'	 => 'Sub category description required',
			'business_type_id.required'	 => 'Business type required',
            'sub_category_icon.max'=>'Maximum file size must not exceeeds 1MB'

		]);
        if(!$validator->fails())
        {
           
            $data['sub_category_name'] = $request->sub_category_name;
            $data['sub_category_name_slug'] = Str::of($request->sub_category_name)->slug('-');
            $data['sub_category_description'] = $request->sub_category_description;
            $data['business_type_id'] =  $request->business_type_id;
            $data['category_id'] =  $request->category_id;

            if($request->hasFile('sub_category_icon'))
            {

                $photo = $request->file('sub_category_icon');
                            $filename = time() . '.' . $photo->getClientOriginalExtension();
                            $destinationPath = 'assets/uploads/category/icons';
                            $thumb_img = Image::make($photo->getRealPath());
                            $thumb_img->save($destinationPath . '/' .$filename, 80);

                    $data['sub_category_icon'] = $filename;

            }

            Mst_SubCategory::where('sub_category_id',$sub_category_id)->update($data);


            return redirect('/admin/sub/category/list')->with('status','Sub category updated successfully.');
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeSubCategory(Request $request,Mst_SubCategory $sub_category,$sub_category_id)
    {
        $gp_count=Mst_GlobalProducts::where('sub_category_id',$sub_category->sub_category_id)->count();
        $sp_count=Mst_store_product::where('sub_category_id',$sub_category->sub_category_id)->count();
        if($gp_count>0||$sp_count>0)
        {
            return redirect()->back()->with('error', 'Sub Category cannot be removed as products exist.');
        }
        Mst_SubCategory::where('sub_category_id',$sub_category_id)->forceDelete();

        
        
        return redirect()->back()->with('status','Sub category deleted successfully.');
    }

    public function statusSubCategory(Request $request,$sub_category_id)
    {

        $sub_category = Mst_SubCategory::Find($sub_category_id);

        $status = $sub_category->sub_category_status;

        if($status == 0)
        {
             $sub_category->sub_category_status  = 1;

        }else
        {

            $sub_category->sub_category_status  = 0;

        }
        $sub_category->update();

    return redirect('/admin/sub/category/list')->with('status','Sub category status changed successfully');
    }

    public function addRewardToCustomer()
    {
        $pageTitle = "Add Reward To Non-Existing Customer";
       
        return view('admin.masters.customer_reward.add_customer_rewards.add',compact('pageTitle'));

    
       // return redirect()->back()->with('status','Reward added successfully.');
    }

    public function storeRewardToCustomer(Request $request,Mst_RewardToCustomer $reward,Trn_RewardToCustomerTemp $temp_reward)
    {
      //  dd($request->all());
      try{ 

            if (Trn_store_customer::where('customer_mobile_number', '=', $request->customer_mobile_number)->exists()) 
            {
                return redirect()->back()->withErrors(['Customer exists'])->withInput();
             }  
            else
            {
                $validator = Validator::make(
                    $request->all(),
                    [
                        'customer_mobile_number'          => 'required|regex:/^[1-9]\d{9}$/u|digits:10',
                        'reward_points'          => 'required|numeric|gt:0',
                        
                    ]
                    
                );
                if (!$validator->fails()) {
                 $reward->user_id 		= auth()->user()->id;
                $reward->customer_mobile_number  	= $request->customer_mobile_number;
                $reward->reward_discription = $request->reward_discription;
                $reward->reward_points = $request->reward_points;
                $reward->added_date 		=  Carbon::now()->format('Y-m-d');
                $reward->save(); 
                }
                else {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
            }
          

        } catch (\Exception $e) {
             //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }

        
        return redirect('admin/list/reward-to-customer')->with('status','Customer reward added successfully');

    }
    
    public function listRewardToCustomer(Request $request)
    {
        $pageTitle = "List Rewards To Customers";

        $rewards = Mst_RewardToCustomer::orderBy('reward_to_customer_id','DESC')->get();
        $dummy_rewards = Trn_RewardToCustomerTemp::orderBy('reward_to_customer_temp_id','DESC')->get();
         
       
        return view('admin.masters.customer_reward.add_customer_rewards.list',compact('dummy_rewards','rewards','pageTitle'));

    }

    public function editRewardToCustomer(Request $request,$reward_to_customer_id)
    {
        $pageTitle = "Edit Reward To Customer";
        $reward_to_customer_id  = Crypt::decryptString($reward_to_customer_id);
        $reward = Mst_RewardToCustomer::find($reward_to_customer_id);
        return view('admin.masters.customer_reward.add_customer_rewards.edit',compact('reward','pageTitle'));

    }

    public function editTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
    {
        $pageTitle = "Edit Reward To Customer";
        $reward_to_customer_temp_id  = Crypt::decryptString($reward_to_customer_temp_id);
        $dummy_reward = Trn_RewardToCustomerTemp::find($reward_to_customer_temp_id);

        return view('admin.masters.customer_reward.add_customer_rewards.edit_temp',compact('dummy_reward','pageTitle'));

    }

    public function updateRewardToCustomer(Request $request,$reward_to_customer_id)
    {
      try{ 
          
           if (Trn_store_customer::where('customer_mobile_number', '=', $request->customer_mobile_number)->exists()) 
            {
                return redirect()->back()->withErrors(['Customer exists'])->withInput();
             }  
            else
            {

                $reward['user_id'] = auth()->user()->id;
                $reward['customer_mobile_number'] = $request->customer_mobile_number;
                $reward['reward_discription'] = $request->reward_discription;
                $reward['reward_points'] = $request->reward_points;
                $reward['added_date'] = Carbon::now()->format('Y-m-d');
            }

        Mst_RewardToCustomer::where('reward_to_customer_id',$reward_to_customer_id)->update($reward);


        return redirect('admin/list/reward-to-customer')->with('status','Customer reward updated successfully');


        } catch (\Exception $e) {
             //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }


    }

    public function updateTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
    {
      try{ 

        $reward['customer_mobile_number'] = $request->customer_mobile_number;
        $reward['reward_discription'] = $request->reward_discription;
        $reward['reward_points'] = $request->reward_points;
        $reward['added_date'] = Carbon::now()->format('Y-m-d');

        Trn_RewardToCustomerTemp::where('reward_to_customer_temp_id',$reward_to_customer_temp_id)->update($reward);

        return redirect('admin/list/reward-to-customer')->with('status','Customer reward updated successfully');


        } catch (\Exception $e) {
             return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
          //  return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }


    }

    public function removeRewardToCustomer(Request $request,$reward_to_customer_id)
    {
        Mst_RewardToCustomer::where('reward_to_customer_id',$reward_to_customer_id)->delete();

        return redirect()->back()->with('status','Deleted successfully.');
    
    }

    public function removeTempRewardToCustomer(Request $request,$reward_to_customer_temp_id)
    {
        Trn_RewardToCustomerTemp::where('reward_to_customer_temp_id',$reward_to_customer_temp_id)->delete();

        return redirect()->back()->with('status','Deleted successfully.');
    
    }
    public function listUserLogs()
    {
        $pageTitle="User Logs";
        $user_logs=DB::table('trn_user_logs')->orderBy('trn_user_logs.created_at','DESC')->get();
                   //dd($user_logs);
        return view('admin.masters.user_logs.list',compact('user_logs','pageTitle'));
    }

    



}
