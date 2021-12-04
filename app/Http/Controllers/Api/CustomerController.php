<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;

use Illuminate\Support\Facades\Validator;
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

use App\Models\admin\Trn_store_customer;
use App\Models\admin\Trn_store_customer_otp_verify;

class CustomerController extends Controller
{
    public function mobUniqueCheck(Request $request){
        $data = array();
          try{
                if ($request->customer_mobile_number) {
                    if(Trn_store_customer::where("customer_mobile_number",'=',$request->customer_mobile_number)->first())
                    {
                        $data['status'] = 0;
                        $data['message'] = "Mobile number already in use";
                    }else{
                        $data['status'] = 1;
                        $data['message'] = "Mobile number accepted";
                    }
                }else{
                    $data['status'] = 2;
                    $data['message'] = "Mobile number cannot be empty";
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




      public function loginStore(Request $request)
      {
          $data = array();
          try
          {
             $phone = $request->input('customer_mobile_number');
             $passChk = $request->input('password');
  
             $validator = Validator::make($request->all(), [      
                  'customer_mobile_number' => 'required',  
                  'password' => 'required',
              ],
              [   
                  'customer_mobile_number.required' => "Mobile number is required", 
                  'password.required' => "Password is required",
              ]);
              if(!$validator->fails())
                  {
                     $custCheck = Trn_store_customer::where('customer_mobile_number','=',$phone)->first();
  
                     if($custCheck)
                         {
                          
                            if(Hash::check($passChk, $custCheck->password))
                                 {
                                      if($custCheck->customer_profile_status!=0)
                                          {
                                              if($custCheck->customer_otp_verify_status!=0)
                                                  {                                                    
                                                      $data['status'] = 1;
                                                      $data['message'] = "Login Success";
                                                      $data['customer_id'] = $custCheck->customer_id;
                                                      $data['customer_name'] = $custCheck->customer_first_name;
                                                      $data['access_token'] = $custCheck->createToken('authToken')->accessToken;
                                                  dd( $data['access_token'] );
                                            
                                            
                                         }else{
                                                      $data['status'] = 2;
                                                      $data['message'] = "OTP not verified";
                                                  }
                                          }else{
                                              $data['status'] = 4;
                                              $data['message'] = "Profile not Activated";
                                          }
                                  }else{
                                      $data['status'] = 3;
                                      $data['message'] = "Mobile Number or Password is Invalid";
                                  }
                          }else{
                              $data['status'] = 0;
                              $data['message'] = "Invalid Login Details";
                          }
                  }else{
                      $data['errors'] = $validator->errors();
                      $data['message'] = "Login Failed";
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




      public function emailUniqueCheck(Request $request){
        $data = array();
          try{
                if ($request->customer_email) {

                    $validator = Validator::make($request->all(),
                    [
                        'customer_email'          => 'email',
                    ]);

                    if(!$validator->fails() )
                    {
                        if(Trn_store_customer::where("customer_email",'=',$request->customer_email)->first())
                        {
                            $data['status'] = 0;
                            $data['message'] = "Email already in use";
                        }else{
                            $data['status'] = 1;
                            $data['message'] = "Email accepted";
                        }
                    }
                    else{
                        $data['status'] = 3;
                        $data['message'] = "Email invalid";
                    }

                }else{
                    $data['status'] = 2;
                    $data['message'] = "Email cannot be empty";
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

      public function saveCustomer(Request $request,Trn_store_customer $customer,Trn_store_customer_otp_verify $otp_verify){
        $data = array();
      try{

            $validator = Helper::validateCustomer($request->all());
            if(!$validator->fails())
            {
                $cusName = explode(' ', $request->customer_name, 2);

                $customer->customer_first_name            = $cusName[0];
                $customer->customer_last_name            = @$cusName[1];
                $customer->customer_email   = $request->customer_email;
                $customer->customer_mobile_number   = $request->customer_mobile_number;
                $customer->customer_password              = Hash::make($request->customer_password);
                $customer->customer_profile_status       = 0;
                $customer->customer_otp_verify_status       = 0;
             
                $customer->save();
                
                    $customer_id = DB::getPdo()->lastInsertId();
                    
                        $customer_otp =  rand ( 1000 , 9999 );
                        $customer_otp_expirytime = Carbon::now()->addMinute(10);

                        $otp_verify->customer_id                 = $customer_id;
                        $otp_verify->customer_otp_expirytime     = $customer_otp_expirytime;
                        $otp_verify->customer_otp                 = $customer_otp;
                        $otp_verify->save();

                $data['customer_id'] = $customer_id;
                $data['otp'] = $customer_otp;
                $data['status'] = 1;
                $data['message'] = "Customer Registration Success";   

            }else{
                $data['errors'] = $validator->errors();
                $data['status'] = 0;
                $data['message'] = "Customer Registration Failed";
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



    public function verifyOtp(Request $request){
        $data = array();
          try{
              $otp = $request->customer_otp;
              
              $customer_id = $request->customer_id;
              
              $otp_verify =  Trn_store_customer_otp_verify::where('customer_id', '=', $customer_id)->latest()->first();
              
              if($otp_verify)
                     {
                      $customer_otp_expirytime = $otp_verify->customer_otp_expirytime;
                      $current_time = Carbon::now()->toDateTimeString();
                      $customer_otp =  $otp_verify->customer_otp;
  
                       if($customer_otp == $request->customer_otp)
                          {
                              if($current_time < $customer_otp_expirytime)
                              {
                                     $customer = Trn_store_customer::Find($customer_id);
                                  $customer->customer_profile_status = 1;
                                  $customer->customer_otp_verify_status = 1;
                                  $customer->update();
  
                                    $data['status'] = 1;
                                  $data['message'] = "OTP Verifiction Success";
                        
                              } else{
                                  $data['status'] = 2;
                                  $data['message'] = "OTP expired.click on resend OTP";	
                              }
  
                              }else{
                                  $data['status'] = 3;
                                  $data['message'] = "Incorrect OTP entered. Please enter a valid OTP.";
                              }
                              }else{
                                  $data['status'] = 3;
                                  $data['message'] = "OTP not found. Please click on resend OTP.";
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



      public function resendOtp(Request $request,Trn_store_customer $customer, Trn_store_customer_otp_verify $otp_verify){
        $data = array();
          try{
              $customer_id = $request->customer_id;
              if($customer_id)
              {
              $otp_verify = Trn_store_customer_otp_verify::where('customer_id','=',$customer_id)->latest()->first();
                if($otp_verify !== null){
                      $customer_otp_verify_id = $otp_verify->customer_otp_verify_id;
                      $otp_verify = Trn_store_customer_otp_verify::Find($customer_otp_verify_id);
                      $extented_time = Carbon::now()->addMinute(10);
                      $otp_verify->customer_otp_expirytime = $extented_time;
                      $otp_verify->update();
                      $data['status'] = 1;
                      $data['otp'] = $otp_verify->customer_otp;
                      $data['message'] = "OTP resent Success.";
                     
                  }else{
                      $otp_verify = new Trn_store_customer_otp_verify;
                      $customer_otp =  rand ( 1000 , 9999 );
                      $customer_otp_expirytime = Carbon::now()->addMinute(10);
                      $otp_verify->customer_id                 = $customer_id;
                      $otp_verify->customer_otp_expirytime     = $customer_otp_expirytime;
                      $otp_verify->customer_otp                 = $customer_otp;
                      $otp_verify->save();
                      $data['status'] = 2;
                      $data['otp'] = $customer_otp;
                      $data['message'] = "OTP registerd successfully. Please verify OTP.";
                  }
              }else{
                  $data['status'] = 0;
                  $data['message'] = "Customer Doesn't Exist.";
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

      public function FpverifyMobile(Request $request,Trn_store_customer_otp_verify $otp_verify){
        $data = array();
          try{
          // 	$mobCode = $request->country_code;
  
              $customer_mobile_number=$request->customer_mobile_number;
              $mobCheck =Trn_store_customer::where("customer_mobile_number",'=',$customer_mobile_number)->latest()->first();
         
          if($mobCheck)
          {
  
              $customer_id = $mobCheck->customer_id;
              $customer_mobile_number = $mobCheck->customer_mobile_number;
  
              $validator = Validator::make($request->all(), [       
                  // 'country_code' => 'required',
                  'customer_mobile_number' => 'required'
              ],
              [ 	
                  // 'country_code.required' => "Country Code is required",
                  'customer_mobile_number.required' => "Mobile number is required",
                  
  
              ]);
  
              if(!$validator->fails())
              {
                   $customer_otp =  rand ( 1000 , 9999 );
                   $customer_otp_expirytime = Carbon::now()->addMinute(10);
                  
                  $otp_verify->customer_id                 = $customer_id;
                  $otp_verify->customer_otp_expirytime     = $customer_otp_expirytime;
                  $otp_verify->customer_otp                = $customer_otp;
                  $otp_verify->save();
  
                  $data['status'] = 1;
                  $data['customer_id'] = $customer_id;
                  $data['customer_mobile_number'] = $customer_mobile_number;
                  $data['customer_otp'] = $customer_otp;
                  $data['message'] = "Mobile Verification Success. OTP Sent to registered mobile number";
  
              }else{
                  
                  $data['status'] = 0;
                  $data['errors'] = $validator->errors();
                  $data['message'] = "Verification Failed";
              }
  
          }else{
              $data['status'] = 0;
              $data['message'] = "Customer Does not exist";
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


      public function FpverifyOTP(Request $request,Trn_store_customer_otp_verify $otp_verify)
    {
    	$data = array();
        try{
            $otp = $request->customer_otp;
            // $mobCode = $request->country_code;
        	$mobNumber=$request->customer_mobile_number;

        	$mobCheck =Trn_store_customer::where("customer_mobile_number",'=',$mobNumber)->latest()->first();
        	if($mobCheck)
        	{
        		$customer_id = $mobCheck->customer_id;
        	    $customer_mobile_number = $mobCheck->customer_mobile_number;
        		$otpCheck = Trn_store_customer_otp_verify::where('customer_id','=',$customer_id)->where('customer_otp','=',$otp)->latest()->first();

            if($otpCheck)
                {
                    $customer_otp_expirytime = $otpCheck->customer_otp_expirytime;
        			$current_time = Carbon::now()->toDateTimeString();
            		$customer_new_otp =  $otpCheck->customer_otp;

                    // $expParse = $expTime->format('Y-m-d H:i:s');

                    if($current_time < $customer_otp_expirytime)
			                {
                       			
			                   $data['status'] = 1;
			                   $data['customer_id'] = $customer_id;
			                    $data['customer_mobile_number'] = $customer_mobile_number;
                                $data['message'] = "OTP verification success. Enter a new password.";
                      
                    		} else{
                    			$data['status'] = 2;
                                $data['message'] = "OTP expired.click on resend OTP";	
                    		}  
                }else{
                    $data['status'] = 0;
                    $data['message'] = "Invalid OTP Entered";
                }
        	}else{
            $data['status'] = 0;
            $data['message'] = "Customer Does not exist";
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


    public function resetPassword(Request $request)
    {
        $data=array();
        try
        {
            $customer_id = $request->customer_id;
        	$mobNumber=$request->customer_mobile_number;
        	$mobCheck =Trn_store_customer::where("customer_mobile_number",'=',$mobNumber)->where('customer_id','=',$customer_id)->first();
        	if($mobCheck)
        		{
        			$validator = Validator::make($request->all(), [      
                		'password'=>'required|string|min:8|confirmed'
            		]);
            		if(!$validator->fails())
                		{
                			$encPass = Hash::make($request->input('password'));
		                    Trn_store_customer::where('customer_id', $customer_id)->where("customer_mobile_number",'=',$mobNumber)->update(['customer_password' => $encPass]);
		                    $data['status'] ="1";
		                    $data['messsage'] = "Password Changed successfully";
	                	}else{
		                    $data['status'] = "0";
		                    $data['errors'] = $validator->errors(); 
                		}	
        		}else{
            		$data['status'] = 0;
            		$data['message'] = "Customer Does not exist";
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


}
