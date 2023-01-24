<?php

namespace App\Console\Commands;

use App\Models\admin\Mst_store_product_varient;
use App\Trn_pos_lock;
use Carbon\Carbon;
use Illuminate\Console\Command;

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
        
    }
}
