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
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_StoreAppBanner;
use App\Models\admin\Mst_CustomerAppBanner;
use App\Models\admin\Mst_Issues;
use App\Models\admin\Sys_IssueType;
use App\Models\admin\Trn_TaxSplitUp;
use App\Models\admin\Mst_SubCategory;
use Auth;
use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Trn_ReviewsAndRating;


class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function removeReview(Request $request, $reviews_id)
    {
        // dd($reviews_id);
        Trn_ReviewsAndRating::where('reviews_id', $reviews_id)->delete();
        return redirect()->back()->with('status', 'Review removed successfully');
    }

    public function reviewStatus(Request $request, $reviews_id)
    {
        $reviewSataus = Trn_ReviewsAndRating::where('reviews_id', $reviews_id)->first();
        if ($reviewSataus->isVisible == 1)
            Trn_ReviewsAndRating::where('reviews_id', $reviews_id)->update(['isVisible' => 0]);
        else
            Trn_ReviewsAndRating::where('reviews_id', $reviews_id)->update(['isVisible' => 1]);


        return redirect()->back()->with('status', 'Visibility status updated successfully');
    }


    public function listReview(Request $request)
    {
        $pageTitle = 'Reviews List';
        $reviews = Trn_ReviewsAndRating::orderBy('reviews_id', 'DESC');
        if ($_GET) {
            if (isset($request->store_id)) {
                $reviews = $reviews->where('store_id', $request->store_id);
            }

            if (isset($request->subadmin_id)) {
                $reviews = $reviews->where('subadmin_id', $request->subadmin_id);
            }

            if (isset($request->product_id)) {
                $reviews = $reviews->where('product_id', $request->product_id);
            }
            if (isset($request->customer_id)) {
                $reviews = $reviews->where('customer_id', $request->customer_id);
            }
            if (isset($request->rating)) {
                $reviews = $reviews->where('rating', $request->rating);
            }

            if (isset($request->isVisible)) {
                $reviews = $reviews->where('isVisible', $request->isVisible);
            }
        }
        $reviews = $reviews->get();


        //dd($reviews);
        $stores  = Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
            ->where("trn__store_admins.role_id", '=', 0)
            ->select("mst_stores.store_name", "mst_stores.store_id","mst_stores.store_code")->orderBy('store_name')->get();
        $subadmins = User::where('user_role_id', '!=', 0)->get();
        $customers = Trn_store_customer::all();

        return view('admin.masters.reviews.list', compact('customers', 'subadmins', 'stores', 'reviews', 'pageTitle'));
    }


    public function saveDeviceToken(Request $request)
    {
        print_r($request);
    }


    public function listResioreIssues(Request $request)
    {
        $pageTitle = "Restore Issues";
        $issues = Mst_Issues::onlyTrashed()->orderBy('issue_id', 'DESC')->get();
        return view('admin.masters.issues.restore', compact('issues', 'pageTitle'));
    }

    public function restoreIssues(Request $request, $issue_id)
    {
        Mst_Issues::withTrashed()->find($issue_id)->restore();
        return redirect('admin/issues/list')->with('status', 'Issue restored successfully');
    }



    public function listRestoreSAB(Request $request)
    {
        $pageTitle = "Restore Customer App Banner";
        $banners = Mst_StoreAppBanner::onlyTrashed()->orderBy('banner_id', 'DESC')->get();
        return view('admin.masters.banners.store_app_banners.restore', compact('banners', 'pageTitle'));
    }

    public function restoreSAB(Request $request, $banner_id)
    {
        Mst_StoreAppBanner::withTrashed()->find($banner_id)->restore();
        return redirect('admin/store/app/banner/list')->with('status', 'Banner restored successfully');
    }



    public function listRestoreCAB(Request $request)
    {
        $pageTitle = "Restore Customer App Banner";
        $banners = Mst_CustomerAppBanner::onlyTrashed()->orderBy('banner_id', 'DESC')->get();
        return view('admin.masters.banners.customer_app_banners.restore', compact('banners', 'pageTitle'));
    }

    public function restoreCAB(Request $request, $banner_id)
    {
        Mst_CustomerAppBanner::withTrashed()->find($banner_id)->restore();
        return redirect('/admin/customer/app/banner/list')->with('status', 'Banner restored successfully');
    }







    public function listRestoreCompany(Request $request)
    {
        $pageTitle = "Restore Companies";
        $companies = Mst_store_companies::onlyTrashed()->orderBy('company_id', 'DESC')->get();
        return view('admin.masters.companies.restore', compact('companies', 'pageTitle'));
    }

    public function restoreCompany(Request $request, $company_id)
    {
        Mst_store_companies::withTrashed()->find($company_id)->restore();
        return redirect('admin/company/list')->with('status', 'Company restored successfully');
    }



    public function restoreProduct(Request $request, $product_id)
    {
        Mst_store_product::withTrashed()->find($product_id)->restore();
        $data =  Mst_store_product::find($product_id);
        $sData =  Mst_store::withTrashed()->find($data->store_id);

        return redirect('admin/store/edit/' . $sData->store_name_slug)->with('status', 'Product restored successfully');
    }

    public function listRestoreAgency(Request $request)
    {
        $pageTitle = "Restore Agency";
        $agencies = Mst_store_agencies::onlyTrashed()->orderBy('agency_id', 'DESC')->get();
        return view('admin.masters.agencies.restore', compact('agencies', 'pageTitle'));
    }

    public function restoreAgency(Request $request, $agency_id)
    {
        Mst_store_agencies::withTrashed()->find($agency_id)->restore();
        return redirect('admin/agency/list')->with('status', 'Agency restored successfully');
    }


    public function listRestoreStore(Request $request)
    {
        $pageTitle = "Restore Store";
        $stores = Mst_store::onlyTrashed()->orderBy('store_id', 'DESC')->get();
        return view('admin.masters.stores.restore', compact('stores', 'pageTitle'));
    }

    public function restoreStore(Request $request, $store_id)
    {
        Mst_store::withTrashed()->find($store_id)->restore();
        return redirect('/admin/store/list')->with('status', 'Store restored successfully');
    }


    public function listResioreGlobalProduct(Request $request)
    {
        $pageTitle = "Restore Global Product";
        $global_product = Mst_GlobalProducts::onlyTrashed()->orderBy('global_product_id', 'DESC')->get();
        return view('admin.masters.global_product.restore', compact('global_product', 'pageTitle'));
    }

    public function restoreGlobalProduct(Request $request, $global_product_id)
    {
        Mst_GlobalProducts::withTrashed()->find($global_product_id)->restore();
        return redirect('admin/global/products/list')->with('status', 'Global product restored successfully');
    }


    public function listResioreSubadmin(Request $request)
    {
        $pageTitle = "Restore Sub Admin";
        $subadmins = User::onlyTrashed()->orderBy('id', 'DESC')->get();
        return view('admin.masters.subadmin.restore', compact('subadmins', 'pageTitle'));
    }

    public function restoreSubadmin(Request $request, $id)
    {
        User::withTrashed()->find($id)->restore();
        return redirect('admin/subadmin/list')->with('status', 'Sub admin restored successfully');
    }


    public function listResioreAttrGroup(Request $request)
    {
        $pageTitle = "Restore Attribute Group";
        $attributegroups = Mst_attribute_group::onlyTrashed()->orderBy('attr_group_id', 'DESC')->get();
        return view('admin.masters.attribute_group.restore', compact('attributegroups', 'pageTitle'));
    }

    public function restoreAttrGroup(Request $request, $attribute_group)
    {
        Mst_attribute_group::withTrashed()->find($attribute_group)->restore();
        return redirect('admin/attribute_group/list')->with('status', 'Attribute group restored successfully');
    }


    public function listResioreSubCategory(Request $request)
    {
        $pageTitle = "Restore Product Sub Categories";
        $sub_category = Mst_SubCategory::onlyTrashed()->orderBy('sub_category_id', 'DESC')->get();
        return view('admin.masters.sub_category.restore', compact('sub_category', 'pageTitle'));
    }

    public function restoreSubCategory(Request $request, $category)
    {
        Mst_SubCategory::withTrashed()->find($category)->restore();
        return redirect('admin/sub/category/list')->with('status', 'sub category restored successfully');
    }

    public function listRestoreTaxes(Request $request)
    {
        $pageTitle = "Restore Taxes";
        $taxes = Mst_Tax::where('is_removed', 1)->orderBy('tax_id', 'DESC')->get();
        $tax_splits = Trn_TaxSplitUp::orderBy('tax_split_up_id', 'DESC')->get();
        return view('admin.masters.taxes.restore', compact('tax_splits', 'pageTitle', 'taxes'));
    }

    public function restoreTax(Request $request, $tax_id)
    {
        $vehicle_type = Mst_Tax::where('tax_id', $tax_id)->update(['is_removed' => 0]);
        return redirect('admin/tax/list')->with('status', 'Tax restored successfully.');
    }


    public function listRestoreVehicleTypes(Request $request)
    {
        $pageTitle = "Restore Vehicle Types";
        $vehicle_types = Sys_vehicle_type::onlyTrashed()->orderBy('vehicle_type_id', 'DESC')->get();
        return view('admin.masters.vehicle_types.restore', compact('pageTitle', 'vehicle_types'));
    }

    public function restoreVehicleTypes(Request $request, $vehicle_type_id)
    {
        $vehicle_type = Sys_vehicle_type::onlyTrashed()->find($vehicle_type_id)->restore();
        return redirect('admin/vehicle_types/list')->with('status', 'Vehicle type restored successfully.');
    }


    public function listRestoreTown(Request $request)
    {
        $pageTitle = "Restore Pincodes";
        $towns = Town::onlyTrashed()->orderBy('town_id', 'DESC')->get();
        return view('admin.masters.towns.restore', compact('pageTitle', 'towns'));
    }

    public function restoreTown(Request $request, $town_id)
    {
        $town = Town::onlyTrashed()->find($town_id)->restore();
        return redirect('admin/pincode/list')->with('status', 'Town restored successfully');
    }




    public function listRestoreDistricts(Request $request)
    {
        $pageTitle = "Restore Districts";
        $districts = District::onlyTrashed()->orderBy('district_id', 'DESC')->get();
        return view('admin.masters.district.restore', compact('districts', 'pageTitle'));
    }

    public function restoreDistricts(Request $request, $district_id)
    {
        District::withTrashed()->find($district_id)->restore();
        return redirect('admin/districts/list')->with('status', 'District restored successfully');
    }

    public function listResioreCategory(Request $request)
    {
        $pageTitle = "Restore Product Categories";
        $categories = Mst_categories::onlyTrashed()->orderBy('category_id', 'DESC')->get();
        return view('admin.masters.categories.restore', compact('categories', 'pageTitle'));
    }

    public function restoreCategory(Request $request, $category)
    {
        Mst_categories::withTrashed()->find($category)->restore();
        return redirect('admin/categories/list')->with('status', 'Category restored successfully');
    }

    public function listResioreBusiness(Request $request)
    {
        $pageTitle = "Restore Business Types";
        $business_types = Mst_business_types::onlyTrashed()->orderBy('created_at', 'DESC')->get();
        return view('admin.masters.business_types.restore', compact('business_types', 'pageTitle'));
    }

    public function restoreBusiness(Request $request, $business_type)
    {
        Mst_business_types::withTrashed()->find($business_type)->restore();
        return redirect('admin/business_type/list')->with('status', 'Business type restored successfully');;
    }

    public function listDistricts(Request $request)
    {

        $pageTitle = "Districts";
        $districts = District::orderBy('district_id', 'DESC')->get();
        $countries   = Country::all();

        return view('admin.masters.district.list', compact('districts', 'pageTitle', 'countries'));
    }

    public function createDistricts(Request $request, District $district)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'state_id' => 'required',
                'district_name'        => 'required|unique:mst_districts,district_name',

            ],
            [
                'state_id.required'         => 'State  required',
                'district_name.required'        => 'District  required',
                'district_name.unique'        => 'District name exists',
            ]
        );
        if (!$validator->fails()) {
            $data = $request->except('_token');
            $district->state_id         = $request->state_id;
            $district->district_name         = $request->district_name;
            $district->save();
            return redirect('admin/districts/list')->with('status', 'District added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    public function removeDistricts(Request $request, $district_id, District $district)
    {
        $district = District::find($district_id);
        $delete = $district->delete();

        return redirect('admin/districts/list')->with('status', 'District deleted successfully');
    }



    public function listTown(Request $request)
    {
        $pageTitle = "Pincodes";
        $towns = Town::orderBy('town_id', 'DESC')->get();
        // $districts = District::orderBy('district_name','ASC')->get();
        $countries   = Country::all();
        return view('admin.masters.towns.list', compact('countries', 'pageTitle', 'towns'));
    }
    public function removeTown(Request $request, $town_id, Town $town)
    {
        $town = Town::find($town_id);
        $count_stores=Mst_store::where('town_id',$town_id)->count();
        $count_customers=Trn_store_customer::where('town_id',$town_id)->count();
        $count_dboys=Mst_delivery_boy::where('town_id',$town_id)->count();
        if($count_stores>0)
        {
            return redirect()->back()->with('error', 'Pincode cannot be removed as stores exists.');
        }
        if($count_customers>0)
        {
            return redirect()->back()->with('error', 'Pincode cannot be removed as customers exists.');
        }
        if($count_dboys>0)
        {
            return redirect()->back()->with('error', 'Pincode cannot be removed as delivery boys exists.');
        }
        $delete = $town->forceDelete();

        return redirect('admin/pincode/list')->with('status', 'Pincode deleted successfully');
    }

    public function editTown(Request $request, $town_id, Town $town)
    {
        $town = Town::find($town_id);
        $town->town_name = $request->town_name;
        $town->district_id = $request->district_id;
        $town->pin = $request->pin;
        $town->update();

        return redirect('admin/pincode/list')->with('status', 'Pincode updated successfully');
    }

    public function editTownView(Request $request, $town_id)
    {
        $pageTitle = "Edit Pincode";
        $town = Town::where('town_id', $town_id)->first();
        $d_data = District::where('district_id', $town->district_id)->first();
        $s_data = State::where('state_id', $d_data->state_id)->first();
        $c_data = Country::where('country_id', $s_data->country_id)->first();

        $countries   = Country::all();
        $states = State::where('country_id', $c_data->country_id)->get();
        $districts = District::where('district_id', $town->district_id)->get();


        return view('admin.masters.towns.edit', compact('town', 's_data', 'c_data', 'town_id', 'districts', 'states', 'pageTitle', 'countries'));
    }


    public function editDistrictsView(Request $request, $district_id)
    {

        $pageTitle = "Edit District";

        $district = District::where('district_id', $district_id)->first();
        $countries   = Country::all();
        $c_id = State::where('state_id', $district->state_id)->first();
        $states = State::where('country_id', $c_id->country_id)->get();

        return view('admin.masters.district.edit', compact('states', 'district_id', 'district', 'pageTitle', 'countries'));
    }


    public function editDistricts(Request $request, $district_id, District $district)
    {
        $district = District::find($district_id);
        $district->district_name = $request->district_name;
        $district->state_id = $request->state_id;
        $district->update();

        return redirect('admin/districts/list')->with('status', 'District updated successfully');
    }

    public function createTown(Request $request, Town $town)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'district_id' => 'required',
                'town_name'        => 'required|unique:mst_towns,town_name',
            ],
            [
                'district_id.required'         => 'District required',
                'town_name.required'        => 'Town  required',
                'town_name.unique'        => 'Town name exists',

            ]
        );
        if (!$validator->fails()) {
            $data = $request->except('_token');
            $town->district_id         = $request->district_id;
            $town->town_name         = $request->town_name;
            $town->pin         = $request->pin;
            $town->save();
            return redirect('admin/pincode/list')->with('status', 'Pincode added successfully.');
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }


    public function setDefaultImage(Request $request)
    {

        $table_id = $request->table_id; //store photos tabel id
        $store_image = \DB::table('mst_store_images')->select('store_id')->where('store_image_id', $table_id)->first();
        // return response()->json($store_image);
        $store_id = $store_image->store_id;
        // echo $store_image;die;
        $store_images = Mst_store_images::where('store_id', $store_id)->where('default_image', 1)->count();
        if ($store_images == 0) {
            $affected = DB::table('mst_store_images')
                ->where('store_image_id', $table_id)
                ->update(['default_image' => 1]);
            return true;
        } else {
            $affected = DB::table('mst_store_images')
                ->where('store_id', $store_id)
                ->update(['default_image' => 0]);

            $affected = DB::table('mst_store_images')
                ->where('store_image_id', $table_id)
                ->update(['default_image' => 1]);
            return true;
        }
    }


    public function changeDefaultImage(Request $request)
    {

        $table_id = $request->table_id; //store photos tabel id


        $affected = DB::table('mst_store_images')
            ->where('store_image_id', $table_id)
            ->update(['default_image' => 0]);
        return true;
    }


    public function listVehicleTypes(Request $request)
    {
        if(Auth::user()->user_role_id != 0 )
	    {
	        return redirect('home');
	    }else{
            $pageTitle = "List Vehicle Types";
            $vehicle_types = Sys_vehicle_type::orderBy('vehicle_type_id', 'DESC')->get();
            return view('admin.masters.vehicle_types.list', compact('pageTitle', 'vehicle_types'));
        }
        
    }

    public function listTaxes(Request $request)
    {
        $pageTitle = "List Taxes";
        $taxes = Mst_Tax::orderBy('tax_id', 'DESC')->where('is_removed', '!=', 1)->get();
        $tax_splits = Trn_TaxSplitUp::orderBy('tax_split_up_id', 'DESC')->get();
        return view('admin.masters.taxes.list', compact('tax_splits', 'pageTitle', 'taxes'));
    }

    public function addTaxes(Request $request)
    {
        $pageTitle = "Add Tax";
        return view('admin.masters.taxes.add', compact('pageTitle'));
    }


    public function createVehicleTypes(Request $request, Sys_vehicle_type $vehicle_type)
    {

        $vehicle_type->vehicle_type_name  = $request->vehicle_type_name;
        //  dd($vehicle_type);
        $vehicle_type->save();

        return redirect()->back()->with('status', 'Vehicle type added successfully.');
    }

    public function removeVehicleTypes(Request $request, Sys_vehicle_type $vehicle_type, $vehicle_type_id)
    {
        $delivery_boy_count=Mst_delivery_boy::where('vehicle_type_id',$vehicle_type_id)->count();
        if($delivery_boy_count>0)
        {
            return redirect()->back()->with('error', 'Vehicle type cannot be removed as delivery boys exist with this vehicle type.');

        }
        $vehicle_type = Sys_vehicle_type::find($vehicle_type_id);
        $vehicle_type->forceDelete();
        return redirect()->back()->with('status', 'Vehicle type removed successfully.');
    }

    public function updateVehicleTypes(Request $request, Sys_vehicle_type $vehicle_type, $vehicle_type_id)
    {
        $vehicle_type = Sys_vehicle_type::find($vehicle_type_id);
        $vehicle_type->vehicle_type_name  = $request->vehicle_type_name;
        $vehicle_type->update();

        return redirect()->back()->with('status', 'Vehicle type updated successfully.');
    }

    public function createTax(Request $request, Mst_Tax $tax)
    {
        
        $validator = Validator::make(
            $request->all(),
            [
                'tax_value'          => 'required|numeric|gte:0',
                'tax_name'          => 'required',
                "split_tax_name.*"  => "required",
                "split_tax_value.*"  => "required|numeric|gte:0",
                
            ],['split_tax_name.*.required'=>'split name is missing',
            'split_tax_value.*.required'=>'split value is missing']
            
        );
        if (!$validator->fails()) 
        {
            $tax->tax_value  = $request->tax_value;
            $tax->tax_name  = $request->tax_name;
            // dd($request->all());
            $tax->save();
            $last_id = DB::getPdo()->lastInsertId();
            $i = 0;
            foreach ($request->split_tax_name as $tax) {
    
    
                $data = [
                    'tax_id'      => $last_id,
                    'split_tax_name'      => $tax,
                    'split_tax_value'      => $request->split_tax_value[$i],
    
                ];
                Trn_TaxSplitUp::create($data);
    
                $i++;
            }
    
    
            return redirect('admin/tax/list')->with('status', 'Tax added successfully.');
        }
        else
            {
                return redirect()->back()->withErrors($validator)->withInput();
            }

       
    }


    public function removeTax(Request $request, Mst_Tax $tax, $tax_id)
    {
        $gp_count=Mst_GlobalProducts::where('tax_id',$tax_id)->count();
        $sp_count=Mst_store_product::where('tax_id',$tax_id)->count();
        if($gp_count>0||$sp_count>0)
        {
            return redirect()->back()->with('error', 'Tax cannot be removed as products exist.');
        }

        $tax = Mst_Tax::where('tax_id', $tax_id)->first();
        $tax->forceDelete();

        return redirect()->back()->with('status', 'Tax removed successfully.');
    }

    public function editTax(Request $request, Mst_Tax $tax, $tax_id)
    {
        $pageTitle = "Edit Tax";
        $tax = Mst_Tax::find($tax_id);
        $tax_splits = Trn_TaxSplitUp::where('tax_id', $tax_id)->get();
        return view('admin.masters.taxes.edit', compact('tax_splits', 'pageTitle', 'tax'));
    }

    public function updateTax(Request $request, Mst_Tax $tax, $tax_id)
    {
        // dd($request->all());
        $validator = Validator::make(
            $request->all(),
            [
                'tax_value'          => 'required|numeric|gte:0',
                'tax_name'          => 'required',
                "split_tax_name.*"  => "required",
                "split_tax_value.*"  => "required|numeric|gte:0",
                
            ],['split_tax_name.*.required'=>'split name is missing',
            'split_tax_value.*.required'=>'split value is missing']
            
        );
        if (!$validator->fails()) 
        {

        $tax = Mst_Tax::find($tax_id);
        $tax->tax_value  = $request->tax_value;
        $tax->tax_name  = $request->tax_name;
        $tax->update();

        Trn_TaxSplitUp::where('tax_id', $tax_id)->delete();

        $i = 0;
        foreach ($request->split_tax_name as $tax) {


            $data = [
                'tax_id'      => $tax_id,
                'split_tax_name'      => $tax,
                'split_tax_value'      => $request->split_tax_value[$i],

            ];
            Trn_TaxSplitUp::create($data);

            $i++;
        }




        return redirect('admin/tax/list')->with('status', 'Tax updated successfully.');
    }
    else
    {
        return redirect()->back()->withErrors($validator)->withInput();

    }
    
    }



    public function listStoreAppBanner(Request $request)
    {
        $pageTitle = "Store App Banners";
        $banners = Mst_StoreAppBanner::orderBy('banner_id','DESC')->get();
        $countries   = Country::all();

        return view('admin.masters.banners.store_app_banners.list', compact('countries', 'pageTitle', 'banners'));
    }

    public function storeStoreAppBanner(Request $request, Mst_StoreAppBanner $banner)
    {
        if ($request->hasFile('images')) {

            $img_validate = Validator::make(
                $request->all(),
                [
                    'images.*' => 'required|max:2048',
                    // 'town_id' => 'required',
                    // 'district_id' => 'required',
                    // 'state_id' => 'required',
                    // 'country_id' => 'required',
                ],
                [
                    'images.*.dimensions' => 'Banner image dimensions invalid',
                    'images.*.max' => 'Banner image size should not exceed 2MB',
                    'town_id.required' => 'Town required',
                ]
            );
            if ($img_validate->fails()) {
                return redirect()->back()->withErrors($img_validate)->withInput();
            }
        }

        if (!$img_validate->fails()) {

            if ($request->hasFile('images')) {



                $images = $request->file('images');
                $town_id = $request->town_id;
                // dd($product_image);
                foreach ($images as $image) {
    
                    // dd($filename);
                    $destination_path = 'assets/uploads/store_banner/';

                    $filename = rand(1, 5000) . time();
                    
                                    // Use Intervention Image to open and manipulate the image
                        $resizedImage = Image::make($image->getRealPath())->resize(225, 225, function ($constraint) {
                            //$constraint->aspectRatio();
                            $constraint->upsize();
                        });
                      
                        $originalSize = $resizedImage->filesize();
                            
                                    // Determine compression quality based on original size
                        if ($originalSize > 50000) { // If original size > 50kb
                            $quality = 50;
                        } 
                        elseif ($originalSize > 30000) { // If original size > 30kb
                            $quality = 60;
                        } 
                        else
                        { // If original size <= 30kb
                            $quality = 100;
                        }
                            
                      
                                    // Convert the image to JPG format
                        $resizedImage->encode('jpg',$quality);
                        $resizedImage->save($destination_path . $filename . '.jpg'); // Adjust quality as needed



                    $data2 = [
                        [
                            'image'      => $filename . '.jpg',
                            'town_id'      => $town_id,
                            'status'      => $request->status,
                        ],
                    ];

                    Mst_StoreAppBanner::insert($data2);
                }
            }
        }

        return redirect()->back()->with('status', 'Banner image added successfully.');
    }


    public function removeStoreAppBanner(Request $request, Mst_StoreAppBanner $banner, $banner_id)
    {
        $banner = Mst_StoreAppBanner::find($banner_id);
        $banner->delete();
        return redirect()->back()->with('status', 'Banner image deleted successfully.');
    }

    public function listCustomerAppBanner(Mst_CustomerAppBanner $banner)
    {
        $pageTitle = "Customer App Banners";
        $banners = Mst_CustomerAppBanner::orderBy('banner_id','DESC')->get();
        $countries   = Country::all();

        return view('admin.masters.banners.customer_app_banners.list', compact('countries', 'pageTitle', 'banners'));
    }

    public function removeCustomerAppBanner(Request $request, Mst_CustomerAppBanner $banner, $banner_id)
    {
        $banner = Mst_CustomerAppBanner::find($banner_id);
        $banner->delete();
        return redirect()->back()->with('status', 'Banner image deleted successfully.');
    }

    public function storeCustomerAppBanner(Request $request, Mst_CustomerAppBanner $banner)
    {
        if ($request->hasFile('images')) {

            $img_validate = Validator::make(
                $request->all(),
                [
                    'images.*' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                    // 'country_id' => 'required',
                    // 'state_id' => 'required',
                    // 'district_id' => 'required',
                    // 'town_id' => 'required',
                ],
                [
                    'images.*.max'=>'Maximum file size must not exceeeds 2MB',
                ]
            );
            if ($img_validate->fails()) {
                return redirect()->back()->withErrors($img_validate)->withInput();
            }
        }

        if (!$img_validate->fails()) {

            if ($request->hasFile('images')) {



                $images = $request->file('images');
                
                // dd($request->all());
                $town_id = 0;



                // dd($product_image);
                foreach ($request->file('images') as $image) {
                    
                    // dd($filename);
                    //echo $filename." - "; 
                    $destination_path = 'assets/uploads/customer_banner/';

                    // $store_img = Image::make($image->getRealPath());
                    // $store_img->save($destination_path . '/' . $filename, 80);

                    /////////////////////////////////////////

                    $filename = rand(1, 5000) . time();
                    
                                    // Use Intervention Image to open and manipulate the image
                        $resizedImage = Image::make($image->getRealPath())->resize(225, 225, function ($constraint) {
                            //$constraint->aspectRatio();
                            $constraint->upsize();
                        });
                      
                        $originalSize = $resizedImage->filesize();
                            
                                    // Determine compression quality based on original size
                        if ($originalSize > 50000) { // If original size > 50kb
                            $quality = 50;
                        } 
                        elseif ($originalSize > 30000) { // If original size > 30kb
                            $quality = 60;
                        } 
                        else
                        { // If original size <= 30kb
                            $quality = 100;
                        }
                            
                      
                                    // Convert the image to JPG format
                        $resizedImage->encode('jpg',$quality);
                        $resizedImage->save($destination_path . $filename . '.jpg'); // Adjust quality as needed
                        

                    //////////////////////////////////////////////////



                    // $data2 = [
                    //     [
                    //         'image'      => $filename,
                    //         'town_id'      => $request->town_id,
                    //         'status' => $request->status,
                    //         'default_status' => 0
                    //     ],
                    // ];

                    Mst_CustomerAppBanner::insert( [
                            'image'      => $filename. '.jpg',
                            'town_id'      => $request->town_id,
                            'status' => $request->status,
                            'default_status' => 0
                        ]);
                }
                
                
                //dd($request->all());
            }
        }

        return redirect()->back()->with('status', 'Banner image added successfully.');
    }

    public function listIssues()
    {
        $pageTitle = "Issues";
        $issues = Mst_Issues::all();
        $issue_types = Sys_IssueType::all();

        return view('admin.masters.issues.list', compact('issue_types', 'issues', 'pageTitle'));
    }

    public function createIssue(Request $request, Mst_Issues $issue)
    {
        $issue->issue  = $request->issue;
        $issue->issue_type_id  = $request->issue_type_id;
        $issue->save();
        return redirect()->back()->with('status', 'Issue added successfully.');
    }

    public function removeIssue(Request $request, Mst_Issues $issue, $issue_id)
    {
        $issue = Mst_Issues::find($issue_id);
        $issue->delete();
        return redirect()->back()->with('status', 'Issue removed successfully.');
    }


    public function updateIssue(Request $request, Mst_Issues $issue, $issue_id)
    {
        $issue = Mst_Issues::find($issue_id);
        $issue->issue  = $request->issue;
        $issue->issue_type_id  = $request->issue_type_id;
        $issue->update();
        return redirect()->back()->with('status', 'Issue updated successfully.');
    }


    public function statusCustomerBanner(Request $request, $banner_id)
    {

        $banner = Mst_CustomerAppBanner::Find($banner_id);
        $status = $banner->status;

        if ($status == 0) {
            $banner->status  = 1;
        } else {
            $banner->status  = 0;
        }
        $banner->update();

        return redirect()->back()->with('status', 'Banner status changed successfully');
    }
    
    public function statusStoreBanner(Request $request, $banner_id)
    {

        $banner = Mst_StoreAppBanner::Find($banner_id);
        $status = $banner->status;

        if ($status == 0) {
            $banner->status  = 1;
        } else {
            $banner->status  = 0;
        }
        $banner->update();

        return redirect()->back()->with('status', 'Banner status changed successfully');
    }
    
}
