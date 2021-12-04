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
use SoapClient;
use Twilio\Rest\Client;

class PublicController extends Controller
{
    public function generatePdf(Request $request,$id)
    {
    
   
       $decrId  = Crypt::decryptString($id);
       $order = Trn_store_order::Find($decrId);
       $order_no = $order->order_number;
       $order_id =   $decrId;
       $pageTitle = "Invoice";
       $order_items = Trn_store_order_item::where('order_id',$decrId)->get();
          $store_data = Mst_store::where('store_id',@$order->store_id)->first();

      // dd($order_no);
   
       $pdf = PDF::loadView('store.elements.order.bill', compact('store_data','order_id','order_items','order','pageTitle'));
   
     //return view('store.elements.order.bill',compact('order_items','pageTitle','order'));
   
   
       $content =  $pdf->download()->getOriginalContent();
   
       Storage::put('uploads\order_invoice\Ivoice_'.$order_no.'.pdf', $content);
   
       return $pdf->download('Ivoice_'.$order_no.'.pdf');
   
    }

    public function generateItemsPdf(Request $request,$id)
    {
           $decrId  = Crypt::decryptString($id);
          $order = Trn_store_order::Find($decrId);
       $order_no = $order->order_number;
       $order_id =   $decrId;
       $pageTitle = "Order Items";
       $order_items = Trn_store_order_item::where('order_id',$order_id)->get();
       
       $pdf = PDF::loadView('store.elements.order.items', compact('order_no','order_id','order_items','order','pageTitle'));
   
       $content =  $pdf->download()->getOriginalContent();
   
       Storage::put('uploads\order_items\Items_'.$order_no.'.pdf', $content);
   
       return $pdf->download('Items_'.$order_no.'.pdf');

    }


  
    public function showTC(Request $request)
    {
       $tc = Trn_TermsAndCondition::find(1);
        return view('store.auth.tc',compact('tc'));

    }
}
