<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Mst_categories;

class CategoryController extends Controller
{
    public function list(Request $request)
    {
        $data = array();
        
        try {

            if (isset($request->store_id)) {
                $storeData = Mst_store::find($request->store_id);
                $data['catDetails'] =  Mst_categories::join('trn__category_business_types', 'trn__category_business_types.category_id', '=', 'mst_store_categories.category_id')
                    ->where('trn__category_business_types.business_type_id',  @$storeData->business_type_id)
                    ->where('mst_store_categories.category_status', 1)
                    ->get();
            } else {
                $data['catDetails'] = Mst_categories::select('category_id')->where('category_status', 1)->orderBy('category_id', 'DESC')->get();
            }

            $data['status'] = 1;
            $data['message'] = "success";
            return response($data);
        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];

            return response($response);
        }
    }
}
