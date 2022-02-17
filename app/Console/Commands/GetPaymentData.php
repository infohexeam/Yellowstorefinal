<?php

namespace App\Console\Commands;

use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Trn_store_order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GetPaymentData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:getPaymentData';

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
        $orderDatas = Trn_store_order::where('payment_type_id', 2)
            ->where('is_split_data_saved', 0)
            ->where('trn_id', '!=', null)
            ->whereDate('created_at', '<', Carbon::now()->subMinutes(5)->toDateTimeString())
            ->get();

        foreach ($orderDatas as $row) {

            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/' . $row->trn_id, [
                'headers' => [
                    'Accept' => 'application/json',
                    'x-api-version' => '2021-05-21',
                    'x-client-id' => '165253d13ce80549d879dba25b352561',
                    'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
                ],
            ]);

            $responseData = $response->getBody()->getContents();

            $responseFinal = json_decode($responseData, true);

            Trn_store_order::where('order_id', $row->order_id)->update(['is_split_data_saved' => 2]);



            // $opt = new Trn_OrderPaymentTransaction;
            // $opt->order_id = $row->order_id;
            // $opt->paymentMode = null;
            // $opt->PGOrderId = $row->trn_id;
            // $opt->txTime = $row->txTime;
            // $opt->referenceId = $row->referenceId;
            // $opt->txMsg = $row->txMsg;
            // $opt->orderAmount = $row->orderAmount;
            // $opt->txStatus = $row->txStatus;
        }
        return 1;
    }
}
