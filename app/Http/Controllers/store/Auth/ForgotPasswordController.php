<?php

namespace App\Http\Controllers\store\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

use Carbon\Carbon;
use Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

use App\Models\admin\Mst_store;
use App\Models\admin\Trn_store_otp_verify;
use App\Models\admin\Trn_StoreAdmin;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */


    use SendsPasswordResetEmails;



     public function __construct()
    {
        $this->middleware('guest:store');
    }


    public function showLinkRequestForm()
    {
        //dd(url()->full());
        return view('store.auth.passwords.forgotpassword');
    }



    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */

    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
   public function broker()
    {
        return Password::broker('stores');
    }


    public function sendResetOTP(Request $request,Trn_store_otp_verify $otp_verify)
    {
        $this->validateStoreMobile($request);



        $stores = Mst_store::where('store_mobile',$request->store_mobile)->first();

        //dd($stores);

         $store_otp =  rand ( 1000 , 9999 );

            $store_otp_expirytime = Carbon::now()->addMinute(10);

            $data['store_otp_expirytime']    = $store_otp_expirytime;
            $data['store_otp']                 = $store_otp;

            if(Trn_store_otp_verify::where('store_id',$stores->store_id)->update($data))
            {
          //  echo "yes";die;
            return view('store.auth.passwords.forgot_password_otp',compact('stores'));
            }
            else
            {
                return redirect()->back();
            }


    }

    public function ResendOTP(Request $request,Mst_store $store,$store_id, Trn_store_otp_verify $otp_verify )
    {
        dd($store_id);
        $otp_verify = Trn_store_otp_verify::where('store_id','=',$store_id)->first();
    // dd($otp_verify);
        if($otp_verify !== null){
        // dd('string');

        $store_otp_verify_id = $otp_verify->store_otp_verify_id;
    // dd($store_otp_verify_id);
        $otp_verify = Trn_store_otp_verify::Find($store_otp_verify_id);
        $extented_time = Carbon::now()->addMinute(10);
        //dd($extented_time);
        $otp_verify->store_otp_expirytime = $extented_time;

        $otp_verify->update();

        return redirect()->back()->with('status','OTP Resended Successfully');

            }else
            {
                $otp_verify = new Trn_store_otp_verify;
                $store_otp =  rand ( 1000 , 9999 );
                //dd($store_otp);

                $store_otp_expirytime = Carbon::now()->addMinute(10);

                $otp_verify->store_id                 = $store_id;
                $otp_verify->store_otp_expirytime     = $store_otp_expirytime;
                $otp_verify->store_otp                 = $store_otp;
                $otp_verify->save();
                return redirect()->back()->with('status','OTP Registerd Successfully.Please verify OTP');

            }
    }

    public function otpVerificationview(Request $request, $id)
    {
        //dd($otp_id);
       // $otp_verify = TrnOTPVerify::Find($otp_id);
        $decrId  = Crypt::decryptString($id);
        $stores = Mst_store::Find($decrId);

        return view('store.auth.passwords.forgot_password_otp',compact('stores'));
    }

    public function otpVerification(Request $request, Trn_store_otp_verify $otp_verify,$store_id)
    {


                $store_id  = $request->store_id;
                //dd($store_id);

            // $cus_otp_availability =  TrnOTPVerify::where('store_id', '=', $store_id)->first();
                // dd($cus_otp_availability);
                $otp_verify =  Trn_store_otp_verify::where('store_id', '=', $store_id)->first();
                if($otp_verify)
                {
                $store_otp_exp_time = $otp_verify->store_otp_expirytime;
                //  dd($cus_otp_exp_time);
                $current_time = Carbon::now()->toDateTimeString();
                // dd($current_time);
                    $store_new_otp =  $otp_verify->store_otp;

                    if($store_new_otp == $request->store_otp)

                        {

                        if($current_time < $store_otp_exp_time)
                        {

                                // change password of user
                            return redirect('change/store-password/'.Crypt::encryptString($store_id))->with('status','OTP Verified! Reset Password.');
                            // REDIRECT TO LOGIN PAGE
                            } else
                            {
                               // echo "OTP expired.click on resend OTP.";die;

                               return redirect()->back()->with('expiry_error', 'OTP expired.click on resend OTP.');
                               return redirect()->back()->withErrors([])->withInput();
                        }

                        }else{
                            return redirect()->back()->with('validation_error','Incorrect OTP entered. please Enter a valid OTP.');

                        }


                    }else
                    {
                        return redirect()->back()->with('validation_error','Store OTP not found. please click on resend OTP.');

                    }

        }


        protected function changePassword(Request $request,$store_id)
        {
            $decrId  = Crypt::decryptString($store_id);
            $stores = Mst_store::Find($decrId);

            return view('store.auth.passwords.change_password',compact('stores'));

        }

        protected function resetPassword(Request $request,$user_id)
        {
            $validator = Validator::make(
                $request->all(),
                [
                    'password'          => 'required|min:8|same:password_confirmation|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/u',
                    
                ],
                [
                    'password.regex'=>'Password must include at least one upper case letter, lower case letter, number, and special character'
                ]
                
            );
        if (!$validator->fails()) {

            $password   = Hash::make($request->password);
            $data['password'] = $password;
            if(Trn_StoreAdmin::where('store_admin_id',$user_id)->update($data))
            {
                return redirect('store-login')->with('message','Password updated login to continue.');
            }
            else
            {
                return redirect()->back()->with('message','Oops! Error.');
            }
        }
        else
        {
            return redirect()->back()->withErrors($validator)->withInput();

        }

        }


    protected function validateStoreMobile(Request $request)
    {
        $request->validate(['store_mobile' => 'required|exists:mst_stores'],
        [
            'store_mobile.required'                => 'Mobile number required',
            'store_mobile.exists'                => 'Mobile number des not exists',

        ]);

    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker('stores')->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

     protected function validateEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
    }

    /**
     * Get the needed authentication credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only('email');
    }
}
