<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Town;

class TownController extends Controller
{
    public function townList(Request $request)
    {
        $data = array();
        try {
        	if(isset($request->district_id))
        	{
            	$districtId = $request->district_id;
    
                $data['Town_List'] = Town::where('district_id','=',$districtId)->select('town_id','town_name')->orderBy('town_name','ASC')->get();
                return response($data); 
        	}
        	else
        	{
        	    $data['Town_List'] = Town::orderBy('town_name','ASC')->get();
                return response($data); 
        	}
           
        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];

            return response($response);
        }
    }
}
