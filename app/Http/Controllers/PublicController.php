<?php

namespace App\Http\Controllers;



use App\Models\admin\Trn_store_customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Response;
use Image;
use DB;
use Hash;
use Carbon\Carbon;
use Crypt;
use Mail;
use PDF;
use Illuminate\Http\Request;
use App\Models\admin\Trn_store_order;
use App\Models\admin\Trn_store_order_item;
use App\Models\admin\Mst_store;
use App\Models\admin\Trn_OrderPaymentTransaction;
use App\Models\admin\Trn_OrderSplitPayments;
use App\Models\admin\Trn_TermsAndCondition;
use App\Models\admin\Trn_StoreAdmin;
use App\Models\admin\Mst_store_product;
use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Mst_SubCategory;
use SoapClient;
use Twilio\Rest\Client;

class PublicController extends Controller
{

  public function GetSubCategory(Request $request)
  {
    $category_id = $request->category_id;

    $subcategory  = Mst_SubCategory::where("category_id", '=', $category_id)->where('sub_category_status', 1)->pluck("sub_category_name", "sub_category_id");
    return response()->json($subcategory);
  }


  public function isPCodeAvailable(Request $request)
  {
    $storeId = $request->store_id;
    $proEx = Mst_store_product::where('product_code', $request->product_code)->where('store_id','=',$storeId);
    if (isset($request->product_id))
      $proEx = $proEx->where('product_id', '!=', $request->product_id);
    $proEx = $proEx->count();
    
    
    
       // dd($request->all(),$proEx,$proExGlob,$totalCount);


    // if ($proEx > 0) { //old
    if ($proEx > 1) { 
      $a = 1;
      return $a;
    } else {
      $a = 0;
      return $a;
    }
  }
  
  
   public function isPCodeAvailableGlobalPro(Request $request)
  {
    
    
    $proExGlob = Mst_GlobalProducts::where('product_code', $request->product_code);
    if (isset($request->global_product_id))
      $proExGlob = $proExGlob->where('global_product_id', '!=', $request->global_product_id);
    $proExGlob = $proExGlob->count();

    $totalCount =  $proExGlob;
    
       // dd($request->all(),$proEx,$proExGlob,$totalCount);


    if ($totalCount > 0) {
      $a = 1;
      return $a;
    } else {
      $a = 0;
      return $a;
    }
  }
  


