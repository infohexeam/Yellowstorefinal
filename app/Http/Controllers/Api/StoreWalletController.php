<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\admin\Trn_configure_points;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreWalletController extends Controller
{
    public function getStoreConfigurePoints(Request $request)
    {
        $data = array(); 
        try {
        $store_id=$request->store_id;
		$configure_points = Trn_configure_points::Where('store_id',$store_id)->first();
            if (isset($configure_points)) {
                $data['status']=1;
                $data['details']=$configure_points;
                $data['message']="configure points fetched";
            } else {
                $data['status']=0;
                $data['details']=$configure_points;
                $data['message']="configure points not fetched";
            }
            return response($data);
           
        }catch (\Exception $e) {
            $response = ['status' => '0', 'message' => $e->getMessage()];
            return response($response);
        }catch (\Throwable $e) {
            $response = ['status' => '0','message' => $e->getMessage()];
            return response($response);
        }
    }
    public function storeConfigurePoints(Request $request)
    {
        
        $store_id=$request->store_id;
        $data=array();
		$validator = Validator::make(
			$request->all(),
			[
				
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
				
				'referal_points.required'          => 'Referal required',
				
				'rupee_points.required'          => 'Ruppes to points required',
				'order_amount.required'          => 'Order amount required',
			]
		);

		if (!$validator->fails()) {

           
			$points = Trn_configure_points::where('store_id',$store_id)->first();
			if (isset($points)) {
				// $points->points = $request->points;
                $points->store_id = $store_id;
				$points->first_order_points = $request->first_order_points??0;
				$points->referal_points = $request->referal_points;
				$points->registraion_points = $request->registraion_points??0;
				$points->rupee = $request->rupee;
				$points->rupee_points = $request->rupee_points;
				$points->order_amount = $request->order_amount;
				$points->order_points = $request->order_points;
				$points->redeem_percentage = $request->redeem_percentage;
				$points->max_redeem_amount = $request->max_redeem_amount;
				$points->joiner_points = $request->joiner_points;

				$points->update();
                $data['status']=1;
                $data['message']="Points updated";
                

			} else {
				$points = new Trn_configure_points;
                $points->store_id = $store_id;
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
                $data['status']=1;
                $data['message']="Points added";
			}
			return response($data);
		} else {
			$data['status'] = 0;
            $data['message'] = "failed";
            $data['errors'] = $validator->errors();
            return response($data);
		}

    }

    
}
