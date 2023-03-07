<?php

namespace App\Console\Commands;

use App\Models\admin\Mst_store_product_varient;
use App\Models\admin\Trn_StoreAdmin;
use App\Trn_pos_lock;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class getUnusedProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getunusedProduct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pos_locks=Trn_pos_lock::where('expiry_time','<=',Carbon::now()->toDateTimeString())->where('status',1);
        $ls=$pos_locks->get();
        foreach($ls as $lock)
        {
            $checker=Trn_pos_lock::where('id',$lock->id);
            $checker->update(['status'=>2]);
            Mst_store_product_varient::where('product_varient_id', '=', $checker->first()->product_varient_id)->increment('stock_count', $checker->first()->quantity);
        }

        $logged_admins=Trn_StoreAdmin::where('is_logged_in',1)->get();
        foreach($logged_admins as $admin)
        {
            $check_expires=Trn_StoreAdmin::where('store_admin_id',$admin->store_admin_id)->first();
            if($check_expires)
            {
                if(Carbon::now()>=$check_expires->login_will_expire_at)
                {
                    
                    $admin->is_logged_in=0;
                    //$admin->last_active_at=Carbon::now();
                    $admin->login_will_expire_at=null;
                    $admin->update();
                    // Auth::guard('store')->logout();
                    // return redirect()->to('/store-login')->with('danger','Session has been Expired');
               
                }

            }

        }
        
    }
}
