<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\admin\Country;
use App\Models\admin\State;
use App\Models\admin\District;

class LocationController extends Controller
{
    public function countryList(Request $request)
    {
        $data = array();
        try {

            $data['contryList'] = Country::select('country_id','country_name')->orderBy('country_name','ASC')->get();
            return response($data);  
           
        }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];

            return response($response);
        }
    }

    public function stateList(Request $request)
    {
    	$data = array();
        try {

	    	$countryId = $request->country_id;
	    	$data['stateList'] = State::where('country_id','=',$countryId)->select('state_id','state_name')->orderBy('state_name','ASC')->get();
	    	return response($data);

	    }catch (\Exception $e) {
           $response = ['status' => '0', 'message' => $e->getMessage()];
           return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];

            return response($response);
        }
    }
    
     public function districtList(Request $request)
    {
    	$data = array();
        try {

	    	$stateId = $request->state_id;
	    	$data['distirctList'] = District::where('state_id','=',$stateId)->select('district_id','district_name')->orderBy('district_name','ASC')->get();
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
