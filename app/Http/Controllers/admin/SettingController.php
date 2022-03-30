<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_store;
use App\Models\admin\Country;
use App\Models\admin\State;
use App\Models\admin\District;
use App\Models\admin\Town;
use App\Models\admin\Mst_store_documents;
use App\Models\admin\Mst_store_images;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_store_link_agency;
use App\Models\admin\Mst_store_companies;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_product_image;
use App\Models\admin\Trn_store_customer_otp_verify;
use App\Models\admin\Mst_delivery_boy;
use App\Models\admin\Sys_delivery_boy_availability;
use App\Models\admin\Mst_store_link_delivery_boy;
use App\Models\admin\Trn_delivery_boy_order;
use App\Models\admin\Sys_vehicle_type;
use App\Models\admin\Sys_store_order_status;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_customer_reward_transaction_type;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_payment;
use App\Models\admin\Mst_store_link_subadmin;
use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Sys_payment_type;
use App\Models\admin\Trn_store_payment_settlment;
use App\Models\admin\Trn_delivery_boy_payment_settlment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Image;
use Hash;
use DB;
use Carbon\Carbon;
use Crypt;

use App\Models\admin\Mst_Subadmin_Detail;
use App\Models\admin\Trn_delivery_boy_payment;
use App\Models\admin\Trn_store_payments_tracker;
use App\Models\admin\Trn_sub_admin_payment_settlment;
use App\Models\admin\Trn_subadmin_payments_tracker;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_registration_point;
use App\Models\admin\Trn_first_order_point;
use App\Models\admin\Trn_referal_point;
use App\Models\admin\Trn_points_to_rupee;
use App\Models\admin\Trn_points_redeemed;
use App\Models\admin\Mst_SubCategory;
use App\Models\admin\Trn_CategoryBusinessType;
use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Trn_TermsAndCondition;
use App\Models\admin\Trn_customerAddress;

use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Trn_OrderSplitPayments;


