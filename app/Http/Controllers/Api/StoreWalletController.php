<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\admin\Mst_store;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_CustomerDeviceToken;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Trn_store_order;
use App\Trn_wallet_log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
                'minimum_order_amount'        => 'numeric|gt:0',
                //'redeem_percentage'    => 'numeric|max:100',

				//'points'          => 'required',
			],
			[
				'order_points.required'          => 'Rupee required',
				'order_points.required'          => 'Order points required',
				//  'points.required'          => 'Points required',
				
				'referal_points.required'          => 'Referal required',
				
				'rupee_points.required'          => 'Rupees to points required',
				'order_amount.required'          => 'Order amount required',
                //'redeem_percentage'    => 'Percentage should not exceed 100',
			]
		);

        if($request->redeem_percentage > 100)
        {
            $data['status'] = 0;
            $data['message'] = "Percentage should not exceed 100";
            return response($data);
        }

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
                $points->minimum_order_amount = $request->minimum_order_amount;
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
                $points->minimum_order_amount = $request->minimum_order_amount;
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
    public function listStoreCustomerRewards(Request $request)
	{
        $data=array();
        $store_id=$request->store_id;
        /*if($store_id!=NULL)
        {
		$customer_rewards = Trn_customer_reward::leftjoin('trn_store_orders','trn_customer_rewards.order_id','=','trn_store_orders.order_id')->orderBy('reward_id', 'DESC')
        ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', 'trn_customer_rewards.customer_id')
        ->where('trn_customer_rewards.store_id',$store_id)
        ->whereNotNull('trn_customer_rewards.store_id')
        ->get();
        }
        else
        {
            $data['status']=0;
            $data['message']="Store id required";
            return response($data);
        }*/

		//if ($_GET) {

			$datefrom = $request->date_from;
			$dateto = $request->date_to;

//test
			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2  = Carbon::parse($request->date_to)->endOfDay();
			$customer_first_name = $request->customer_name;
			//$query = Trn_customer_reward::with(['customer','order'])->Where('trn_customer_rewards.store_id',$store_id);
            //->orWhere('trn_store_orders.store_id',$store_id);
            $query = Trn_customer_reward::with(['customer','order'])->where('store_id',$store_id);
            // $query->whereHas('order', function (Builder $qry) use($store_id) {
            //     return $qry->where('store_id','=',$store_id);
                
            //   });
			// if (isset($request->date_from) && isset($request->date_to)) {
			// 	$query = $query->whereBetween('trn_customer_rewards.created_at', [$a1, $a2]);
			// }
            $a1 = Carbon::parse($request->date_from)->startOfDay();
            $a2  = Carbon::parse($request->date_to)->endOfDay();

            if(isset($request->date_from))
             {
              $query = $query->whereDate('trn_customer_rewards.created_at','>=',$a1);
             }

             if(isset($request->date_to))
             {
                $query = $query->whereDate('trn_customer_rewards.created_at','<=',$a2);
             }

			if (isset($request->customer_name)) {
                $cust_name=$request->customer_name;
                $query=$query->whereHas('customer', function (Builder $qry) use($cust_name) {
                    return $qry->where('customer_first_name','like','%'.$cust_name.'%');
                    
                  });
				
			}

			$customer_rewards = $query->orderBy('reward_id', 'DESC');
            if (isset($request->page)) {
                $customer_rewards = $customer_rewards->paginate(10, ['*'], 'page', $request->page);
            } else {
                $customer_rewards = $customer_rewards->paginate(10);
            }
            if($customer_rewards!=NULL)
            {
                $data['status']=1;
                //return response($customer_rewards->customer_rewards['data']);
                //return $customer_rewards['status'];
            /* foreach($customer_rewards->customer_rewards['data'] as $customer)
                {
                    if($customer->customer_last_name==null)
                    {
                        $customer->customer_last_name='';
                    }
                   
                }*/

                $data['customer_rewards']=$customer_rewards;
                $data['message']="Customer rewards fetched";
                

            }
            else
            {
                $data['status']=0;
                $data['message']="No Customer rewards found";

            }
            return response($data);
           
            



			
		//}


        if($customer_rewards!=NULL)
        {
            $data['status']=1;
           /* foreach($customer_rewards->customer_rewards->data['customer'] as $customer)
            {
                if($customer->customer_last_name==null)
                {
                    $customer->customer_last_name='';
                }
               
            }*/
            $data['customer_rewards']=$customer_rewards;
            $data['message']="Customer rewards fetched";

        }
        else
        {
            $data['status']=0;
            $data['message']="No Customer rewards found";

        }
        return response($data);
	}
    public function getRewardCustomers(Request $request)
    {
        $data=array();
        $store_id=$request->store_id;
        $customer_ids=[];

        $customer_orders=Trn_store_order::where('store_id',$store_id)->get();
    foreach ($customer_orders as $order) {
        $customer_ids[] = $order->customer_id;
    }
    if(empty($customer_ids))
    {
        $data['status']=0;
        $data['customers']=[];
        $data['message']="No eligible customers found";
        return response($data);

    }
       $customers = Trn_store_customer::select('customer_id','customer_first_name','customer_last_name','customer_mobile_number')
                    ->whereIn('customer_id',$customer_ids)
                    ->where('customer_id','!=',3)
                    ->get();
        $data['status']=1;
        foreach($customers as $customer)
        {
            if($customer->customer_last_name==null)
            {
                $customer->customer_last_name='';
            }
           
        }
        $data['customers']=$customers;
        $data['message']="rewared eligible customers fetched";
        return response($data);
       // return redirect()->back()->with('status','Reward added successfully.');
    }
    public function saveRewards(Request $request)
    {
        $store_id=$request->store_id;
      try{ 
          if(isset($request->customer_id))
          {
            $reward = new Trn_customer_reward;
            $reward->store_id=$store_id;
            $reward->transaction_type_id  	= 0;
            $reward->reward_points_earned  	= $request->reward_points;
            $reward->customer_id  	= $request->customer_id;
            $reward->reward_approved_date 		=  Carbon::now()->format('Y-m-d');
            $reward->reward_point_expire_date 		=  Carbon::now()->format('Y-m-d');
            $reward->reward_point_status  	= 1;
            $reward->discription  	= $request->reward_discription;
            $reward->save(); 

            $wallet_log=new Trn_wallet_log();
			$wallet_log->store_id=$request->store_id;
			$wallet_log->customer_id=$request->customer_id;
			$wallet_log->order_id=null;
			$wallet_log->type='credit';
			$wallet_log->points_debited=null;
			$wallet_log->points_credited=$request->reward_points;
            $wallet_log->description=$request->reward_discription;
			$wallet_log->save();

            $customerDevice = Trn_CustomerDeviceToken::where('customer_id', $request->customer_id)->get();
           $store_name=Mst_store::find($store_id)->store_name;
            foreach ($customerDevice as $cd)
		   {
                $title = 'Rewards points credited';
                //  $body = 'First order points credited successully..';
                $body = $request->reward_points . ' points credited to your wallet from store '.$store_name;
                $clickAction = "MyWalletFragment";
                $type = "wallet";
                $data['response'] =  Helper::customerNotification($cd->customer_device_token, $title, $body,$clickAction,$type);
            }
            
            $data['status']=1;
            $data['message']="Customer reward added successfully";
            return response($data);
          }
          else
          {
           $data['status']=0;
           $data['message']="Customer does not exist";
           response($data);
          }
          
        } catch (\Exception $e) {
            $data['status']=0;
            $data['message']="something went wrong";
            response($data);
        }

      

    }
    
}
