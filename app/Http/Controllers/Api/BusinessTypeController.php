<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Mst_business_types;

class BusinessTypeController extends Controller
{
    public function typeList(Request $request)
    {
        $data = array();
        try {

            $data['Business_Types'] = Mst_business_types::withTrashed()->select('business_type_id', 'business_type_name')
                //->where('business_type_status', 1)
                ->orderBy('business_type_name', 'ASC')->get();
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];

            return response($response);
        }
    }
    public function typeListRegister(Request $request)
    {
        $data = array();
        try {

            $data['Business_Types'] = Mst_business_types::select('business_type_id', 'business_type_name')
                ->where('business_type_status', 1)
                ->orderBy('business_type_name', 'ASC')->get();
            return response($data);
        } catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        } catch (\Throwable $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];

            return response($response);
        }
    }
}
