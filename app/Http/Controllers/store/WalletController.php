<?php

namespace App\Http\Controllers\store;

use App\Http\Controllers\Controller;
use App\Models\admin\Trn_configure_points;
use App\Models\admin\Trn_customer_reward;
use App\Models\admin\Trn_store_customer;
use App\Models\admin\Trn_store_order;
use App\Trn_wallet_log;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:store');
  }
  public function createConfigurePoints(Request $request)
	{

		$pageTitle = "Configure Points";
        $store_id=Auth::guard('store')->user()->store_id;
		$configure_points = Trn_configure_points::Where('store_id',$store_id)->first();
		if (isset($configure_points)) {
			$configure_points_id = $configure_points->configure_points_id;
		} else {
			$configure_points_id = 1;
		}

		return view('store.elements.configure_points.create', compact('configure_points_id', 'configure_points', 'pageTitle'));
	}
    public function storeConfigurePoints(Request $request, Trn_configure_points $points)
	{
        $store_id=Auth::guard('store')->user()->store_id;
		$validator = Validator::make(
			$request->all(),
			[
				//'registraion_points'          => 'required',
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
				//'registraion_points.required'          => 'Registration required',
				'rupee_points.required'          => 'Ruppes to points required',
				'order_amount.required'          => 'Order amount required',
			]
		);

		if (!$validator->fails()) {

           
			$points = Trn_configure_points::where('store_id',$store_id)->first();
			if (isset($points)) {
				// $points->points = $request->points;
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

				$points->update();
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
			}
			return redirect('store/configure_points/list')->with('status', 'Configure points updated successfully.');
		} else {
			return redirect()->back()->withErrors($validator)->withInput();
		}
	}
    public function listCustomerReward(Request $request)
	{

		$pageTitle = "Customer Reward";
        $store_id=Auth::guard('store')->user()->store_id;
		$customer_rewards = Trn_customer_reward::leftjoin('trn_store_orders','trn_customer_rewards.order_id','=','trn_store_orders.order_id')->orderBy('reward_id', 'DESC')
        ->leftjoin('trn_store_customers', 'trn_store_customers.customer_id', 'trn_customer_rewards.customer_id')
        ->where('trn_customer_rewards.store_id',$store_id)
        ->orWhere('trn_store_orders.store_id',$store_id)
        ->get();
		if ($_GET) {

			$datefrom = $request->date_from;
			$dateto = $request->date_to;


			$a1 = Carbon::parse($request->date_from)->startOfDay();
			$a2  = Carbon::parse($request->date_to)->endOfDay();
			$customer_first_name = $request->customer_name;
			$query = Trn_customer_reward::with(['customer','order']);
            $query=$query->whereHas('order', function (Builder $qry) use($store_id) {
                return $qry->where('store_id','=',$store_id);
                
              })->orWhere('trn_customer_rewards.store_id',$store_id);
            //->orWhere('trn_store_orders.store_id',$store_id);

			if (isset($request->date_from) && isset($request->date_to)) {
				$query = $query->whereBetween('trn_customer_rewards.created_at', [$a1, $a2]);
			}

			if (isset($request->customer_name)) {
                $cust_name=$request->customer_name;
                $query=$query->whereHas('customer', function (Builder $qry) use($cust_name) {
                    return $qry->where('customer_first_name','like','%'.$cust_name.'%');
                    
                  });
				
			}

			$customer_rewards = $query->orderBy('reward_id', 'DESC')->get();


			return view('store.elements.customer_reward.list', compact('dateto', 'datefrom', 'customer_rewards', 'pageTitle'));
		}



		return view('store.elements.customer_reward.list', compact('customer_rewards', 'pageTitle'));
	}
    public function addReward()
    {
        //dd('test');
        $pageTitle = "Add Reward To Existing Customer";
        $store_id=Auth::guard('store')->user()->store_id;
        $customer_orders=Trn_store_order::where('store_id',$store_id)->get();
    foreach ($customer_orders as $order) {
        $customer_ids[] = $order->customer_id;
    }
       $customers = Trn_store_customer::select('customer_id','customer_first_name','customer_last_name','customer_mobile_number')
                    ->whereIn('customer_id',$customer_ids)
                    ->get();
        return view('store.elements.customer_reward.add',compact('pageTitle','customers'));

    
       // return redirect()->back()->with('status','Reward added successfully.');
    }
    public function storeReward(Request $request)
    {
        $store_id=Auth::guard('store')->user()->store_id;
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
			$wallet_log->store_id=$store_id;
			$wallet_log->customer_id=$request->customer_id;
			$wallet_log->order_id=null;
			$wallet_log->type='credit';
			$wallet_log->points_debited=null;
			$wallet_log->points_credited=$request->reward_points;
			$wallet_log->save();
          }else
          {
            return redirect()->back()->withErrors(['Customer not exist!'])->withInput();
          }
          
        } catch (\Exception $e) {
             //return redirect()->back()->withErrors([  $e->getMessage() ])->withInput();
        
            return redirect()->back()->withErrors(['Something went wrong!'])->withInput();
        }

        return redirect('store/customer-rewards/list')->with('status','Customer reward added successfully');

    }
}