  public function pgtest()
  {

    $curl = curl_init();


    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://api.cashfree.com/api/v2/easy-split/vendors',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => '{
            "email": "binupb39@outlook.com",
            "status": "ACTIVE",
            "bank": 
              {
                "accountNumber": "91901001378660",
                "accountHolder": "Binu P Benny",
                "ifsc": "UTIB0003377"
              },
             
            "phone": "7510569328",
            "name": "VendorName",
            "id": "merchantVendorId1",
            "settlementCycleId": 2
          }',
      CURLOPT_HTTPHEADER => array(
        'x-client-id: 165253d13ce80549d879dba25b352561',
        'x-client-secret: bab0967cdc3e5559bded656346423baf0b1d38c4',
        'x-api-version: 2021-05-21',
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $jData = json_decode($response);
    if($jData->subCode == 400){
      $data['message'] = $jData->subCode;
    }
    echo $jData->subCode;
    die;



    // $client = new \GuzzleHttp\Client();
    // $response = $client->request('POST', 'https://api.cashfree.com/api/v2/easy-split/vendors', [
    //   'headers' => [
    //     'Accept' => 'application/json',
    //     'x-api-version' => '2021-05-21',
    //     'x-client-id' => '165253d13ce80549d879dba25b352561',
    //     'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4',
    //     "data-row" => {
    //         "email": "name@cashfree.com",
    //         "status": "ACTIVE/BLOCKED",
    //         "bank": 
    //           {
    //             "accountNumber": "12345678890",
    //             "accountHolder": "John Doe",
    //             "ifsc": "HDFC019345"
    //           },
    //          "upi": 
    //           {
    //             "vpa": "upi@vpa",
    //             "accountHolder": "Account Holder Name"
    //           },
    //         "phone": "1234567890",
    //         "name": "VendorName1",
    //         "id": "merchantVendorId1",
    //         "settlementCycleId": 123
    //       }
    //   ],
    // ]);


    // $responseData = $response->getBody()->getContents();

    // $responseFinal = json_decode($responseData, true);

    // dd($responseFinal);



    $orderDatas = Trn_store_order::where('payment_type_id', 2)
      ->where('is_split_data_saved', 0)
      ->where('trn_id', '!=', null)
      ->whereDate('created_at', '<', Carbon::now()->subMinutes(5)->toDateTimeString())
      ->get();



    // foreach ($orderDatas as $row) {

    //   $client = new \GuzzleHttp\Client();
    //   $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/' . $row->trn_id, [
    //     'headers' => [
    //       'Accept' => 'application/json',
    //       'x-client-id' => '165253d13ce80549d879dba25b352561',
    //       'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4',
    //       'data-raw' => {
    //         "email": "name@cashfree.com",
    //         "status": "ACTIVE/BLOCKED",
    //         "bank": 
    //           {
    //             "accountNumber": "12345678890",
    //             "accountHolder": "John Doe",
    //             "ifsc": "HDFC019345"
    //           },
    //          "upi": 
    //           {
    //             "vpa": "upi@vpa",
    //             "accountHolder": "Account Holder Name"
    //           },
    //         "phone": "1234567890",
    //         "name": "VendorName1",
    //         "id": "merchantVendorId1",
    //         "settlementCycleId": 123
    //       }
    //     ],
    //   ]);

    $responseData = $response->getBody()->getContents();

    $responseFinal = json_decode($responseData, true);






    $opt = new Trn_OrderPaymentTransaction;
    $opt->order_id = $row->order_id;
    $opt->paymentMode = null;
    $opt->PGOrderId = $row->trn_id;
    $opt->txTime = $row->txTime;
    $opt->referenceId = $row->referenceId;
    $opt->txMsg = $row->txMsg;
    $opt->orderAmount = $row->orderAmount;
    $opt->txStatus = $row->txStatus;

    if ($opt->save()) {

      $opt_id = DB::getPdo()->lastInsertId();
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

      $osp = new Trn_OrderSplitPayments;
      $osp->opt_id = $opt_id;
      $osp->order_id = $row->order_id;
      $osp->splitAmount = $responseFinal["settlementAmount"];
      $osp->serviceCharge = $responseFinal["serviceCharge"];
      $osp->serviceTax = $responseFinal["serviceTax"];
      $osp->splitServiceCharge = $responseFinal["splitServiceCharge"];
      $osp->splitServiceTax = $responseFinal["splitServiceTax"];
      $osp->settlementAmount = $responseFinal["settlementAmount"];
      $osp->settlementEligibilityDate = $responseFinal["settlementEligibilityDate"];

      $osp->paymentRole = 1; // 1 == store's split
      if ($osp->save()) {
        if (count($responseFinal['vendors']) > 0) {
          foreach ($responseFinal['vendors'] as $row) {
            $osp = new Trn_OrderSplitPayments;
            $osp->opt_id = $opt_id;
            $osp->order_id = $row->order_id;
            $osp->vendorId = $row["id"];
            $osp->settlementId = $row["settlementId"];
            $osp->splitAmount = $row["settlementAmount"];
            $osp->serviceCharge = @$row["serviceCharge"];
            $osp->serviceTax = @$row["serviceTax"];
            $osp->splitServiceCharge = @$row["splitServiceCharge"];
            $osp->splitServiceTax = @$row["splitServiceTax"];
            $osp->settlementAmount = @$row["settlementAmount"];
            $osp->settlementEligibilityDate = @$row["settlementEligibilityDate"];
            $osp->paymentRole = 0;
            $osp->save();
          }
        }
        Trn_store_order::where('order_id', $row->order_id)->update(['is_split_data_saved' => 1]);
      }
    }





    // $client = new \GuzzleHttp\Client();
    // $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/16817139', [
    //   'headers' => [
    //     'Accept' => 'application/json',
    //     'x-api-version' => '2021-05-21',
    //     'x-client-id' => '165253d13ce80549d879dba25b352561',
    //     'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
    //   ],
    // ]);

    // $responseData = $response->getBody()->getContents();

    // return    $responseFinal = json_decode($responseData, true);




    // $data = array();
    // $query['order_id'] = "order_1626945143520";
    // $query['order_amount'] = "10.12";
    // $query['order_currency'] = "INR";
    // $query1['customer_id'] = "12345";
    // $query1['customer_email'] = "techsupport@cashfree.com";
    // $query1['customer_phone'] = "9816512345";


    // $client = new \GuzzleHttp\Client();
    // $api = $client->get('https://api.cashfree.com/api/v2/easy-split/orders/95018443', [
    //   'headers' => [
    //     // 'Accept' => 'application/json',
    //     // 'Content-type' => 'application/json',
    //     // 'x-api-version' => '2021-05-21',
    //     'x-client-id' => '165253d13ce80549d879dba25b352561',
    //     'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
    //   ],
    //   'json' => $query
    // ]);

    // dd($api);
    // $data = $api->getBody()->getContents();

    // $response = json_decode($data, true);
  }



