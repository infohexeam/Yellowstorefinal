<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Mst_store_agencies;

class AgencyController extends Controller
{
    public function list(Request $request)
    {
        $data = array(); 
        
        try {

            $data['agencyDetails'] = Mst_store_agencies::select('*')->where('agency_account_status',1)->orderBy('agency_id','DESC')->get();

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