class SettingController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}

	public function listCategory(Request $request)
	{

		$pageTitle = "Product Categories";
		$categories = Mst_categories::orderBy('category_id', 'DESC')->get();
		$business_types = Mst_business_types::where('business_type_status', 1)->get();



		if ($_GET) {


			$business_type_id = $request->business_type_id;


			$categories = Mst_categories::join('trn__category_business_types', 'trn__category_business_types.category_id', '=', 'mst_store_categories.category_id')
				->where('trn__category_business_types.business_type_id',  $request->business_type_id)
				->get();



			return view('admin.masters.categories.list', compact('categories', 'pageTitle', 'business_types'));
		}
		return view('admin.masters.categories.list', compact('categories', 'pageTitle', 'business_types'));
	}

	public function createCategory()
	{

		$pageTitle = "Create Product Category";
		$categories = Mst_categories::where('category_status', '=', '1')->get();

		$business_types = Mst_business_types::where('business_type_status', '=', 1)->get();
		return view('admin.masters.categories.create', compact('pageTitle', 'categories', 'business_types'));
	}

	public function storeCategory(Request $request, Mst_categories $category)
	{
		//echo "here";die;

		$validator = Validator::make(
			$request->all(),
			[
				'category_name'       => 'required|unique:mst_store_categories',
				//	'category_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
				//	'category_description' => 'required',
				//	'business_type_id'		=> 'required',


			],
			[
				'category_name.required'         => 'Category name required',
				'category_icon.required'        => 'Category icon required',
				'category_icon.dimensions'        => 'Category icon dimensions is invalid',
				'category_description.required'	 => 'Category description required',
				'business_type_id.required'	 => 'Business type required',



			]
		);

		if (!$validator->fails()) {

			$data = $request->except('_token');

			$category->category_name 		= $request->category_name;
			$category->category_name_slug  	= Str::of($request->category_name)->slug('-');
			$category->category_description = $request->category_description;
			$category->business_type_id = 0;
			$category->parent_id 		= 0;

			if ($request->hasFile('category_icon')) {

				$photo = $request->file('category_icon');
				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/category/icons';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);


				// 			$category_icon = $request->file('category_icon');

				// 			$filename = time().'.'.$category_icon->getClientOriginalExtension();

				// 			$location = public_path('assets/uploads/category/icons/'.$filename);

				// 			Image::make($category_icon)->save($location);
				$category->category_icon = $filename;
			}

			$category->category_status 		= 1;

			if ($category->save()) {
				$lastCatid = DB::getPdo()->lastInsertId();
				foreach (array_unique($request->business_type_ids) as  $row) {
					$cb = new Trn_CategoryBusinessType;
					$cb->category_id = $lastCatid;
					$cb->business_type_id = $row;
					$cb->status = 1;
					$cb->save();
				}
			}




			return redirect('admin/categories/list')->with('status', 'Category added successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function editCategory(Request $request, $id)
	{
		$pageTitle = "Edit Product Category";

		$business_types = Mst_business_types::where('business_type_status', '=', 1)->get();
		$category = Mst_categories::where('category_name_slug', '=', $id)
			->first();

		return view('admin.masters.categories.edit', compact('category', 'pageTitle', 'business_types'));
	}
	public function updateCategory(
		Request $request,
		Mst_categories $category,
		$category_id
	) {

		$catId = $request->category_id;
		$category = Mst_categories::Find($catId);


		$validator = Validator::make(
			$request->all(),
			[
				'category_name'       => 'required|unique:mst_store_categories,category_name,' . $category_id . ',category_id',
				//	'category_icon'        => 'dimensions:width=150,height=150|image|mimes:jpeg,png,jpg',
				//		'category_description' => 'required',
				//'business_type_id'		=> 'required',


			],
			[
				'category_name.required'         => 'Category name required',
				'category_icon.dimensions'        => 'Category icon dimensions is invalid',
				'category_icon.required'        => 'Category icon required',
				'category_description.required'	 => 'Category description required',
				'business_type_id.required'	 => 'Business type required',

			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$category->category_name = $request->category_name;
			$category->category_name_slug  	= Str::of($request->category_name)->slug('-');

			$category->category_description = $request->category_description;
			$category->business_type_id = 0;


			if ($request->hasFile('category_icon')) {



				$category_icon = $request->file('category_icon');
				/*	$old_category_icon = 'assets/uploads/category/icons/' . $category->category_icon;
			if (is_file($old_category_icon)) {
                  unlink($old_category_icon);

                    }

			$filename = time().'.'.$category_icon->getClientOriginalExtension();

			$location = public_path('assets/uploads/category/icons/'.$filename);

			Image::make($category_icon)->save($location);
			$category->category_icon = $filename;*/


				$photo = $request->file('category_icon');
				$old_category_icon = 'assets/uploads/category/icons/' . $category->category_icon;
				if (is_file($old_category_icon)) {
					unlink($old_category_icon);
				}
				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/category/icons';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$category->category_icon = $filename;
			}

			if ($request->parent_id == '') {
				$category->parent_id	= 0;
			} else {
				$category->parent_id = $request->parent_id;
			}

			if ($category->update()) {
				Trn_CategoryBusinessType::where('category_id', $category_id)->delete();
				foreach (array_unique($request->business_type_ids) as  $row) {
					$cb = new Trn_CategoryBusinessType;
					$cb->category_id = $category_id;
					$cb->business_type_id = $row;
					$cb->status = 1;
					$cb->save();
				}
			}





			return redirect('admin/categories/list')->with('status', 'Category updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function removeCB(Request $request, Trn_CategoryBusinessType $category, $cbt_id)
	{

		Trn_CategoryBusinessType::where('cbt_id', $cbt_id)->delete();

		return redirect()->back()->with('status', 'Row deleted successfully');
	}


	public function destroyCategory(Request $request, Mst_categories $category)
	{

		$delete = $category->delete();

		return redirect('admin/categories/list')->with('status', 'Category deleted successfully');
	}


	public function statusCategory(Request $request, Mst_categories $category, $category_id)
	{

		$cat_id = $request->category_id;

		$category = Mst_categories::Find($cat_id);

		$status = $category->category_status;

		if ($status == 0) {
			$category->category_status  = 1;
		} else {

			$category->category_status  = 0;
		}
		$category->update();

		return redirect('admin/categories/list')->with('status', 'Category status changed successfully');
	}


	public function listBusiness(Request $request)
	{

		$pageTitle = "Business Types";
		$business_types = Mst_business_types::orderBy('created_at', 'DESC')->get();

		return view('admin.masters.business_types.list', compact('business_types', 'pageTitle'));
	}

	public function createBusiness()
	{

		$pageTitle = "Create Business Type";
		$business_types = Mst_business_types::all();

		return view('admin.masters.business_types.create', compact('pageTitle', 'business_types'));
	}

	public function storeBusiness(Request $request, Mst_business_types $business_type)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'business_type_name'       => 'required|unique:mst_store_business_types',
				'business_type_icon'        => 'required',
				//	'business_type_icon'        => 'image|mimes:jpeg,png,jpg|dimensions:width=150,height=150',



			],
			[
				'business_type_name.required'         => 'Business type name required',
				'business_type_icon.required'        => 'Business type icon required',
				'business_type_icon.dimensions'        => 'Business type icon size invalid',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$business_type->business_type_name 		= $request->business_type_name;
			$business_type->business_type_name_slug  	= Str::of($request->business_type_name)->slug('-');

			if ($request->hasFile('business_type_icon')) {
				/*	$business_type_icon = $request->file('business_type_icon');

			$filename = time().'.'.$business_type_icon->getClientOriginalExtension();

			$location = public_path('assets/uploads/business_type/icons/'.$filename);

			Image::make($business_type_icon)->save($location);*/
				$photo = $request->file('business_type_icon');
				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/business_type/icons';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$business_type->business_type_icon = $filename;
			}

			$business_type->business_type_status 		= 1;


			$business_type->save();

			return redirect('admin/business_type/list')->with('status', 'Business type added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function editBusiness(Request $request, $id)
	{
		$pageTitle = "Edit Business Type";


		$business_type = Mst_business_types::where('business_type_name_slug', '=', $id)
			->first();

		return view('admin.masters.business_types.edit', compact('business_type', 'pageTitle'));
	}
	public function updateBusiness(
		Request $request,
		Mst_categories $business_type,
		$business_type_id
	) {

		$type_Id = $request->business_type_id;
		$business_type = Mst_business_types::Find($type_Id);


		$validator = Validator::make(
			$request->all(),
			[
				'business_type_name'       => 'required|unique:mst_store_business_types,business_type_name,' . $business_type_id . ',business_type_id',
				//	'business_type_icon'        => 'image|mimes:jpeg,png,jpg|dimensions:width=150,height=150',



			],
			[
				'business_type_name.required'         => 'Business type name required',
				'business_type_icon.required'        => 'Business type icon required',
				'business_type_icon.dimensions'        => 'Business type icon size invalid',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$business_type->business_type_name 		= $request->business_type_name;
			$business_type->business_type_name_slug  	= Str::of($request->business_type_name)->slug('-');

			if ($request->hasFile('business_type_icon')) {
				/*	$business_type_icon = $request->file('business_type_icon');

			$filename = time().'.'.$business_type_icon->getClientOriginalExtension();

			$location = public_path('assets/uploads/business_type/icons/'.$filename);

			Image::make($business_type_icon)->save($location);
			$business_type->business_type_icon = $filename;*/

				$photo = $request->file('business_type_icon');
				$old_business_type_icon = 'assets/uploads/business_type/icons/' . $business_type->business_type_icon;
				if (is_file($old_business_type_icon)) {
					unlink($old_business_type_icon);
				}
				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/business_type/icons';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$business_type->business_type_icon = $filename;
			}

			$business_type->business_type_status 		= 1;


			$business_type->update();

			return redirect('admin/business_type/list')->with('status', 'Business type updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function destroyBusiness(Request $request, Mst_business_types $business_type)
	{

		$delete = $business_type->delete();

		return redirect('admin/business_type/list')->with('status', 'Business type deleted successfully');;
	}


	public function statusBusiness(Request $request, Mst_business_types $business_type, $business_type_id)
	{

		$type_id = $request->business_type_id;

		$business_type = Mst_business_types::Find($type_id);

		$status = $business_type->business_type_status;

		if ($status == 0) {
			$business_type->business_type_status  = 1;
		} else {

			$business_type->business_type_status  = 0;
		}
		$business_type->update();

		return redirect('admin/business_type/list')->with('status', 'Business type status changed successfully');
	}

	// store

	public function listStore(Request $request)
	{
		//	echo public_path();die;

		$pageTitle = "Stores";
		$subadmins = User::where('user_role_id', '!=', 0)->get();

		if (auth()->user()->user_role_id  == 0) {
	        $stores = Mst_store::
			    leftjoin('users', 'users.id', '=', 'mst_stores.subadmin_id')
->orderBy('mst_stores.store_id', 'desc')->get();				
		    
		} else {
		    
		      $stores = Mst_store::leftjoin('users', 'users.id', '=', 'mst_stores.subadmin_id')
				->where('mst_stores.subadmin_id', auth()->user()->id)
			->orderBy('mst_stores.store_id', 'desc')->get();
				
// 			$stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
			//  dd($store);
		}

		$countries = Country::all();
		$count = $stores->count();
		$agencies = Mst_store_agencies::all();

		if ($_GET) {

			$subadmin_id = $request->subadmin_id;
			$country_id = $request->store_country_id;
			$state_id = $request->store_state_id;
			$district_id = $request->store_district_id;
			$town_id = $request->store_town_id;
			$store_name  = $request->store_name;
			$email = $request->store_email_address;
			$store_contact_person_phone_number = $request->store_contact_person_phone_number;
			$store_account_status = $request->store_account_status;

			$states = State::where('country_id', $request->store_country_id)->get();
			$districts = District::where('state_id', $request->store_state_id)->get();
			$town = Town::where('district_id', $request->store_district_id)->get();


			$query = Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
			    ->leftjoin('users', 'users.id', '=', 'mst_stores.subadmin_id')
				->where('trn__store_admins.role_id', 0)
				->select('*');

			if (auth()->user()->user_role_id  != 0) {
				$query = $query->where('mst_stores.subadmin_id', auth()->user()->id);
			}

			if (isset($subadmin_id)) {
				$query = $query->where('users.id', $subadmin_id);
			}

			if (isset($country_id)) {
				$query = $query->where('mst_stores.store_country_id', $country_id);
			}
			if (isset($store_state_id)) {
				$query = $query->where('mst_stores.store_state_id', $state_id);
			}
			if (isset($district_id)) {
				$query = $query->where('mst_stores.store_district_id', $district_id);
			}
			if (isset($town_id)) {
				$query = $query->where('mst_stores.town_id', $town_id);
			}
			if (isset($store_name)) {
				$query = $query->where('mst_stores.store_name', 'like', '%' . $store_name . '%');
			}
			if (isset($email)) {
				$query = $query->where('mst_stores.email', 'like', '%' . $email . '%');
			}
			if (isset($store_contact_person_phone_number)) {
				$query = $query->where('trn__store_admins.store_mobile', 'like', '%' . $store_contact_person_phone_number . '%');
			}
			if (isset($store_account_status)) {
				$query = $query->where('trn__store_admins.store_account_status', $store_account_status);
			}

			$stores =  $query->get();


			//    where('store_country_id','like', '%'.$country_id.'%')
			// 	->where('store_state_id','like', '%'.$state_id.'%')
			// 	->where('store_district_id','like', '%'.$district_id.'%')
			// 	->where('town_id','like', '%'.$town_id.'%')
			// 	->where('store_name','like', '%'.$store_name.'%')
			// 	->where('email','like', '%'.$email.'%')
			// 	->where('store_contact_person_phone_number','like', '%'.$store_contact_person_phone_number.'%')
			// 	->where('store_account_status','like', '%'.$store_account_status.'%')
			//     ->get();





			return view('admin.masters.stores.list', compact('subadmins', 'town', 'districts', 'states', 'stores', 'pageTitle', 'countries', 'agencies', 'count'));
		}

		return view('admin.masters.stores.list', compact('subadmins', 'stores', 'pageTitle', 'countries', 'agencies', 'count'));
	}

	public function createStore()
	{

		$pageTitle = "Create Store";
		if (auth()->user()->user_role_id  == 0) {
			$stores = Mst_store::all();
		} else {
			$stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
			//  dd($store);
		}
		$subadmins = User::where('user_role_id', '!=', 0)->get();
		$countries   = Country::all();
		$business_types = Mst_business_types::where('business_type_status', '=', 1)->get();

		return view('admin.masters.stores.create', compact('pageTitle', 'subadmins', 'stores', 'countries', 'business_types'));
	}

	public function addStore(Request $request, Mst_store $store, Mst_store_documents
	$document)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'store_name'       				   => 'required|unique:mst_stores',
				'store_contact_person_name'        => 'required',
				'store_contact_person_phone_number' => 'required',
				'store_pincode'				       => 'required',
				'store_primary_address'            => 'required',
				'store_country_id'			       => 'required',
				'store_state_id'       		       => 'required',
				//'email'       		       => 'required',

				//	'store_commision_amount'                => 'required',

				'store_district_id'                => 'required',
				//	'store_commision_percentage'       => 'required',
				'store_username' 				   => 'required|unique:mst_stores',
				'store_mobile' 				   => 'required|unique:mst_stores',
				'password'       			   => 'required|min:5|same:password_confirmation',



			],
			[
				'store_name.required'         				 => 'Store name required',
				'store_mobile.required'         				 => 'Store name required',
				'store_mobile.unique'         				 => 'Store mobile number already exists',
				// 'email.required'         				 => 'Email required',
				'store_contact_person_name.required'     	 => 'Contact person name required',
				'store_contact_person_phone_number.required' => 'Contact person number required',
				'store_pincode.required'        			 => 'Pincode required',
				'store_primary_address.required'             => 'Primary address required',
				'store_country_id.required'         		 => 'Country required',
				'store_state_id.required'        			 => 'State required',
				'store_district_id.required'        		 => 'District  required',
				'store_username.required'        			 => 'Username required',
				'password.required'					 => 'Password required',
				'store_commision_amount.required'                => 'Store commision amount required',

				'store_commision_percentage.required'	=> 'Store commision percentage requird',


			]
		);

		if ($request->hasFile('store_document_other_file')) {

			$doc_validate = Validator::make(
				$request->all(),
				[
					'store_document_other_file.*'        => 'mimes:pdf,doc,docx,txt',
				],
				[
					'store_document_other_file.*.mimes' => "store documents file format error",
				]
			);
			if ($doc_validate->fails()) {
				return redirect()->back()->withErrors($doc_validate)->withInput();
			}
		}

		if ($request->hasFile('store_image')) {

			$img_validate = Validator::make(
				$request->all(),
				[
					'store_image.*' => 'required|dimensions:min_width=1000,min_height=800',
				],
				[
					'store_image.*.dimensions' => 'store image dimensions invalid',
				]
			);
			if ($img_validate->fails()) {
				return redirect()->back()->withErrors($img_validate)->withInput();
			}
		}

		if (!$validator->fails()) {
			$data = $request->except('_token');

			$store_added_by = Auth()->user()->user_role_id;
			//	dd($data);
			$store->store_name 					= $request->store_name;
			$store->store_name_slug   		    = Str::of($request->store_name)->slug('-');
			$store->store_contact_person_name   = $request->store_contact_person_name;


			$store->store_mobile = $request->store_mobile;
			$store->store_contact_person_phone_number = $request->store_contact_person_phone_number;

			$store->store_website_link 		     = $request->store_website_link;
			$store->store_pincode   	         = $request->store_pincode;
			$store->store_primary_address        = $request->store_primary_address;
			$store->email          = $request->email;
			$store->store_added_by          	= $store_added_by;
			$store->store_country_id             = $request->store_country_id;
			$store->business_type_id             = $request->business_type_id;
			$store->store_state_id  		     = $request->store_state_id;
			$store->store_commision_percentage   = $request->store_commision_percentage;
			$store->store_district_id   	     = $request->store_district_id;
			$store->store_username               = $request->store_username;
			$store->password                     = Hash::make($request->password);
			$store->store_otp_verify_status       = 0;
			if ($store_added_by == 0) {
				$store->store_account_status         = 1;
			} else {
				$store->store_account_status         = 1;
			}
			if (auth()->user()->user_role_id == 0) {
				$store->subadmin_id          = $request->subadmin_id;
			} else {
				$store->subadmin_id          = auth()->user()->id;
			}

			$store->town_id          = $request->store_town;
			$store->place          = $request->store_place;
			$store->store_commision_amount          = $request->store_commision_amount;

			//dd($data);
			$timestamp = time();
			$qrco = Str::of($request->store_name)->slug('-') . "-" . @$request->store_mobile;

			\QrCode::format('svg')->size(500)->generate($qrco, 'assets/uploads/store_qrcodes/' . $qrco . '.svg');
			$store->store_qrcode          = $qrco;


			$senderAddrs = Str::of($request->store_place)->replace(' ', '+');
			$sendrJson = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$senderAddrs&key=AIzaSyBSqyoP-FHj6nJpuIvNYmb1YaGqBmh3xdQ");

			$json = json_decode($sendrJson);

			$sendLat = $json->results[0]->geometry->location->lat;
			$sendLong = $json->results[0]->geometry->location->lng;
			$sendPlaceId = $json->results[0]->place_id;

			$store->latitude          = $sendLat;
			$store->longitude          = $sendLong;
			$store->place_id          = $sendPlaceId;




			$store->save();

			$last_id = DB::getPdo()->lastInsertId();


			$insert['store_id'] = $last_id;
			$insert['admin_name'] = $request->store_name;
			$insert['email'] = $request->email;
			$insert['username'] = $request->store_username;
			$insert['store_mobile'] = $request->store_mobile;
			$insert['role_id'] = 0;
			$insert['store_account_status'] = 1;
			$insert['password'] = Hash::make($request->password);

			if (auth()->user()->user_role_id == 0) {
				$insert['subadmin_id']         = $request->subadmin_id;
			} else {
				$insert['subadmin_id']          = auth()->user()->id;
			}


			Trn_StoreAdmin::create($insert);


			$date = Carbon::now();

			if ($request->hasFile('store_document_other_file')) {


				$allowedfileExtension = ['pdf', 'doc', 'txt',];
				$files = $request->file('store_document_other_file');
				$files_head = $request->store_document_other_file_head;
				$k = 0;
				foreach ($files as $file) {
					//  print_r($files_head[$k]);die;
					$filename = $file->getClientOriginalName();
					$extension = $file->getClientOriginalExtension();

					// $fullpath = $filename . '.' . $extension ;
					$file->move('assets/uploads/store_document/files', $filename);
					$date = Carbon::now();
					$data1 = [
						[
							'store_id'               => $last_id,
							'store_document_license'  => $request->store_document_license,
							'store_document_gstin'     => $request->store_document_gstin,
							'store_document_file_head' => $files_head[$k],
							'store_document_other_file' => $filename,
							'created_at'         		=> $date,
							'updated_at'         		=> $date,
						],
					];

					Mst_store_documents::insert($data1);
					$k++;
				}
			}


			// multiple image upload

			if ($request->hasFile('store_image')) {



				$store_image = $request->file('store_image');
				// dd($product_image);
				foreach ($store_image as $image) {
					$filename = time() . '.' . $image->getClientOriginalExtension();
					// dd($filename);
					$destination_path = 'assets/uploads/store_images/images';

					$store_img = Image::make($image->getRealPath());
					$store_img->save($destination_path . '/' . $filename, 80);



					$data2 = [
						[
							'store_image'      => $filename,
							'store_id' 			=> $last_id,
							'created_at'         => $date,
							'updated_at'         => $date,
						],
					];

					Mst_store_images::insert($data2);
				}
			}


			if (Auth()->user()->user_role_id == 0) {

				return redirect('admin/store/list')->with('status', 'Store added successfully.');
			} else {
				return redirect('admin/store/subadmin/list')->with('status', 'Store added successfully.');
			}
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function editStore(Request $request, $id)
	{

		$pageTitle = "Edit Store";

		$store = Mst_store::where('store_name_slug', '=', $id)->first();
		$store_id = $store->store_id;
		$countries = Country::all();
		$store_documents  = Mst_store_documents::where('store_id', '=', $store_id)->get();
		$store_images = Mst_store_images::where('store_id', '=', $store_id)->get();
		$agencies = Mst_store_link_agency::where('store_id', '=', $store_id)->get();

		$delivery_boys = Mst_store_link_delivery_boy::where('store_id', '=', $store_id)->get();

		if (auth()->user()->user_role_id  == 0) {
			$all_delivery_boys = Mst_delivery_boy::all();
		} else {
			$all_delivery_boys = \DB::table('mst_delivery_boys')
				->join('mst_stores', 'mst_stores.store_id', '=', 'mst_delivery_boys.store_id')
				->where('mst_stores.subadmin_id', auth()->user()->id)
				->get();
		}

		$delivery_boys = Mst_store_link_delivery_boy::where('store_id', '=', $store_id)->get();
		$business_types = Mst_business_types::where('business_type_status', '=', 1)->get();
		$subadmins = User::where('user_role_id', '!=', 0)->get();

		$products = Mst_store_product::where('store_id', $store_id)->orderBy('product_id', 'DESC')
		->where('is_removed',0)
		->get();

		return view('admin.masters.stores.edit', compact('products', 'subadmins', 'all_delivery_boys', 'store', 'pageTitle', 'countries', 'store_images', 'store_documents', 'agencies', 'delivery_boys', 'business_types'));
	}
	public function updateStore(Request $request, Mst_store $store, $store_id)
	{

		$store_Id = $request->store_id;
		$store = Mst_store::Find($store_Id);

		$password = $store->password;
		$newpassword = $request->password;


		$validator = Validator::make(
			$request->all(),
			[
				'store_name'    => 'required|unique:mst_stores,store_name,' . $store_id . ',store_id',
				'store_contact_person_name'        => 'required',
				'store_contact_person_phone_number' => 'required',
				'store_pincode'				       => 'required',
				'store_primary_address'            => 'required',
				'store_country_id'			       => 'required',
				'store_state_id'       		       => 'required',
				//'email'       		       => 'required',


				//'store_commision_amount'			       => 'required',

				'store_district_id'                => 'required',
				'store_username'   => 'required',
				//	'password'       			   => 'sometimes|same:password_confirmation',
				//'store_commision_percentage' =>'required',


			],
			[
				'store_name.required'         				 => 'Store name required',
				'store_contact_person_name.required'     	 => 'Contact person name required',
				'store_contact_person_phone_number.required' => 'Contact person number required',

				//  'email.required'         				 => 'Email required',

				'store_pincode.required'        			 => 'Pincode required',
				'store_primary_address.required'             => 'Primary address required',
				'store_country_id.required'         		 => 'Country required',
				'store_state_id.required'        			 => 'State required',
				'store_district_id.required'        		 => 'District  required',
				'store_username.required'        			 => 'Username required',
				'password.required'					 => 'Password required',
				//'store_commision_amount.required'                => 'Store commision amount required',

				//'store_commision_percentage.required'	=>'Store commision percentage required',



			]
		);


		if ($request->hasFile('store_document_other_file')) {

			$doc_validate = Validator::make(
				$request->all(),
				[
					'store_document_other_file.*'        => 'mimes:pdf,doc,docx,txt',
				],
				[
					'store_document_other_file.*.mimes' => "store documents file format error",
				]
			);
			if ($doc_validate->fails()) {
				return redirect()->back()->withErrors($doc_validate)->withInput();
			}
		}

		if ($request->hasFile('store_image')) {

			$img_validate = Validator::make(
				$request->all(),
				[
					'store_image.*' => 'required|dimensions:min_width=1000,min_height=800',
				],
				[
					'store_image.*.dimensions' => 'store image dimensions invalid',
				]
			);
			if ($img_validate->fails()) {
				return redirect()->back()->withErrors($img_validate)->withInput();
			}
		}

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$store->store_name 					= $request->store_name;
			$store->store_name_slug   		= Str::of($request->store_name)->slug('-');
			$store->store_contact_person_name   = $request->store_contact_person_name;

			$store->store_mobile = $request->store_mobile;
			$store->store_contact_person_phone_number = $request->store_contact_person_phone_number;

			$store->store_website_link 		     = $request->store_website_link;
			$store->store_pincode   	     = $request->store_pincode;
			$store->store_primary_address        = $request->store_primary_address;
			$store->email          				= $request->email;


			$store->store_country_id             = $request->store_country_id;
			$store->store_state_id  		     = $request->store_state_id;
			$store->store_district_id   	     = $request->store_district_id;
			$store->business_type_id   	     	= $request->business_type_id;
			$store->store_username               = $request->store_username;

			if (auth()->user()->user_role_id == 0) {
				$store->subadmin_id          = $request->subadmin_id;
				$store->store_commision_percentage   = $request->store_commision_percentage;
				$store->store_commision_amount          = $request->store_commision_amount;
			} else {
				$store->subadmin_id          = auth()->user()->id;
			}

			$store->town_id          = $request->store_town;
			$store->place          = $request->store_place;

			$senderAddrs = Str::of($request->store_place)->replace(' ', '+');
			$sendrJson = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$senderAddrs&key=AIzaSyBSqyoP-FHj6nJpuIvNYmb1YaGqBmh3xdQ");

			$json = json_decode($sendrJson);

			$sendLat = $json->results[0]->geometry->location->lat;
			$sendLong = $json->results[0]->geometry->location->lng;
			$sendPlaceId = $json->results[0]->place_id;

			$store->latitude          = $sendLat;
			$store->longitude          = $sendLong;
			$store->place_id          = $sendPlaceId;



			// 		if($newpassword == '')
			// 			{
			// 				$store->password = $password;
			// 			}else
			// 			{
			// 				$store->password = Hash::make($request->password);
			// 			}
			$store->update();

			$date = Carbon::now();
			if ($request->hasFile('store_document_other_file')) {



				$allowedfileExtension = ['pdf', 'doc', 'txt',];
				$files = $request->file('store_document_other_file');
				$files_head = $request->store_document_other_file_head;
				$k = 0;
				foreach ($files as $file) {
					$filename = $file->getClientOriginalName();
					$extension = $file->getClientOriginalExtension();

					$file->move('assets/uploads/store_document/files', $filename);

					$data1 = [
						[
							'store_id'               => $store_Id,
							'store_document_license'  => $request->store_document_license,
							'store_document_gstin'     => $request->store_document_gstin,
							'store_document_file_head' => $files_head[$k],
							'store_document_other_file' => $filename,
							'created_at'         		=> $date,
							'updated_at'         		=> $date,
						],
					];

					Mst_store_documents::insert($data1);
					$k++;
				}
			}


			// multiple image upload

			if ($request->hasFile('store_image')) {



				$store_image = $request->file('store_image');
				// dd($product_image);
				foreach ($store_image as $image) {
					$filename = time() . '.' . $image->getClientOriginalExtension();
					// dd($filename);
					$destination_path = 'assets/uploads/store_images/images';

					$store_img = Image::make($image->getRealPath());
					$store_img->save($destination_path . '/' . $filename, 80);



					$data2 = [
						[
							'store_image'      => $filename,
							'store_id' 			=> $store_Id,
							'created_at'         => $date,
							'updated_at'         => $date,
						],
					];

					Mst_store_images::insert($data2);
				}
			}

			return redirect('admin/store/list')->with('status', 'Store updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}




	public function GetStore(Request $request)
	{
		$subadmin_id = $request->subadmin_id;
		//dd($country_id);
		$stores = Mst_store::where('subadmin_id', $subadmin_id)->orderBy('store_id', 'desc')->pluck("store_id", "store_name");

		return response()->json($stores);
	}

	public function GetState(Request $request)
	{
		$country_id = $request->country_id;
		//dd($country_id);
		$state = State::where("country_id", '=', $country_id)
			->pluck("state_name", "state_id");
		return response()->json($state);
	}

	public function GetCity(Request $request)
	{
		$state_id = $request->state_id;
		//dd($state_id);
		$city = District::where("state_id", '=', $state_id)
			->pluck("district_name", "district_id");
		return response()->json($city);
	}

	public function GetTown(Request $request)
	{
		$city_id = $request->city_id;
		//dd($city_id);
		$town = Town::where("district_id", '=', $city_id)
			->pluck("town_name", "town_id");
		//	echo $town;die;
		return response()->json($town);
	}

	public function statusStore(Request $request, Mst_store $store, $store_id)
	{

		$cat_id = $request->store_id;

		$store = Mst_store::Find($cat_id);

		$status = $store->store_account_status;

		if ($status == 0) {
			$store->store_account_status  = 1;
			$storeAdmin['store_account_status'] = 1;
		} else {
			$storeAdmin['store_account_status'] = 0;

			$store->store_account_status  = 0;
		}
		$store->update();

		Trn_StoreAdmin::where('store_id', $store_id)->update($storeAdmin);

		return redirect('admin/store/list')->with('status', 'Store status changed successfully');
	}



	public function statusStorePG(Request $request, Mst_store $store, $store_id)
	{

		$cat_id = $request->store_id;

		$store = Mst_store::Find($cat_id);

		$status = $store->is_pgActivated;

		if ($status == 0) {
			//	$store->is_pgActivated  = 1;
			$storeD['is_pgActivated'] = 1;
		} else {
			$storeD['is_pgActivated'] = 0;

			//	$store->is_pgActivated  = 0;
		}
		//store->update();

		Mst_store::where('store_id', $store_id)->update($storeD);

		return redirect('admin/store/list')->with('status', 'Pament gateway status changed successfully');
	}



	public function destroyStore(Request $request, Mst_store $store)
	{

		$delete = $store->delete();

		return redirect('admin/store/list')->with('status', 'Store deleted successfully');;
	}

	public function destroyStore_Doc(Request $request, Mst_store_documents $document)
	{


		$document =  $document->delete();

		return redirect()->back()->with('status', 'Store document deleted successfully');
	}
	public function destroyAssignedDelivery_boy(Request $request, Mst_store_link_delivery_boy $link_delivery_boy)
	{


		$link_delivery_boy =  $link_delivery_boy->delete();

		return redirect()->back()->with('status', 'Assigned delivery boy deleted successfully');
	}

	public function destroyStore_Image(Request $request, Mst_store_images $image)
	{

		$image = $image->delete();

		return redirect()->back()->with('status', 'Store image deleted Successfully');;
	}


	public function destroyStore_Agency(Request $request, Mst_store_link_agency $agency)
	{


		$agency =  $agency->delete();

		return redirect()->back()->with('status', 'Agency deleted successfully');
	}

	public function destroyStore_Delivery_boy(Request $request, Mst_store_link_delivery_boy $delivery_boy)
	{


		$agency =  $delivery_boy->delete();

		return redirect()->back()->with('status', 'Delivery boy deleted successfully');
	}



	public function viewStore(Request $request, $id)
	{

		$pageTitle = "View Store";

		$store = Mst_store::where('store_name_slug', '=', $id)->first();
		$store_id = $store->store_id;
		$agencies = Mst_store_link_agency::where('store_id', '=', $store_id)->get();
		$delivery_boys = Mst_store_link_delivery_boy::where('store_id', '=', $store_id)->get();
		$countries = Country::all();
		$store_documents  = Mst_store_documents::where('store_id', '=', $store_id)->get();
		$store_products  = Mst_store_product::where('store_id', '=', $store_id)->get();
		$store_images = Mst_store_images::where('store_id', '=', $store_id)->get();
		//dd($store_documents);
		return view('admin.masters.stores.view', compact('delivery_boys', 'store', 'pageTitle', 'countries', 'store_images', 'store_documents', 'agencies', 'store_products'));
	}

	// ajax check for email existance


	function CheckEmail(Request $request)
	{

		$email = $request->email;
		$data = Mst_store::where('email', $email)
			->count();

		if ($data > 0) {
			echo 'not_unique';
		} else {
			echo 'unique';
		}
	}

	function CheckUsername(Request $request)
	{

		$username = $request->store_username;
		$data = Mst_store::where('store_username', $username)
			->count();

		if ($data > 0) {
			echo 'not_unique';
		} else {
			echo 'unique';
		}
	}


	public function listAgency(Request $request)
	{

		$pageTitle = "Agencies";
		$agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->get();
		$countries = Country::all();
		$states = State::all();
		$districts = District::all();

		if ($_GET) {


			$country_id = $request->country_id;
			$state_id = $request->state_id;
			$district_id = $request->district_id;

			$states = State::where('country_id', $request->country_id)->get();
			$districts = District::where('state_id', $request->state_id)->get();


			$agencies = Mst_store_agencies::select("*");
			if ($country_id)
				$agencies->where('country_id', $country_id);
			if ($state_id)
				$agencies->where('state_id', $state_id);
			if ($district_id)
				$agencies->where('district_id', $district_id);
			$agencies = $agencies->get();

			return view('admin.masters.agencies.list', compact('districts', 'states', 'agencies', 'pageTitle', 'countries'));
		}

		return view('admin.masters.agencies.list', compact('districts', 'states', 'agencies', 'pageTitle', 'countries'));
	}

	public function createAgency()
	{

		$pageTitle = "Create Agency";
		$agencies = Mst_store_agencies::all();
		$countries   = Country::all();
		$business_types = Mst_business_types::all();

		return view('admin.masters.agencies.create', compact('pageTitle', 'agencies', 'countries', 'business_types'));
	}


	public function storeAgency(Request $request, Mst_store_agencies $agency)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'agency_name'       			    => 'required|unique:mst_store_agencies',
				'agency_contact_person_name'        => 'required',
				'agency_contact_person_phone_number' => 'required',
				'agency_pincode'				    => 'required',
				'agency_primary_address'            => 'required',
				'agency_email_address'        	    => 'required',
				'country_id'			            => 'required',
				'state_id'       		            => 'required',
				'district_id'                       => 'required',
				'agency_username' 				    => 'required|unique:mst_store_agencies',
				'agency_password'       	        => 'required|min:5|same:password_confirmation',
				// 'agency_logo'					    => 'required|mimes:jpeg,png,jpg,gif,svg'


			],
			[
				'agency_name.required'         				 => 'Agency name required',
				'agency_contact_person_name.required'     	 => 'Contact person name required',
				'agency_contact_person_phone_number.required' => 'Contact person number required',
				'agency_pincode.required'        			 => 'Pincode required',
				'agency_primary_address.required'             => 'Primary address required',
				'agency_email_address.required'               => 'Email required',
				'country_id.required'         		          => 'Country required',
				'state_id.required'        			          => 'State required',
				'district_id.required'        		          => 'District  required',
				'agency_username.required'        			  => 'Username required',
				'agency_password.required'					  => 'Password Required',
				// 'agency_logo.required'						  =>'Agency logo required'


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$agency->agency_name 			= $request->agency_name;
			$agency->agency_name_slug   	= Str::of($request->agency_name)->slug('-');
			$agency->agency_contact_person_name   = $request->agency_contact_person_name;
			$agency->agency_contact_person_phone_number =    $request->agency_contact_person_phone_number;
			$agency->agency_contact_number_2       = $request->agency_contact_number_2;
			$agency->agency_website_link 		   = $request->agency_website_link;
			$agency->agency_pincode   	     	   = $request->agency_pincode;
			$agency->agency_primary_address        = $request->agency_primary_address;
			$agency->agency_email_address          = $request->agency_email_address;
			$agency->country_id             	   = $request->country_id;
			$agency->state_id  		               = $request->state_id;
			$agency->district_id   	               = $request->district_id;
			$agency->business_type_id				= $request->business_type_id;
			$agency->agency_username               = $request->agency_username;
			$agency->agency_password               = Hash::make($request->agency_password);
			$agency->agency_account_status         = 1;





			if ($request->hasFile('agency_logo')) {
				/*	$agency_logo = $request->file('agency_logo');


			$filename = time().'.'.$agency_logo->getClientOriginalExtension();

			$location = public_path('assets/uploads/agency/logos/'.$filename);

			Image::make($agency_logo)->save($location);
			$agency->agency_logo = $filename;
*/

				// $photo = $request->file('agency_logo');
				// $filename = time() . '.' . $photo->getClientOriginalExtension();
				// $destinationPath = 'assets/uploads/agency/logos';
				// $thumb_img = Image::make($photo->getRealPath());
				// $thumb_img->save($destinationPath . '/' .$filename, 80);
				// $agency->agency_logo = $filename;
			}

			$agency->save();
			return redirect('admin/agency/list')->with('status', 'Agency added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function statusAgency(Request $request, Mst_store_agencies $agency, $agency_id)
	{

		$agencyId = $request->agency_id;

		$agency = Mst_store_agencies::Find($agencyId);

		$status = $agency->agency_account_status;

		if ($status == 0) {
			$agency->agency_account_status  = 1;
		} else {

			$agency->agency_account_status  = 0;
		}
		$agency->update();

		return redirect()->back()->with('status', 'Agency status changed successfully');
	}

	public function CheckAgencyEmail(Request $request)
	{

		$email = $request->agency_email_address;

		$data = Mst_store_agencies::where('agency_email_address', $email)
			->count();

		if ($data > 0) {
			echo 'not_unique';
		} else {
			echo 'unique';
		}
	}

	public function CheckAgencyUsername(Request $request)
	{

		$username = $request->agency_username;
		$data = Mst_store_agencies::where('agency_username', $username)
			->count();

		if ($data > 0) {
			//dd()
			echo 'not_unique';
		} else {
			echo 'unique';
		}
	}

	public function editAgency(Request $request, $id)
	{

		$pageTitle = "Edit Agency";

		$agency = Mst_store_agencies::where('agency_name_slug', '=', $id)->first();
		$countries = Country::all();
		$business_types = Mst_business_types::all();

		return view('admin.masters.agencies.edit', compact('agency', 'pageTitle', 'countries', 'business_types'));
	}

	public function updateAgency(
		Request $request,
		Mst_store_agencies $agency,
		$agency_id
	) {

		$agency_Id = $request->agency_id;
		$agency = Mst_store_agencies::Find($agency_Id);

		$password = $agency->agency_password;
		$newpassword = $request->agency_password;


		$validator = Validator::make(
			$request->all(),
			[
				'agency_name'    					=> 'required|unique:mst_store_agencies,agency_name,' . $agency_id . ',agency_id',
				'agency_contact_person_name'        => 'required',
				'agency_contact_person_phone_number' => 'required',
				'agency_pincode'				    => 'required',
				'agency_primary_address'            => 'required',
				'agency_email_address'        	    => 'required',
				'country_id'			            => 'required',
				'state_id'       		            => 'required',
				'district_id'                       => 'required',
				'agency_email_address'        	    => 'required',
				'agency_username'   				=>
				'required|unique:mst_store_agencies,agency_username,' . $agency_id . ',agency_id',
				//'agency_password'        			=> 'sometimes|same:password_confirmation',
				'business_type_id'					=> 'required',


			],
			[
				'agency_name.required'         				  => 'Agency name required',
				'agency_contact_person_name.required'     	  => 'Contact person name required',
				'agency_contact_person_phone_number.required' => 'Contact person number required',
				'agency_pincode.required'        			  => 'Pincode required',
				'agency_primary_address.required'             => 'Primary address required',
				'agency_email_address.required'               => 'Email required',
				'country_id.required'         				  => 'Country required',
				'state_id.required'        			          => 'State required',
				'district_id.required'        		          => 'District  required',
				'agency_username.required'        			  => 'Username required',
				//	'agency_password.required'					  => 'Password required',
				'business_type_id.required'					 => 'Business type required',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$agency->agency_name 					= $request->agency_name;
			$agency->agency_name_slug   		    = Str::of($request->agency_name)->slug('-');
			$agency->agency_contact_person_name     = $request->agency_contact_person_name;
			$agency->agency_contact_person_phone_number = $request->agency_contact_person_phone_number;
			$agency->agency_contact_number_2        = $request->agency_contact_number_2;
			$agency->agency_website_link 		    = $request->agency_website_link;
			$agency->agency_pincode   	            = $request->agency_pincode;
			$agency->agency_primary_address         = $request->agency_primary_address;
			$agency->agency_email_address           = $request->agency_email_address;
			$agency->country_id            		    = $request->country_id;
			$agency->state_id  		                = $request->state_id;
			$agency->district_id   	                = $request->district_id;
			$agency->agency_username                = $request->agency_username;
			$agency->business_type_id                = $request->business_type_id;


			if ($newpassword == '') {
				$agency->agency_password = $password;
			} else {
				$agency->agency_password = Hash::make($request->agency_password);
			}


			if ($request->hasFile('agency_logo')) {
				/*$agency_logo = $request->file('agency_logo');


			$filename = time().'.'.$agency_logo->getClientOriginalExtension();

			$location = public_path('assets/uploads/agency/logos/'.$filename);

			Image::make($agency_logo)->save($location);
			$agency->agency_logo = $filename;*/

				$photo = $request->file('agency_logo');
				$old_agency_logo = 'assets/uploads/agency/logos/' . $agency->agency_logo;
				if (is_file($old_agency_logo)) {
					unlink($old_agency_logo);
				}
				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/agency/logos';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$agency->agency_logo = $filename;
			}

			$agency->update();



			return redirect('admin/agency/list')->with('status', 'Agency updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}


	public function viewAgency(Request $request, $id)
	{

		$pageTitle = "View Agency";

		$agency = Mst_store_agencies::where('agency_name_slug', '=', $id)->first();
		$countries = Country::all();
		$business_types = Mst_business_types::all();

		return view('admin.masters.agencies.view', compact('agency', 'pageTitle', 'countries', 'business_types'));
	}

	public function destroyAgency(Request $request, Mst_store_agencies $agency)
	{

		$delete = $agency->delete();

		return redirect()->back()->with('status', 'Agency deleted successfully');;
	}

	public function assignAgency(Request $request, $id)
	{

		$pageTitle = "Assign Agency";

		$store = Mst_store::where('store_name_slug', '=', $id)->first();

		$agencies = Mst_store_agencies::all();
		$linked_agencies = Mst_store_link_agency::where('store_id', $store->store_id)->get();
		//dd($agencies);
		return view('admin.masters.stores.assign_agency', compact('linked_agencies', 'store', 'pageTitle', 'agencies'));
	}

	public function addStoreAgency(Request $request, Mst_store_link_agency $link_agency)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'agency_id'    					=> 'required',

			],
			[
				'agency_id.required'       => 'Agency required',



			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$date =  Carbon::now();
			$values = $request->agency_id;
			//dd($values);
			foreach ($values as $value) {

				$data = [
					[
						'agency_id' => $value,
						'store_id' => $request->store_id,
						'created_at' => $date,
						'updated_at' => $date,


					],
				];

				Mst_store_link_agency::insert($data);
			}

			return redirect('admin/store/list')->with('status', 'Agency assigned successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function listCompany(Request $request)
	{

		$pageTitle = "Companies";
		$companies = Mst_store_companies::orderBy('company_id', 'DESC')->get();
		$count = $companies->count();
		$countries = Country::all();

		if ($_GET) {


			$country_id = $request->country_id;
			$state_id = $request->state_id;
			$district_id = $request->district_id;


			$companies = Mst_store_companies::where('country_id', 'like', '%' . $country_id . '%')
				->where('state_id', 'like', '%' . $state_id . '%')
				->where('district_id', 'like', '%' . $district_id . '%')
				->get();

			return view('admin.masters.companies.list', compact('companies', 'pageTitle', 'countries', 'count'));
		}

		return view('admin.masters.companies.list', compact('companies', 'pageTitle', 'countries', 'count'));
	}

	public function createCompany()
	{

		$pageTitle = "Create Company";
		$companies = Mst_store_companies::all();
		$countries   = Country::all();
		$business_types = Mst_business_types::all();

		return view('admin.masters.companies.create', compact('pageTitle', 'companies', 'countries', 'business_types'));
	}




	public function storeCompany(Request $request, Mst_store_companies $company)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'company_name'       			    => 'required|unique:mst_store_companies',
				'company_contact_person_name'        => 'required',
				'company_contact_person_phone_number' => 'required',
				//'company_contact_number_2'           => 'required',
				//'company_website_link'        	    => 'required',
				'company_pincode'				    => 'required',
				'company_primary_address'            => 'required',
				//	'company_email_address'        	    => 'required',
				'country_id'			            => 'required',
				'state_id'       		            => 'required',
				'district_id'                       => 'required',
				'company_username' 				    => 'required|unique:mst_store_companies',
				'company_password'       	        => 'required|min:5|same:password_confirmation',
				'company_logo'					    => 'mimes:jpeg,png,jpg,gif,svg'


			],
			[
				'company_name.required'         				 => 'Company name required',
				'company_contact_person_name.required'     	 => 'Contact person name required',
				'company_contact_person_phone_number.required' => 'Contact person number required',
				//'company_contact_number_2.required'            => 'Contact number 2 required',
				//'company_website_link.required'         		 => 'website link required',
				'company_pincode.required'        			 => 'Pincode required',
				'company_primary_address.required'             => 'Primary Address required',
				//'company_email_address.required'               => 'Email required',
				'country_id.required'         		          => 'Country required',
				'state_id.required'        			          => 'State required',
				'district_id.required'        		          => 'District required',
				'company_username.required'        			  => 'Username required',
				'company_password.required'					  => 'Password Required',
				//'company_logo.required'						  =>'company logo Required'


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$company->company_name 			= $request->company_name;
			$company->company_name_slug   	= Str::of($request->company_name)->slug('-');
			$company->company_contact_person_name   = $request->company_contact_person_name;
			$company->company_contact_person_phone_number =    $request->company_contact_person_phone_number;
			$company->company_contact_number_2       = $request->company_contact_number_2;
			$company->company_website_link 		   = $request->company_website_link;
			$company->company_pincode   	     	   = $request->company_pincode;
			$company->company_primary_address        = $request->company_primary_address;
			$company->company_email_address          = $request->company_email_address;
			$company->country_id             	   = $request->country_id;
			$company->state_id  		               = $request->state_id;
			$company->district_id   	               = $request->district_id;
			$company->business_type_id				= $request->business_type_id;
			$company->company_username               = $request->company_username;
			$company->company_password               = Hash::make($request->company_password);
			$company->company_account_status         = 0;


			if ($request->hasFile('company_logo')) {
				/*	$company_logo = $request->file('company_logo');


			$filename = time().'.'.$company_logo->getClientOriginalExtension();

			$location = public_path('assets/uploads/company/logos/'.$filename);

			Image::make($company_logo)->save($location);
			$company->company_logo = $filename;*/

				$photo = $request->file('company_logo');

				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/company/logos';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$company->company_logo = $filename;
			}

			$company->save();
			return redirect('admin/company/list')->with('status', 'Company added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function statusCompany(Request $request, Mst_store_companies $company, $company_id)
	{

		$companyId = $request->company_id;

		$company = Mst_store_companies::Find($companyId);

		$status = $company->company_account_status;

		if ($status == 0) {
			$company->company_account_status  = 1;
		} else {

			$company->company_account_status  = 0;
		}
		$company->update();

		return redirect()->back()->with('status', 'Company status changed successfully');
	}

	public function CheckCompanyEmail(Request $request)
	{

		$email = $request->company_email_address;

		$data = Mst_store_companies::where('company_email_address', $email)
			->count();

		if ($data > 0) {
			echo 'not_unique';
		} else {
			echo 'unique';
		}
	}

	public function CheckCompanyUsername(Request $request)
	{

		$username = $request->company_username;
		$data = Mst_store_companies::where('company_username', $username)
			->count();

		if ($data > 0) {

			echo 'not_unique';
		} else {
			echo 'unique';
		}
	}

	public function editCompany(Request $request, $id)
	{

		$pageTitle = "Edit Company";

		$company = Mst_store_companies::where('company_name_slug', '=', $id)->first();
		$countries = Country::all();
		$business_types = Mst_business_types::all();

		return view('admin.masters.companies.edit', compact('company', 'pageTitle', 'countries', 'business_types'));
	}

	public function updateCompany(
		Request $request,
		Mst_store_companies $company,
		$company_id
	) {

		$company_Id = $request->company_id;
		$company = Mst_store_companies::Find($company_Id);

		$password = $company->company_password;
		$newpassword = $request->company_password;


		$validator = Validator::make(
			$request->all(),
			[
				'company_name'    					=> 'required|unique:mst_store_companies,company_name,' . $company_id . ',company_id',
				'company_contact_person_name'        => 'required',
				'company_contact_person_phone_number' => 'required',
				//	'company_contact_number_2'           => 'required',
				//'company_website_link'        	    => 'required',
				'company_pincode'				    => 'required',
				'company_primary_address'            => 'required',
				//	'company_email_address'        	    => 'required',
				'country_id'			            => 'required',
				'state_id'       		            => 'required',
				'district_id'                       => 'required',
				//'company_email_address'        	    => 'required',
				'company_username'   				=>
				'required|unique:mst_store_companies,company_username,' . $company_id . ',company_id',
				'company_password'        			=> 'sometimes|same:password_confirmation',
				'business_type_id'					=> 'required',


			],
			[
				'company_name.required'         				  => 'Company name required',
				'company_contact_person_name.required'     	  => 'Contact person name required',
				'company_contact_person_phone_number.required' => 'Contact person number required',
				//	'company_contact_number_2.required'            => 'Contact number 2 required',
				//'company_website_link.required'         		  => 'website Link required',
				'company_pincode.required'        			  => 'Pincode required',
				'company_primary_address.required'             => 'Primary address required',
				//'company_email_address.required'               => 'Email required',
				'country_id.required'         				  => 'Country required',
				'state_id.required'        			          => 'State required',
				'district_id.required'        		          => 'District  required',
				'company_username.required'        			  => 'Username required',
				'company_password.required'					  => 'Password required',
				'business_type_id.required'					 => 'Business type required',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$company->company_name 					= $request->company_name;
			$company->company_name_slug   		    = Str::of($request->company_name)->slug('-');
			$company->company_contact_person_name     = $request->company_contact_person_name;
			$company->company_contact_person_phone_number = $request->company_contact_person_phone_number;
			$company->company_contact_number_2        = $request->company_contact_number_2;
			$company->company_website_link 		    = $request->company_website_link;
			$company->company_pincode   	            = $request->company_pincode;
			$company->company_primary_address         = $request->company_primary_address;
			$company->company_email_address           = $request->company_email_address;
			$company->country_id            		    = $request->country_id;
			$company->state_id  		                = $request->state_id;
			$company->district_id   	                = $request->district_id;
			$company->company_username                = $request->company_username;
			$company->business_type_id                = $request->business_type_id;


			if ($newpassword == '') {
				$company->company_password = $password;
			} else {
				$company->company_password = Hash::make($request->company_password);
			}


			if ($request->hasFile('company_logo')) {
				/*$company_logo = $request->file('company_logo');


			$filename = time().'.'.$company_logo->getClientOriginalExtension();

			$location = public_path('assets/uploads/company/logos/'.$filename);

			Image::make($company_logo)->save($location);
			$company->company_logo = $filename;*/

				$photo = $request->file('company_logo');
				$old_company_logo = 'assets/uploads/company/logos/' . $company->company_logo;
				if (is_file($old_company_logo)) {
					unlink($old_company_logo);
				}
				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/company/logos';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$company->company_logo = $filename;
			}

			$company->update();


			return redirect('admin/company/list')->with('status', 'Company updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}


	public function viewCompany(Request $request, $id)
	{

		$pageTitle = "View Company";

		$company = Mst_store_companies::where('company_name_slug', '=', $id)->first();
		$countries = Country::all();
		$business_types = Mst_business_types::all();

		return view('admin.masters.companies.view', compact('company', 'pageTitle', 'countries', 'business_types'));
	}

	public function destroyCompany(Request $request, Mst_store_companies $company)
	{

		$delete = $company->delete();

		return redirect()->back()->with('status', 'Company deleted successfully');;
	}

	public function listCustomer(Request $request)
	{
		$pageTitle = "Customers";
		$customers = Trn_store_customer::orderBy('customer_id', 'DESC')->get();
		$count = $customers->count();

		$countries = Country::all();


		if ($_GET) {

			// echo "<pre>";
			// print_r($request->all());die;

			// if(!$request->customer_first_name && !$request->customer_email
			// && !$request->customer_mobile_number && !$request->date_from
			// && !$request->date_to  && !$request->customer_profile_status
			// )
			// {
			//   return redirect('/admin/customer/list');
			// }

			$customer_first_name = $request->customer_first_name;
			$customer_email = $request->customer_email;
			$customer_mobile_number = $request->customer_mobile_number;
			$customer_profile_status = $request->customer_profile_status;


			// print_r($customer_profile_status);die;
			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2  = Carbon::parse($request->date_to)->endOfDay();


			// $customers = Trn_store_customer::where('customer_first_name','like', '%'.$customer_first_name.'%')
			// 	->where('customer_email','like', '%'.$customer_email.'%')
			// 	->where('customer_mobile_number','like', '%'.$customer_mobile_number.'%')
			// 	->where('customer_profile_status','like', '%'.$customer_profile_status.'%')
			// 	->whereBetween('created_at',[$a,$a])
			// 	->get(); 
			\DB::enableQueryLog();
			//   echo $customer_profile_status;die;
			$query = Trn_store_customer::select("*");

			if ($customer_first_name != "") {

				$query = $query->where('customer_first_name', 'LIKE', "%{$customer_first_name}%")->orWhere('customer_last_name', 'LIKE', "%{$customer_first_name}%");
			}

			if (isset($customer_email)) {
				$query = $query->where('customer_email', 'LIKE', "%{$customer_email}%");
			}

			if (isset($customer_mobile_number)) {
				$query = $query->where('customer_mobile_number', 'LIKE', "%{$customer_mobile_number}%");
			}

			if (isset($customer_profile_status)) {
			    if($customer_profile_status == 0){
				    $query = $query->whereIn("customer_profile_status", [$customer_profile_status, null]);
			    }else{
				    $query = $query->where("customer_profile_status", $customer_profile_status);
			    }
			}

			if (isset($request->date_from) && isset($request->date_to)) {

				$query = $query->whereBetween('created_at', [$a1, $a2]);
			}



			$customers = $query->get();


			return view('admin.masters.customers.list', compact('customers', 'pageTitle', 'countries', 'count'));
		}

		return view('admin.masters.customers.list', compact('customers', 'pageTitle', 'countries', 'count'));
	}

	public function statusCustomer(Request $request, Trn_store_customer $customer, $customer_id)
	{


		$cus_id = $request->customer_id;

		$customer = Trn_store_customer::Find($cus_id);

		$status = $customer->customer_profile_status;

		if ($status == 0) {
			$customer->customer_profile_status  = 1;
		} else {

			$customer->customer_profile_status  = 0;
		}
		$customer->update();

		return redirect()->back()->with('status', 'Customer status changed successfully');
	}

	public function statusOTPCustomer(Request $request, Trn_store_customer $customer, $customer_id)
	{


		$cus_id = $request->customer_id;

		$customer = Trn_store_customer::Find($cus_id);

		$status = $customer->customer_otp_verify_status;

		if ($status == 0) {
			$customer->customer_otp_verify_status  = 1;
		} else {

			$customer->customer_otp_verify_status  = 0;
		}
		$customer->update();

		return redirect()->back()->with('status', 'Customer status changed successfully');
	}
	public function viewcustomer(Request $request, $id)
	{

		$pageTitle = "View Customer";
		$decrId  = Crypt::decryptString($id);
		$customers = Trn_store_customer::Find($decrId);
		$redeemedpoints = Trn_points_redeemed::where('customer_id', $decrId)->orderBy('points_redeemed_id','DESC')->get();
		$redeemedPointsSum = Trn_points_redeemed::where('customer_id', $decrId)->sum('points');
        
        $customerRewards = Trn_customer_reward::where('customer_id', $decrId)
                    ->where('reward_point_status', 1)->orderBy('reward_id', 'DESC')->get();
                    
		$customerAddress = Trn_customerAddress::where('customer_id', $decrId)->get();
		$countries = Country::all();

		return view('admin.masters.customers.view', compact('customerRewards','redeemedPointsSum', 'redeemedpoints', 'customerAddress', 'customers', 'pageTitle', 'countries'));
	}
	public function editcustomer(Request $request, $id)
	{

		$pageTitle = "Edit Customer";

		$decrId  = Crypt::decryptString($id);
		$customer = Trn_store_customer::Find($decrId);
		$countries = Country::all();

		return view('admin.masters.customers.edit', compact('customer', 'pageTitle', 'countries'));
	}

	public function updatecustomer(Request $request, Trn_store_customer $customer, $customer_id)
	{
		//dd($request->all());
		$customer_Id = $request->customer_id;
		$customer = Trn_store_customer::Find($customer_Id);

		//$password = $customer->customer_password;
		//	 $newpassword = $request->customer_password;

		$validator = Validator::make(
			$request->all(),
			[
				'customer_first_name'        => 'required',
				// 'customer_last_name'         => 'required',
				// 'customer_mobile_number'     => 'required',
				// 'customer_email'             => 'required|unique:trn_store_customers,customer_email,'.$customer_id.',customer_id',
				// 'country_id'                 => 'required',
				// 'state_id'                   => 'required',
				// 'district_id'          => 'required',
				// 'town_id'          => 'required',
				//  'customer_location'          => 'required',
				//  'customer_pincode'           => 'required',
				//  'customer_bank_account'      => 'required',
				//  'customer_username' 		 => 'unique:trn_store_customers,customer_username,'.$customer_id.',customer_id',
				'password'                         => 'same:password_confirmation',



			],
			[
				'customer_first_name.required'   => 'Customer first name required',
				'customer_last_name.required'    => 'Customer last name required',
				'customer_mobile_number.required' => 'Customer mobile required',
				'customer_email.required'        => 'Contact email required',
				'country_id.required'            => 'Contry required',
				'state_id.required'              => 'State required',
				'customer_location.required'      => 'Customer location required',
				'customer_pincode.required'       => 'Pincode required',
				'customer_bank_account.required'  => 'Bank Account required',
				'customer_username.required'      => 'Username required',
				'customer_password.same'               => 'Passwords not matching',
				'district_id.required'              => 'Country required',
				'town_id.required'              => 'Town required',



			]
		);
		// $this->uploads($request);

		if (!$validator->fails()) {
			$data = $request->except('_token');

			$customer->customer_first_name           = $request->customer_first_name;
			$customer->customer_last_name            = $request->customer_last_name;
			// $customer->customer_mobile_number        = $request->customer_mobile_number;
			$customer->customer_email                = $request->customer_email;
			$customer->country_id                    = $request->country_id;
			$customer->state_id                      = $request->state_id;


			$customer->district_id                      = $request->district_id;
			$customer->town_id                      = $request->town_id;


			$customer->customer_location             = $request->customer_location;
			$customer->customer_pincode              = $request->customer_pincode;
			$customer->customer_bank_account        = $request->customer_bank_account;
			$customer->customer_address        	= $request->customer_address;
			$customer->customer_username             = $request->customer_username;

			if ($request->password != '') {
				$customer->customer_password = Hash::make($request->customer_password);
			}


			$customer->update();



			return redirect('admin/customer/list')->with('status', 'Customer updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function destroyCustomer(Request $request, Trn_store_customer $customer)
	{

		$delete = $customer->delete();

		return redirect()->back()->with('status', 'Customer deleted successfully');;
	}
	public function CheckCustomerEmail(Request $request)
	{

		$email = $request->customer_email_address;

		$data = Mst_store_customer::where('customer_email_address', $email)
			->count();

		if ($data > 0) {
			echo 'not_unique';
		} else {
			echo 'unique';
		}
	}

	public function CheckCustomerUsername(Request $request)
	{

		$username = $request->customer_username;
		$data = Mst_store_customer::where('customer_username', $username)
			->count();

		if ($data > 0) {

			echo 'not_unique';
		} else {
			echo 'unique';
		}
	}

	public function listSubadmin(Request $request)
	{

		$pageTitle = "Sub Admin";
		$subadmins = User::where('user_role_id', '!=', 0)->orderBy('id', 'DESC')->get();
		$countries   = Country::all();

		if ($_GET) {

			$country_id = $request->country_id;
			$state_id = $request->state_id;
			$district_id = $request->district_id;
			$town_id = $request->town_id;

			// $conditionOne = ' subadmin_details_id != null';

			//    if(isset($country_id))
			//    {
			//        $conditionOne = '  country_id = '.$country_id;
			//    }

			//    if(isset($state_id))
			//    {
			//        $conditionOne .= '  state_id = '.$state_id;
			//    }

			//    if(isset($district_id))
			//    {
			//        $conditionOne .= '  district_id = '.$district_id;
			//    }

			//    if(isset($country_id))
			//    {
			//        $conditionOne .= '  town_id = '.$town_id;
			//  }

			//echo $country_id." ".$state_id." ".$district_id." ".$town_id;die;

			$states = State::where('country_id', $request->country_id)->get();
			$districts = District::where('state_id', $request->state_id)->get();
			$town = Town::where('district_id', $request->district_id)->get();

			DB::enableQueryLog();



			$subadmins = DB::table('mst_subadmin_details')
				->join('users', 'users.id', '=', 'mst_subadmin_details.subadmin_id')
				->when($country_id, function ($query) use ($country_id) {
					return $query->where('mst_subadmin_details.country_id', $country_id);
				})
				->when($state_id, function ($query) use ($state_id) {
					return $query->where('mst_subadmin_details.state_id', $state_id);
				})
				->when($district_id, function ($query) use ($district_id) {
					return $query->where('mst_subadmin_details.district_id', $district_id);
				})
				->when($town_id, function ($query) use ($town_id) {
					return $query->where('mst_subadmin_details.town_id', $town_id);
				})
				->select('users.id')
				->get();
			$subadmins_id[] = 0;
			foreach ($subadmins as $uid) {
				@$subadmins_id[] =  $uid->id;
			}
			$subadmins = User::where('user_role_id', '!=', 0)->whereIn('id', @$subadmins_id)->get();


			// $subadmins= Mst_Subadmin_Detail::where('state_id',$state_id)
			//  ->where('country_id',$country_id)
			// ->where('district_id',$district_id)
			// ->where('town_id',$town_id)
			// ->get();


			//   $subadmins =  \DB::select('select * from mst_subadmin_details where  '.@$conditionOne);


			// $query = DB::getQueryLog();


			//    echo "<pre>";
			//     print_r($subadmins);die;

			return view('admin.masters.subadmin.list', compact('states', 'districts', 'town', 'countries', 'subadmins', 'pageTitle'));
		}

		return view('admin.masters.subadmin.list', compact('countries', 'subadmins', 'pageTitle'));
	}
	public function destroyLink(Request $request, $link_table_id)
	{
		//print_r($link_table_id);die;

		$pageTitle = "Subadmin";
		$subadmins = User::where('user_role_id', '!=', 0)->get();
		//	dd($subadmins);
		return view('admin.masters.subadmin.list', compact('subadmins', 'pageTitle'));
	}

	public function RemoveSubadminLink($store_id, Mst_store $store)
	{
		$store = Mst_store::where('store_id', $store_id)->first();
		$store->subadmin_id = 0;
		$store->update();
		return redirect()->back()->with('status', 'Store removed');
	}

	public function RemoveDeliveryBoyStoreLink($link_table_id)
	{
		$banner = Mst_store_link_delivery_boy::FindOrFail($link_table_id);
		$banner->delete();
		return redirect()->back()->with('status', 'Store removed');
	}

	public function RemoveAgencyStoreLink($link_table_id)
	{
		$banner = Mst_store_link_agency::FindOrFail($link_table_id);
		$banner->delete();
		return redirect()->back()->with('status', 'Agency removed');
	}



	public function createSubadmin()
	{

		$pageTitle = "Create Sub Admin";
		$subadmin = User::all();
		$countries   = Country::all();

		//	$stores = Mst_store::all(); //select stores
		//dd($stores);
		return view('admin.masters.subadmin.create', compact('pageTitle', 'subadmin', 'countries'));
	}

	public function storeSubadmin(Request $request, User $user, Mst_Subadmin_Detail $subadmin_detail)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'name'       => 'required|unique:users|max:20',
				'admin_name'       => 'required',
				//'email'        => 'required',
				'phone'        => 'required',
				'password'		=> 'required|confirmed|min:6',
				'subadmin_commision_percentage'		=> 'required',
				'subadmin_commision_amount'		=> 'required',
				'subadmin_address'		=> 'required',

				'country_id'		=> 'required',
				'state_id'		=> 'required',
				'district_id'		=> 'required',
				'town_id'		=> 'required',



			],
			[
				'admin_name.required'         => 'Name required',
				'name.required'         => 'Username required',
				'name.unique'         => 'Username already taken',
				//'email.required'        => 'Email required',
				'password.required'				=> 'Password required',
				'password.confirmed'				=> 'Password not matching',
				'subadmin_commision_percentage.required'		=> 'Commision percentage required',
				'subadmin_commision_amount.required'		=> 'Commision amount required',
				'subadmin_address.required'		=> ' Subadmin address required',
				'phone.required'         => 'Phone required',

				'country_id.required'         => 'Country required',
				'state_id.required'         => 'State required',
				'district_id.required'         => 'District required',
				'town_id.required'         => 'Town required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$user->name 		= $request->name;
			$user->admin_name 		= $request->admin_name;
			$user->email  		= $request->email;
			$user->user_role_id = 1;
			$user->added_by     = 0;
			$user->password  	= Hash::make($request->password);


			$user->save();
			$last_insert_id = DB::getPdo()->lastInsertId();



			$subadmin_detail->subadmin_id = $last_insert_id;
			$subadmin_detail->subadmin_address =  $request->subadmin_address;
			$subadmin_detail->phone =  $request->phone;

			$subadmin_detail->country_id =  $request->country_id;
			$subadmin_detail->state_id =  $request->state_id;
			$subadmin_detail->district_id =  $request->district_id;
			$subadmin_detail->town_id =  $request->town_id;



			$subadmin_detail->subadmin_commision_amount =  $request->subadmin_commision_amount;
			$subadmin_detail->subadmin_commision_percentage =  $request->subadmin_commision_percentage;

			$subadmin_detail->save();

			return redirect('admin/subadmin/list')->with('status', 'Subadmin added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function editSubadmin(Request $request, $id)
	{
		$pageTitle = "Edit Sub Admin";


		$decrId  = Crypt::decryptString($id);
		$subadmin = User::Find($decrId);
		$subadmin_details = Mst_Subadmin_Detail::where('subadmin_id', $decrId)->first();
		$countries   = Country::all();

		return view('admin.masters.subadmin.edit', compact('countries', 'subadmin_details', 'subadmin', 'pageTitle'));
	}
	public function updateSubadmin(Request $request, User $user, $id)
	{

		$user_Id = $request->id;
		$user = User::Find($user_Id);

		$password = $user->password;
		$newpassword = $request->password;
		$validator = Validator::make(
			$request->all(),
			[
				'name'       => 'required',
				'phone'       => 'required',
				'admin_name'       => 'required',
				//'email'        => 'required',
				'password'		=> 'sometimes|same:password_confirmation',

				'subadmin_commision_percentage'		=> 'required',
				'subadmin_commision_amount'		=> 'required',
				'subadmin_address'		=> 'required',

				'country_id'		=> 'required',
				'state_id'		=> 'required',
				'district_id'		=> 'required',
				'town_id'		=> 'required',



			],
			[
				'admin_name.required'         => 'Name required',
				'name.required'         => 'Username required',
				'password.required'				=> 'Password required',
				'subadmin_commision_percentage.required'		=> 'Commision percentage required',
				'subadmin_commision_amount.required'		=> 'Commision amount required',
				'subadmin_address.required'		=> ' Subadmin address required',
				'phone.required'		=> 'Phone required',

				'country_id.required'         => 'Country required',
				'state_id.required'         => 'State required',
				'district_id.required'         => 'District required',
				'town_id.required'         => 'Town required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$user->name 		= $request->name;
			$user->admin_name 		= $request->admin_name;
			$user->email  	    = $request->email;
			$user->user_role_id = 1;
			if ($newpassword == '') {
				$user->password = $password;
			} else {
				$user->password = Hash::make($request->password);
			}



			$user->update();


			$s['subadmin_address'] =  $request->subadmin_address;
			$s['subadmin_commision_amount'] =  $request->subadmin_commision_amount;
			$s['subadmin_commision_percentage'] =  $request->subadmin_commision_percentage;

			$s['phone'] =  $request->phone;
			$s['country_id'] =  $request->country_id;
			$s['state_id'] =  $request->state_id;
			$s['district_id'] =  $request->district_id;
			$s['town_id'] =  $request->town_id;

			\DB::table('mst_subadmin_details')->where('subadmin_id', $user_Id)->update($s);
			return redirect('admin/subadmin/list')->with('status', 'Subadmin updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function destroySubadmin(Request $request, User $user)
	{

		$delete = $user->delete();

		return redirect('admin/subadmin/list')->with('status', 'Subadmin deleted successfully');;
	}

	public function assignSubadmin_store(Request $request, $id)
	{
		$pageTitle = "Assign Store";
		$decrId  = Crypt::decryptString($id);
		$subadmin = User::Find($decrId);
		$store = Mst_store::where('subadmin_id', $decrId)->get();
		$full_store = Mst_store::all();


		// $subadmin_linked_stores = collect(\DB::table('mst_store_link_subadmins')
		// ->where('mst_store_link_subadmins.subadmin_id',$decrId)
		// ->get());

		return view('admin.masters.subadmin.assign_subadmin', compact('full_store', 'store', 'pageTitle', 'subadmin'));
	}

	public function addStoreSubadmin(Request $request, Mst_store $query)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'store_id'    					=> 'required',

			],
			[
				'store_id.required'       => 'Store required',



			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$date =  Carbon::now();
			$values = $request->store_id;
			//dd($values);
			foreach ($values as $value) {
				if ($value != 0) {
					$data = [
						'subadmin_id' => $request->id,

					];


					$query = Mst_store::where('store_id', $value);

					$query->update($data);
				}
			}

			return redirect('admin/subadmin/list')->with('status', 'Store assigned successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function listDelivery_boy(Request $request)
	{

		$pageTitle = "Delivery Boy";
		if (auth()->user()->user_role_id  == 0) {
			$delivery_boys = Mst_delivery_boy::orderBy('delivery_boy_id', 'DESC')->get();
		} else {
		    
		    		    			$storesSubadmins = Mst_store::where('subadmin_id', auth()->user()->id)->pluck('store_id');


			$delivery_boys = \DB::table('mst_delivery_boys')
							->join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')

				->whereIn('mst_store_link_delivery_boys.store_id',$storesSubadmins )
				->orderBy('mst_delivery_boys.delivery_boy_id', 'DESC')
				->groupBy('mst_store_link_delivery_boys.delivery_boy_id')
				->get();
				
				
		}

		$countries = Country::all();
		if (auth()->user()->user_role_id  == 0) {
			$stores = Mst_store::all();
		} else {
			$stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
			//  dd($store);
		}
		if ($_GET) {

			$store_id = $request->store_id;
			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2  = Carbon::parse($request->date_to)->endOfDay();

			$query = DB::table('mst_delivery_boys')
				->join('mst_store_link_delivery_boys', 'mst_store_link_delivery_boys.delivery_boy_id', '=', 'mst_delivery_boys.delivery_boy_id')
				//     ->join('mst_stores','mst_stores.store_id','=','mst_delivery_boys.store_id')
				//->where('mst_stores.subadmin_id',auth()->user()->id)
				// ->where('mst_store_link_delivery_boys.subadmin_id',auth()->user()->id)
				->select("mst_delivery_boys.*");

			// if(auth()->user()->user_role_id  != 0)
			// {
			//     $query = $query->where('mst_delivery_boys.subadmin_id',auth()->user()->id);
			// }

			if (isset($store_id)) {
				$query = $query->where('mst_store_link_delivery_boys.store_id', $store_id);
			}

			if (isset($request->date_from) && isset($request->date_to)) {
				$query = $query->whereDate('mst_delivery_boys.created_at', '>=', $a1->format('Y-m-d') . " 00:00:00");
				$query = $query->whereDate('mst_delivery_boys.created_at', '<=', $a2->format('Y-m-d') . " 00:00:00");
			}


			$delivery_boys = $query->get();

			return view('admin.masters.delivery_boy.list', compact('delivery_boys', 'pageTitle', 'countries', 'stores'));
		}

		return view('admin.masters.delivery_boy.list', compact('delivery_boys', 'pageTitle', 'countries', 'stores'));
	}




	public function createDelivery_boy()
	{

		$pageTitle = "Create Delivery Boy";
		$delivery_boy = Mst_delivery_boy::all();
		$countries   = Country::all();

		if (auth()->user()->user_role_id  == 0) {
			$stores = Mst_store::all();
		} else {
			$stores = Mst_store::where('subadmin_id', auth()->user()->id)->get();
			//  dd($store);
		}
		$vehicle_types = Sys_vehicle_type::all();
		$availabilities = Sys_delivery_boy_availability::all();
		//dd($availabilities);
		return view('admin.masters.delivery_boy.create', compact('pageTitle', 'delivery_boy', 'countries', 'stores', 'vehicle_types', 'availabilities'));
	}

	public function editDelivery_boy(Request $request, $id)
	{
		$pageTitle = "Edit Delivery Boy";

		$decrId  = Crypt::decryptString($id);
		$delivery_boy = Mst_delivery_boy::Find($decrId);
		$countries = Country::all();
		if (auth()->user()->user_role_id  == 0) {
			$stores = Mst_store::all();
		} else {
			$stores = Mst_store::where('subadmin_id', auth()->user()->id)->get();
			//  dd($store);
		}
		$vehicle_types = Sys_vehicle_type::all();
		$availabilities = Sys_delivery_boy_availability::all();

		return view('admin.masters.delivery_boy.edit', compact('pageTitle', 'delivery_boy', 'countries', 'stores', 'vehicle_types', 'availabilities'));
	}


	public function viewDelivery_boy(Request $request, $id)
	{
		$pageTitle = "View Delivery Boy"; 

		$decrId  = Crypt::decryptString($id);
		$delivery_boy = Mst_delivery_boy::Find($decrId);
		$delivery_boy_id = $delivery_boy->delivery_boy_id;
		$countries = Country::all();

		if (auth()->user()->user_role_id  == 0) {
			$stores = Mst_store::all();
		} else {
			$stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
			//  dd($store);
		}



		$vehicle_types = Sys_vehicle_type::all();
		$assigned_stores = Mst_store_link_delivery_boy::where('delivery_boy_id', '=', $delivery_boy_id)->get();
		//dd($assigned_stores);
		$availabilities = Sys_delivery_boy_availability::all();
		
		
			$delivery_boy_orders = Trn_store_order::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
			->join('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id')->where('trn_store_orders.delivery_boy_id', $delivery_boy_id)
			->orderBy('trn_store_orders.order_id', 'DESC')->get();
			

		return view('admin.masters.delivery_boy.view', compact('delivery_boy_orders','pageTitle', 'delivery_boy', 'countries', 'stores', 'vehicle_types', 'availabilities', 'assigned_stores'));
	}
	public function updateDelivery_boy(Request $request, Mst_delivery_boy $delivery_boy, $delivery_boy_id)
	{
		$boy_Id = $request->delivery_boy_id;
		$delivery_boy = Mst_delivery_boy::Find($boy_Id);


		$validator = Validator::make(
			$request->all(),
			[
				'delivery_boy_name'       			=> 'required',
				'delivery_boy_mobile'           	=> 'required',
				//'delivery_boy_email'=> 'required',
				'delivery_boy_address'          	=> 'required',
				'vehicle_number'        	    	=> 'required',
				'vehicle_type_id'					=> 'required',
			//	'delivery_boy_availability_id'  	=> 'required',
				//'store_id'        	        		=> 'required',
				'country_id'			    		=> 'required',
				'state_id'       		    		=> 'required',
				'town_id'       		    		=> 'required',
				'district_id'               		=> 'required',
				'delivery_boy_commision'            => 'required',
				'delivery_boy_commision_amount'            => 'required',
				'delivery_boy_username' => 'required|unique:mst_delivery_boys,delivery_boy_username,' . $delivery_boy_id . ',delivery_boy_id',
				'password'  => 'sometimes|same:password_confirmation',

			],
			[
				'delivery_boy_name.required'           => 'Delivery boy name required',
				'delivery_boy_mobile.required'     	   => 'Mobile required',
				//'delivery_boy_email.required' 		   => 'Email required',
				'delivery_boy_address.required'        => 'Address required',
				'vehicle_type_id.required'        	   => 'Vehicle type required',
				'delivery_boy_availability_id.required' => 'Availability required',
				//'store_id.required'               	   => 'Store required',
				'country_id.required'         		   => 'Country required',
				'state_id.required'        			   => 'State required',
				'town_id.required'        			   => 'Town required',
				'district_id.required'        		   => 'District  required',
				'delivery_boy_username.required'       => 'Username required',
				'password.required'	   => 'Password required',
				'delivery_boy_commision.required'	   => 'Delivery boy commision percentage required',
				'delivery_boy_commision_amount.required'	=> 'Delivery boy commision percentage required',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');
		//	$data['delivery_boy_availability_id'] = implode(',', $data['delivery_boy_availability_id']);
			// $data->delivery_boy_availability_id = implode($data->delivery_boy_availability_id);
			//dd($data);

			$delivery_boy->delivery_boy_name 	 = $request->delivery_boy_name;
			$delivery_boy->delivery_boy_mobile   = $request->delivery_boy_mobile;
			$delivery_boy->delivery_boy_email    = $request->delivery_boy_email;
			$delivery_boy->delivery_boy_address  = $request->delivery_boy_address;
			$delivery_boy->vehicle_number 		 = $request->vehicle_number;
			$delivery_boy->vehicle_type_id   	 = $request->vehicle_type_id;
		//	$delivery_boy->delivery_boy_availability_id  = $data['delivery_boy_availability_id'];
			//$delivery_boy->store_id               = $request->store_id;
			$delivery_boy->country_id             = $request->country_id;
			$delivery_boy->state_id  		      = $request->state_id;
			$delivery_boy->district_id   	      = $request->district_id;
			$delivery_boy->town_id   	      = $request->town_id;

			$delivery_boy->delivery_boy_commision = $request->delivery_boy_commision;
			$delivery_boy->delivery_boy_commision_amount = $request->delivery_boy_commision_amount;

			$delivery_boy->delivery_boy_username  = $request->delivery_boy_username;
			//	$delivery_boy->delivery_boy_status   = 0;
			if (isset($request->password)) {
				$delivery_boy->password = Hash::make($request->password);
			}



			if ($request->hasFile('delivery_boy_image')) {
				/*	$delivery_boy_image = $request->file('delivery_boy_image');


			$filename = time().'.'.$delivery_boy_image->getClientOriginalExtension();

			$location = public_path('assets/uploads/delivery_boy/images/'.$filename);

			Image::make($delivery_boy_image)->save($location);
			$delivery_boy->delivery_boy_image = $filename;*/

				$photo = $request->file('delivery_boy_image');
				$old_delivery_boy_image = 'assets/uploads/company/logos/' . $delivery_boy->delivery_boy_image;
				if (is_file($old_delivery_boy_image)) {
					unlink($old_delivery_boy_image);
				}
				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/delivery_boy/images';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$delivery_boy->delivery_boy_image = $filename;
			}

			$delivery_boy->update();
			return redirect('admin/delivery_boy/list')->with('status', 'Delivery boy updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}



	public function storeDelivery_boy(Request $request, Mst_delivery_boy $delivery_boy)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'delivery_boy_name'       			=> 'required',
				'delivery_boy_mobile' => 'required|unique:mst_delivery_boys',
				//'delivery_boy_email'=> 'required',
				'delivery_boy_address'          	=> 'required',
				'vehicle_number'        	    	=> 'required',
				'vehicle_type_id'					=> 'required',
			//	'delivery_boy_availability_id'  	=> 'required',
				//'store_id'        	        		=> 'required',
				'country_id'			    		=> 'required',
				'state_id'       		    		=> 'required',
				'town_id'       		    		=> 'required',
				'district_id'               		=> 'required',
				'delivery_boy_commision'            => 'required',
				'delivery_boy_commision_amount'            => 'required',
				'delivery_boy_username' => 'required|unique:mst_delivery_boys',
				'delivery_boy_password'  => 'required|min:5|same:password_confirmation',
				'delivery_boy_image'		 => 'mimes:jpeg,png,jpg,gif,svg|dimensions:min_width=1000,min_height=800'


			],
			[
				'delivery_boy_name.required'           => 'Delivery boy name required',
				'delivery_boy_mobile.required'     	   => 'Mobile required',
				//'delivery_boy_email.required' 		   => 'Email required',
				'delivery_boy_address.required'        => 'Address required',
				'vehicle_type_id.required'        	   => 'Vehicle type required',
			//	'delivery_boy_availability_id.required' => 'Availability required',
				//'store_id.required'               	   => 'Store required',
				'country_id.required'         		   => 'Country required',
				'state_id.required'        			   => 'State required',
				'town_id.required'        			   => 'Town required',
				'district_id.required'        		   => 'District  required',
				'delivery_boy_username.required'       => 'Username required',
				'delivery_boy_password.required'	   => 'Password required',
				//'delivery_boy_image.required'		   =>'Image Required',
				'delivery_boy_image.dimensions'		   => 'Image dimensions invalid',
				'delivery_boy_commision.required'	   => 'Delivery boy commision percentage required',
				'delivery_boy_commision_amount.required'	=> 'Delivery boy commision percentage required',

			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');
		//	$data['delivery_boy_availability_id'] = implode(',', $data['delivery_boy_availability_id']);
			//dd($data);

			$delivery_boy->delivery_boy_name 	 = $request->delivery_boy_name;
			$delivery_boy->delivery_boy_mobile   = $request->delivery_boy_mobile;
			$delivery_boy->delivery_boy_email    = $request->delivery_boy_email;
			$delivery_boy->delivery_boy_address  = $request->delivery_boy_address;
			$delivery_boy->vehicle_number 		 = $request->vehicle_number;
			$delivery_boy->vehicle_type_id   	 = $request->vehicle_type_id;
		//	$delivery_boy->delivery_boy_availability_id  = $data['delivery_boy_availability_id'];
			$delivery_boy->store_id               = $request->store_id;





			$delivery_boy->country_id             = $request->country_id;
			$delivery_boy->state_id  		      = $request->state_id;
			$delivery_boy->district_id   	      = $request->district_id;
			$delivery_boy->town_id   	      = $request->town_id;


			if (auth()->user()->user_role_id  != 0) {
				$delivery_boy->subadmin_id = auth()->user()->id;
			}

			$delivery_boy->delivery_boy_commision = $request->delivery_boy_commision;
			$delivery_boy->delivery_boy_commision_amount = $request->delivery_boy_commision_amount;

			$delivery_boy->delivery_boy_username  = $request->delivery_boy_username;
			$delivery_boy->password  = Hash::make($request->delivery_boy_password);
			$delivery_boy->delivery_boy_status   = 0;


			if ($request->hasFile('delivery_boy_image')) {
				/*	$delivery_boy_image = $request->file('delivery_boy_image');


			$filename = time().'.'.$delivery_boy_image->getClientOriginalExtension();

			$location = public_path('assets/uploads/delivery_boy/images/'.$filename);

			Image::make($delivery_boy_image)->save($location);
			$delivery_boy->delivery_boy_image = $filename;*/

				$photo = $request->file('delivery_boy_image');

				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/delivery_boy/images';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$delivery_boy->delivery_boy_image = $filename;
			}

			$delivery_boy->save();
			if (isset($request->store_id)) {

				$last_insert_id = DB::getPdo()->lastInsertId();

				$date =  Carbon::now();

				$dataz = [
					'store_id' => $request->store_id,
					'delivery_boy_id' => $last_insert_id,
					'created_at' => $date,
					'updated_at' => $date,

				];

				Mst_store_link_delivery_boy::insert($dataz);
			}



			return redirect('admin/delivery_boy/list')->with('status', 'Delivery boy added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function destroyDelivery_boy(Request $request, mst_delivery_boy $delivery_boy)
	{

		$delete = $delivery_boy->delete();

		return redirect('admin/delivery_boy/list')->with('status', 'Delivery boy deleted successfully');;
	}

	public function assignDelivery_boy(Request $request, $id)
	{

		$pageTitle = "Assign Delivery boy";

		$store = Mst_store::where('store_name_slug', '=', $id)->first();

		$delivery_boys = Mst_delivery_boy::all();

		$dboy_linked_stores = collect(\DB::table('mst_store_link_subadmins')
			->where('mst_store_link_subadmins.subadmin_id', $id)
			->get());

		return view('admin.masters.stores.assign_delivery_boy', compact('store', 'pageTitle', 'delivery_boys'));
	}

	public function addStoreDelivery_boy(Request $request, Mst_store_link_delivery_boy $link_delivery_boy)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'delivery_boy_id'    					=> 'required',

			],
			[
				'delivery_boy_id.required'       => 'Delivery boy required',



			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$date =  Carbon::now();
			$values = $request->delivery_boy_id;
			//dd($values);
			foreach ($values as $value) {

				$data = [
					[
						'delivery_boy_id' => $value,
						'store_id' => $request->store_id,
						'created_at' => $date,
						'updated_at' => $date,


					],
				];

				Mst_store_link_delivery_boy::insert($data);
			}

			return redirect('admin/store/list')->with('status', 'Delivery boy assigned successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}


	public function assignStore(Request $request, $id)
	{

		$pageTitle = "Assign Store";
		$decrId  = Crypt::decryptString($id);
		$delivery_boy = Mst_delivery_boy::Find($decrId);

		/* $store_id = $delivery_boy->store_id;*/
		if (auth()->user()->user_role_id  == 0) {
			$store = Mst_store::all();
		} else {
			$store = Mst_store::where('subadmin_id', auth()->user()->id)->get();
			//  dd($store);
		}

		$dboy_linked_stores = collect(\DB::table('mst_store_link_delivery_boys')
			->where('mst_store_link_delivery_boys.delivery_boy_id', $decrId)
			->get());

		return view('admin.masters.delivery_boy.assign_store', compact('dboy_linked_stores', 'store', 'pageTitle', 'delivery_boy'));
	}
	public function addAssignedDeliveryBoy(Request $request)
	{

		$date =  Carbon::now();
		$store_id = $request->store_id;
		$delivery_boy_id = $request->delivery_boy_id;
		$date =  Carbon::now();
		$values = $request->store_id;
		foreach ($delivery_boy_id as $value) {

			$data = [
				'store_id' => $store_id,
				'delivery_boy_id' => $value,
				'created_at' => $date,
				'updated_at' => $date,

			];

			Mst_store_link_delivery_boy::insert($data);
		}
		$store_info = Mst_store::where('store_id', $store_id)->first();

		return redirect('/admin/store/edit/' . $store_info->store_name_slug)->with('status', 'Delivery boy assigned successfully.');


		//return redirect('/admin/store/edit/'.$request->store_name)->with('status','Delivery Boy Assigned successfully.');

	}

	public function addAssignedStore(Request $request)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'store_id'    					=> 'required',

			],
			[
				'store_id.required'       => 'Agency required',



			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$date =  Carbon::now();
			$values = $request->store_id;
			//dd($values);
			foreach ($values as $value) {

				$data = [
					[
						'store_id' => $value,
						'delivery_boy_id' => $request->delivery_boy_id,
						'created_at' => $date,
						'updated_at' => $date,


					],
				];

				Mst_store_link_delivery_boy::insert($data);
			}

			return redirect('admin/delivery_boy/list')->with('status', 'Store assigned successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}


	public function listOrder(Request $request)
	{



		$pageTitle = "Store Orders";
		//$orders = Trn_store_order::all();

		$status = Sys_store_order_status::all();
		$store = Mst_store::select("*");

        if (auth()->user()->user_role_id  != 0) {
           $store = $store->where('subadmin_id', auth()->user()->id);
        }

		$store = $store->get();

		
		$product = Mst_store_product::all();
		$subadmins = User::where('user_role_id', '!=', 0)->get();

		$datefrom = Carbon::now()->format('Y-m-d');
		$dateto = Carbon::now()->format('Y-m-d');
		$a1 = Carbon::parse($datefrom)->startOfDay();
		$a2 = Carbon::parse($dateto)->endOfDay();
		
		$orders = Trn_store_order::join('mst_stores','mst_stores.store_id','=','trn_store_orders.store_id')
		    ->select("*");
		if (auth()->user()->user_role_id  != 0) {
           $orders = $orders->where('mst_stores.subadmin_id', auth()->user()->id);
        }
		$orders = $orders->whereDate('trn_store_orders.created_at', '>=', $a1->format('Y-m-d') . " 00:00:00");
		$orders = $orders->whereDate('trn_store_orders.created_at', '<=', $a2->format('Y-m-d') . " 00:00:00");
		$orders = $orders->orderBy('trn_store_orders.order_id', 'DESC')->get();
		$count = $orders->count();

		if ($_GET) {

			$datefrom = $request->date_from;
			$dateto = $request->date_to;

			$store_id = $request->store_id;
			$status_id = $request->status_id;
			$subadmin_id = $request->subadmin_id;

			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2 = Carbon::parse($request->date_to)->endOfDay();


			DB::enableQueryLog();

			$query = Trn_store_order::select("*");

			if ($status_id) {
				$query = $query->where('status_id', $status_id);
			}
			
				if (auth()->user()->user_role_id  != 0) {  // if subadmin
                   $store_id = 0;
                   $subadmin_id = auth()->user()->id;
                }

			if ($store_id == 0 && isset($subadmin_id)) {
				$store_data = DB::table('mst_stores')->select("store_id")->where('subadmin_id', $subadmin_id)->get();
				$store_array[] = 0;
				foreach ($store_data as $val) {
					$store_array[] = $val->store_id;
				}
				$query = $query->whereIn('store_id', $store_array);
			} elseif ($store_id == 0) {

				$store_data = \DB::table('mst_stores')->select("store_id")->get();
				foreach ($store_data as $val) {
					$store_array[] = $val->store_id;
				}
				$query = $query->whereIn('store_id', $store_array);
			} else {
				$store_array[] = $store_id;
				$query = $query->whereIn('store_id', $store_array);
			}

			if (isset($request->date_from) && isset($request->date_to)) {
				// $query = $query->whereBetween('created_at',[$a1->format('Y-m-d')." 00:00:00",$a2->format('Y-m-d')." 00:00:00"]);
				//echo "die";die;
				$query = $query->whereDate('created_at', '>=', $a1->format('Y-m-d') . " 00:00:00");
				$query = $query->whereDate('created_at', '<=', $a2->format('Y-m-d') . " 00:00:00");
			}

			if (isset($request->date_from) && !isset($request->date_to)) {
				$query = $query->whereDate('created_at', '>=', $a1->format('Y-m-d') . " 00:00:00");
			}
			if (!isset($request->date_from) && isset($request->date_to)) {
				$query = $query->whereDate('created_at', '<=', $a2->format('Y-m-d') . " 00:00:00");
			}


			$orders = $query->orderBy('order_id', 'DESC')->get();


			$quries = DB::getQueryLog();
			//dd($quries);







			return view('admin.masters.order.list', compact('datefrom', 'dateto', 'subadmins', 'orders', 'pageTitle', 'status', 'store', 'status', 'product', 'count'));
		}

		return view('admin.masters.order.list', compact('datefrom', 'dateto', 'orders', 'subadmins', 'pageTitle',  'store', 'status', 'product', 'count'));
	}
	public function statusDisputes(Request $request, $dispute_id)
	{
		$data['dispute_status']  = $request->dispute_status;
		$data['store_response']  = $request->store_response;
		$query = \DB::table("mst_disputes")->where('dispute_id', $dispute_id)->update($data);
		return redirect()->back()->with('status', 'Status updated successfully.');
	}


	public function listDisputes(Request $request)
	{
		$pageTitle = "Disputes";
		$stores = Mst_Store::all();
		if ($_GET) {


			$datefrom = $request->date_from;
			$dateto = $request->date_to;

			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2  = Carbon::parse($request->date_to)->endOfDay();

			$order_number  = $request->order_number;
			$store_id  = $request->store_id;

			$query = \DB::table("mst_disputes")->select("*");

			if (isset($store_id)) {
				$query = $query->where('store_id', $store_id);
			}

			if (isset($order_number)) {
				$query = $query->where('order_number', $order_number);
			}

			if (isset($request->date_from) && isset($request->date_to)) {
				$query = $query->whereBetween('dispute_date', [$a1, $a2]);
			}

			$disputes = $query->orderBy('dispute_id','DESC')->get();


			return view('admin.masters.disputes.list', compact('dateto', 'datefrom', 'disputes', 'stores', 'pageTitle'));
		}

		$disputes = \DB::table("mst_disputes")->select("*")->orderBy('dispute_id','DESC')->get();
		return view('admin.masters.disputes.list', compact('disputes', 'stores', 'pageTitle'));
	}


	public function viewOrder(Request $request, $id)
	{
		try {

			$pageTitle = "View Order";
			$decrId  = Crypt::decryptString($id);
			$order = Trn_store_order::Find($decrId);
			$customer = Trn_store_customer::all();
			$status = Sys_store_order_status::all();
			$order_items = Trn_store_order_item::where('order_id', $decrId)->get();

			$payments = Trn_OrderPaymentTransaction::join('trn__order_split_payments', 'trn__order_split_payments.opt_id', '=', 'trn__order_payment_transactions.opt_id')
				->join('trn_store_orders', 'trn_store_orders.order_id', '=', 'trn__order_payment_transactions.order_id')
				//->where('trn__order_split_payments.paymentRole', '=', 1)
				->where('trn_store_orders.order_id', '=', $decrId)
				->get();


			// $delivery_Boy = Mst_delivery_boy::Find($decrId);
			//delivery_boy_id
			return view('admin.masters.order.view', compact('payments', 'order_items', 'order', 'pageTitle', 'status', 'customer'));
		} catch (\Exception $e) {
			//echo $e->getMessage();die;
			return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
		}
	}
	public function viewInvoice(Request $request, $id)
	{
		try {

			$pageTitle = "View Invoice";
			$decrId  = Crypt::decryptString($id);
			$order = Trn_store_order::Find($decrId);
			$customer = Trn_store_customer::all();
			$status = Sys_store_order_status::all();
			$order_items = Trn_store_order_item::where('order_id', $decrId)->get();
			$store_data = Mst_store::where('store_id', $order->store_id)->first();

			return view('admin.masters.order.invoice', compact('store_data', 'order_items', 'order', 'pageTitle', 'status', 'customer'));
		} catch (\Exception $e) {
			//echo $e->getMessage();die;
			return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
		}
	}

	public function listPayment(Request $request, Trn_store_payment $payments_array)
	{

		$pageTitle = "Incoming Payments";
		$order = Trn_store_order::all();
		$customer = Trn_store_customer::all();
		$store = Mst_store::all();
		$payment_type = Sys_payment_type::all();
		$subadmins = User::where('user_role_id', '!=', 0)->get();
		$datefrom = '';
		$dateto = '';
		$payments = Trn_OrderPaymentTransaction::join('trn__order_split_payments', 'trn__order_split_payments.opt_id', '=', 'trn__order_payment_transactions.opt_id')
			->join('trn_store_orders', 'trn_store_orders.order_id', '=', 'trn__order_payment_transactions.order_id')
			->where('trn__order_split_payments.paymentRole', '!=', 1)
			->get();
		//dd($payments);

		if ($_GET) {

			$datefrom = $request->date_from;
			$dateto = $request->date_to;

			$payments = Trn_OrderPaymentTransaction::join('trn__order_split_payments', 'trn__order_split_payments.opt_id', '=', 'trn__order_payment_transactions.opt_id')
				->join('trn_store_orders', 'trn_store_orders.order_id', '=', 'trn__order_payment_transactions.order_id')
				->where('trn__order_split_payments.paymentRole', '!=', 1);

			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2  = Carbon::parse($request->date_to)->endOfDay();

			if (isset($request->date_from)) {
				$payments = $payments->whereDate('trn_store_orders.created_at', '>=', $a1);
			}

			if (isset($request->date_to)) {
				$payments = $payments->whereDate('trn_store_orders.created_at', '<=', $a2);
			}

			if (isset($request->subadmin_id)) {
				$payments = $payments->where('trn_store_orders.subadmin_id', '=', $request->subadmin_id);
			}

			if (isset($request->store_id)) {
				$payments = $payments->where('trn_store_orders.store_id', '=', $request->store_id);
			}

			$payments = $payments->get();


			$payment_type_id = $request->payment_type_id;
			$subadmin_id = $request->subadmin_id;
			$store_id = $request->store_id;



			return view('admin.masters.payments.list', compact('dateto', 'datefrom', 'payments', 'subadmins', 'pageTitle', 'order', 'customer', 'payment_type', 'store'));
		}

		return view('admin.masters.payments.list', compact('subadmins', 'payments', 'pageTitle', 'order', 'customer', 'payment_type', 'store'));
	}

	public function listSubadminOrder(Request $request, Trn_store_order $query)
	{
		$pageTitle = "List Order";
		$orders = Trn_store_order::join('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id')
			->where('mst_stores.subadmin_id', auth()->user()->id)->get();
		$status = Sys_store_order_status::all();

		if (auth()->user()->user_role_id  == 0) {
			$store = Mst_store::all();
		} else {
			$store = Mst_store::where('subadmin_id', auth()->user()->id)->get();
			//  dd($store);
		}

		if ($_GET) {

			$store_id = $request->store_id;
			//dd($store_id);
			$status_id = $request->status_id;
			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2  = Carbon::parse($request->date_to)->endOfDay();
			//dd($a);
			// $orders = Trn_store_order::where('store_id','=',$store_id)
			// 				->where('status_id','=',$status_id)
			//                 ->whereBetween('created_at',[$a1,$a2])
			//                 ->get();

			if (auth()->user()->user_role_id  == 0) {
				$query = Trn_store_order::where('store_id', '=', $store_id);
			} else {
				$query = Trn_store_order::join('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id')
					->where('mst_stores.subadmin_id', auth()->user()->id);
			}

			if ($store_id != "") {
				$query = $query->where('store_id', '=', $store_id);
			}

			if ($status_id != "") {
				$query = $query->where('status_id', '=', $status_id);
			}

			if (isset($request->date_from) && isset($request->date_to)) {
				$query = $query->whereBetween('created_at', [$a1, $a2]);
			}

			$orders = $query->get();

			return view('subadmin.elements.order.list', compact('orders', 'pageTitle', 'status', 'store', 'status'));
		}



		return view('subadmin.elements.order.list', compact('orders', 'pageTitle', 'status', 'store', 'status'));
	}
	public function listDeliveryboyOrder(Request $request)
	{

		$pageTitle = "Delivery Boy Orders";
		$delivery_boy_orders = Trn_store_order::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
			->join('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

		if (isset($request->delivery_boy_id)) {
			$delivery_boy_orders = $delivery_boy_orders->where('trn_store_orders.delivery_boy_id', $request->delivery_boy_id);
		}

		if (isset($request->payment_type_id)) {
			$delivery_boy_orders = $delivery_boy_orders->where('trn_store_orders.payment_type_id', $request->payment_type_id);
		}


		$datefrom = $request->date_from;
		$dateto = $request->date_to;

		if (isset($request->date_from)) {
			$datefrom = $request->date_from;

			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$delivery_boy_orders = $delivery_boy_orders->whereDate('trn_store_orders.created_at', '>=', $a1);
		}

		if (isset($request->date_to)) {
			$dateto = $request->date_to;
			$a2  = Carbon::parse($request->date_to)->endOfDay();

			$delivery_boy_orders = $delivery_boy_orders->whereDate('trn_store_orders.created_at', '<=', $a2);
		}

		$delivery_boy_orders = $delivery_boy_orders->orderBy('trn_store_orders.order_id', 'DESC')->get();

        //dd($delivery_boy_orders);

		$order_item = Trn_store_order_item::all();
		$store = Mst_Store::all();

		$delivery_boy  = Mst_delivery_boy::all();
		$payment_types = Sys_payment_type::all();




		return view('admin.masters.delivery_boy_order.list', compact('dateto', 'datefrom', 'store', 'pageTitle', 'delivery_boy', 'order_item', 'delivery_boy_orders', 'payment_types'));
	}
	public function AddtoCart(Request $request, Trn_store_order_item $order_item)

	{

		/*
 		$validator = Validator::make($request->all(),
		[
		    'product_varient_id'    => 'required',
			'customer_id'           => 'required',
			'store_id' 				=> 'required',
			'delivery_boy_id'       => 'required',
			'store_commision_percentage'=> 'required',
			'cart_status'           => 'required',
			'quantity'        	    => 'required',
			'unit_price'			=> 'required',
			'total_amount'          => 'required',
			'delivery_status'       => 'required',
			'discount_percentage'	=> 'required',
			'payment_type_id'       => 'required',
			'order_date'            => 'required',
			'pay_datedelivery_date' => 'required',

         ],
		[
		    'product_varient_id.required' => 'Product Varient Id required',
			'customer_id.required'        => 'Customer Id required',
			'store_id.required'	 		  => 'Store required',
			'delivery_boy_id .required'   => 'Delivery Boy required',
			'store_commision_percentage.required' => 'Store Commision required',
			'cart_status.required'        => 'quantity required',
			'quantity.required'           => 'quantity required',
			'unit_price.required'         => 'unit price required',
			'total_amount.required'       => 'Total Amount required',
			'delivery_status.required'    => 'Delivery Status required',
			'discount_percentage.required'=> 'Discount Percentage required',
			'payment_type_id.required'    => 'Payment Type required',
			'order_date.required'         => 'Order Date  required',
			'pay_date.required'        	  => 'Pay Date required',
			'delivery_date.required'      => 'devilvery Date  required'


		]);
      // $this->uploads($request);
        if(!$validator->fails())
		{
     	$data= $request->except('_token');*/

		$product_id = 1;
		$product = Mst_store_product_varient::where('product_id', '=', $product_id)->first();
		$product_price = $product->product_varient_price;
		$store_id = $product->store_id;
		//dd($store_id);
		$quantity = 2;
		$total_amount = $product_price * $quantity;

		//dd($total_amount);
		$delivery_boy = Mst_store_link_delivery_boy::where('store_id', '=', $store_id)->first();



		$order_item->product_varient_id 	= $product->product_varient_id;
		$order_item->customer_id 	        = 1;
		$order_item->store_id 		    = $store_id;
		$order_item->delivery_boy_id    = $delivery_boy->delivery_boy_id;
		$order_item->store_commision_percentage =   3.5;
		$order_item->cart_status        = 1;
		$order_item->quantity 		    = 1;
		$order_item->unit_price   	    = $product->product_varient_price;
		$order_item->total_amount       = $total_amount;
		$order_item->delivery_status    = 1;
		$order_item->discount_percentage = 10;
		$order_item->payment_type_id    = 1;
		$order_item->order_date   	    = Carbon::now();
		$order_item->delivery_date      = Carbon::now()->addDays(7);

		if ($order_item->payment_type_id == 1) {
			$order_item->pay_date	= Carbon::now()->addDays(7);
		} else {
			$order_item->pay_date = Carbon::now();
		}



		$order_item->save();

		return redirect()->back()->with('status', 'Order item added successfully');
		/*}else
	{

		return redirect()->back()->withErrors($validator)->withInput();
	}
*/
	}

	public function Store_settlment(Request $request, Trn_store_order $order, Trn_store_payment_settlment $payment_settlment)
	{

		$order_id = 20;
		$orders = Trn_store_order::where('order_id', '=', $order_id)->first();
		$store_id = $orders->store_id;
		$total_amount = $orders->product_total_amount;
		$stores = Mst_store::where('store_id', '=', $store_id)->first();
		$store_commision_percentage  = $stores->store_commision_percentage;


		$admin_commision = ($store_commision_percentage / 100) * $total_amount;
		$store_commision = $total_amount - $admin_commision;
		$commision_paid = 0;
		$commision_to_be_paid = $store_commision - $commision_paid;

		//dd($commision_to_be_paid);


		$payment_settlment->order_id                 = $order_id;
		$payment_settlment->store_id                 = $store_id;
		$payment_settlment->store_commision_amount = $store_commision;
		$payment_settlment->admin_commision_amount = $admin_commision;
		$payment_settlment->total_amount = $total_amount;
		$payment_settlment->commision_paid = 0;
		$payment_settlment->commision_to_be_paid = $commision_to_be_paid;

		$payment_settlment->save();


		return redirect()->back()->with('status', 'Store payment settlement placed.');
	}

	public function Delivery_boy_settlment(Request $request, Trn_store_order $order, Trn_delivery_boy_payment_settlment $delivery_boy_payment_settlment)
	{

		$order_id = 20;
		$orders = Trn_store_order::where('order_id', '=', $order_id)->first();
		$delivery_boy_id = $orders->delivery_boy_id;
		$store_id = $orders->store_id;
		//dd($delivery_boy_id);
		$total_amount = $orders->product_total_amount;
		$delivery_boy = Mst_delivery_boy::where('delivery_boy_id', '=', $delivery_boy_id)->first();
		$delivery_boy_commision_percentage  = $delivery_boy->delivery_boy_commision;
		//dd($delivery_boy_commision);


		$delivery_boy_commision = ($delivery_boy_commision_percentage / 100) * $total_amount;
		$store_commision = $total_amount - $delivery_boy_commision;
		$commision_paid = 0;
		$commision_to_be_paid = $store_commision - $commision_paid;

		//dd($commision_to_be_paid);


		$delivery_boy_payment_settlment->order_id                 = $order_id;
		$delivery_boy_payment_settlment->store_id                 = $store_id;
		$delivery_boy_payment_settlment->delivery_boy_id          = $delivery_boy_id;
		$delivery_boy_payment_settlment->store_commision_amount = $store_commision;
		$delivery_boy_payment_settlment->delivery_boy_commision_amount = $delivery_boy_commision;
		$delivery_boy_payment_settlment->total_amount = $total_amount;
		$delivery_boy_payment_settlment->commision_paid = 0;
		$delivery_boy_payment_settlment->commision_to_be_paid = $commision_to_be_paid;
		$delivery_boy_payment_settlment->save();


		return redirect()->back()->with('status', 'Delivery boy payment settlement placed.');
	}

	public function list_store_payment_settlment(Request $request)
	{
		$pageTitle = "Store Payment Settlement";
		$store_payment_settlments = Trn_store_payment_settlment::all();
		$store = Mst_store::all();

		if ($_GET) {


			$store_id = $request->store_id;

			$store_payment_settlments = Trn_store_payment_settlment::where('store_id', 'like', '%' . $store_id . '%')->get();

			return view('admin.masters.store_payment.list', compact('store_payment_settlments', 'pageTitle', 'store'));
		}

		return view('admin.masters.store_payment.list', compact('store_payment_settlments', 'pageTitle', 'store'));
	}

	// subadmin payments list


	public function list_subadmin_payment_settlment()
	{
		$pageTitle = "Sub Admin Payment Settlement";
		$subadmins = User::where('user_role_id', 1)->get();
		return view('admin.masters.subadmin_payment.list', compact('subadmins', 'pageTitle'));
	}
	public function list_subadmin_payments(Request $request, $subadmin_name, $subadmin_id)
	{
		$subadmin_id  = Crypt::decryptString($subadmin_id);

		$pageTitle = "Sub Admin Payment Settlement";


		if ($_GET) {

			$year = $request->year;

			$month = $request->month;
			$a1 = Carbon::parse($year . '-' . $month)->startOfMonth();
			$a2  = Carbon::parse($year . '-' . $month)->endOfMonth();

			$payments = Trn_sub_admin_payment_settlment::where('subadmin_id', $subadmin_id)->whereBetween('created_at', [@$a1, @$a2])->get();

			$payments_datas_query = Trn_subadmin_payments_tracker::where('subadmin_id', $subadmin_id);
			if ($request->year && $request->month) {
				$payments_datas_query = $payments_datas_query->whereBetween('date_of_payment', [$a1, $a2]);
			}
			$payments_datas = $payments_datas_query->get();

			$subadmin = Mst_Subadmin_Detail::where('subadmin_id', $subadmin_id)->get();
			return view('admin.masters.subadmin_payment.list_payments', compact('subadmin', 'payments_datas', 'subadmin_id', 'payments', 'pageTitle'));
		}
		$payments_datas = Trn_subadmin_payments_tracker::where('subadmin_id', $subadmin_id)->get();

		$payments = Trn_sub_admin_payment_settlment::where('subadmin_id', $subadmin_id)->get();
		return view('admin.masters.subadmin_payment.list_payments', compact('payments_datas', 'subadmin_id', 'payments', 'pageTitle'));
	}
	public function pay_subadmin_payments(Request $request, Trn_subadmin_payments_tracker $payments, $subadmin_id)
	{
		$subadmin_id  = Crypt::decryptString($subadmin_id);
		$validator = Validator::make(
			$request->all(),
			[

				'commision_paid'   => 'required',

			],
			[
				'commision_paid.required' => 'Amount required',


			]
		);
		if (!$validator->fails()) {
			$data = $request->except('_token');

			$payments->subadmin_id = $subadmin_id;
			$payments->commision_paid = $request->commision_paid;
			$payments->payment_note = $request->payment_note;
			if ($request->date_of_payment) {
				$changeDate = date("Y-m-d", strtotime($request->date_of_payment));
				$payments->date_of_payment = $changeDate;
			} else {
				$payments->date_of_payment = date('Y-m-d');
			}

			$payments->save();

			return redirect()->back()->with('status', 'Payment updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}




	public function list_delivery_payment_settlment(Request $request)
	{
		$pageTitle = "Delivery Boy Payment Settlement";
		$delivery_boy_payments = Trn_delivery_boy_payment_settlment::all();
		$delivery_boy = Mst_delivery_boy::all();


		if ($_GET) {


			$delivery_boy_id = $request->delivery_boy_id;

			$delivery_boy_payments = Trn_delivery_boy_payment_settlment::where('delivery_boy_id', 'like', '%' . $delivery_boy_id . '%')->get();


			return view('admin.masters.delivery_boy_payment.list', compact('delivery_boy_payments', 'pageTitle', 'delivery_boy'));
		}

		return view('admin.masters.delivery_boy_payment.list', compact('delivery_boy_payments', 'pageTitle', 'delivery_boy'));
	}
	public function update_delivery_boy_Commision(Request $request, Trn_delivery_boy_payment_settlment $payment_settlment, $delivery_boy_settlment_id)
	{
		$settlment_id = $request->delivery_boy_settlment_id;
		$payment_settlment = Trn_delivery_boy_payment_settlment::Find($settlment_id);

		$validator = Validator::make(
			$request->all(),
			[

				'commision_paid'   => 'required',

			],
			[
				'commision_paid.required' => 'Commision required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$payment_settlment->commision_paid = $request->commision_paid;

			$store_commision = $payment_settlment->store_commision_amount;
			$amount_paid =  $request->commision_paid;
			$commision_to_be_paid = $store_commision - $amount_paid;
			//dd($commision_to_be_paid);
			$payment_settlment->commision_to_be_paid = $commision_to_be_paid;


			$payment_settlment->update();

			return redirect()->back()->with('status', 'Commision updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function updateCommision(Request $request, Trn_store_payment_settlment $payment_settlment, $settlment_id)
	{
		$settlment_id = $request->settlment_id;
		$payment_settlment = Trn_store_payment_settlment::Find($settlment_id);

		$validator = Validator::make(
			$request->all(),
			[

				'commision_paid'   => 'required',

			],
			[
				'commision_paid.required' => 'Commision required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$payment_settlment->commision_paid = $request->commision_paid;

			$store_commision = $payment_settlment->store_commision_amount;
			$amount_paid =  $request->commision_paid;
			$commision_to_be_paid = $store_commision - $amount_paid;
			//dd($commision_to_be_paid);
			$payment_settlment->commision_to_be_paid = $commision_to_be_paid;


			$payment_settlment->update();

			return redirect()->back()->with('status', 'Commision updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}



	public function list_stores_payment_settlment(Request $request)
	{
		$pageTitle = "Store Payment Settlement";
		$store_payment_settlments = Trn_store_payment_settlment::all();
		$store = Mst_store::all();
		// dd($store);
		if ($_GET) {


			$store_id = $request->store_id;

			$store_payment_settlments = Trn_store_payment_settlment::where('store_id', 'like', '%' . $store_id . '%')->get();

			return view('admin.masters.store_payments.list', compact('store_payment_settlments', 'pageTitle', 'store'));
		}

		return view('admin.masters.store_payments.list', compact('store_payment_settlments', 'pageTitle', 'store'));
	}

	public function list_stores_payments(Request $request, $store_name, $store_id)
	{ //s
		$pageTitle = $store_name . " (store) Payment Settlement";
		$store_id  = Crypt::decryptString($store_id);

		$paidAmount = Trn_store_payments_tracker::where('store_id', $store_id)->sum('commision_paid');
		$paid_details = Trn_store_payments_tracker::where('store_id', $store_id)->orderBy('store_payments_tracker_id', 'DESC')->limit(15)->get();


		$a1 = Carbon::parse($request->date_from)->startOfDay();
		$a2  = Carbon::parse($request->date_to)->endOfDay();

		$payments_datas = Trn_store_payment_settlment::where('store_id', $store_id);

		if (isset($request->date_from)) {
			$payments_datas = $payments_datas->whereDate('trn_store_orders.created_at', '>=', $a1);
		}
		if (isset($request->date_to)) {
			$payments_datas = $payments_datas->whereDate('trn_store_orders.created_at', '<=', $a2);
		}

		$payments_datas = $payments_datas->orderBy('settlment_id', 'DESC')->get();

		$store_payments = Trn_OrderPaymentTransaction::join('trn_store_orders', 'trn_store_orders.order_id', '=', 'trn__order_payment_transactions.order_id')
			->join('trn__order_split_payments', 'trn__order_split_payments.opt_id', '=', 'trn__order_payment_transactions.opt_id');

		if (isset($request->date_from)) {
			$store_payments = $store_payments->whereDate('trn_store_orders.created_at', '>=', $a1);
		}

		if (isset($request->date_to)) {
			$store_payments = $store_payments->whereDate('trn_store_orders.created_at', '<=', $a2);
		}

		$store_payments = $store_payments->where('trn__order_payment_transactions.isFullPaymentToAdmin', 1)
			->where('trn_store_orders.store_id', $store_id)
			->where('trn__order_split_payments.paymentRole', 1)->orderBy('trn__order_payment_transactions.opt_id', 'DESC')
			->get();

		//return view('store.elements.payments.view', compact('store_id', 'payments_datas','store_payments', 'pageTitle'));
		return view('admin.masters.store_payments.list_payments', compact('paid_details', 'paidAmount', 'store_id', 'store_payments', 'pageTitle', 'payments_datas'));


		//$store_payments = Trn_store_payment_settlment::where('store_id', $store_id)->get();

		// if (auth()->user()->user_role_id  == 0) {
		// 	$stores = Mst_store::all();
		// } else {
		// 	$stores = Mst_store::where('subadmin_id', auth()->user()->id)->get();
		// 	//  dd($store);
		// }

		// $payments_datas = \DB::table('trn_store_payments_tracker')->where('store_id', $store_id)->get();

		// if ($_GET) {

		// 	$year = $request->year;
		// 	$month = $request->month;
		// 	$a1 = Carbon::parse($year . '-' . $month)->startOfMonth();
		// 	$a2  = Carbon::parse($year . '-' . $month)->endOfMonth();

		// 	$store_payments = Trn_store_payment_settlment::where('store_id', $store_id)
		// 		->whereBetween('created_at', [@$a1, @$a2])->get();

		// 	$payments = Trn_store_payment_settlment::whereBetween('created_at', [@$a1, @$a2])->get();
		// 	$payments_datas = \DB::table('trn_store_payments_tracker')
		// 		->where('store_id', $store_id)
		// 		->whereBetween('date_of_payment', [@$a1, @$a2])
		// 		->get();
		// 	return view('admin.masters.store_payments.list_payments', compact('store_id', 'store_payments', 'pageTitle', 'stores', 'payments_datas'));
		// }

		// return view('admin.masters.store_payments.list_payments', compact('store_id', 'store_payments', 'pageTitle', 'stores', 'payments_datas'));
	}


	public function pay_stores_payments(Request $request, Trn_store_payments_tracker $payments, $store_id)
	{
		$store_id  = Crypt::decryptString($store_id);
		$validator = Validator::make(
			$request->all(),
			[

				'commision_paid'   => 'required',

			],
			[
				'commision_paid.required' => 'Amount required',


			]
		);
		if (!$validator->fails()) {
			$data = $request->except('_token');

			$payments->store_id = $store_id;
			$payments->commision_paid = $request->commision_paid;
			$payments->payment_note = $request->payment_note;

			if ($request->date_of_payment) {
				$changeDate = date("Y-m-d", strtotime($request->date_of_payment));
				$payments->date_of_payment = $changeDate;
			} else {
				$payments->date_of_payment = date('Y-m-d');
			}

			$payments->save();

			return redirect()->back()->with('status', 'Payment updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}


	// delivery boys payments main admin


	public function list_delivery_boys_payment_settlment(Request $request)
	{
		$pageTitle = "Delivery Boys Payment Settlement";
		$delivery_boy = Mst_delivery_boy::orderBy('delivery_boy_id', 'DESC')->get();
		$delivery_boys = Mst_delivery_boy::orderBy('delivery_boy_id', 'DESC')->get();


		if ($_GET) {


			$delivery_boy_id = $request->delivery_boy_id;
			$query = new  Mst_delivery_boy;
			if (isset($request->delivery_boy_id)) {
				$query = $query->where('delivery_boy_id', $request->delivery_boy_id);
			}

			$delivery_boys = $query->get();

			return view('admin.masters.delivery_boys_payment.list', compact('pageTitle', 'delivery_boys', 'delivery_boy'));
		}

		return view('admin.masters.delivery_boys_payment.list', compact('pageTitle', 'delivery_boys', 'delivery_boy'));
	}



	public function DeliveryBoyPaymentSettlment(Request $request, $delivery_boy_name, $delivery_boy_id)
	{
		$pageTitle = $delivery_boy_name . " (delivery boy) Payment Settlement";
		$delivery_boy_id  = Crypt::decryptString($delivery_boy_id);

		$delivery_boy_payments = Trn_delivery_boy_payment_settlment::where('delivery_boy_id', $delivery_boy_id)->get();
		$delivery_boy = Mst_delivery_boy::all();

		$payments_datas = \DB::table('trn_delivery_boy_payments')->where('delivery_boy_id', $delivery_boy_id)
			->orderBy('delivery_boy_payment_id', 'desc')->limit(6)->get();



		if ($_GET) {
			$year = $request->year;
			$month = $request->month;
			$a1 = Carbon::parse($year . '-' . $month)->startOfMonth();
			$a2  = Carbon::parse($year . '-' . $month)->endOfMonth();

			// echo $a1." ".$a2;die;


			$payments_datas = \DB::table('trn_delivery_boy_payments')
				->where('delivery_boy_id', $delivery_boy_id)
				->whereBetween('date_of_payment', [@$a1, @$a2])
				->orderBy('delivery_boy_payment_id', 'desc')->limit(6)->get();


			$delivery_boy_payments = Trn_delivery_boy_payment_settlment::where('delivery_boy_id', $delivery_boy_id)
				->whereBetween('created_at', [@$a1, @$a2])->get();
			return view('admin.masters.delivery_boys_payment.list_payments', compact('payments_datas', 'delivery_boy_id', 'delivery_boy_payments', 'pageTitle', 'delivery_boy'));
		}

		return view('admin.masters.delivery_boys_payment.list_payments', compact('payments_datas', 'delivery_boy_id', 'delivery_boy_payments', 'pageTitle', 'delivery_boy'));
	}
	public function payDeliveryBoy(Request $request, $delivery_boy_id, Trn_delivery_boy_payment $payments)
	{
		$delivery_boy_id  = Crypt::decryptString($delivery_boy_id);
		$validator = Validator::make(
			$request->all(),
			[

				'commision_paid'   => 'required',

			],
			[
				'commision_paid.required' => 'Amount required',


			]
		);
		if (!$validator->fails()) {
			$data = $request->except('_token');

			$payments->delivery_boy_id = $delivery_boy_id;
			$payments->commision_paid = $request->commision_paid;
			$payments->payment_note = $request->payment_note;
			if ($request->date_of_payment) {
				$changeDate = date("Y-m-d", strtotime($request->date_of_payment));
				$payments->date_of_payment = $changeDate;
			} else {
				$payments->date_of_payment = date('Y-m-d');
			}

			$payments->save();

			return redirect()->back()->with('status', 'Payment updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function Check(Request $request, Trn_store_order $order, Trn_store_payment $payment, Trn_delivery_boy_order $delivery_boy_order)
	{



		$customer_id = 2;
		$customer_checkouts  = Trn_store_order_item::where('customer_id', '=', $customer_id)->get();
		//dd($customer_checkouts);
		$product_price =  $customer_checkouts->implode('unit_price', ',');

		$product_quantity =  $customer_checkouts->implode('quantity', ',');

		$product_id =  $customer_checkouts->implode('product_varient_id', ',');
		$order_item_id =   $customer_checkouts->implode('order_item_id', ',');
		$delivery_boy_id = $customer_checkouts->implode('delivery_boy_id', ',');

		$store_id =    $customer_checkouts->implode('store_id', ',');

		/*
            $validator = Validator::make($request->all(),
        [
            //'product_id'                => 'required',
            //'vendor_id'                 => 'required',
            'product_total_amount'      => 'required',
            'shipping_address'          => 'required',
            'country_id'                =>'required',
            'state_id'                  => 'required',
            'district_id'               => 'required',
            'shipping_landmark'         => 'required',
            'shipping_pincode'          =>'required',


         ],
        [

            //'vendor_id.required'                  => 'Vendor required',
            //'product_id.required'                 => 'Product required',
            'product_total_amount.required'       => 'Total Amount required',
            'shipping_address.required'           => 'Shipping Address required',
            'country_id.required'                 => 'Country required',
            'state_id.required'                   => 'State Required',
            'district_id.required'                => 'City Required',
            'shipping_landmark.required'          => 'Shipping Street required',
            'shipping_pincode.required'           => 'Pincode Required',




        ]);
      // $this->uploads($request);
        if(!$validator->fails())
        {
        $data= $request->except('_token');*/
		// $customer_id = Auth::guard('customer')->user()->customer_id;

		$order->order_number                 = "ORDRYSTR00";
		$order->customer_id                 = 2;
		$order->order_item_id               = $order_item_id;
		$order->product_id                  = $product_id;
		$order->store_id                    = $store_id;
		$order->product_total_amount        = 6000;
		$order->quantity       			    = 5;
		$order->shipping_address            = "test";
		$order->country_id                  = 1;
		$order->state_id                    = 1;
		$order->district_id                 = 1;
		$order->shipping_landmark           = "Beppoor";
		$order->shipping_pincode            = 345678;
		$order->coupon_discount_percentage  = 10;
		$order->delivery_date               = Carbon::now()->addDays(7);
		$order->payment_type_id             = 1;
		$order->status_id                    = 1;


		$order->save();
		$last_insert_id = DB::getPdo()->lastInsertId();
		$order_number = "ORD00" . '' . $last_insert_id;

		DB::table('trn_store_orders')->where('order_id', $last_insert_id)->update(['order_number' => $order_number]);
		$payment->order_item_id = $order_item_id;
		$payment->order_id = $last_insert_id;
		$payment->delivery_boy_id = $delivery_boy_id;
		$payment->customer_id  = 1;
		$payment->payment_type_id = 1;
		$payment->store_id = $store_id;
		$payment->store_commision_percentage = 3.5;

		$commision_amount = (3.5 / 100) * 6000;
		$balance_return_amount = (6000 - $commision_amount);

		$payment->admin_commision_amount  = $commision_amount;
		$payment->return_amount = $balance_return_amount;
		$payment->total_amount = 6000;
		$payment->save();

		$delivery_boy_order->order_item_id = $order_item_id;
		$delivery_boy_order->order_id = $last_insert_id;
		$delivery_boy_order->store_id = $store_id;
		$delivery_boy_order->delivery_boy_id  = $delivery_boy_id;
		$delivery_boy_order->assigned_date_time = Carbon::now();
		$delivery_boy_order->delivery_date_time = Carbon::now()->addDays(7);
		$delivery_boy_order->Expected_date_time = Carbon::now()->addDays(7);
		$delivery_boy_order->delivery_status = 1;
		$delivery_boy_order->payment_type_id  = 1;
		if ($delivery_boy_order->payment_type_id == 1) {
			$delivery_boy_order->payment_status = "pending";
		} else {
			$delivery_boy_order->payment_status = "paid";
		}

		$delivery_boy_order->save();



		return redirect()->back()->with('status', 'Order placed.');
		/*}else
    {
        //return redirect('/')->withErrors($validator->errors());
        return redirect()->back()->withErrors($validator)->withInput();
    }*/
	}



	public function PayStatus(
		Request $request,
		Trn_store_order $order,
		$order_id
	) {


		$order_id = $request->order_id;
		$order = Trn_store_order::Find($order_id);

		$validator = Validator::make(
			$request->all(),
			[

				'payment_status'   => 'required',

			],
			[
				'payment_status.required' => 'Status required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$order->payment_status = $request->payment_status;

			$order->update();

			return redirect()->back()->with('status', 'Status updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}




	public function OrderStatus(
		Request $request,
		Trn_store_order $order,
		$order_id
	) {


		$order_id = $request->order_id;
		$order = Trn_store_order::Find($order_id);

		$validator = Validator::make(
			$request->all(),
			[

				'status_id'   => 'required',

			],
			[
				'status_id.required' => 'Status required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$order->status_id = $request->status_id;

			$order->update();

			return redirect()->back()->with('status', 'Status updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function listRewardType(Request $request)
	{

		$pageTitle = "Customer Reward Transaction Type";
		$reward_transactions = Trn_customer_reward_transaction_type::all();
		/*$order = Trn_store_order::all();
    $customer = Trn_store_customer::all();
    $store = Mst_store::all();
    $transaction_type = Sys_transaction_type::all();

      */
		return view('admin.masters.reward_transaction_type.list', compact('reward_transactions', 'pageTitle'));
	}
	public function createRewardType()
	{

		$pageTitle = "Create Reward Transaction Type";
		$reward_transactions = Trn_customer_reward_transaction_type::all();


		return view('admin.masters.reward_transaction_type.create', compact('pageTitle', 'reward_transactions'));
	}

	public function storeRewardType(Request $request, Trn_customer_reward_transaction_type $reward_type)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'transaction_type'          => 'required',
				'transaction_rule'          => 'required',
				'transaction_point_value'   => 'required',
				'transaction_earning_point' => 'required',
				'min_purchase_amount'		=> 'required',


			],
			[
				'transaction_type.required'          => 'Reward transaction type required',
				'transaction_rule.required'          => 'Reward transaction rule required',
				'transaction_point_value.required'	 => 'Transaction point value required',
				'transaction_earning_point.required' => 'Earning point required',
				'min_purchase_amount.required'		=> 'Minimum purchase amount required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$reward_type->transaction_type 			= $request->transaction_type;
			$reward_type->transaction_rule  		= $request->transaction_rule;
			$reward_type->transaction_point_value  = $request->transaction_point_value;
			$reward_type->transaction_earning_point = $request->transaction_earning_point;
			$reward_type->min_purchase_amount      = $request->min_purchase_amount;
			$reward_type->save();

			return redirect('admin/reward_transaction_type/list')->with('status', 'Reward transaction type added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function editRewardType(Request $request, $id)
	{
		$pageTitle = "Edit Reward Transaction Type";

		$decrId  = Crypt::decryptString($id);
		$reward_type = Trn_customer_reward_transaction_type::Find($decrId);

		return view('admin.masters.reward_transaction_type.edit', compact('pageTitle', 'reward_type'));
	}
	public function updateRewardType(
		Request $request,
		Trn_customer_reward_transaction_type $reward_type,
		$transaction_type_id
	) {


		$reward_type = Trn_customer_reward_transaction_type::Find($transaction_type_id);

		$validator = Validator::make(
			$request->all(),
			[
				'transaction_type'          => 'required',
				'transaction_rule'          => 'required',
				'transaction_point_value'   => 'required',
				'transaction_earning_point' => 'required',
				'min_purchase_amount'      => 'required',


			],
			[
				'transaction_type.required'          => 'Reward transaction type required',
				'transaction_rule.required'          => 'Reward transaction rule required',
				'transaction_point_value.required'	 => 'Transaction point value required',
				'transaction_earning_point.required' => 'Earning point required',
				'min_purchase_amount.required' 		=> 'Minimum purchase amount required',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$reward_type->transaction_type = $request->transaction_type;
			$reward_type->transaction_rule  		= $request->transaction_rule;
			$reward_type->transaction_point_value  = $request->transaction_point_value;
			$reward_type->transaction_earning_point = $request->transaction_earning_point;
			$reward_type->min_purchase_amount      = $request->min_purchase_amount;

			$reward_type->update();

			return redirect('admin/reward_transaction_type/list')->with('status', 'Category updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function destroyRewardType(Request $request, Trn_customer_reward_transaction_type $reward_type)
	{

		$delete = $reward_type->delete();

		return redirect()->back()->with('status', 'Reward type deleted successfully');;
	}

	public function redeemRewardPoint(Request $request)
	{
		$pr = new Trn_points_redeemed;
		$pr->customer_id = $request->customer_id;
		$pr->points = $request->points;
		$pr->discription = $request->discription;
		$pr->isActive = 1;
		$pr->save();
		return redirect()->back()->with('status', 'Reward redeemed successfully');;
	}

	public function listCustomerReward(Request $request)
	{

		$pageTitle = "Customer Reward";
		$customer_rewards = Trn_customer_reward::orderBy('reward_id', 'DESC')->get();
		if ($_GET) {

			$datefrom = $request->date_from;
			$dateto = $request->date_to;


			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2  = Carbon::parse($request->date_to)->endOfDay();
			$customer_first_name = $request->customer_name;
			$query = Trn_customer_reward::join('trn_store_customers', 'trn_store_customers.customer_id', 'trn_customer_rewards.customer_id');

			if (isset($request->date_from) && isset($request->date_to)) {
				$query = $query->whereBetween('trn_customer_rewards.created_at', [$a1, $a2]);
			}

			if (isset($request->customer_name)) {
				$query = $query->orWhereRaw("concat(trn_store_customers.customer_first_name, ' ', trn_store_customers.customer_last_name) like '%$customer_first_name%' ");
			}

			$customer_rewards = $query->orderBy('reward_id', 'DESC')->get();


			return view('admin.masters.customer_reward.list', compact('dateto', 'datefrom', 'customer_rewards', 'pageTitle'));
		}



		return view('admin.masters.customer_reward.list', compact('customer_rewards', 'pageTitle'));
	}


	//configure points

	public function listConfigurePoints(Request $request)
	{

		$pageTitle = "Configure Points";
		$configure_points = Trn_configure_points::first();
		if (isset($configure_points)) {
			$configure_points_id = $configure_points->configure_points_id;
		} else {
			$configure_points_id = 1;
		}

		return view('admin.masters.configure_points.create', compact('configure_points_id', 'configure_points', 'pageTitle'));
	}

	public function createConfigurePoints(Request $request)
	{
		$pageTitle = "Add Configure Points";

		return view('admin.masters.configure_points.create', compact('pageTitle'));
	}


	public function storeConfigurePoints(Request $request, Trn_configure_points $points, $cf_id)
	{
		$validator = Validator::make(
			$request->all(),
			[
				'registraion_points'          => 'required',
				'first_order_points'          => 'required',
				'referal_points'          => 'required',
				'rupee_points'          => 'required',
				'rupee'          => 'required',
				'order_points'          => 'required',
				'order_amount'          => 'required',
				//'points'          => 'required',
			],
			[
				'order_points.required'          => 'Rupee required',
				'order_points.required'          => 'Order points required',
				//  'points.required'          => 'Points required',
				'first_order_points.required'          => 'First order points required',
				'referal_points.required'          => 'Referal required',
				'registraion_points.required'          => 'Registration required',
				'rupee_points.required'          => 'Ruppes to points required',
				'order_amount.required'          => 'Order amount required',
			]
		);

		if (!$validator->fails()) {


			$points = Trn_configure_points::find($cf_id);
			if (isset($points)) {
				// $points->points = $request->points;
				$points->first_order_points = $request->first_order_points;
				$points->referal_points = $request->referal_points;
				$points->registraion_points = $request->registraion_points;
				$points->rupee = $request->rupee;
				$points->rupee_points = $request->rupee_points;
				$points->order_amount = $request->order_amount;
				$points->order_points = $request->order_points;
				$points->redeem_percentage = $request->redeem_percentage;
				$points->max_redeem_amount = $request->max_redeem_amount;
				$points->joiner_points = $request->joiner_points;

				$points->update();
			} else {
				$points = new Trn_configure_points;
				$points->first_order_points = $request->first_order_points;
				$points->referal_points = $request->referal_points;
				$points->registraion_points = $request->registraion_points;
				$points->rupee = $request->rupee;
				$points->rupee_points = $request->rupee_points;
				$points->order_amount = $request->order_amount;
				$points->order_points = $request->order_points;
				$points->redeem_percentage = $request->redeem_percentage;
				$points->max_redeem_amount = $request->max_redeem_amount;
				$points->joiner_points = $request->joiner_points;

				$points->save();
			}
			return redirect('admin/configure_points/list')->with('status', 'Configure points updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function removeConfigurePoints(Request $request, $cp_id, Trn_configure_points $points)
	{
		$points = Trn_configure_points::find($cp_id);

		$points->delete();

		return redirect()->back()->with('status', 'Configure points added successfully.');
	}

	public function statusConfigurePoints(Request $request, $cp_id, Trn_configure_points $points)
	{
		$points = Trn_configure_points::find($cp_id);

		$points->isActive = $request->isActive;

		$points->update();

		return redirect()->back()->with('status', 'Status updated successfully.');
	}
	public function editConfigurePoints(Request $request, $cp_id, Trn_configure_points $points)
	{
		$pageTitle = "Edit Configure Points";
		$configure_point = Trn_configure_points::find($cp_id);

		return view('admin.masters.configure_points.edit', compact('pageTitle', 'configure_point'));
	}
	public function updateConfigurePoints(Request $request, $cp_id, Trn_configure_points $points)
	{
		$points = Trn_configure_points::find($cp_id);
		$validator = Validator::make(
			$request->all(),
			[
				'points'          => 'required',
				'order_amount'          => 'required',
				'valid_from'          => 'required',
			],
			[
				'points.required'          => 'Points required',
				'order_amount.required'          => 'Order amount required',
				'valid_from.required'          => 'Valid from required',
			]
		);

		if (!$validator->fails()) {
			$points->points = $request->points;
			$points->order_amount = $request->order_amount;
			$points->valid_from = $request->valid_from;
			$points->isActive = $request->isActive;
			$points->update();
			return redirect('admin/configure_points/list')->with('status', 'Configure points updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}



	//registration points

	public function listRegistrationPoints(Request $request)
	{

		$pageTitle = "Registration Points Points";
		$registration_points = Trn_registration_point::all();

		return view('admin.masters.registration_points.list', compact('pageTitle', 'registration_points'));
	}


	public function createRegistrationPoints(Request $request, Trn_registration_point $points)
	{
		$validator = Validator::make(
			$request->all(),
			[
				'registration_point'          => 'required',
				'valid_from'          => 'required',
			],
			[
				'registration_point.required'          => 'Points required',
				'valid_from.required'          => 'Valid from required',
			]
		);

		if (!$validator->fails()) {
			//dd($request->all());
			$points->registration_point = $request->registration_point;
			$points->valid_from = $request->valid_from;
			$points->isActive = $request->isActive;
			$points->save();
			return redirect('admin/configure_points/list')->with('status', 'Registration points added successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function removeRegistrationPoint(Request $request, $rp_id, Trn_registration_point $points)
	{
		$points = Trn_registration_point::find($rp_id);

		$points->delete();

		return redirect()->back()->with('status', 'Registration points deleted successfully.');
	}

	public function statusRegistrationPoints(Request $request, $rp_id, Trn_registration_point $points)
	{
		$points = Trn_registration_point::find($rp_id);

		$points->isActive = $request->isActive;

		$points->update();

		return redirect()->back()->with('status', 'Status updated successfully.');
	}

	// first order points

	public function listFirstOrderPoints(Request $request)
	{

		$pageTitle = "First Order Points Points";
		$first_order_points = Trn_first_order_point::all();

		return view('admin.masters.first_order_points.list', compact('pageTitle', 'first_order_points'));
	}

	public function createFirstOrderPoints(Request $request, Trn_first_order_point $points)
	{
		$validator = Validator::make(
			$request->all(),
			[
				'registration_point'          => 'required',
				'valid_from'          => 'required',
			],
			[
				'registration_point.required'          => 'Points required',
				'valid_from.required'          => 'Valid from required',
			]
		);

		if (!$validator->fails()) {
			//dd($request->all());
			$points->registration_point = $request->registration_point;
			$points->valid_from = $request->valid_from;
			$points->isActive = $request->isActive;
			$points->save();
			return redirect('admin/configure_points/list')->with('status', 'First order points added successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function statusFirstOrderPoints(Request $request, $fp_id, Trn_first_order_point $points)
	{
		$points = Trn_first_order_point::find($fp_id);

		$points->isActive = $request->isActive;

		$points->update();

		return redirect()->back()->with('status', 'Status updated successfully.');
	}

	public function removeFirstOrderPoint(Request $request, $rp_id, Trn_first_order_point $points)
	{
		$points = Trn_first_order_point::find($rp_id);

		$points->delete();

		return redirect()->back()->with('status', 'points deleted successfully.');
	}

	//referal points

	public function listReferalPoints(Request $request)
	{

		$pageTitle = "Referal Points Points";
		$referal_points = Trn_referal_point::all();

		return view('admin.masters.referal_points.list', compact('pageTitle', 'referal_points'));
	}

	public function createReferalPoints(Request $request, Trn_referal_point $points)
	{
		$validator = Validator::make(
			$request->all(),
			[
				'point'          => 'required',
				'valid_from'          => 'required',
			],
			[
				'point.required'          => 'Points required',
				'valid_from.required'          => 'Valid from required',
			]
		);

		if (!$validator->fails()) {
			//dd($request->all());
			$points->point = $request->point;
			$points->valid_from = $request->valid_from;
			$points->isActive = $request->isActive;
			$points->save();
			return redirect('admin/configure_points/list')->with('status', 'First order points added successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function statusReferalPoints(Request $request, $rp_id, Trn_referal_point $points)
	{
		$points = Trn_referal_point::find($rp_id);

		$points->isActive = $request->isActive;

		$points->update();

		return redirect()->back()->with('status', 'Status updated successfully.');
	}

	public function removeReferalPoints(Request $request, $rp_id, Trn_referal_point $points)
	{
		$points = Trn_referal_point::find($rp_id);

		$points->delete();

		return redirect()->back()->with('status', 'points deleted successfully.');
	}

	//point to rupee

	public function createPointsToRupee(Request $request, Trn_points_to_rupee $points)
	{
		$validator = Validator::make(
			$request->all(),
			[
				'point'          => 'required',
				'rupee'          => 'required',
			],
			[
				'point.required'          => 'Points required',
				'rupee.required'          => 'Rupee  required',
			]
		);

		if (!$validator->fails()) {
			//dd($request->all());
			$points->point = $request->point;
			$points->rupee = $request->rupee;
			$points->isActive = $request->isActive;
			$points->save();
			return redirect('admin/configure_points/list')->with('status', 'points to rupee added successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}



	public function statusRupeePoints(Request $request, $rp_id, Trn_points_to_rupee $points)
	{
		$points = Trn_points_to_rupee::find($rp_id);

		$points->isActive = $request->isActive;

		$points->update();

		return redirect()->back()->with('status', 'Status updated successfully.');
	}
	public function RemoveRupeePoints(Request $request, $rp_id, Trn_points_to_rupee $points)
	{
		$points = Trn_points_to_rupee::find($rp_id);

		$points->delete();

		return redirect()->back()->with('status', 'Deleted successfully.');
	}

	// points to be redeemed in a single purchase

	public function createPointsRedeemed(Request $request, Trn_points_redeemed $points)
	{
		$validator = Validator::make(
			$request->all(),
			[
				'point_in_percentage'          => 'required',
			],
			[
				'point_in_percentage.required'          => 'Points required',
			]
		);

		if (!$validator->fails()) {
			//dd($request->all());
			$points->point_in_percentage = $request->point_in_percentage;
			$points->isActive = $request->isActive;
			$points->save();
			return redirect('admin/configure_points/list')->with('status', 'Points added successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function RemovePointsRedeemed(Request $request, $rp_id, Trn_points_redeemed $points)
	{
		$points = Trn_points_redeemed::find($rp_id);

		$points->delete();

		return redirect()->back()->with('status', 'points deleted successfully.');
	}


	public function editCustomerReward(Request $request, $id)
	{
		$pageTitle = "Edit Customer Reward";

		$decrId  = Crypt::decryptString($id);
		$customer_reward = Trn_customer_reward::Find($decrId);
		$transaction_types = Trn_customer_reward_transaction_type::all();

		return view('admin.masters.customer_reward.edit', compact('pageTitle', 'customer_reward', 'transaction_types'));
	}
	public function updateCustomerReward(
		Request $request,
		Trn_customer_reward $customer_reward,
		$reward_id
	) {


		$customer_reward = Trn_customer_reward::Find($reward_id);
		//dd($customer_reward);

		$validator = Validator::make(
			$request->all(),
			[
				'transaction_type_id'          	=> 'required',
				'reward_points_earned'          => 'required',
				'reward_approved_date'   		=> 'required',
				'reward_point_expire_date' 		=> 'required',


			],
			[
				'transaction_type_id.required'       => 'Transaction type required',
				'reward_points_earned.required'      => 'Reward point required',
				'reward_approved_date.required'	     => 'Approved date required',
				'reward_point_expire_date.required'  => 'Expiry date required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$customer_reward->transaction_type_id   = $request->transaction_type_id;
			$customer_reward->reward_points_earned	= $request->reward_points_earned;
			$customer_reward->reward_approved_date  = $request->reward_approved_date;
			$customer_reward->reward_point_expire_date = $request->reward_point_expire_date;

			$customer_reward->update();
			return redirect('admin/customer_reward/list')->with('status', 'Customer reward updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}



	//list  store under subadmin
	public function listStoreSubadmin(Request $request)
	{

		$pageTitle = "Stores";
		$subadmin_id = Auth()->user()->id;
		$stores = Mst_store_link_subadmin::where('subadmin_id', '=', $subadmin_id)->get();
		$subadmins = User::where('user_role_id', '!=', 0)->get();

		$countries = Country::all();
		$agencies = Mst_store_agencies::all();

		if ($_GET) {

			$country_id = $request->store_country_id;
			$state_id = $request->store_state_id;
			$district_id = $request->store_district_id;
			$store_town_id = $request->store_town_id;

			//$town_id = $request->//;
			$store_name  = $request->store_name;
			$email = $request->store_email_address;
			$store_contact_person_phone_number = $request->store_contact_person_phone_number;
			$store_account_status = $request->store_account_status;

			$states = State::where('country_id', $request->store_country_id)->get();
			$districts = District::where('state_id', $request->store_state_id)->get();
			$town = Town::where('district_id', $request->store_district_id)->get();

			$query = Mst_store::select("*");

			if ($country_id) {
				$query = $query->where('store_country_id', 'like', '%' . $country_id . '%');
			}

			if ($state_id) {
				$query = $query->where('store_state_id', 'like', '%' . $state_id . '%');
			}

			if ($store_town_id) {
				$query = $query->where('town_id', 'like', '%' . $store_town_id . '%');
			}

			if ($district_id) {
				$query = $query->where('store_district_id', 'like', '%' . $district_id . '%');
			}

			if ($store_name) {
				//echo $store_name;die;
				$query = $query->where('store_name', 'like', '%' . $store_name . '%');
			}

			if ($email) {
				$query = $query->where('email', 'like', '%' . $email . '%');
			}

			if ($store_contact_person_phone_number) {
				$query = $query->where('store_contact_person_phone_number', 'like', '%' . $store_contact_person_phone_number . '%');
			}

			if ($store_account_status) {
				$query = $query->where('store_account_status', 'like', '%' . $store_account_status . '%');
			}

			dd($query->get());

			return view('admin.masters.subadmin_store.list', compact('subadmins', 'town', 'districts', 'states', 'stores', 'pageTitle', 'countries', 'agencies'));
		}

		return view('admin.masters.subadmin_store.list', compact('subadmins', 'stores', 'pageTitle', 'countries', 'agencies'));
	}
	public function viewStoreSubadmin(Request $request, $id)
	{

		$pageTitle = "View Store";

		$decrId  = Crypt::decryptString($id);
		$store_link = Mst_store_link_subadmin::Find($decrId);
		$store_id = $store_link->store_id;
		$store = Mst_store::where('store_id', '=', $store_id)->first();
		$countries = Country::all();
		$store_documents  = Mst_store_documents::where('store_id', '=', $store_id)->get();
		$store_products  = Mst_store_product::where('store_id', '=', $store_id)->get();
		$store_images = Mst_store_images::where('store_id', '=', $store_id)->get();
		$agencies = Mst_store_link_agency::where('store_id', '=', $store_id)->get();
		$delivery_boys = Mst_store_link_delivery_boy::where('store_id', '=', $store_id)->get();

		return view('admin.masters.subadmin_store.view', compact('store', 'pageTitle', 'countries', 'store_images', 'store_documents', 'agencies', 'delivery_boys', 'store_products'));
	}

	public function editStoreSubadmin(Request $request, $id)
	{

		$pageTitle = "Edit Store";
		$decrId  = Crypt::decryptString($id);
		$store_link = Mst_store_link_subadmin::Find($decrId);
		$store_id = $store_link->store_id;
		$store = Mst_store::where('store_id', '=', $store_id)->first();
		$countries = Country::all();
		$store_documents  = Mst_store_documents::where('store_id', '=', $store_id)->get();
		$store_images = Mst_store_images::where('store_id', '=', $store_id)->get();
		$agencies = Mst_store_link_agency::where('store_id', '=', $store_id)->get();
		$delivery_boys = Mst_store_link_delivery_boy::where('store_id', '=', $store_id)->get();

		return view('admin.masters.subadmin_store.edit', compact('store', 'pageTitle', 'countries', 'store_images', 'store_documents', 'agencies', 'delivery_boys'));
	}

	public function updateStoreSubadmin(Request $request, Mst_store $store, $store_id)
	{

		$store_Id = $request->store_id;

		$store = Mst_store::Find($store_Id);

		$password = $store->password;
		$newpassword = $request->password;


		$validator = Validator::make(
			$request->all(),
			[
				'store_name'    => 'required|unique:mst_stores,store_name,' . $store_id . ',store_id',
				'store_contact_person_name'        => 'required',
				'store_contact_person_phone_number' => 'required',
				'store_website_link'        	   => 'required',
				'store_pincode'				       => 'required',
				'store_primary_address'            => 'required',
				'email'        	   => 'required',
				'store_country_id'			       => 'required',
				'store_state_id'       		       => 'required',
				'store_district_id'                => 'required',
				'store_username'   => 'required|unique:mst_stores,store_username,' . $store_id . ',store_id',
				'password'       			   => 'sometimes|same:password_confirmation',


			],
			[
				'store_name.required'         				 => 'Store name required',
				'store_contact_person_name.required'     	 => 'Contact person name required',
				'store_contact_person_phone_number.required' => 'Contact person number required',

				'store_website_link.required'         		 => 'website link required',
				'store_pincode.required'        			 => 'Pincode required',
				'store_primary_address.required'             => 'Primary address required',
				'email.required'               => 'Email required',
				'store_country_id.required'         		 => 'Country required',
				'store_state_id.required'        			 => 'State required',
				'store_district_id.required'        		 => 'District  required',
				'store_username.required'        			 => 'Username required',
				'password.required'					 => 'Password required',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$store->store_name 					= $request->store_name;
			$store->store_name_slug   		= Str::of($request->store_name)->slug('-');
			$store->store_contact_person_name   = $request->store_contact_person_name;
			$store->store_contact_person_phone_number = $request->store_contact_person_phone_number;
			$store->store_contact_number_2       = $request->store_contact_number_2;
			$store->store_website_link 		     = $request->store_website_link;
			$store->store_pincode   	     = $request->store_pincode;
			$store->store_primary_address        = $request->store_primary_address;
			$store->email          = $request->email;
			$store->store_country_id             = $request->store_country_id;
			$store->store_state_id  		     = $request->store_state_id;
			$store->store_district_id   	     = $request->store_district_id;
			$store->store_username               = $request->store_username;

			if ($newpassword == '') {
				$store->password = $password;
			} else {
				$store->password = Hash::make($request->password);
			}
			$store->update();

			$date = Carbon::now();

			if ($request->hasFile('store_document_other_file')) {
				$store_documents = $request->file('store_document_other_file');
				// dd($product_image);
				foreach ($store_documents as $document) {
					$filename = time() . '.' . $document->getClientOriginalExtension();
					// dd($filename);
					$destination_path = 'assets/uploads/store_document/files';

					$store_doc = File::put($document->getRealPath());
					dd($store_doc);
					$store_doc->save($destination_path . '/' . $filename);

					$date = Carbon::now();

					$data1 = [
						[
							'store_id'      => $store_Id,
							'store_document_other_file' => $filename,
							'created_at'         => $date,
							'updated_at'         => $date,
						],
					];

					Mst_store_documents::insert($data1);
				}
			}

			// multiple image upload

			if ($request->hasFile('store_image')) {
				$store_image = $request->file('store_image');
				// dd($product_image);
				foreach ($store_image as $image) {
					$filename = time() . '.' . $image->getClientOriginalExtension();
					// dd($filename);
					$destination_path = 'assets/uploads/store_images/images';

					$store_img = Image::make($image->getRealPath());
					$store_img->save($destination_path . '/' . $filename, 80);



					$data2 = [
						[
							'store_image'      => $filename,
							'store_id' 			=> $store_Id,
							'created_at'         => $date,
							'updated_at'         => $date,
						],
					];

					Mst_store_images::insert($data2);
				}
			}

			return redirect('admin/store/subadmin/list')->with('status', 'Store updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function assignSubadminAgency(Request $request, $id)
	{

		$pageTitle = "Assign Agency";
		$decrId  = Crypt::decryptString($id);
		$store_link = Mst_store_link_subadmin::Find($decrId);
		//dd($store_link);
		$store_id = $store_link->store_id;
		$store = Mst_store::where('store_id', '=', $store_id)->first();

		$agencies = Mst_store_agencies::all();

		return view('admin.masters.subadmin_store.assign_agency', compact('store', 'pageTitle', 'agencies'));
	}

	public function addSubadminAgency(Request $request, Mst_store_link_agency $link_agency)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'agency_id'    					=> 'required',

			],
			[
				'agency_id.required'       => 'Agency required',



			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$date =  Carbon::now();
			$values = $request->agency_id;
			//dd($values);
			foreach ($values as $value) {

				$data = [
					[
						'agency_id' => $value,
						'store_id' => $request->store_id,
						'created_at' => $date,
						'updated_at' => $date,


					],
				];

				Mst_store_link_agency::insert($data);
			}

			return redirect('admin/store/subadmin/list')->with('status', 'Agency assigned successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function assignDelivery_boy_subadmin(Request $request, $id)
	{

		$pageTitle = "Assign Delivery boy";
		$decrId  = Crypt::decryptString($id);
		$store_link = Mst_store_link_subadmin::Find($decrId);
		//dd($store_link);
		$store_id = $store_link->store_id;
		$store = Mst_store::where('store_id', '=', $store_id)->first();

		$delivery_boys = Mst_delivery_boy::all();

		return view('admin.masters.subadmin_store.assign_delivery_boy', compact('store', 'pageTitle', 'delivery_boys'));
	}

	public function addStoreDelivery_boy_subadmin(Request $request, Mst_store_link_delivery_boy $link_delivery_boy)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'delivery_boy_id'    					=> 'required',

			],
			[
				'delivery_boy_id.required'       => 'Delivery boy required',



			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$date =  Carbon::now();
			$values = $request->delivery_boy_id;
			//dd($values);
			foreach ($values as $value) {

				$data = [
					[
						'delivery_boy_id' => $value,
						'store_id' => $request->store_id,
						'created_at' => $date,
						'updated_at' => $date,


					],
				];

				Mst_store_link_delivery_boy::insert($data);
			}

			return redirect('admin/store/subadmin/list')->with('status', 'Delivery boy assigned successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function destroyStoreSubadmin(Request $request, Mst_store_link_subadmin $link_subadmin)
	{

		$delete = $link_subadmin->delete();

		return redirect('admin/store/subadmin/list')->with('status', 'Store deleted successfully');
	}

	public function statusStoreSubadmin(
		Request $request,
		Mst_store $store,
		$store_link_subadmin_id
	) {

		$store_link = Mst_store_link_subadmin::Find($store_link_subadmin_id);

		$store_id = $store_link->store_id;
		$store = Mst_store::Find($store_id);
		//dd($store);
		$status = $store->store_account_status;

		if ($status == 0) {
			$store->store_account_status  = 1;
		} else {

			$store->store_account_status  = 0;
		}
		$store->update();

		return redirect()->back()->with('status', 'Store status changed successfully');
	}

	public function listProduct(Request $request)
	{
		$pageTitle = "Products";

		$products = Mst_store_product::all();
		//dd($products);
		$store = Mst_store::all();
		$categories = Mst_categories::where('category_status', '=', 1)->get();

		if ($_GET) {


			$product_name = $request->product_name;
			$product_code = $request->product_code;
			$stock_status =  $request->stock_status;
			$product_status =  $request->product_status;
			$store_id = $request->store_id;
			$store_id = $request->store_id;


			$a[] = Carbon::parse($request->From_date)->startOfDay();
			$a[] = Carbon::parse($request->To_date)->endOfDay();
			$b[] = $request->start_price;
			$b[] = $request->end_price;

			$products = Mst_store_product::where('product_name', 'like', '%' . $product_name . '%')
				->where('product_code', 'like', '%' . $product_code . '%')
				->where('stock_status', 'like', '%' . $stock_status . '%')
				->where('product_status', 'like', '%' . $product_status . '%')
				->where('store_id', 'like', '%' . $store_id . '%')
				->whereBetween('created_at', [$a, $a])
				->whereBetween('product_price', [$b, $b])
				->get();

			return view('admin.masters.product.list', compact('products', 'pageTitle', 'store', 'categories'));
		}

		return view('admin.masters.product.list', compact('products', 'pageTitle', 'store', 'categories'));
	}


	public function createProduct()
	{
		$pageTitle = "Create Products";

		$products = Mst_store_product::all();
		$attr_groups = Mst_attribute_group::all();

		$business_types = Mst_business_types::where('business_type_status', '=', 1)->get();
		$store = Mst_store::all();

		return view('admin.masters.product.create', compact('products', 'pageTitle', 'attr_groups', 'store', 'business_types'));
	}

	public function GetAttr_Value(Request $request)
	{
		$grp_id = $request->attr_group_id;
		// dd($grp_id);
		$attr_values  = Mst_attribute_value::where("attribute_group_id", '=', $grp_id)
			->pluck("group_value", "attr_value_id");


		return response()->json($attr_values);
	}
	public function GetCategory(Request $request)
	{
		$business_id = $request->business_type_id;

		$category  = Mst_categories::where("business_type_id", '=', $business_id)->where("category_status", 1)->pluck("category_name", "category_id");
		return response()->json($category);
	}
	public function GetSubCategory(Request $request)
	{
		$category_id = $request->category_id;

		$subcategory  = Mst_SubCategory::where("category_id", '=', $category_id)->pluck("sub_category_name", "sub_category_id");
		return response()->json($subcategory);
	}



	public function storeProduct(Request $request, Mst_store_product $product, Mst_store_product_varient $varient_product, Mst_product_image $product_img)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'product_name'          => 'required|unique:mst_store_products',
				'product_cat_id'        => 'required',
				'business_type_id'     => 'required',
				// 'product_description'   => 'required',
				'product_specification' => 'required',


			],
			[

				'product_name.required'             => 'Product name required',
				'business_type_id.required'        => 'Business type required',
				'product_cat_id.required'           => 'Product category required',
				'product_description.required'      => 'Product description required',
				'product_specification.required'    => 'Product specification required',

			]
		);

		if (!$validator->fails()) {




			$product->product_code          = "PRDCT00";
			$product->product_name           = $request->product_name;
			$product->product_name_slug      = Str::of($request->product_name)->slug('-');
			$product->product_cat_id         = $request->product_cat_id;
			$product->business_type_id       = $request->business_type_id;
			$product->product_description    = $request->product_description;
			$product->product_specification  = $request->product_specification;
			$product->store_id               = $request->store_id;
			$product->attr_group_id          = $request->attr_group_id;
			$product->attr_value_id          = $request->attr_value_id;
			$product->product_offer_from_date = $request->product_offer_from_date;
			$product->product_offer_to_date  = $request->product_offer_to_date;
			$product->product_price          = $request->product_price;
			$product->product_price_offer   = $request->product_price_offer;
			$product->product_delivery_info  = $request->product_delivery_info;
			$product->product_shipping_info  = $request->product_shipping_info;
			$product->stock_count            = $request->stock_count;
			if ($request->stock_count == 0) {
				$product->stock_status = 0;
			} else {
				$product->stock_status = 1;
			}

			$product->product_commision_rate     = 3.5;
			$product->product_status         = 0;



			$product->save();
			$getProductId = DB::getPdo()->lastInsertId();
			$product_code = "PRDCT00" . '' . $getProductId;

			DB::table('mst_store_products')->where('product_id', $getProductId)->update(['product_code' => $product_code]);


			$varient_product->product_varient_price           = $request->product_price;
			$varient_product->product_varient_offer_price    = $request->product_price_offer;
			$varient_product->product_varient_offer_from_date = $request->product_offer_from_date;
			$varient_product->product_varient_offer_to_date   = $request->product_offer_to_date;

			$varient_product->product_id                      = $getProductId;
			$varient_product->store_id                        = $request->store_id;
			$varient_product->attr_group_id                 = $request->attr_group_id;
			$varient_product->attr_value_id                 = $request->attr_value_id;



			$varient_product->save();


			$productVarientId = DB::getPdo()->lastInsertId();

			if ($request->hasFile('product_image')) {
				$photo = $request->file('product_image');

				$modelImageName = time() . '.' . $photo['0']->getClientOriginalExtension();
				$i = '0';
				foreach ($photo as $photos) {
					$i++;
					$mulImages = $i . $storyimagename = time() . '.' . $photos->getClientOriginalExtension();
					$destinationPath = 'assets/uploads/products/base_product/feature_image';
					$thumb_img = Image::make($photos->getRealPath());
					$thumb_img->save($destinationPath . '/' . $i . $storyimagename, 80);

					$resImgId = Mst_product_image::insertGetId([
						'product_varient_id'    =>  $productVarientId,
						'product_id' => $getProductId,
						'product_image' =>  $mulImages,
						'image_flag' => 1,
						'created_at'  =>  \Carbon\Carbon::now(),
						'updated_at'  =>  \Carbon\Carbon::now(),
					]);
				}
				$flgCheck = Mst_product_image::where('product_varient_id', $productVarientId)->where('product_image', '=', '0')->get();

				if ($flgCheck->isEmpty()) {
					Mst_product_image::where('product_image_id', '=', $resImgId)->update([
						'image_flag' => 0 //base image
					]);
				}
			}



			/*
                 $date = Carbon::now();

                if($request->hasFile('product_image'))
                {
                    $product_image = $request->file('product_image');
                    //dd($product_image);
                    foreach($product_image as $image)
                    {
                        $filename[] = time().'.'.$image->getClientOriginalExtension();
                        dd($filename);
                        $destination_path = 'assets/uploads/products/base_product/feature_image';

                        $product_img = Image::make($image->getRealPath());
                        $product_img->save($destination_path.'/'.$filename,80);

                            $data1= [[
                                'product_image'      => $filename,
                                'product_varient_id' => $varient_id,
                                'image_flag'         => 1,
                                'created_at'         => $date,
                                'updated_at'         => $date,
                                    ],
                                  ];

                             Mst_product_image::insert($data1);

                    }
                }
                */
			/* $product_img->product_image = $filename1;
                $product_img->product_varient_id = $varient_id;
                $product_img->image_flag = 0;
                $product_img->save();*/


			return redirect('admin/product/list')->with('status', 'Product added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}


	public function viewProduct(Request $request, $id)
	{
		$pageTitle = "View Product";

		$product = Mst_store_product::where('product_name_slug', '=', $id)->first();
		$product_id = $product->product_id;

		$varient_product = Mst_store_product_varient::where('product_id', '=', $product_id)->first();

		$product_varient_id = $varient_product->product_varient_id;

		$attr_groups = Mst_attribute_group::all();
		$product_images = Mst_product_image::where('product_varient_id', '=', $product_varient_id)->get();

		$store = Mst_store::all();
		$categories = Mst_categories::where([['category_status', '=', '1'], ['parent_id', '==', '0'],])->whereIn('category_id', ['1', '4', '9'])->get();


		return view('admin.masters.product.view', compact('product', 'pageTitle', 'attr_groups', 'store', 'categories', 'product_images'));
	}
	public function editProduct(Request $request, $id)
	{
		$pageTitle = "Edit Product";

		$product = Mst_store_product::where('product_name_slug', '=', $id)->first();
		$product_id = $product->product_id;

		$varient_product = Mst_store_product_varient::where('product_id', '=', $product_id)->first();

		$product_varient_id = $varient_product->product_varient_id;
		$business_types = Mst_business_types::all();
		$attr_groups = Mst_attribute_group::all();
		$product_images = Mst_product_image::where('product_varient_id', '=', $product_varient_id)->get();
		$attr_varient_products = Mst_store_product_varient::where('product_id', '=', $product_id)->get();

		$store = Mst_store::all();

		return view('admin.masters.product.edit', compact('product', 'pageTitle', 'attr_groups', 'store', 'product_images', 'business_types', 'attr_varient_products'));
	}

	public function updateProduct(Request $request, Mst_store_product $product, $product_id, Mst_store_product_varient $varient_product)
	{

		$product_id = $request->product_id;
		$product = Mst_store_product::Find($product_id);
		$varient_product = Mst_store_product_varient::where('product_id', '=', $product_id)->first();
		//dd($product_id);
		$validator = Validator::make(
			$request->all(),
			[
				'product_name'          => 'required|unique:mst_store_products,product_name,' . $product_id . ',product_id',
				'product_cat_id'        => 'required',
				'business_type_id'     => 'required',
				//  'product_description'   => 'required',
				'product_specification' => 'required',


			],
			[

				'product_name.required'             => 'Product name required',
				'business_type_id.required'        => 'Business type required',
				'product_cat_id.required'           => 'Product category required',
				'product_description.required'      => 'Product description required',
				'product_specification.required'    => 'Product specification required',

			]
		);

		if (!$validator->fails()) {


			$product->product_code          = "PRDCT00";
			$product->product_name           = $request->product_name;
			$product->product_name_slug      = Str::of($request->product_name)->slug('-');
			$product->product_cat_id         = $request->product_cat_id;
			$product->business_type_id      = $request->business_type_id;
			$product->product_description    = $request->product_description;
			$product->product_specification  = $request->product_specification;
			$product->store_id               = $request->store_id;
			$product->product_offer_from_date = $request->product_offer_from_date;
			$product->product_offer_to_date  = $request->product_offer_to_date;
			$product->product_price          = $request->product_price;
			$product->product_price_offer   = $request->product_price_offer;
			$product->product_delivery_info  = $request->product_delivery_info;
			$product->product_shipping_info  = $request->product_shipping_info;
			$product->stock_count            = $request->stock_count;
			if ($request->stock_count == 0) {
				$product->stock_status = 0;
			} else {
				$product->stock_status = 1;
			}



			if ($request->hasFile('product_base_image')) {
				$product_image = $request->file('product_base_image');
				//dd($product_base_image);


				$filename = time() . '.' . $product_image->getClientOriginalExtension();

				$destination_path = 'assets/uploads/products/base_product/base_image';

				$product_img = Image::make($product_image->getRealPath());
				$product_img->save($destination_path . '/' . $filename, 80);
				$product->product_base_image = $filename;

				//dd($filename);
			}
			$product->update();


			$varient_product->product_varient_price           = $request->product_price;
			$varient_product->product_varient_offer_price    = $request->product_price_offer;
			$varient_product->product_varient_offer_from_date = $request->product_offer_from_date;
			$varient_product->product_varient_offer_to_date   = $request->product_offer_to_date;


			$varient_product->attr_group_id                 = $request->attr_group_id;
			$varient_product->attr_value_id                 = $request->attr_value_id;

			if ($request->hasFile('product_base_image')) {
				$product_image = $request->file('product_base_image');
				//dd($product_base_image);


				$filename1 = time() . '.' . $product_image->getClientOriginalExtension();

				$destination_path = 'assets/uploads/products/varient_product/base_image';

				$product_img = Image::make($product_image->getRealPath());
				$product_img->save($destination_path . '/' . $filename1, 80);
				$varient_product->product_varient_base_image  = $filename1;
			}

			$varient_product->update();



			return redirect('admin/product/list')->with('status', 'Product updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function destroyProduct(Request $request, Mst_store_product $product)
	{

		$product->delete();

		return redirect('admin/product/list')->with('status', 'Product deleted successfully');
	}

	public function statusProduct(Request $request, Mst_store_product $product, $product_id)
	{

		$pro_id = $request->product_id;
		$product = Mst_store_product::Find($pro_id);
		$status = $product->product_status;

		if ($status == 0) {
			$product->product_status  = 1;
		} else {

			$product->product_status  = 0;
		}
		$product->update();

		return redirect()->back()->with('status', 'Product status changed successfully');
	}

	public function stockUpdate(
		Request $request,
		Mst_store_product $product,
		$product_id
	) {


		$product_id = $request->product_id;
		$product = Mst_store_product::Find($product_id);

		$validator = Validator::make(
			$request->all(),
			[

				'stock_count'   => 'required',

			],
			[
				'stock_count.required' => 'Status required',


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');


			$product->stock_count = $request->stock_count;
			if ($request->stock_count == 0) {
				$product->stock_status = 0;
			} else {
				$product->stock_status = 1;
			}

			$product->update();

			return redirect()->back()->with('status', 'Stock updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function listAttributeGroup()
	{

		$pageHeading = "attribute_group";
		$pageTitle = "List Attribute Group";
		$attributegroups = Mst_attribute_group::orderBy('attr_group_id', 'DESC')->get();

		return view('admin.masters.attribute_group.list', compact('attributegroups', 'pageTitle', 'pageHeading'));
	}



	public function storeAttribute(Request $request, Mst_attribute_group $attr_group)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'group_name'                 => 'required',


			],
			[
				'group_name.required'                 => 'Group name required',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');


			$attr_group->group_name      = $request->group_name;

			$attr_group->save();
			return redirect()->back()->with('status', 'Attribute added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function editAttributeGroup(Request $request, $id)
	{

		$decryptId = Crypt::decryptString($id);


		$pageTitle = "Edit Attribute Group";
		$attributegroup = Mst_attribute_group::Find($decryptId);

		return view('admin.masters.attribute_group.edit', compact('attributegroup', 'pageTitle'));
	}

	public function updateAtrGroup(
		Request $request,
		Mst_attribute_group $attributegroup,
		$attr_group_id
	) {

		$GrpId = $request->attr_group_id;
		$attributegroup = Mst_attribute_group::Find($GrpId);

		$validator = Validator::make(
			$request->all(),
			[
				'group_name'   => 'required',

			],
			[
				'group_name.required'        => 'Group name required',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');

			$attributegroup->group_name  = $request->group_name;


			$attributegroup->update();

			return redirect('admin/attribute_group/list')->with('status', 'Attribute group updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function listAttr_Value()
	{

		$pageTitle = "List Attribute Value";
		$attributevalues = Mst_attribute_value::orderBy('attr_value_id', 'DESC')->get();
		$attributegroups = Mst_attribute_group::orderBy('attr_group_id', 'DESC')->get();


		return view('admin.masters.attribute_value.list', compact('attributevalues', 'pageTitle', 'attributegroups'));
	}

	public function createAttr_Value(Request $request, Mst_attribute_value $attribute_value)
	{


		$pageTitle = "Create Attribute Value";
		$attributevalues = Mst_attribute_value::all();
		$attributegroups = Mst_attribute_group::all();

		//$attr_grps    = $request->$attribute_group_id;
		return view('admin.masters.attribute_value.create', compact('attributevalues', 'pageTitle', 'attributegroups'));
	}

	public function storeAttr_Value(Request $request, Mst_attribute_value $attribute_value)
	{

		$validator = Validator::make(
			$request->all(),
			[
				'group_value.*'       => 'required',
				'attribute_group_id' => 'required',

			],
			[
				'group_value.*.required'          => 'Attribute value required',
				'attribute_group_id.required|nimeric' => 'Select group Of attribute'


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');

			$values = $request->group_value;

			//dd($values);
			$attr_grp_value = $request->attribute_group_id;
			$Hexvalue = $request->Hexvalue;
			$group_value = $request->group_value;
			$status = 1;
			$date =  Carbon::now();
			// dd($date);
			if ($attr_grp_value == 2) {
				if ($Hexvalue) {
					$count = count($Hexvalue);
					//dd($count);

					//$countvalue = 2;
					for ($i = 0; $i < $count; $i++) {

						$attribute_value = new Mst_attribute_value;
						$attribute_value->attribute_group_id = $attr_grp_value;
						$attribute_value->attr_value_status = $status;
						$attribute_value->group_value = $request->group_value[$i];
						$attribute_value->Hexvalue = $Hexvalue[$i];
						$attribute_value->created_at = $date;
						$attribute_value->updated_at = $date;

						$attribute_value->save();
					}
				}
			} else {

				foreach ($values as $value) {

					$data = [
						[
							'group_value' => $value,
							'attribute_group_id' => $request->attribute_group_id,
							'attr_value_status' => 1,
							'created_at' => $date,
							'updated_at' => $date,


						],
					];
					//dd($data);

					Mst_attribute_value::insert($data);
				}
			}

			return redirect('admin/attribute_value/list')->with('status', 'Attribute added successfully.');
		} else {
			//return redirect('/')->withErrors($validator->errors());
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function editAttr_Value(Request $request, $id)
	{

		$decryptId = Crypt::decryptString($id);

		$pageTitle = "Edit Attribute Value";
		$attributevalue = Mst_attribute_value::Find($decryptId);
		$attributegroups = Mst_attribute_group::all();

		return view('admin.masters.attribute_value.edit', compact('attributevalue', 'attributegroups', 'pageTitle'));
	}

	public function updateAttr_Value(
		Request $request,
		Mst_attribute_value $attributevalue,
		$attr_value_id
	) {

		$GrpId = $request->attr_value_id;
		$attributevalue = Mst_attribute_value::Find($GrpId);

		$validator = Validator::make(
			$request->all(),
			[
				'group_value'   => 'required',
				'attribute_group_id' => 'required',

			],
			[
				'group_value.required'        => 'Group value required',
				'attribute_group_id'          => 'Group name required'


			]
		);
		// $this->uploads($request);
		if (!$validator->fails()) {
			$data = $request->except('_token');

			$attributevalue->group_value  = $request->group_value;
			$attributevalue->attribute_group_id  = $request->attribute_group_id;
			if ($request->attribute_group_id == 2) {
				$attributevalue->Hexvalue  = $request->Hexvalue;
			}

			$attributevalue->update();
			//dd($fetch);
			return redirect('admin/attribute_value/list')->with('status', 'Attribute value updated successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
	public function destroyAttr_Value(Request $request, Mst_attribute_value $attribute_value)
	{

		$delete = $attribute_value->delete();


		return redirect('admin/attribute_value/list')->with('status', 'Attribute value deleted successfully.');
	}
	public function destroyAttr_Group(Request $request, Mst_attribute_group $attribute_group)
	{

		$delete = $attribute_group->delete();


		return redirect('admin/attribute_group/list')->with('status', 'Attribute group deleted successfully.');;
	}
	public function store_payment_settlment()
	{

		$order = Trn_store_order::all();
		return view('admin.masters.store_payment.list', compact('order'));
	}


	public function ViewDocument(Request $request, $id)
	{


		$decrId  = Crypt::decryptString($id);
		$document = Mst_store_documents::Find($decrId);
		$doc_name = $document->store_document_other_file;
		dd($doc_name);
	}
	// product image

	public function destroy_product_image(Request $request, Mst_product_image $product_image)
	{

		$product_image->delete();

		return redirect()->back()->with('status', 'Product image  deleted successfully');
	}

	public function store_product_image(Request $request, Mst_product_image $product_image, $product_id)
	{
		$product_id = $request->product_id;
		$product_varient = Mst_store_product_varient::where('product_id', '=', $product_id)->first();
		$product_varient_id = $product_varient->product_varient_id;


		$validator = Validator::make(
			$request->all(),
			[

				'product_image'        => 'image|mimes:jpeg,png,jpg,gif,svg',



			],
			[

				'product_image.required'        => 'Image required',


			]
		);

		if (!$validator->fails()) {
			$data = $request->except('_token');





			if ($request->hasFile('product_image')) {
				/*$product_img = $request->file('product_image');

			$filename = time().'.'.$product_img->getClientOriginalExtension();

			$location = public_path('assets/uploads/products/base_product/feature_image/'.$filename);

			Image::make($product_img)->save($location);
			$product_image->product_image = $filename;*/

				$photo = $request->file('product_image');
				$filename = time() . '.' . $photo->getClientOriginalExtension();
				$destinationPath = 'assets/uploads/products/base_product/feature_image';
				$thumb_img = Image::make($photo->getRealPath());
				$thumb_img->save($destinationPath . '/' . $filename, 80);
				$product_image->product_image = $filename;
			}


			$product_image->image_flag = 1;
			$product_image->product_id = $product_id;
			$product_image->product_varient_id = $product_varient_id;

			$product_image->save();

			return redirect()->back()->with('status', 'Image added successfully.');
		} else {

			return redirect()->back()->withErrors($validator)->withInput();
		}
	}

	public function status_product_image(Request $request, Mst_product_image $product_image, $product_image_id)
	{

		$pro_id = $request->product_image_id;
		$product_image = Mst_product_image::Find($pro_id);

		$image_flag = $product_image->image_flag;

		// dd($image_flag);


		if ($image_flag == 1) {
			$product_id = $product_image->product_id;
			$product_img = Mst_product_image::where('product_id', '=', $product_id)->where('image_flag', '=', 0)->first();
			$product_image_id = $product_img->product_image_id;

			$product_img->image_flag = 1;
			/*dd($product_image_id);
         	*/
			$product_img->update();

			$product_image->image_flag  = 0;
		} else {


			$product_image->image_flag  = 1;
		}

		$product_image->update();

		return redirect()->back()->with('status', 'Product status changed successfully');
	}

	public function updateTerms(Request $request)
	{
		$pageTitle = "Edit Store Terms & Conditions";
		$tc = Trn_TermsAndCondition::where('role', 1)->first();
		return view('admin.masters.tc.list', compact('pageTitle', 'tc'));
	}



	public function updateCusTerms(Request $request)
	{
		$pageTitle = "Edit Customer Terms & Conditions";
		$tc = Trn_TermsAndCondition::where('role', 2)->first();
		return view('admin.masters.tc.list_cus_tc', compact('pageTitle', 'tc'));
	}

	public function updateTC(Request $request)
	{
		$tcCount = Trn_TermsAndCondition::where('role', 1)->count();
		if ($tcCount > 0) {
			Trn_TermsAndCondition::where('role', 1)->update(['terms_and_condition' => $request->tc, 'role' => 1]);
		} else {
			Trn_TermsAndCondition::where('role', 1)->create(['terms_and_condition' => $request->tc, 'role' => 1]);
		}

		return redirect()->back()->with('status', 'Terms and conditions updated.');
	}

	public function updateCusTC(Request $request)
	{

		$tcCount = Trn_TermsAndCondition::where('role', 2)->count();
		if ($tcCount > 0) {
			Trn_TermsAndCondition::where('role', 2)->update(['terms_and_condition' => $request->tc, 'role' => 2]);
		} else {
			Trn_TermsAndCondition::where('role', 2)->create(['terms_and_condition' => $request->tc, 'role' => 2]);
		}
		return redirect()->back()->with('status', 'Terms and conditions updated.');
	}

	public function changedBoyStatus(Request $request, Mst_delivery_boy $delivery_boy, $delivery_boy_id)
	{


		$delivery_boy = Mst_delivery_boy::Find($delivery_boy_id);

		$status = $delivery_boy->delivery_boy_status;

		if ($status == 0) {
			$delivery_boy->delivery_boy_status  = 1;
		} else {

			$delivery_boy->delivery_boy_status  = 0;
		}
		$delivery_boy->update();

		return redirect()->back()->with('status', 'Status changed successfully');
	}
}