  function CheckName(Request $request)
  {

    $store_name = $request->store_name;
    $data = Mst_store::where('store_name', $store_name)
      ->count();

    if ($data > 0) {
      echo 'not_unique';
    } else {
      echo 'unique';
    }
  }

  function CheckPhone(Request $request)
  {

    $store_mobile = $request->store_mobile;
    $data = Trn_StoreAdmin::where('store_mobile', $store_mobile)
      ->count();

    if ($data > 0) {
      echo 'not_unique';
    } else {
      echo 'unique';
    }
  }


  public function payment()
  {
    $data = array();
    $query['order_id'] = "order_1626945143520";
    $query['order_amount'] = "10.12";
    $query['order_currency'] = "INR";
    $query1['customer_id'] = "12345";
    $query1['customer_email'] = "techsupport@cashfree.com";
    $query1['customer_phone'] = "9816512345";


    $client = new \GuzzleHttp\Client();
    $api = $client->post('https://sandbox.cashfree.com/pg/orders', [
      'headers' => [
        'Accept' => 'application/json',
        'Content-type' => 'application/json',
        'x-api-version' => '2021-05-21',
        'x-client-id' => '1159124beeb38480c16b093237219511',
        'x-client-secret' => 'f4201506d616394eebf87fa82e0b12385cd6c730'
      ],
      'json' => $query
    ]);

    dd($api);
    $data = $api->getBody()->getContents();

    $response = json_decode($data, true);
  }



  public function generatePdf(Request $request, $id)
  {


    $decrId  = Crypt::decryptString($id);
    $order = Trn_store_order::Find($decrId);
    $order_no = $order->order_number;
    $order_id =   $decrId;
    $pageTitle = "Invoice";
    $order_items = Trn_store_order_item::where('order_id', $decrId)->get();
    $store_data = Mst_store::where('store_id', @$order->store_id)->first();

    // dd($order_no);

    $pdf = PDF::loadView('store.elements.order.bill', compact('store_data', 'order_id', 'order_items', 'order', 'pageTitle'));

    //return view('store.elements.order.bill',compact('order_items','pageTitle','order'));


    $content =  $pdf->download()->getOriginalContent();

    Storage::put('uploads\order_invoice\Ivoice_' . $order_no . '.pdf', $content);

    return $pdf->download('Ivoice_' . $order_no . '.pdf');
  }

  public function generateItemsPdf(Request $request, $id)
  {
    $decrId  = Crypt::decryptString($id);
    $order = Trn_store_order::Find($decrId);
    $order_no = $order->order_number;
    $order_id =   $decrId;
    $pageTitle = "Order Items";
    $order_items = Trn_store_order_item::where('order_id', $order_id)->get();

    $pdf = PDF::loadView('store.elements.order.items', compact('order_no', 'order_id', 'order_items', 'order', 'pageTitle'));

    $content =  $pdf->download()->getOriginalContent();

    Storage::put('uploads\order_items\Items_' . $order_no . '.pdf', $content);

    return $pdf->download('Items_' . $order_no . '.pdf');
  }



  public function showTC(Request $request)
  {
    $tc = Trn_TermsAndCondition::where('role', 1)->first();
    // dd($tc);
    return view('store.auth.tc', compact('tc'));
  }


  public function showCusTC(Request $request)
  {
    $tc = Trn_TermsAndCondition::where('role', 2)->first();
    return view('customer_terms', compact('tc'));
  }

  public function showDeliveryTC(Request $request)
  {
    $tc = Trn_TermsAndCondition::where('role', 3)->first();
    return view('delivery_terms', compact('tc'));
  }

  public function showDeliveryPrivacy(Request $request)
  {
    $tc = Trn_TermsAndCondition::where('role', 4)->first();
    return view('delivery_policy', compact('tc'));
  }



}
