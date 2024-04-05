<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Image;
use Hash;
use DB;
use Carbon\Carbon;
use Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\GlobalProductsImport;

use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Trn_GlobalProductImage;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_product_image;
use App\Models\admin\Trn_GlobalProductVideo;

use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_order_link_delivery_boy;
use App\Models\admin\Mst_StockDetail;
use App\Models\admin\Mst_SubCategory;


use App\Models\admin\Trn_RecentlyVisitedProducts;
use App\Models\admin\Trn_RecentlyVisitedStore;
use App\Models\admin\Mst_store;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Town;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Mst_store_link_delivery_boy;
use App\Models\admin\Sys_store_order_status;

use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Trn_Cart;
use Illuminate\Database\Eloquent\Builder;
use Intervention\Image\Facades\Image  as InterventionImage;
//use Illuminate\Support\Facades\File;

class ProductController extends Controller
{

  public function __construct()
  {
    $this->middleware('auth');
  }


  public function statusGlobalIMG(Request $request, $imgId)
  {
    try {

      $product_image = Trn_GlobalProductImage::where('global_product_image_id', $imgId)->first();
      Mst_GlobalProducts::where('global_product_id', $product_image->global_product_id)->update(['product_base_image' => $product_image->image_name]);




      return redirect()->back()->with('status', 'Base image successfully updated.');
    } catch (\Exception $e) {
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function listTownNames(Request $request)
  {
    $town = Town::pluck("town_name", "town_id");

    return response()->json($town);
  }


  public function listSubCategoryNames(Request $request)
  {
    $category_id = $request->category_id;
    $subcategories = Mst_SubCategory::where('category_id', $category_id)
      ->where('sub_category_status', 1)
      ->pluck("sub_category_name", "sub_category_id");

    return response()->json($subcategories);
  }


  public function listStoreNamesOld(Request $request)
  {
    $subadmin_id = $request->subadmin_id;
    $stores  = Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
      ->where("trn__store_admins.role_id", '=', 0)
      ->where("mst_stores.subadmin_id", '=', $subadmin_id);
    if (isset($subadmin_id)) {
      if ($subadmin_id != '') {
        $stores = $stores->where("mst_stores.subadmin_id", '=', $subadmin_id);
      }
    }
    $stores = $stores->pluck("mst_stores.store_name", "mst_stores.store_id");

    return response()->json($stores);
  }
  public function listStoreNames(Request $request)
  {
    $subadmin_id = $request->subadmin_id;

    $stores = Mst_store::join('trn__store_admins', 'trn__store_admins.store_id', '=', 'mst_stores.store_id')
      ->where("trn__store_admins.role_id", '=', 0)
      ->where("mst_stores.subadmin_id", '=', $subadmin_id);

    if (isset($subadmin_id) && $subadmin_id != '') {
      $stores = $stores->where("mst_stores.subadmin_id", '=', $subadmin_id);
    }

    $stores = $stores->get(["mst_stores.store_id", "mst_stores.store_name", "mst_stores.store_code"]);

    $formattedStores = $stores->map(function ($store) {
      return [
        'store_id' => $store->store_id,
        'store_name' => $store->store_name,
        'store_code' => $store->store_code,
      ];
    });

    return response()->json($formattedStores);
  }



  public function listProductNames(Request $request)
  {
    //$subadmin_id = $request->subadmin_id;
    $store_id = $request->store_id;


    $products  = Mst_store_product::join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id');

    if (isset($store_id)) {
      if ($store_id != '') {
        $products = $products->where("mst_store_products.store_id", '=', $store_id);
      }
    }


    if (isset($subadmin_id)) {
      $products = $products->where("mst_stores.subadmin_id", '=', $subadmin_id);
    }


    $products = $products->pluck("mst_store_products.product_name", "mst_store_products.product_id");

    return response()->json($products);
  }

  // Global Products

  public function listGlobalProducts(Request $request)
  {
    $pageTitle = "List Global Products";
    $category = Mst_categories::where('category_status', 1)->get();
    $global_product = Mst_GlobalProducts::whereHas('product_cat', function (Builder $qry) {
      return $qry->whereNull('deleted_at');
    });
    if ($request->product_cat_id) {
      $global_product = $global_product->where('product_cat_id', $request->product_cat_id);
    }

    if (isset($request->product_name)) {
      $global_product = $global_product->where('product_name', 'LIKE', '%' . $request->product_name . '%');
    }
    $global_product = $global_product->orderBy('global_product_id', 'DESC')->get();
    return view('admin.masters.global_product.list', compact('category', 'global_product', 'pageTitle'));
  }

  public function createGlobalProduct()
  {
    $pageTitle = "Create Global Product";
    $category = Mst_categories::all();

    $attr_groups = Mst_attribute_group::all();
    $tax = Mst_Tax::where('is_removed', '!=', 1)->get();
    $colors = Mst_attribute_value::join('mst_attribute_groups', 'mst_attribute_groups.attr_group_id', '=', 'mst_attribute_values.attribute_group_id')
      ->where('mst_attribute_groups.group_name', 'LIKE', '%color%')
      ->select('mst_attribute_values.*')
      ->get();
    $agencies = Mst_store_agencies::all();
    $business_types = Mst_business_types::all();

    return view('admin.masters.global_product.create', compact('category', 'business_types', 'agencies', 'colors', 'tax', 'attr_groups', 'pageTitle'));
  }

  public function storeGlobalProduct(Request $request, Mst_GlobalProducts $global_product)
  {

    //  dd($request->all());

    $data = $request->except('_token');

    $validator = Validator::make(
      $request->all(),
      [
        'product_name' => ['required'],
        'product_description' => ['required'],
        'regular_price' => ['required', 'gt:0'],
        'sale_price' => ['required', 'gt:0'],
        'tax_id' => ['required'],
        'min_stock' => ['required'],
        'product_code' => ['required'],
        // 'business_type_id' => ['required' ],
        //   'color_id' => ['required' ],
        //   'product_brand' => ['required' ],
        //  'attr_group_id' => ['required' ],
        'product_cat_id' => ['required'],
        //'vendor_id' => ['required'],
        'sub_category_id' => ['required'],
        //  'attr_value_id' => ['required' ],
        //  'product_image.*' => ['required', 'dimensions:min_width=1000,min_height=800'],
        'product_image.*' => ['required', 'max:30'],

      ],
      [
        'product_name.required'         => 'Product name required',
        'product_name.unique'         => 'Product name exists',
        'product_description.required'         => 'Product description required',
        'regular_price.required'         => 'MRP is required',
        'regular_price.gt'         => 'MRP should be greater than 0',
        'sale_price.required'         => 'Sale price is required',
        'sale_price.gt'         => 'Sale price should be greater than zero',
        'tax_id.required'         => 'Tax required',
        'min_stock.required'         => 'Min stock required',
        'product_code.required'         => 'Product code required',
        'business_type_id.required'         => 'Business type required',
        'color_id.required'     => 'Color required',
        'product_brand.required'    => 'Product Brand required',
        'attr_group_id.required'         => 'Attribute group required',
        'attr_value_id.required'         => 'Attribute value required',
        'product_cat_id.required'         => 'Product category required',
        'sub_category_id.required'         => 'Product subcategory required',
        'vendor_id.required'         => 'Vendor required',
        //   'product_image.required'        => 'Product image required',
        'product_image.*.max' => 'Product image size should not exceed 30KB',
        'product_image.dimensions'        => 'Product image dimensions invalid',

      ]
    );
    if (!$validator->fails()) {
      try {
        //check store code
        $ChkCodeExstnce = DB::table('mst_store_products')->where('product_code', $request->product_code)->count();

        //check global code
        $chkGlobalExistence = DB::table('mst__global_products')->where('product_code', $request->product_code)->count();


        if ($ChkCodeExstnce > 0 || $chkGlobalExistence > 0) {
          return redirect()->back()->with('status-error', 'Product code already used.')->withInput();
        } else {

          $global_product->product_name = $request->product_name;
          $global_product->product_name_slug = Str::of($request->product_name)->slug('-');
          $global_product->product_description = $request->product_description;
          $global_product->regular_price = $request->regular_price;
          $global_product->sale_price = $request->sale_price;
          $global_product->tax_id = $request->tax_id;
          $global_product->min_stock = $request->min_stock;
          $global_product->product_code = $request->product_code;

          if (isset($request->business_type_id))
            $global_product->business_type_id = $request->business_type_id;
          else
            $global_product->business_type_id =  0;



          $global_product->color_id = $request->color_id;
          $global_product->product_brand = $request->product_brand;


          if (isset($request->attr_group_id))
            $global_product->attr_group_id = $request->attr_group_id;
          else
            $global_product->attr_group_id = 0;

          if (isset($request->attr_value_id))
            $global_product->attr_value_id = $request->attr_value_id;
          else
            $global_product->attr_value_id = 0;





          $global_product->product_cat_id = $request->product_cat_id;
          $global_product->sub_category_id = $request->sub_category_id;
          $global_product->vendor_id = $request->vendor_id;
          $global_product->product_base_image = $request->product_base_image; // update after image uploads
          $global_product->created_date = Carbon::now()->format('Y-m-d');
          // $global_product->created_by = auth()->user()->id; // since store id and admin id seems to be 1 to solve jira issue 476
          $global_product->created_by = 0;

          $global_product->save();

          $global_product_id = DB::getPdo()->lastInsertId();

          $k = 0;

          foreach ($request->video_code as $vc) {
            if (isset($vc) && isset($request->platform[$k])) {
              $data2 = [[
                'global_product_id'   => $global_product_id,
                'video_code'  => $vc,
                'platform'  => $request->platform[$k],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
              ],];
              Trn_GlobalProductVideo::insert($data2);
            }
            $k++;
          }

          if ($request->hasFile('product_image')) {
            $allowedfileExtension = ['jpg', 'png', 'jpeg',];
            $files = $request->file('product_image');
            $c = 1;
            foreach ($files as $file) {
              $filename = $file->getClientOriginalName();
              $extension = $file->getClientOriginalExtension();
              $file->move('assets/uploads/products/base_product/base_image', $filename);
              $data1 = [[
                'global_product_id'   => $global_product_id,
                'image_name'  => $filename,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
              ],];
              Trn_GlobalProductImage::insert($data1);
              if ($c == 1) {
                DB::table('mst__global_products')->where('global_product_id', $global_product_id)->update(['product_base_image' => $filename]);
                $c++;
              }
            }
          }
          return redirect('/admin/global/products/list')->with('status', 'Global product added successfully.');
        }
      } catch (\Exception $e) {

        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      }
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }


  public function storeGlobalProductNew(Request $request, Mst_GlobalProducts $global_product)
  {
    //  dd($request->all());

    $data = $request->except('_token');

    $validator = Validator::make(
      $request->all(),
      [
        'product_name' => ['required'],
        'product_description' => ['required'],
        'regular_price' => ['required', 'gt:0'],
        'sale_price' => ['required', 'gt:0'],
        'tax_id' => ['required'],
        'min_stock' => ['required'],
        'product_code' => ['required'],
        // 'business_type_id' => ['required' ],
        //   'color_id' => ['required' ],
        //   'product_brand' => ['required' ],
        //  'attr_group_id' => ['required' ],
        'product_cat_id' => ['required'],
        //'vendor_id' => ['required'],
        'sub_category_id' => ['required'],
        //  'attr_value_id' => ['required' ],
        //  'product_image.*' => ['required', 'dimensions:min_width=1000,min_height=800'],
      'product_image.*' => ['required', 'max:2048'], // 2MB limit

      ],
      [
        'product_name.required'         => 'Product name required',
        'product_name.unique'         => 'Product name exists',
        'product_description.required'         => 'Product description required',
        'regular_price.required'         => 'MRP is required',
        'regular_price.gt'         => 'MRP should be greater than 0',
        'sale_price.required'         => 'Sale price is required',
        'sale_price.gt'         => 'Sale price should be greater than zero',
        'tax_id.required'         => 'Tax required',
        'min_stock.required'         => 'Min stock required',
        'product_code.required'         => 'Product code required',
        'business_type_id.required'         => 'Business type required',
        'color_id.required'     => 'Color required',
        'product_brand.required'    => 'Product Brand required',
        'attr_group_id.required'         => 'Attribute group required',
        'attr_value_id.required'         => 'Attribute value required',
        'product_cat_id.required'         => 'Product category required',
        'sub_category_id.required'         => 'Product subcategory required',
        'vendor_id.required'         => 'Vendor required',
        //   'product_image.required'        => 'Product image required',
        'product_image.*.max' => 'Product image size should not exceed 2MB',
        'product_image.dimensions'        => 'Product image dimensions invalid',

      ]
    );
    if (!$validator->fails()) {
      try {
        //check store code
        $ChkCodeExstnce = DB::table('mst_store_products')->where('product_code', $request->product_code)->count();

        //check global code
        $chkGlobalExistence = DB::table('mst__global_products')->where('product_code', $request->product_code)->count();


        if ($ChkCodeExstnce > 0 || $chkGlobalExistence > 0) {
          return redirect()->back()->with('status-error', 'Product code already used.')->withInput();
        } else {

          $global_product->product_name = $request->product_name;
          $global_product->product_name_slug = Str::of($request->product_name)->slug('-');
          $global_product->product_description = $request->product_description;
          $global_product->regular_price = $request->regular_price;
          $global_product->sale_price = $request->sale_price;
          $global_product->tax_id = $request->tax_id;
          $global_product->min_stock = $request->min_stock;
          $global_product->product_code = $request->product_code;

          if (isset($request->business_type_id))
            $global_product->business_type_id = $request->business_type_id;
          else
            $global_product->business_type_id =  0;



          $global_product->color_id = $request->color_id;
          $global_product->product_brand = $request->product_brand;


          if (isset($request->attr_group_id))
            $global_product->attr_group_id = $request->attr_group_id;
          else
            $global_product->attr_group_id = 0;

          if (isset($request->attr_value_id))
            $global_product->attr_value_id = $request->attr_value_id;
          else
            $global_product->attr_value_id = 0;





          $global_product->product_cat_id = $request->product_cat_id;
          $global_product->sub_category_id = $request->sub_category_id;
          $global_product->vendor_id = $request->vendor_id;
          $global_product->product_base_image = $request->product_base_image; // update after image uploads
          $global_product->created_date = Carbon::now()->format('Y-m-d');
          // $global_product->created_by = auth()->user()->id; // since store id and admin id seems to be 1 to solve jira issue 476
          $global_product->created_by = 0;

          $global_product->save();

          $global_product_id = DB::getPdo()->lastInsertId();

          $k = 0;

          foreach ($request->video_code as $vc) {
            if (isset($vc) && isset($request->platform[$k])) {
              $data2 = [[
                'global_product_id'   => $global_product_id,
                'video_code'  => $vc,
                'platform'  => $request->platform[$k],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
              ],];
              Trn_GlobalProductVideo::insert($data2);
            }
            $k++;
          }

          if ($request->hasFile('product_image')) {
            $allowedfileExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file('product_image');
            $c = 1;
            foreach ($files as $file) {
              // Check file size
              // if ($file->getSize() > 2 * 1024 * 1024) {
              //   return redirect()->back()->withErrors(['File size exceeds the maximum limit of 2MB.'])->withInput();
              // }

              //$filename = $file->getClientOriginalName();
              $filename = rand(1, 5000) . time();
              $extension = $file->getClientOriginalExtension();

              // Use Intervention Image to open and manipulate the image
              $resizedImage = InterventionImage::make($file)->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            //$originalSize = $resizedImage->filesize();

        // Determine compression quality based on original size
        // if ($originalSize > 50000) { // If original size > 50kb
        //     $quality = 50;
        // } elseif ($originalSize > 30000) { // If original size > 30kb
        //     $quality = 60;
        // } else { // If original size <= 30kb
        //     $quality = 80;
        // }


              // Resize the image if necessary
              //$image->resize(300, 200); // Adjust dimensions as needed

              // Convert the image to WebP format
              $resizedImage->encode('webp',$quality);
              // Compress the image if its size exceeds 2MB
              $resizedImage->save('assets/uploads/products/base_product/base_image/' . $filename . '.webp'); // Adjust quality as needed
              // if ($file->getSize() >= 2 * 1024 * 1024) {
              //   $image->save('assets/uploads/products/base_product/base_image/' . $filename . '.webp', 95); // Adjust quality as needed
              // } else {
              //   $image->save('assets/uploads/products/base_product/base_image/' . $filename . '.webp');
              // }

              // Save the image
              // $image->save('assets/uploads/products/base_product/base_image/' . $filename . '.webp');

              // Insert data into the database
              $data1 = [
                'global_product_id' => $global_product_id,
                'image_name' => $filename . '.webp', // Update the filename with the new extension
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
              ];
              Trn_GlobalProductImage::insert($data1);

              if ($c == 1) {
                DB::table('mst__global_products')->where('global_product_id', $global_product_id)->update(['product_base_image' => $filename . '.webp']); // Update the filename with the new extension
                $c++;
              }
            }
          }
          return redirect('/admin/global/products/list')->with('status', 'Global product added successfully.');
        }
      } catch (\Exception $e) {

        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      }
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }


  public function editGlobalProduct($global_product_id)
  {
    $global_product_id  = Crypt::decryptString($global_product_id);
    $pageTitle = "Edit Global Product";
    $product = Mst_GlobalProducts::Find($global_product_id);
    $product_images = Trn_GlobalProductImage::where('global_product_id', $global_product_id)->get();
    $videos = Trn_GlobalProductVideo::where('global_product_id', $global_product_id)->get();

    $attr_groups = Mst_attribute_group::all();
    $tax = Mst_Tax::all();
    $colors = Mst_attribute_value::join('mst_attribute_groups', 'mst_attribute_groups.attr_group_id', '=', 'mst_attribute_values.attribute_group_id')
      ->where('mst_attribute_groups.group_name', 'LIKE', '%color%')
      ->select('mst_attribute_values.*')
      ->get();
    $agencies = Mst_store_agencies::all();
    $business_types = Mst_business_types::all();

    $category = Mst_categories::withTrashed()->get();
    $subcategories = Mst_SubCategory::withTrashed()->where('category_id', @$product->product_cat_id)->get();
    // dd($subcategories);

    return view('admin.masters.global_product.edit', compact('subcategories', 'category', 'videos', 'business_types', 'agencies', 'colors', 'tax', 'attr_groups', 'product_images', 'product', 'global_product_id', 'pageTitle'));
  }

  public function updateGlobalProduct(Request $request, Mst_GlobalProducts $global_product, $global_product_id)
  {


    $validator = Validator::make(
      $request->all(),
      [
        'product_name' => ['required'],
        'product_description' => ['required'],
        'regular_price' => ['required', 'gt:0'],
        'sale_price' => ['required', 'gt:0'],
        'tax_id' => ['required'],
        'min_stock' => ['required'],
        'product_code' => ['required'],
        //'business_type_id' => ['required' ],
        // 'color_id' => ['required' ],
        // 'product_brand' => ['required' ],
        // 'attr_group_id' => ['required' ],
        'product_cat_id' => ['required'],
        //'vendor_id' => ['required'],
        //'attr_value_id' => ['required' ],
        // 'product_image.*' => 'dimensions:min_width=1000,min_height=800'
        //'product_image.*' => ['required', 'max:30'],

      ],
      [
        'product_name.required'         => 'Product name required',
        'product_name.unique'         => 'Product name exist',
        'product_description.required'         => 'Product description required',
        'regular_price.required'         => 'Regular price name required',
        'regular_price.gt'         => 'MRP should be grater than zero',
        'sale_price.required'         => 'Sale price name required',
        'sale_price.gt'         => 'Sale price should be greater than zero',
        'tax_id.required'         => 'Tax required',
        'min_stock.required'         => 'Min stock required',
        'product_code.required'         => 'Product code required',
        'business_type_id.required'         => 'Business type required',
        'color_id.required'     => 'Color required',
        'product_brand.required'    => 'Product Brand required',
        'attr_group_id.required'         => 'Attribute group required',
        'attr_value_id.required'         => 'Attribute value required',
        'product_cat_id.required'         => 'Product category required',
        'vendor_id.required'         => 'Vendor required',
        // 'product_image.required'        => 'Product image required',
        'product_image.*.max' => 'Product image size should not exceed 30KB',
        //'product_image.dimensions'        => 'Product image dimensions invalid',

      ]
    );


    if (!$validator->fails()) {
      try {

        //check store code
        $ChkCodeExstnce = DB::table('mst_store_products')->where('product_code', $request->product_code)->count();

        //check global code
        $chkGlobalExistence = DB::table('mst__global_products')->where('product_code', $request->product_code)->count();


        if ($chkGlobalExistence > 1) {
          return redirect()->back()->with('status-error', 'Product code already used.')->withInput();
        } else {

          $data['product_name'] = $request->product_name;
          $data['product_name_slug'] = Str::of($request->product_name)->slug('-');
          $data['product_description'] = $request->product_description;
          $data['sale_price'] = $request->sale_price;
          $data['tax_id'] = $request->tax_id;
          $data['min_stock'] = $request->min_stock;
          $data['regular_price'] = $request->regular_price;
          $data['product_code'] = $request->product_code;


          if (isset($request->business_type_id))
            $data['business_type_id'] = $request->business_type_id || 0;
          else
            $data['business_type_id'] = 0;


          $data['color_id'] = 0;
          $data['product_brand'] = $request->product_brand;



          if (isset($request->attr_group_id))
            $data['attr_group_id'] = $request->attr_group_id;
          else
            $data['attr_group_id'] = 0;

          if (isset($request->attr_value_id))
            $data['attr_value_id'] = $request->attr_value_id;
          else
            $data['attr_value_id'] = 0;



          $data['product_cat_id'] = $request->product_cat_id;
          $data['sub_category_id'] = $request->sub_category_id;
          $data['vendor_id'] = $request->vendor_id;

          Mst_GlobalProducts::where('global_product_id', $global_product_id)->update($data);


          $k = 0;

          foreach ($request->video_code as $vc) {
            if (isset($vc) && isset($request->platform[$k])) {
              $data2 = [[
                'global_product_id'   => $global_product_id,
                'video_code'  => $vc,
                'platform'  => $request->platform[$k],
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
              ],];
              Trn_GlobalProductVideo::insert($data2);
            }
            $k++;
          }


          if ($request->hasFile('product_image')) {
            $allowedfileExtension = ['jpg', 'png', 'jpeg'];
            $files = $request->file('product_image');
            $c = 1;
            foreach ($files as $file) {
              // Check file size
              // if ($file->getSize() > 2 * 1024 * 1024) {
              //   return redirect()->back()->withErrors(['File size exceeds the maximum limit of 2MB.'])->withInput();
              // }

              //$filename = $file->getClientOriginalName();
              $filename = rand(1, 5000) . time() . '.' . $file->getClientOriginalExtension();
              $extension = $file->getClientOriginalExtension();

              // Use Intervention Image to open and manipulate the image
              $image = InterventionImage::make($file);

              // Resize the image if necessary
              //$image->resize(300, 200); // Adjust dimensions as needed

              // Convert the image to WebP format
              $image->encode('webp');
              // Compress the image if its size exceeds 2MB
              if ($file->getSize() >= 2 * 1024 * 1024) {
                $image->save('assets/uploads/products/base_product/base_image/' . $filename . '.webp', 80); // Adjust quality as needed
              } else {
                $image->save('assets/uploads/products/base_product/base_image/' . $filename . '.webp');
              }

              // Save the image
              // $image->save('assets/uploads/products/base_product/base_image/' . $filename . '.webp');

              // Insert data into the database
              $data1 = [
                'global_product_id' => $global_product_id,
                'image_name' => $filename . '.webp', // Update the filename with the new extension
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
              ];
              Trn_GlobalProductImage::insert($data1);

              if ($c == 1) {
                DB::table('mst__global_products')->where('global_product_id', $global_product_id)->update(['product_base_image' => $filename . '.webp']); // Update the filename with the new extension
                $c++;
              }
            }
          }
        }
      } catch (\Exception $e) {

        return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
      }


      return redirect('/admin/global/products/list')->with('status', 'Global product updated successfully.');
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  public function removeGlobalProduct(Request $request, $global_product_id)
  {
    $count_product = Mst_store_product::where('global_product_id', $global_product_id)->count();
    if ($count_product > 0) {
      return redirect()->back()->with('error', 'Global products cannot be removed as store products exist.');
    }
    Mst_GlobalProducts::where('global_product_id', $global_product_id)->delete();

    return redirect()->back()->with('status', 'Global product deleted successfully.');
  }



  public function removeGlobalProductImage(Request $request, $global_product_image_id)
  {
    $gp_image = Trn_GlobalProductImage::where('global_product_image_id', $global_product_image_id)->first();
    if ($gp_image) {
      $gp = Mst_GlobalProducts::where('global_product_id', $gp_image->global_product_id)->first();
      if ($gp->product_base_image == $gp_image->image_name) {
        Mst_GlobalProducts::where('global_product_id', $gp_image->global_product_id)->update(['product_base_image' => null]);
      }
    }
    Trn_GlobalProductImage::where('global_product_image_id', $global_product_image_id)->delete();

    return redirect()->back()->with('status', 'Product image deleted successfully.');
  }

  public function removeGlobalProductVideo(Request $request, $global_product_video_id)
  {
    Trn_GlobalProductVideo::where('global_product_video_id', $global_product_video_id)->delete();

    return redirect()->back()->with('status', 'Product video deleted successfully.');
  }

  public function editGlobalProductVideo($global_product_video_id)
  {
    $global_product_video_id  = Crypt::decryptString($global_product_video_id);
    $pageTitle = "Edit Global Product Video";
    $video = Trn_GlobalProductVideo::Find($global_product_video_id);
    return view('admin.masters.global_product.edit_video', compact('video', 'global_product_video_id', 'pageTitle'));
  }

  public function updateGlobalProductVideo(Request $request, $global_product_video_id)
  {
    $data = $request->except('_token');

    $validator = Validator::make(
      $request->all(),
      [
        'platform' => ['required',],
        'video_code' => ['required',],

      ],
      [
        'platform.required'         => 'Platform required',
        'video_code.required'         => 'Video code required',

      ]
    );
    if (!$validator->fails()) {

      $data['platform'] = $request->platform;
      $data['video_code'] = $request->video_code;

      Trn_GlobalProductVideo::where('global_product_video_id', $global_product_video_id)->update($data);


      return redirect()->back()->with('status', 'Video updated successfully.');
    } else {
      return redirect()->back()->withErrors($validator)->withInput();
    }
  }

  public function viewGlobalProduct(Request $request, $global_product_id)
  {
    $global_product_id  = Crypt::decryptString($global_product_id);
    $pageTitle = "View Global Product";
    $product = Mst_GlobalProducts::Find($global_product_id);
    $product_images = Trn_GlobalProductImage::where('global_product_id', $global_product_id)->get();
    $product_videos = Trn_GlobalProductVideo::where('global_product_id', $global_product_id)->get();


    return view('admin.masters.global_product.view', compact('product_videos', 'product_images', 'product', 'global_product_id', 'pageTitle'));
  }

  public function importGlobalProduct(Request $request)
  {
    $pageTitle = "Import Global Products";

    return view('admin.masters.global_product.import', compact('pageTitle'));
  }

  public function postImportGlobalProduct(Request $request)
  {
    //dd($request->all());

    $validator = Validator::make(
      $request->all(),
      [
        'products_file'                  => 'required|mimes:xlsx',


      ],
      [
        'products_file.required'         => 'Products file  required',
        'products_file.mimes'         => 'Invalid file format',


      ]
    );

    //   try{ 

    $file = $request->file('products_file')->store('import');

    (new GlobalProductsImport)->import($file);
    return redirect()->back()->with('status', 'Global products imported successfully.');
    // } catch (\Exception $e) {
    //                return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();

    //   //  return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    // }

  }

  public function convertProduct(Request $request, $product_id, Mst_GlobalProducts $global_product)
  {
    try {

      $product = Mst_store_product::find($product_id);
      $productGlobal = Mst_GlobalProducts::where('isConvertedFromProducts', $product_id)->count();
      if ($productGlobal >= 1) {
        return redirect()->back()->withErrors(['Product already added!'])->withInput();
      }
      $product_image = Mst_product_image::where('product_id', $product_id)->where('product_varient_id', '!=', 0)->get();
      //dd($product->store_id);
      $global_product->product_name = $product->product_name;
      $global_product->created_by = $product->store_id;
      $global_product->product_name_slug = Str::of($product->product_name)->slug('-');
      $global_product->product_description = $product->product_description;
      if ($product->product_price)
        $global_product->regular_price = $product->product_price;
      else
        $global_product->regular_price = 0;

      if ($product->product_price_offer)
        $global_product->sale_price = $product->product_price_offer;
      else
        $global_product->sale_price = 0;

      if ($product->tax_id)
        $global_product->tax_id = $product->tax_id;
      else
        $global_product->tax_id =  0;

      if ($product->stock_count)
        $global_product->min_stock = $product->stock_count;
      else
        $global_product->min_stock =  0;

      if ($product->product_code)
        $global_product->product_code = $product->product_code;
      else
        $global_product->product_code =  0;

      if ($product->business_type_id)
        $global_product->business_type_id = $product->business_type_id;
      else
        $global_product->business_type_id =  0;

      if ($product->color_id)
        $global_product->color_id = $product->color_id;
      else
        $global_product->color_id = 0;

      if ($product->attr_group_id)
        $global_product->attr_group_id = $product->attr_group_id;
      else
        $global_product->attr_group_id = 0;

      if ($product->attr_value_id)
        $global_product->attr_value_id = $product->attr_value_id;
      else
        $global_product->attr_value_id = 0;

      if ($product->product_cat_id)
        $global_product->product_cat_id = $product->product_cat_id;
      else
        $global_product->product_cat_id = 0;

      if ($product->sub_category_id)
        $global_product->sub_category_id = $product->sub_category_id;
      else
        $global_product->sub_category_id = 0;

      if ($product->vendor_id)
        $global_product->vendor_id = $product->vendor_id;
      else
        $global_product->vendor_id =  0;


      $global_product->isConvertedFromProducts =  $product_id;

      $global_product->product_base_image = $product->product_base_image; // update after image uploads
      $global_product->created_date = Carbon::now()->format('Y-m-d');
      $global_product->product_brand = @$product->product_brand;

      $global_product->save();

      $global_product_id = \DB::getPdo()->lastInsertId();


      $data1 = [[
        'global_product_id'   => $global_product_id,
        'image_name'  => $product->product_base_image,
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now(),
      ],];
      Trn_GlobalProductImage::insert($data1);



      return redirect()->back()->with('status', 'Product updated to global product successfully.');
    } catch (\Exception $e) {
      // return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();

      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }



  public function showInHome(Request $request, $product_id)
  {
    try {

      $product = Mst_store_product::find($product_id);

      if ($product->show_in_home_screen == 0) {
        if ($product->product_price_offer < $product->product_price) {

          Mst_store_product::where('product_id', $product_id)->update(['show_in_home_screen' => 1]);
        } else {
          return redirect()->back()->withErrors(['Offer price should not be equals to MRP!'])->withInput();
          //return redirect()->back()->with('status', ' ');

        }

        return redirect()->back()->with('status', 'Offer product added to home screen successfully.');
      } else {
        Mst_store_product::where('product_id', $product_id)->update(['show_in_home_screen' => 0]);
        return redirect()->back()->with('status', 'Offer product removed from home screen successfully.');
      }
    } catch (\Exception $e) {
      // return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();

      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function showReport(Request $request)
  {
    // dd($request);
    // try {

    if (auth()->user()->user_role_id  == 0) {
      $stores = Mst_store::orderby('store_id', 'DESC')->get();
    } else {
      $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
    }

    $subadmins = User::where('user_role_id', '!=', 0)->get();
    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_categories::orderBy('category_id', 'DESC')->where('category_status', 1)->get();
    $subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();
    $customers = Trn_store_customer::all();

    $pageTitle = "Product Wise Reports";
    if (auth()->user()->user_role_id  == 0) {


      $data = Trn_RecentlyVisitedProducts::select(
        'trn__recently_visited_products.rvp_id',
        'trn__recently_visited_products.visit_count',
        'trn__recently_visited_products.created_at',
        'trn__recently_visited_products.updated_at',
        'trn__recently_visited_products.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'trn__recently_visited_products.product_id',
        'mst_store_products.product_code',
        'mst_store_products.product_name',
        'mst_store_products.product_brand',
        'mst_store_product_varients.is_base_variant',
        'mst_store_product_varients.variant_status',
        'trn__recently_visited_products.product_varient_id',
        'mst_store_product_varients.variant_name',
        'mst_store_agencies.agency_id',
        'mst_store_agencies.agency_name',
        'mst_store_categories.category_id',
        'mst_store_categories.category_name',
        'mst_store_products.sub_category_id',
        'mst__sub_categories.sub_category_name'
      )->selectRaw('SUM(trn__recently_visited_products.visit_count) as sum')
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
        ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
        ->join('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
        ->leftJoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
        ->leftJoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
        ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
        ->leftJoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
        ->groupBy(DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"), 'trn__recently_visited_products.product_varient_id')->orderBy('trn__recently_visited_products.rvp_id', 'DESC')
        ->paginate(10);


      if ($_GET) {

        // dd($request->all());

        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();


        $data = Trn_RecentlyVisitedProducts::select(
          'trn__recently_visited_products.rvp_id',
          'trn__recently_visited_products.visit_count',
          'trn__recently_visited_products.created_at',
          'trn__recently_visited_products.updated_at',
          'trn_store_customers.customer_id',
          'trn_store_customers.customer_first_name',
          'trn_store_customers.customer_last_name',
          'trn_store_customers.customer_mobile_number',
          'mst_stores.store_id',
          'mst_stores.store_name',
          'mst_stores.store_code',
          'mst_stores.store_mobile',
          'mst_store_products.product_id',
          'mst_store_products.product_code',
          'mst_store_products.product_name',
          'mst_store_products.product_brand',
          'mst_store_product_varients.product_varient_id',
          'mst_store_product_varients.variant_name',
          'mst_store_product_varients.is_base_variant',
          'mst_store_product_varients.variant_status',
          'mst_store_agencies.agency_id',
          'mst_store_agencies.agency_name',
          'mst_store_categories.category_id',
          'mst_store_categories.category_name',
          'mst_store_products.sub_category_id',
          'mst__sub_categories.sub_category_name'
        )->selectRaw('SUM(trn__recently_visited_products.visit_count) as sum')
          ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
          ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
          ->join('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
          ->leftJoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
          ->leftJoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
          ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
          ->leftJoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id');


        if (isset($request->date_from)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '<=', $a2);
        }


        if (isset($request->store_id)) {
          $data = $data->where('mst_stores.store_id', $request->store_id);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('mst_stores.subadmin_id', $request->subadmin_id);
        }


        if (isset($request->product_id)) {
          $data = $data->where('mst_store_products.product_id', $request->product_id);
        }

        if (isset($request->vendor_id)) {
          $data = $data->where('mst_store_products.vendor_id', $request->vendor_id);
        }

        if (isset($request->category_id)) {
          $data = $data->where('mst_store_products.product_cat_id', $request->category_id);
        }

        if (isset($request->sub_category_id)) {
          $data = $data->where('mst_store_products.sub_category_id', $request->sub_category_id);
        }

        if (isset($request->customer_id)) {
          $data = $data->where('trn__recently_visited_products.customer_id', $request->customer_id);
        }

        $data = $data->groupBy(DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"), 'trn__recently_visited_products.product_varient_id')->orderBy('trn__recently_visited_products.rvp_id', 'DESC')->paginate(10);
        foreach ($data as $inventoryD) {
          if (is_null($inventoryD->sub_category_name)) {
            $inventoryD->sub_category_name = 'Others';
          }
        }

        return view('admin.masters.reports.product_report', compact('customers', 'subCategories', 'categories', 'agencies', 'dateto', 'datefrom', 'subadmins', 'stores', 'data', 'pageTitle'));
      }
    } else {
      $data = Trn_RecentlyVisitedProducts::select(
        'trn__recently_visited_products.rvp_id',
        'trn__recently_visited_products.visit_count',
        'trn__recently_visited_products.created_at',
        'trn__recently_visited_products.updated_at',
        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_store_products.product_id',
        'mst_store_products.product_code',
        'mst_store_products.product_name',
        'mst_store_products.product_brand',
        'mst_store_product_varients.product_varient_id',
        'mst_store_product_varients.variant_name',
        'mst_store_product_varients.is_base_variant',
        'mst_store_product_varients.variant_status',
        'mst_store_agencies.agency_id',
        'mst_store_agencies.agency_name',
        'mst_store_categories.category_id',
        'mst_store_categories.category_name',
        'mst_store_products.sub_category_id',
        'mst__sub_categories.sub_category_name'
      )->selectRaw('SUM(trn__recently_visited_products.visit_count) as sum')
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
        ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
        ->join('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
        ->leftjoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
        ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
        ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
        ->leftJoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
        ->where('mst_stores.subadmin_id', auth()->user()->id)
        ->groupBy(DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"), 'trn__recently_visited_products.product_varient_id')->orderBy('trn__recently_visited_products.rvp_id', 'DESC')          //  ->groupBy('trn__recently_visited_products.product_varient_id', 'trn__recently_visited_products.customer_id', DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"))
        ->paginate(10);


      if ($_GET) {

        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();


        $data = Trn_RecentlyVisitedProducts::select(
          'trn__recently_visited_products.rvp_id',
          'trn__recently_visited_products.visit_count',
          'trn__recently_visited_products.created_at',
          'trn__recently_visited_products.updated_at',
          'trn_store_customers.customer_id',
          'trn_store_customers.customer_first_name',
          'trn_store_customers.customer_last_name',
          'trn_store_customers.customer_mobile_number',
          'mst_stores.store_id',
          'mst_stores.store_name',
          'mst_stores.store_code',
          'mst_stores.store_mobile',
          'mst_store_products.product_id',
          'mst_store_products.product_code',
          'mst_store_products.product_name',
          'mst_store_products.product_brand',
          'mst_store_product_varients.product_varient_id',
          'mst_store_product_varients.variant_name',
          'mst_store_agencies.agency_id',
          'mst_store_agencies.agency_name',
          'mst_store_categories.category_id',
          'mst_store_categories.category_name',
          'mst_store_products.sub_category_id',
          'mst__sub_categories.sub_category_name'
        )->selectRaw('SUM(trn__recently_visited_products.visit_count) as sum')
          ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
          ->join('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
          ->join('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
          ->leftjoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
          ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
          ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
          ->leftJoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id');

        $data = $data->where('mst_stores.subadmin_id', auth()->user()->id);

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '<=', $a2);
        }

        if (isset($request->store_id)) {
          $data = $data->where('mst_stores.store_id', $request->store_id);
        }

        if (isset($request->product_id)) {
          $data = $data->where('mst_store_products.product_id', $request->product_id);
        }

        if (isset($request->vendor_id)) {
          $data = $data->where('mst_store_products.vendor_id', $request->vendor_id);
        }

        if (isset($request->category_id)) {
          $data = $data->where('mst_store_products.product_cat_id', $request->category_id);
        }
        if (isset($request->sub_category_id)) {
          $data = $data->where('mst_store_products.sub_category_id', $request->sub_category_id);
        }

        if (isset($request->customer_id)) {
          $data = $data->where('trn__recently_visited_products.customer_id', $request->customer_id);
        }



        $data = $data->groupBy(DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"), 'trn__recently_visited_products.product_varient_id')->orderBy('trn__recently_visited_products.rvp_id', 'DESC')->paginate(10);
        foreach ($data as $inventoryD) {
          if (is_null($inventoryD->sub_category_name)) {
            $inventoryD->sub_category_name = 'Others';
          }
        }
        return view('admin.masters.reports.product_report', compact('customers', 'subCategories', 'categories', 'agencies', 'dateto', 'datefrom', 'subadmins', 'stores', 'data', 'pageTitle'));
      }
    }

    return view('admin.masters.reports.product_report', compact('customers', 'subCategories', 'categories', 'agencies', 'subadmins', 'stores', 'data', 'pageTitle'));
    // } catch (\Exception $e) {
    //   // return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
    //   return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    // }
  }
  public function showReportNew(Request $request)
  {
    $stores = Mst_store::when(auth()->user()->user_role_id !== 0, function ($query) {
      return $query->where('subadmin_id', auth()->user()->id);
    })->orderby('store_id', 'DESC')->get();

    $subadmins = User::where('user_role_id', '!=', 0)->get();
    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_categories::orderBy('category_id', 'DESC')->where('category_status', 1)->get();
    $subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();
    $customers = Trn_store_customer::all();
    $pageTitle = "Product Wise Reports";

    $data = Trn_RecentlyVisitedProducts::select(
      'trn__recently_visited_products.rvp_id',
      'trn__recently_visited_products.visit_count',
      'trn__recently_visited_products.created_at',
      'trn__recently_visited_products.updated_at',
      'trn_store_customers.customer_id',
      'trn_store_customers.customer_first_name',
      'trn_store_customers.customer_last_name',
      'trn_store_customers.customer_mobile_number',
      'mst_stores.store_id',
      'mst_stores.store_name',
      'mst_stores.store_code',
      'mst_stores.store_mobile',
      'mst_store_products.product_id',
      'mst_store_products.product_code',
      'mst_store_products.product_name',
      'mst_store_products.product_brand',
      'mst_store_product_varients.product_varient_id',
      'mst_store_product_varients.variant_name',
      'mst_store_agencies.agency_id',
      'mst_store_agencies.agency_name',
      'mst_store_categories.category_id',
      'mst_store_categories.category_name',
      'mst__sub_categories.sub_category_id',
      'mst__sub_categories.sub_category_name'
    )
      ->with('customer', 'store', 'product', 'productVariant', 'agency', 'category', 'subCategory')
      ->selectRaw('SUM(trn__recently_visited_products.visit_count) as sum');

    if ($_GET) {
      $data->when(isset($request->date_from), function ($query) use ($request) {
        return $query->whereDate('trn__recently_visited_products.created_at', '>=', Carbon::parse($request->date_from)->startOfDay());
      })->when(isset($request->date_to), function ($query) use ($request) {
        return $query->whereDate('trn__recently_visited_products.created_at', '<=', Carbon::parse($request->date_to)->endOfDay());
      });

      $data->when(isset($request->store_id), function ($query) use ($request) {
        return $query->where('mst_stores.store_id', $request->store_id);
      })->when(isset($request->subadmin_id), function ($query) use ($request) {
        return $query->where('mst_stores.subadmin_id', $request->subadmin_id);
      });

      $data->when(isset($request->product_id), function ($query) use ($request) {
        return $query->where('mst_store_products.product_id', $request->product_id);
      })->when(isset($request->vendor_id), function ($query) use ($request) {
        return $query->where('mst_store_products.vendor_id', $request->vendor_id);
      });

      $data->when(isset($request->category_id), function ($query) use ($request) {
        return $query->where('mst_store_products.product_cat_id', $request->category_id);
      })->when(isset($request->sub_category_id), function ($query) use ($request) {
        return $query->where('mst_store_products.sub_category_id', $request->sub_category_id);
      });

      $data->when(isset($request->customer_id), function ($query) use ($request) {
        return $query->where('trn__recently_visited_products.customer_id', $request->customer_id);
      });

      $data->groupBy(DB::raw("DATE_FORMAT(trn__recently_visited_products.created_at, '%d-%m-%Y')"), 'trn__recently_visited_products.product_varient_id')
        ->orderBy('trn__recently_visited_products.rvp_id', 'DESC');

      $processedData = collect(); // Collection to store processed data

      $data->chunk(10, function ($chunk) use ($processedData) {
        // Process each chunk of data
        $processedData->push($chunk);
      });

      return view('admin.masters.reports.product_report', compact('customers', 'subCategories', 'categories', 'agencies', 'dateto', 'datefrom', 'subadmins', 'stores', 'processedData', 'pageTitle'));
    }

    return view('admin.masters.reports.product_report', compact('customers', 'subCategories', 'categories', 'agencies', 'subadmins', 'stores', 'data', 'pageTitle'));
  }



  public function showVisitReport(Request $request)
  {
    // try {

    $pageTitle = "Product Visit Reports";

    if (auth()->user()->user_role_id  == 0) {
      $stores = Mst_store::orderby('store_id', 'DESC')->get();
    } else {
      $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
    }

    $subadmins = User::where('user_role_id', '!=', 0)->get();
    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_categories::orderBy('category_id', 'DESC')->where('category_status', 1)->get();
    $subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();
    $customers = Trn_store_customer::all();


    if (auth()->user()->user_role_id  == 0) {


      $data = Trn_RecentlyVisitedProducts::select(
        'trn__recently_visited_products.rvp_id',
        'trn__recently_visited_products.visit_count',
        'trn__recently_visited_products.created_at',
        'trn__recently_visited_products.updated_at',
        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_store_products.product_id',
        'mst_store_products.product_code',
        'mst_store_products.product_name',
        'mst_store_products.product_brand',
        'mst_store_product_varients.product_varient_id',
        'mst_store_product_varients.variant_name',
        'mst_store_product_varients.is_base_variant',
        'mst_store_product_varients.variant_status',
        'mst_store_agencies.agency_id',
        'mst_store_agencies.agency_name',
        'mst_store_categories.category_id',
        'mst_store_categories.category_name',
        'mst_store_products.sub_category_id',
        'mst__sub_categories.sub_category_name'
      )
        ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
        ->leftjoin('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
        ->leftjoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
        ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
        ->leftjoin('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
        ->leftJoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
        ->orderBy('trn__recently_visited_products.rvp_id', 'DESC')
        ->paginate(10);

      //dd($data,'under maintanence');


      if ($_GET) {

        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();


        $data = Trn_RecentlyVisitedProducts::select(
          'trn__recently_visited_products.rvp_id',
          'trn__recently_visited_products.visit_count',
          'trn__recently_visited_products.created_at',
          'trn__recently_visited_products.updated_at',
          'trn__recently_visited_products.store_id',
          'trn__recently_visited_products.customer_id',
          'trn__recently_visited_products.sub_category_id',
          'trn__recently_visited_products.category_id',
          'trn_store_customers.customer_id',
          'trn_store_customers.customer_first_name',
          'trn_store_customers.customer_last_name',
          'trn_store_customers.customer_mobile_number',
          'mst_stores.store_id',
          'mst_stores.store_code',
          'mst_stores.store_name',
          'mst_stores.store_mobile',
          'mst_stores.subadmin_id',
          'mst_store_products.product_id',
          'mst_store_products.vendor_id',
          'mst_store_products.product_cat_id',
          'mst_store_products.sub_category_id',
          'mst_store_products.product_code',
          'mst_store_products.product_name',
          'mst_store_products.product_brand',
          // 'mst_store_product_varients.product_varient_id',
          'mst_store_product_varients.variant_name',
          // 'mst_store_agencies.agency_id',
          'mst_store_agencies.agency_name',
          // 'mst_store_categories.category_id',
          'mst_store_categories.category_name',
          // 'mst__sub_categories.sub_category_id',
          'mst__sub_categories.sub_category_name',
          'mst_store_product_varients.is_base_variant',
          'mst_store_product_varients.variant_status',
        )
          ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
          ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
          ->leftjoin('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
          ->leftjoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
          ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
          ->leftjoin('mst_store_categories', 'mst_store_categories.category_id', '=', 'trn__recently_visited_products.category_id')
          ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'trn__recently_visited_products.sub_category_id');

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '<=', $a2);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn__recently_visited_products.store_id', $request->store_id);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('mst_stores.subadmin_id', $request->subadmin_id);
        }

        if (isset($request->product_id)) {
          $data = $data->where('mst_store_products.product_id', $request->product_id);
        }

        if (isset($request->vendor_id)) {
          $data = $data->where('mst_store_products.vendor_id', $request->vendor_id);
        }

        if (isset($request->category_id)) {
          $data = $data->where('mst_store_products.product_cat_id', $request->category_id);
        }

        if (isset($request->sub_category_id)) {
          $data = $data->where('mst_store_products.sub_category_id', $request->sub_category_id);
        }

        if (isset($request->customer_id)) {
          $data = $data->where('trn__recently_visited_products.customer_id', $request->customer_id);
        }



        $data = $data->orderBy('trn__recently_visited_products.rvp_id', 'DESC')
          ->paginate(10);

        foreach ($data as $dat) {
          if (is_null($dat->sub_category_name)) {
            $dat->sub_category_name = 'Others';
          }
        }

        // dd($request->category_id,$request->sub_category_id,$data);




        return view('admin.masters.reports.product_visit_report', compact(
          'customers',
          'subCategories',
          'categories',
          'agencies',
          'subadmins',
          'stores',
          'data',
          'pageTitle',
          'datefrom',
          'dateto'
        ));
      }
      return view('admin.masters.reports.product_visit_report', compact(
        'customers',
        'subCategories',
        'categories',
        'agencies',
        'subadmins',
        'stores',
        'data',
        'pageTitle',

      ));
    } else {
      $data = Trn_RecentlyVisitedProducts::select(
        'trn__recently_visited_products.rvp_id',
        'trn__recently_visited_products.visit_count',
        'trn__recently_visited_products.created_at',
        'trn__recently_visited_products.updated_at',
        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_store_products.product_id',
        'mst_store_products.product_code',
        'mst_store_products.product_name',
        'mst_store_products.product_brand',
        'mst_store_product_varients.product_varient_id',
        'mst_store_product_varients.variant_name',
        'mst_store_product_varients.is_base_variant',
        'mst_store_product_varients.variant_status',
        'mst_store_agencies.agency_id',
        'mst_store_agencies.agency_name',
        'mst_store_categories.category_id',
        'mst_store_categories.category_name',
        'mst__sub_categories.sub_category_id',
        'mst__sub_categories.sub_category_name'
      )
        ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
        ->leftjoin('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
        ->leftjoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
        ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
        ->leftjoin('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
        ->leftJoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
        ->where('mst_stores.subadmin_id', auth()->user()->id)
        ->orderBy('trn__recently_visited_products.rvp_id', 'DESC')
        ->paginate(10);



      if ($_GET) {

        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();


        $data = Trn_RecentlyVisitedProducts::select(
          'trn__recently_visited_products.rvp_id',
          'trn__recently_visited_products.visit_count',
          'trn__recently_visited_products.created_at',
          'trn__recently_visited_products.updated_at',
          'trn_store_customers.customer_id',
          'trn_store_customers.customer_first_name',
          'trn_store_customers.customer_last_name',
          'trn_store_customers.customer_mobile_number',
          'mst_stores.store_id',
          'mst_stores.store_name',
          'mst_stores.store_code',
          'mst_stores.store_mobile',
          'mst_store_products.product_id',
          'mst_store_products.product_code',
          'mst_store_products.product_name',
          'mst_store_products.product_brand',
          'mst_store_product_varients.product_varient_id',
          'mst_store_product_varients.variant_name',
          'mst_store_product_varients.is_base_variant',
          'mst_store_product_varients.variant_status',
          'mst_store_agencies.agency_id',
          'mst_store_agencies.agency_name',
          'mst_store_categories.category_id',
          'mst_store_categories.category_name',
          'mst__sub_categories.sub_category_id',
          'mst__sub_categories.sub_category_name'
        )
          ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_products.customer_id')
          ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_products.store_id')
          ->leftjoin('mst_store_products', 'mst_store_products.product_id', '=', 'trn__recently_visited_products.product_id')
          ->leftjoin('mst_store_product_varients', 'mst_store_product_varients.product_varient_id', '=', 'trn__recently_visited_products.product_varient_id')
          ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
          ->leftjoin('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
          ->leftJoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id');

        $data = $data->where('mst_stores.subadmin_id', auth()->user()->id);

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn__recently_visited_products.created_at', '<=', $a2);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn__recently_visited_products.store_id', $request->store_id);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('mst_stores.subadmin_id', $request->subadmin_id);
        }

        if (isset($request->product_id)) {
          $data = $data->where('mst_store_products.product_id', $request->product_id);
        }

        if (isset($request->vendor_id)) {
          $data = $data->where('mst_store_products.vendor_id', $request->vendor_id);
        }

        if (isset($request->category_id)) {
          $data = $data->where('mst_store_products.product_cat_id', $request->category_id);
        }

        if (isset($request->sub_category_id)) {
          $data = $data->where('mst_store_products.sub_category_id', $request->sub_category_id);
        }

        if (isset($request->customer_id)) {
          $data = $data->where('trn__recently_visited_products.customer_id', $request->customer_id);
        }



        $data = $data->orderBy('trn__recently_visited_products.rvp_id', 'DESC')
          ->paginate(10);




        return view('admin.masters.reports.product_visit_report', compact(
          'customers',
          'subCategories',
          'categories',
          'agencies',
          'subadmins',
          'stores',
          'data',
          'pageTitle',
          'datefrom',
          'dateto'
        ));
      }
    }
    //    dd($subadmins);

    return view('admin.masters.reports.product_visit_report', compact('customers', 'subCategories', 'categories', 'agencies', 'subadmins', 'stores', 'data', 'pageTitle'));
    // } catch (\Exception $e) {
    //   // return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
    //   return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    // }
  }

  public function showStoreVisitReport(Request $request)
  {
    try {
      //echo "here";die;

      $pageTitle = "Store Visit Reports";

      if (auth()->user()->user_role_id  == 0) {
        $stores = Mst_store::orderby('store_id', 'DESC')->get();
      } else {
        $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
      }

      $customers = Trn_store_customer::all();

      $subadmins = User::where('user_role_id', '!=', 0)->get();

      if (auth()->user()->user_role_id  == 0) {


        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();



        $data = Trn_RecentlyVisitedStore::select(
          'trn__recently_visited_stores.rvs_id',
          'trn__recently_visited_stores.visit_count',
          'trn__recently_visited_stores.created_at',
          'trn__recently_visited_stores.updated_at',
          'trn_store_customers.customer_id',
          'trn_store_customers.customer_first_name',
          'trn_store_customers.customer_last_name',
          'trn_store_customers.customer_mobile_number',
          'trn_store_customers.place',
          'mst_stores.store_id',
          'mst_stores.store_name',
          'mst_stores.store_code',
          'mst_stores.store_mobile',
          'mst_towns.town_name',
          'trn_store_customers.town_id'

        )
          ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_stores.customer_id')
          ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_stores.store_id')
          ->leftjoin('mst_towns', 'mst_towns.town_id', '=', 'trn_store_customers.town_id');




        if (isset($request->date_from)) {
          $data = $data->whereDate('trn__recently_visited_stores.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn__recently_visited_stores.created_at', '<=', $a2);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn__recently_visited_stores.store_id', $request->store_id);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('mst_stores.subadmin_id', $request->subadmin_id);
        }


        if (isset($request->customer_id)) {
          $data = $data->where('trn__recently_visited_stores.customer_id', $request->customer_id);
        }

        if (isset($request->town_id)) {
          $data = $data->where('trn_store_customers.town_id', $request->town_id);
        }



        $data = $data->groupBy('mst_stores.store_id', 'trn__recently_visited_stores.customer_id', DB::raw("DATE_FORMAT(trn__recently_visited_stores.created_at, '%d-%m-%Y')"))
          ->orderBy('trn__recently_visited_stores.rvs_id', 'DESC')
          ->paginate(10);
      } else { //subadmin


        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();



        $data = Trn_RecentlyVisitedStore::select(
          'trn__recently_visited_stores.rvs_id',
          'trn__recently_visited_stores.visit_count',
          'trn__recently_visited_stores.created_at',
          'trn__recently_visited_stores.updated_at',
          'trn_store_customers.customer_id',
          'trn_store_customers.customer_first_name',
          'trn_store_customers.customer_last_name',
          'trn_store_customers.customer_mobile_number',
          'trn_store_customers.place',
          'mst_stores.store_id',
          'mst_stores.store_name',
          'mst_stores.store_code',
          'mst_stores.store_mobile',
          'mst_towns.town_name',
          'trn_store_customers.town_id'

        )
          ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn__recently_visited_stores.customer_id')
          ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn__recently_visited_stores.store_id')
          ->leftjoin('mst_towns', 'mst_towns.town_id', '=', 'trn_store_customers.town_id');

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn__recently_visited_stores.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn__recently_visited_stores.created_at', '<=', $a2);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn__recently_visited_stores.store_id', $request->store_id);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('mst_stores.subadmin_id', $request->subadmin_id);
        }


        if (isset($request->customer_id)) {
          $data = $data->where('trn__recently_visited_stores.customer_id', $request->customer_id);
        }

        if (isset($request->town_id)) {
          $data = $data->where('trn_store_customers.town_id', $request->town_id);
        }

        $data = $data->where('mst_stores.subadmin_id', auth()->user()->id)
          ->groupBy('mst_stores.store_id', 'trn__recently_visited_stores.customer_id', DB::raw("DATE_FORMAT(trn__recently_visited_stores.created_at, '%d-%m-%Y')"))
          ->orderBy('trn__recently_visited_stores.rvs_id', 'DESC')
          ->paginate(10);
      }

      return view('admin.masters.reports.store_visit_report', compact('stores', 'subadmins', 'customers', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function showSalesReport(Request $request)
  {
    //echo "working...";die;
    try {

      $pageTitle = "Sales Reports";

      if (auth()->user()->user_role_id  == 0) {
        $stores = Mst_store::orderby('store_id', 'DESC')->get();
      } else {
        $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
      }

      $customers = Trn_store_customer::all();
      $subadmins = User::where('user_role_id', '!=', 0)->get();
      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->groupBy('mst_store_link_delivery_boys.delivery_boy_id')
        ->get();

      $orderStatus = Sys_store_order_status::all();


      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.reward_points_used_store',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',
        'trn_store_orders.delivery_date',
        'trn_store_orders.delivery_time',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_stores.subadmin_id',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if (auth()->user()->user_role_id  != 0) {
        // $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
        $data = $data->where('trn_store_orders.subadmin_id', '=', auth()->user()->id);
      }

      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;
        // dd($request->all());
        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }

        if (isset($request->customer_id)) {
          $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('mst_stores.subadmin_id', '=', $request->subadmin_id);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn_store_orders.store_id', '=', $request->store_id);
        }
      }

      $data = $data->orderBy('trn_store_orders.order_id', 'DESC')
        ->paginate(10);



      return view('admin.masters.reports.sales_report', compact('orderStatus', 'deliveryBoys', 'stores', 'subadmins', 'customers', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function showOnlineSalesReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Online Sales Reports";
      $datefrom = '';
      $dateto = '';

      if (auth()->user()->user_role_id  == 0) {
        $stores = Mst_store::orderby('store_id', 'DESC')->get();
      } else {
        $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
      }

      $customers = Trn_store_customer::all();
      $subadmins = User::where('user_role_id', '!=', 0)->get();

      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->groupBy('mst_store_link_delivery_boys.delivery_boy_id')
        ->get();

      $orderStatus = Sys_store_order_status::all();


      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.reward_points_used_store',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',
        'trn_store_orders.delivery_date',
        'trn_store_orders.delivery_time',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_stores.subadmin_id',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if (auth()->user()->user_role_id  != 0) {
        $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
        $data = $data->where('trn_store_orders.subadmin_id', '=', auth()->user()->id);
      }


      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }


        if (isset($request->customer_id)) {
          $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('mst_stores.subadmin_id', '=', $request->subadmin_id);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn_store_orders.store_id', '=', $request->store_id);
        }
      }

      $data = $data->where('trn_store_orders.order_type', 'APP')
        ->orderBy('trn_store_orders.order_id', 'DESC')
        ->paginate(10);


      return view('admin.masters.reports.online_sales_report', compact('subadmins', 'stores', 'orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function showOfflineSalesReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Offline Sales Reports";
      $datefrom = '';
      $dateto = '';


      if (auth()->user()->user_role_id  == 0) {
        $stores = Mst_store::orderby('store_id', 'DESC')->get();
      } else {
        $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
      }

      $customers = Trn_store_customer::all();
      $subadmins = User::where('user_role_id', '!=', 0)->get();

      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->get();

      $orderStatus = Sys_store_order_status::all();



      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_stores.subadmin_id',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if (auth()->user()->user_role_id  != 0) {
        // $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
        $data = $data->where('trn_store_orders.subadmin_id', '=', auth()->user()->id);
      }



      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }


        if (isset($request->customer_id)) {
          $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('mst_stores.subadmin_id', '=', $request->subadmin_id);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn_store_orders.store_id', '=', $request->store_id);
        }
      }

      $data = $data->where('trn_store_orders.order_type', 'POS')
        ->orderBy('trn_store_orders.order_id', 'DESC')
        ->get();


      return view('admin.masters.reports.offline_sales_report', compact('subadmins', 'stores', 'orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function paymentReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Payment Reports";
      $datefrom = '';
      $dateto = '';

      if (auth()->user()->user_role_id  == 0) {
        $stores = Mst_store::orderby('store_id', 'DESC')->get();
      } else {
        $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
      }

      $customers = Trn_store_customer::all();
      $subadmins = User::where('user_role_id', '!=', 0)->get();

      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->get();

      $orderStatus = Sys_store_order_status::all();



      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.trn_id',

        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.reward_points_used_store',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',
        'trn_store_orders.delivery_date',
        'trn_store_orders.delivery_time',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_stores.subadmin_id',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if (auth()->user()->user_role_id  != 0) {
        // $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
        $data = $data->where('trn_store_orders.subadmin_id', '=', auth()->user()->id);
      }



      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }


        if (isset($request->customer_id)) {
          $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }


        if (isset($request->subadmin_id)) {
          $data = $data->where('trn_store_orders.subadmin_id', '=', $request->subadmin_id);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn_store_orders.store_id', '=', $request->store_id);
        }
      }

      $data = $data->where('trn_store_orders.order_type', 'APP')
        ->orderBy('trn_store_orders.order_id', 'DESC')
        ->paginate(10);


      return view('admin.masters.reports.payment_report', compact('subadmins', 'stores', 'orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }


  public function deliveryReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Delivery Reports";
      $datefrom = '';
      $dateto = '';


      if (auth()->user()->user_role_id  == 0) {
        $stores = Mst_store::orderby('store_id', 'DESC')->get();
      } else {
        $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
      }

      $subadmins = User::where('user_role_id', '!=', 0)->get();

      $customers = Trn_store_customer::all();

      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->get();

      $orderStatus = Sys_store_order_status::all();



      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.reward_points_used_store',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',
        'trn_store_orders.delivery_date',
        'trn_store_orders.delivery_time',


        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_stores.subadmin_id',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');
      $data = $data->where('trn_store_orders.status_id', '=', 9);

      if (auth()->user()->user_role_id  != 0) {
        // $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
        $data = $data->where('trn_store_orders.subadmin_id', '=', auth()->user()->id);
      }

      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }


        if (isset($request->customer_id)) {
          $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('trn_store_orders.subadmin_id', '=', $request->subadmin_id);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn_store_orders.store_id', '=', $request->store_id);
        }
      }

      $data = $data->orderBy('trn_store_orders.order_id', 'DESC')
        ->paginate(10);


      return view('admin.masters.reports.delivery_report', compact('subadmins', 'stores', 'orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }
  public function deliveryBoyPayoutReport(Request $request)
  {
    // echo "working...";die;
    try {

      $pageTitle = "Delivery Boy Payout Reports";
      $datefrom = '';
      $dateto = '';
      $total_count = 0;


      if (auth()->user()->user_role_id  == 0) {
        $stores = Mst_store::orderby('store_id', 'DESC')->get();
      } else {
        $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
      }

      $subadmins = User::where('user_role_id', '!=', 0)->get();

      $customers = Trn_store_customer::all();

      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->get();

      $orderStatus = Sys_store_order_status::all();



      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.reward_points_used_store',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',
        'trn_store_orders.delivery_date',
        'trn_store_orders.delivery_time',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',

        'mst_delivery_boys.delivery_boy_name',
        'mst_delivery_boys.delivery_boy_mobile',
        'mst_delivery_boys.delivery_boy_commision',
        'mst_delivery_boys.delivery_boy_commision_amount'




      )
        ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'trn_store_orders.delivery_boy_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if (auth()->user()->user_role_id  != 0) {
        // $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
        $data = $data->where('trn_store_orders.subadmin_id', '=', auth()->user()->id);
      }
      $data = $data->where('trn_store_orders.delivery_status_id', '=', 3)
        ->whereNotNull('mst_delivery_boys.delivery_boy_name');

      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }


        if (isset($request->customer_id)) {
          $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('trn_store_orders.subadmin_id', '=', $request->subadmin_id);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn_store_orders.store_id', '=', $request->store_id);
        }
      }

      $data = $data->orderBy('trn_store_orders.order_id', 'DESC')->get();
      $check_array = [];
      $i = 0;
      $tot_pre = [];
      $tot_now = [];
      $tot_prev_count = [];
      $tot_now_count = [];
      // $cm=[];
      // $co=[];
      //dd(count($data));
      $tot_prev_count[0] = 0;
      $tot_now_count[0] = 0;
      $prev_amount[0] = 0;

      $prev_amount = []; // Use an associative array to store previous amounts for each delivery boy
      /* foreach ($data->reverse() as $d) {
        $i++;
        $total_count = Trn_store_order::whereIn('order_id', $check_array)
            ->where('delivery_boy_id', @$d->delivery_boy_id)
            ->orderBy('order_id', 'DESC')
            ->count();
    
        $orlink = Mst_order_link_delivery_boy::where('order_id', $d->order_id)
            ->where('delivery_boy_id', @$d->delivery_boy_id)
            ->first();
    
        $tot_now_count[$i] = $total_count;
        $tot_prev_count[$i] = $tot_now_count[$i] - 1;
        $cm = 0;
        $co = 0;
    
        if ($orlink) {
            $cm = $orlink->commision_per_month;
            $co = $orlink->commision_per_order;
        }
    
        $d->previous_amount = $prev_amount[$i-1];
        
        // Check if values are numeric before performing operations
        $prev_amount_numeric = is_numeric($prev_amount[$i-1]) ? (float) $prev_amount[$i-1] : 0;
        $co_numeric = is_numeric($co) ? (float) $co : 0;
    
        $d->new_amount = $prev_amount_numeric + $co_numeric;
        $prev_amount[$i] = $d->new_amount;
        $d->c_month = $cm;
        $d->c_order = $co;
    }*/
      foreach ($data->reverse() as $d) {
        $i++;
        $delivery_boy_id = $d->delivery_boy_id;

        // Use the delivery boy ID as the key in the associative array
        if (!isset($prev_amount[$delivery_boy_id])) {
          $prev_amount[$delivery_boy_id] = 0;
        }

        $total_count = Trn_store_order::whereIn('order_id', $check_array)
          ->where('delivery_boy_id', $delivery_boy_id)
          ->orderBy('order_id', 'DESC')
          ->count();

        $orlink = Mst_order_link_delivery_boy::where('order_id', $d->order_id)
          ->where('delivery_boy_id', $delivery_boy_id)
          ->first();

        $tot_now_count[$i] = $total_count;
        $tot_prev_count[$i] = $tot_now_count[$i] - 1;
        $cm = 0;
        $co = 0;

        if ($orlink) {
          $cm = $orlink->commision_per_month;
          $co = $orlink->commision_per_order;
        }

        $d->previous_amount = $prev_amount[$delivery_boy_id];

        // Check if values are numeric before performing operations
        $prev_amount_numeric = is_numeric($prev_amount[$delivery_boy_id]) ? (float) $prev_amount[$delivery_boy_id] : 0;
        $co_numeric = is_numeric($co) ? (float) $co : 0;

        $d->new_amount = $prev_amount_numeric + $co_numeric;
        $prev_amount[$delivery_boy_id] = $d->new_amount;
        $d->c_month = $cm;
        $d->c_order = $co;
      }
      //dd($check_array,$tot_now_count,$tot_prev_count);



      return view('admin.masters.reports.deliveryboy_payout_report', compact('subadmins', 'stores', 'orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle', 'tot_now_count', 'tot_prev_count', 'check_array', 'tot_pre', 'tot_now', 'total_count'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  //wallet redeem reports
  public function showRedeemReports(Request $request)
  {

    try {

      $pageTitle = "Wallet Points Redeem Reports";
      $datefrom = '';
      $dateto = '';


      if (auth()->user()->user_role_id  == 0) {
        $stores = Mst_store::orderby('store_id', 'DESC')->get();
      } else {
        $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
      }


      $subadmins = User::where('user_role_id', '!=', 0)->get();

      $customers = Trn_store_customer::all();

      $deliveryBoys =  Mst_store_link_delivery_boy::join('mst_delivery_boys', 'mst_delivery_boys.delivery_boy_id', '=', 'mst_store_link_delivery_boys.delivery_boy_id')
        ->get();

      $orderStatus = Sys_store_order_status::all();



      $data = Trn_store_order::select(

        'trn_store_orders.order_id',
        'trn_store_orders.order_number',
        'trn_store_orders.customer_id',
        'trn_store_orders.store_id',
        'trn_store_orders.subadmin_id',
        'trn_store_orders.product_total_amount',
        'trn_store_orders.delivery_charge',
        'trn_store_orders.packing_charge',
        'trn_store_orders.payment_type_id',
        'trn_store_orders.status_id',
        'trn_store_orders.payment_status',
        'trn_store_orders.delivery_status_id',
        'trn_store_orders.delivery_boy_id',
        'trn_store_orders.coupon_id',
        'trn_store_orders.coupon_code',
        'trn_store_orders.reward_points_used',
        'trn_store_orders.reward_points_used_store',
        'trn_store_orders.amount_before_applying_rp',
        'trn_store_orders.trn_id',
        'trn_store_orders.created_at',
        'trn_store_orders.amount_reduced_by_coupon',
        'trn_store_orders.order_type',

        'trn_store_customers.customer_id',
        'trn_store_customers.customer_first_name',
        'trn_store_customers.customer_last_name',
        'trn_store_customers.customer_mobile_number',
        'trn_store_customers.place',

        'mst_stores.store_id',
        'mst_stores.store_name',
        'mst_stores.store_code',
        'mst_stores.store_mobile',
        'mst_stores.subadmin_id'



      )
        ->join('trn_store_customers', 'trn_store_customers.customer_id', '=', 'trn_store_orders.customer_id')
        ->leftjoin('mst_stores', 'mst_stores.store_id', '=', 'trn_store_orders.store_id');

      if (auth()->user()->user_role_id  != 0) {
        $data = $data->where('trn_store_orders.subadmin_id', '=', auth()->user()->id);
      }

      if ($_GET) {
        $datefrom = $request->date_from;
        $dateto = $request->date_to;

        $a1 = Carbon::parse($request->date_from)->startOfDay();
        $a2  = Carbon::parse($request->date_to)->endOfDay();

        if (isset($request->date_from)) {
          $data = $data->whereDate('trn_store_orders.created_at', '>=', $a1);
        }

        if (isset($request->date_to)) {
          $data = $data->whereDate('trn_store_orders.created_at', '<=', $a2);
        }


        if (isset($request->customer_id)) {
          $data = $data->where('trn_store_orders.customer_id', '=', $request->customer_id);
        }

        if (isset($request->delivery_boy_id)) {
          $data = $data->where('trn_store_orders.delivery_boy_id', '=', $request->delivery_boy_id);
        }

        if (isset($request->status_id)) {
          $data = $data->where('trn_store_orders.status_id', '=', $request->status_id);
        }

        if (isset($request->order_type)) {
          $data = $data->where('trn_store_orders.order_type', '=', $request->order_type);
        }

        if (isset($request->subadmin_id)) {
          $data = $data->where('trn_store_orders.subadmin_id', '=', $request->subadmin_id);
        }

        if (isset($request->store_id)) {
          $data = $data->where('trn_store_orders.store_id', '=', $request->store_id);
        }
      }

      $data = $data->where('reward_points_used', '!=', NULL)->where('reward_points_used', '>', '0.00')->orderBy('trn_store_orders.order_id', 'DESC')
        ->paginate(10);
      //DD($request->store_id,$request->subadmin_id,$data);

      return view('admin.masters.reports.redeem_report', compact('subadmins', 'stores', 'orderStatus', 'deliveryBoys', 'customers', 'dateto', 'datefrom', 'data', 'pageTitle'));
    } catch (\Exception $e) {
      return redirect()->back()->withErrors([$e->getMessage()])->withInput();
      return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
    }
  }

  public function showInventoryReportLegacy(Request $request)
  {
    //echo "working..";die;
    $pageTitle = "Inventory Reports";
    $datefrom = '';
    $dateto = '';
    if (auth()->user()->user_role_id  == 0) {
      $stores = Mst_store::orderby('store_id', 'DESC')->get();
    } else {
      $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
    }

    $subadmins = User::where('user_role_id', '!=', 0)->get();


    $products = Mst_store_product::join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
      ->select('mst_store_products.product_id', 'mst_store_products.product_name')
      //->where('mst_store_products.store_id',Auth::guard('store')->user()->store_id)
      ->orderBy('mst_store_products.product_id', 'DESC')->get();

    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_categories::orderBy('category_id', 'DESC')->where('category_status', 1)->get();
    $subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();



    $data =   Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
      ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
      ->leftjoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
      ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
      ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
      ->join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id')

      ->where('mst__stock_details.stock', '>', 0)

      ->where('mst_store_products.product_type', 1)
      //   ->where('mst_store_products.is_removed', 0)
      ->where('mst_store_product_varients.is_removed', 0)

      ->select(
        'mst_store_products.product_id',
        'mst_store_products.product_name',
        'mst_store_products.product_code',
        'mst_store_products.product_cat_id',
        'mst_store_products.product_base_image',
        'mst_store_products.product_status',
        'mst_store_products.product_brand',
        'mst_store_products.min_stock',

        'mst_store_products.tax_id',
        'mst_store_product_varients.product_varient_id',
        'mst_store_product_varients.variant_name',
        'mst_store_product_varients.product_varient_price',
        'mst_store_product_varients.product_varient_offer_price',
        'mst_store_product_varients.product_varient_base_image',
        'mst_store_product_varients.is_base_variant',
        'mst_store_product_varients.variant_status',
        'mst_store_product_varients.stock_count',
        'mst_store_product_varients.created_at',
        'mst_store_categories.category_id',
        'mst_store_categories.category_name',
        'mst__stock_details.stock',
        'mst__stock_details.prev_stock',
        'mst__stock_details.created_at AS updated_time',
        'mst_store_agencies.agency_name',
        'mst__sub_categories.sub_category_name',

        'mst_stores.store_id',
        'mst_stores.subadmin_id',
        'mst_stores.store_name',
        'mst_stores.store_code',

      );

    if (auth()->user()->user_role_id  != 0) {
      $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
    }

    if ($_GET) {
      $datefrom = $request->date_from;
      $dateto = $request->date_to;

      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();

      // if(isset($request->date_from))
      // {
      //   $data = $data->whereDate('trn_store_orders.created_at','>=',$a1);
      // }

      // if(isset($request->date_to))
      // {
      //   $data = $data->whereDate('trn_store_orders.created_at','<=',$a2);
      // }

      if (isset($request->product_id)) {
        $data = $data->where('mst_store_products.product_id', $request->product_id);
      }

      if (isset($request->vendor_id)) {
        $data = $data->where('mst_store_agencies.agency_id', $request->vendor_id);
      }

      if (isset($request->category_id)) {
        $data = $data->where('mst_store_categories.category_id', $request->category_id);
      }

      if (isset($request->sub_category_id)) {
        $data = $data->where('mst__sub_categories.sub_category_id', $request->sub_category_id);
      }


      if (isset($request->subadmin_id)) {
        $data = $data->where('mst_stores.subadmin_id', '=', $request->subadmin_id);
      }

      if (isset($request->store_id)) {
        $data = $data->where('mst_stores.store_id', '=', $request->store_id);
      }
    }

    $data = $data->orderBy('updated_time', 'DESC')->get();
    //   

    //  dd($inventoryData);

    $data = collect($data);
    $data = $data->unique('product_varient_id');
    $data =   $data->values()->all();

    return view('admin.masters.reports.inventory_report', compact('stores', 'subadmins', 'subCategories', 'categories', 'agencies', 'products', 'dateto', 'datefrom', 'data', 'pageTitle'));
  }
  public function showInventoryReport(Request $request)
  {
    //echo "working..";die;
    $pageTitle = "Inventory Reports";
    $datefrom = '';
    $dateto = '';
    if (auth()->user()->user_role_id  == 0) {
      $stores = Mst_store::orderby('store_id', 'DESC')->get();
    } else {
      $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
    }

    $subadmins = User::where('user_role_id', '!=', 0)->get();


    $products = Mst_store_product::join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
      ->select('mst_store_products.product_id', 'mst_store_products.product_name')
      ->orderBy('mst_store_products.product_id', 'DESC')->get();

    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_categories::orderBy('category_id', 'DESC')->where('category_status', 1)->get();
    $subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();



    $data =   Mst_store_product_varient::leftjoin('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
      ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
      ->leftjoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
      ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
      ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
      ->join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id')

      ->where('mst__stock_details.stock', '>', 0)

      ->where('mst_store_products.product_type', 1)
      ->where('mst_store_product_varients.is_removed', 0)

      ->select(
        'mst_store_products.product_id',
        'mst_store_products.product_name',
        'mst_store_products.product_code',
        'mst_store_products.product_cat_id',
        'mst_store_products.product_base_image',
        'mst_store_products.product_status',
        'mst_store_products.product_brand',
        'mst_store_products.min_stock',

        'mst_store_products.tax_id',
        'mst_store_products.sub_category_id',
        'mst_store_product_varients.product_varient_id',
        'mst_store_product_varients.variant_name',
        'mst_store_product_varients.product_varient_price',
        'mst_store_product_varients.product_varient_offer_price',
        'mst_store_product_varients.product_varient_base_image',
        'mst_store_product_varients.stock_count',
        'mst_store_product_varients.created_at',
        'mst_store_product_varients.is_base_variant',
        'mst_store_product_varients.variant_status',
        'mst_store_categories.category_id',
        'mst_store_categories.category_name',
        'mst__stock_details.stock',
        'mst__stock_details.prev_stock',
        'mst__stock_details.updated_at AS updated_time',
        'mst_store_agencies.agency_name',
        'mst__sub_categories.sub_category_name',

        'mst_stores.store_id',
        'mst_stores.subadmin_id',
        'mst_stores.store_name',
        'mst_stores.store_code',

      );

    if (auth()->user()->user_role_id  != 0) {
      $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
    }

    if ($_GET) {
      $datefrom = $request->date_from;
      $dateto = $request->date_to;

      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();

      if (isset($request->product_id)) {
        $data = $data->where('mst_store_products.product_id', $request->product_id);
      }

      if (isset($request->vendor_id)) {
        $data = $data->where('mst_store_agencies.agency_id', $request->vendor_id);
      }

      if (isset($request->category_id)) {
        $data = $data->where('mst_store_categories.category_id', $request->category_id);
      }

      if (isset($request->sub_category_id)) {
        $data = $data->where('mst_store_products.sub_category_id', $request->sub_category_id);
      }


      if (isset($request->subadmin_id)) {
        $data = $data->where('mst_stores.subadmin_id', '=', $request->subadmin_id);
      }

      if (isset($request->store_id)) {
        $data = $data->where('mst_stores.store_id', '=', $request->store_id);
      }
    }

    $data = $data->orderBy('updated_time', 'DESC')->paginate(10);

    foreach ($data as $da) {
      if (is_null($da->sub_category_name)) {
        $da->sub_category_name = "Others";
      }
      $stock_info = Mst_StockDetail::where('product_varient_id', $da->product_varient_id)->orderBy('stock_detail_id', 'DESC')->first();
      if ($stock_info) {
        $da->prev_stock = $stock_info->prev_stock;
        $da->stock = $stock_info->stock;
        $da->prev_stock = (string)$da->prev_stock;
        $da->stock = (string)$da->stock;
        $da->updated_time = Carbon::parse($stock_info->created_at)->format('Y-m-d H:i:s');
      }
    }

    $data = $data->sortByDesc(function ($item) {
      return $item->updated_time;
    });

    $data = collect($data);
    $data = $data->unique('product_varient_id');
    $data = $data->values()->all();

    $perPage = 10;
    $currentPage = $request->page ?? 1;
    $totalResults = count($data);
    $startIndex = ($currentPage - 1) * $perPage;
    $paginatedData = array_slice($data, $startIndex, $perPage);

    $data = new LengthAwarePaginator(
      $paginatedData,
      $totalResults,
      $perPage,
      $currentPage,
      ['path' => $request->url()]
    );
    //return $data;

    return view('admin.masters.reports.inventory_report', compact('stores', 'subadmins', 'subCategories', 'categories', 'agencies', 'products', 'dateto', 'datefrom', 'data', 'pageTitle'));
  }

  public function showOutofStockReport(Request $request)
  {
    $pageTitle = "Out of Stock Reports";
    $datefrom = '';
    $dateto = '';
    //dd('test1');

    $storesQuery = Mst_store::orderBy('store_id', 'DESC');
    if (auth()->user()->user_role_id != 0) {
      $storesQuery->where('subadmin_id', auth()->user()->id);
    }
    $stores = $storesQuery->get();
    //dd('test2');

    $subadmins = User::where('user_role_id', '!=', 0)->get();

    $products = [];
    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_categories::orderBy('category_id', 'DESC')->where('category_status', 1)->get();
    $subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();

    $dataQuery = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
      ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
      ->join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id')
      ->leftJoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
      ->leftJoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
      ->leftJoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')
      ->where('mst_store_product_varients.stock_count', '<=', 0)
      ->where('mst_store_products.product_type', 1)
      ->where('mst_store_products.is_removed', 0)
      ->where('mst_store_product_varients.is_removed', 0);

    if (auth()->user()->user_role_id != 0) {
      $dataQuery->where('mst_stores.subadmin_id', '=', auth()->user()->id);
    }

    if ($request->has('date_from') && $request->has('date_to')) {
      $dataQuery->whereBetween('mst__stock_details.created_at', [$request->date_from, $request->date_to]);
    }

    $data = $dataQuery->orderBy('mst__stock_details.created_at', 'DESC')
      ->groupBy('mst_store_product_varients.product_varient_id')
      ->paginate(10);


    foreach ($data as $da) {
      if (is_null($da->sub_category_name)) {
        $da->sub_category_name = "Others";
      }
    }


    return view('admin.masters.reports.out_of_stock_report', compact('stores', 'subadmins', 'subCategories', 'categories', 'agencies', 'products', 'dateto', 'datefrom', 'data', 'pageTitle'));
  }

  public function showOutofStockReportPrevious(Request $request)
  {
    //echo "working..";die;
    $pageTitle = "Out of Stock Reports";
    $datefrom = '';
    $dateto = '';


    if (auth()->user()->user_role_id  == 0) {
      $stores = Mst_store::orderby('store_id', 'DESC')->get();
    } else {
      $stores = Mst_store::where('subadmin_id', auth()->user()->id)->orderBy('store_id', 'desc')->get();
    }

    $subadmins = User::where('user_role_id', '!=', 0)->get();

    // $products = Mst_store_product::join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
    //   ->select('mst_store_products.product_id', 'mst_store_products.product_name')
    //   // ->where('mst_store_products.store_id',Auth::guard('store')->user()->store_id)
    //   ->orderBy('mst_store_products.product_id', 'DESC')->get();
    $products = [];

    $agencies = Mst_store_agencies::orderBy('agency_id', 'DESC')->where('agency_account_status', 1)->get();
    $categories = Mst_categories::orderBy('category_id', 'DESC')->where('category_status', 1)->get();
    $subCategories = Mst_SubCategory::orderBy('sub_category_id', 'DESC')->where('sub_category_status', 1)->get();


    $data = Mst_store_product_varient::join('mst_store_products', 'mst_store_products.product_id', '=', 'mst_store_product_varients.product_id')
      ->join('mst_store_categories', 'mst_store_categories.category_id', '=', 'mst_store_products.product_cat_id')
      ->join('mst_stores', 'mst_stores.store_id', '=', 'mst_store_products.store_id')

      ->leftjoin('mst__stock_details', 'mst__stock_details.product_varient_id', '=', 'mst_store_product_varients.product_varient_id')
      ->leftjoin('mst_store_agencies', 'mst_store_agencies.agency_id', '=', 'mst_store_products.vendor_id')
      ->leftjoin('mst__sub_categories', 'mst__sub_categories.sub_category_id', '=', 'mst_store_products.sub_category_id')

      //->where('mst_store_products.store_id',$store_id)
      ->where('mst_store_product_varients.stock_count', '<=', 0)
      ->where('mst_store_products.product_type', 1)
      // ->orderBy('mst_store_products.product_name','ASC')
      //->orderBy('mst_store_product_varients.stock_count', 'ASC')
      ->where('mst_store_products.is_removed', 0)
      ->where('mst_store_product_varients.is_removed', 0)


      ->select(
        'mst_store_products.product_id',
        'mst_store_products.product_name',
        'mst_store_products.product_code',
        'mst_store_products.product_cat_id',
        'mst_store_products.product_base_image',
        'mst_store_products.product_status',
        'mst_store_products.product_brand',
        'mst_store_products.min_stock',
        'mst_store_products.sub_category_id',

        'mst_store_products.tax_id',
        'mst_store_product_varients.product_varient_id',
        'mst_store_product_varients.variant_name',
        'mst_store_product_varients.product_varient_price',
        'mst_store_product_varients.product_varient_offer_price',
        'mst_store_product_varients.product_varient_base_image',
        'mst_store_product_varients.stock_count',
        // 'mst_store_product_varients.created_at',
        'mst__stock_details.created_at',
        'mst_store_categories.category_id',
        'mst_store_categories.category_name',
        'mst__stock_details.stock',
        'mst__stock_details.prev_stock',
        'mst__stock_details.created_at AS updated_time',
        'mst__stock_details.product_varient_id as stocked_varient',
        'mst_store_agencies.agency_name',
        'mst__sub_categories.sub_category_name',

        'mst_stores.store_id',
        'mst_stores.subadmin_id',
        'mst_stores.store_name',
        'mst_stores.store_code',

      );

    if (auth()->user()->user_role_id  != 0) {
      $data = $data->where('mst_stores.subadmin_id', '=', auth()->user()->id);
    }
    //$data=$data->where('mst__stock_details.stocked_varient', '!=', NULL);


    if ($_GET) {
      $datefrom = $request->date_from;
      $dateto = $request->date_to;

      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();

      // if(isset($request->date_from))
      // {
      //   $data = $data->whereDate('trn_store_orders.created_at','>=',$a1);
      // }

      // if(isset($request->date_to))
      // {
      //   $data = $data->whereDate('trn_store_orders.created_at','<=',$a2);
      // }

      if (isset($request->product_id)) {
        $data = $data->where('mst_store_products.product_id', $request->product_id);
      }

      if (isset($request->vendor_id)) {
        $data = $data->where('mst_store_agencies.agency_id', $request->vendor_id);
      }

      if (isset($request->category_id)) {
        $data = $data->where('mst_store_categories.category_id', $request->category_id);
      }

      if (isset($request->sub_category_id)) {
        $data = $data->where('mst_store_products.sub_category_id', $request->sub_category_id);
      }

      if (isset($request->subadmin_id)) {
        $data = $data->where('mst_stores.subadmin_id', '=', $request->subadmin_id);
      }

      if (isset($request->store_id)) {
        $data = $data->where('mst_stores.store_id', '=', $request->store_id);
      }
    }

    //old
    // $data = $data->orderBy('updated_time', 'DESC')->get();
    $data = $data->orderBy('updated_time', 'DESC');
    $data = $data->groupBy('mst_store_product_varients.product_varient_id')->paginate(10);
    //dd($data);
    // dd(1);
    foreach ($data as $da) {
      if (is_null($da->sub_category_name)) {
        $da->sub_category_name = "Others";
      }
    }


    //   dd($data);

    return view('admin.masters.reports.out_of_stock_report', compact('stores', 'subadmins', 'subCategories', 'categories', 'agencies', 'products', 'dateto', 'datefrom', 'data', 'pageTitle'));
  }







  public function showReferalReport(Request $request)
  {
    //echo "working..";die;
    $pageTitle = "Referral Reports";

    $data = Trn_store_customer::select(
      'customer_first_name',
      'customer_last_name',
      'customer_mobile_number',
      'referred_by',
    )->where('referred_by', '!=', null)->where('referred_by', '!=', 0);


    if ($_GET) {
      $datefrom = $request->date_from;
      $dateto = $request->date_to;

      $a1 = Carbon::parse($request->date_from)->startOfDay();
      $a2  = Carbon::parse($request->date_to)->endOfDay();

      if (isset($request->date_from)) {
        $data = $data->whereDate('created_at', '>=', $a1);
      }

      if (isset($request->date_to)) {
        $data = $data->whereDate('created_at', '<=', $a2);
      }
    }
    $data = $data->get();


    return view('admin.masters.reports.referal_reports', compact('data', 'pageTitle'));
  }
}
