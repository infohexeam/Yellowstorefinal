<?php

namespace App\Console\Commands;

use App\Models\admin\Trn_store_order;
use Illuminate\Console\Command;

class getRefundData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getRefundData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checking for processed refunds ';

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
        $RefundOrderDatas = Trn_store_order::where('isRefunded', 1)
            ->get(); //status 1 = initiated refund

        foreach ($RefundOrderDatas as $row) {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.cashfree.com/api/v1/refundStatus/',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array('appId' => '165253d13ce80549d879dba25b352561', 'secretKey' => 'bab0967cdc3e5559bded656346423baf0b1d38c4', 'refundId' => '11644414'),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            $refundResponseFinal = json_decode($response, true);
            if ($refundResponseFinal['refund'][0]['processed'] == "YES") {
                Trn_store_order::where('order_id', $row->order_id)->update([
                    "isRefunded" => 2,
                    "refundStatus" => "Success",
                    "refundNote" => $refundResponseFinal['refund'][0]['note'],
                    "refundProcessStatus" => "YES",
                    "refundStartDate" => $refundResponseFinal['refund'][0]['initiatedOn'],
                    "refundProcessDate" => $refundResponseFinal['refund'][0]['processedOn']
                ]);
            }
        }
        return 1;
    }
}
