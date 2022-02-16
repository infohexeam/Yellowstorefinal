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
use App\Models\admin\Trn_TermsAndCondition;
use App\Models\admin\Trn_StoreAdmin;

use SoapClient;
use Twilio\Rest\Client;

class PublicController extends Controller
{



  public function pgtest()
  {


    $client = new \GuzzleHttp\Client();
    $response = $client->request('GET', 'https://api.cashfree.com/api/v2/easy-split/orders/16817139', [
      'headers' => [
        'Accept' => 'application/json',
        'x-api-version' => '2021-05-21',
        'x-client-id' => '165253d13ce80549d879dba25b352561',
        'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
      ],
    ]);

    $responseData = $response->getBody()->getContents();

    return    $responseFinal = json_decode($responseData, true);




    $data = array();
    $query['order_id'] = "order_1626945143520";
    $query['order_amount'] = "10.12";
    $query['order_currency'] = "INR";
    $query1['customer_id'] = "12345";
    $query1['customer_email'] = "techsupport@cashfree.com";
    $query1['customer_phone'] = "9816512345";


    $client = new \GuzzleHttp\Client();
    $api = $client->get('https://api.cashfree.com/api/v2/easy-split/orders/95018443', [
      'headers' => [
        // 'Accept' => 'application/json',
        // 'Content-type' => 'application/json',
        // 'x-api-version' => '2021-05-21',
        'x-client-id' => '165253d13ce80549d879dba25b352561',
        'x-client-secret' => 'bab0967cdc3e5559bded656346423baf0b1d38c4'
      ],
      'json' => $query
    ]);

    dd($api);
    $data = $api->getBody()->getContents();

    $response = json_decode($data, true);
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
}
