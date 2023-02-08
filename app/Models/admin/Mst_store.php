<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\notifications\StoreResetPasswordNotification;


class Mst_store extends Authenticatable
{

    use SoftDeletes;
    use Notifiable;

    protected $guard =  "store";
    protected $table = "mst_stores";
    protected $primaryKey = "store_id";

    protected $fillable = [
        'store_id', 'store_name', 'store_name_slug', 'store_mobile',
        'store_contact_person_name', 'store_contact_person_phone_number',
        'store_contact_number_2',
        'store_website_link', 'store_pincode', 'store_primary_address',
        'email', 'store_added_by', 'business_type_id',
        'store_commision_percentage', 'store_username', 'password', 'store_account_status',
        'store_country_id',
        'store_state_id',
        'store_district_id',
        'remember_token',
        'subadmin_id', 'place', 'town', 'store_commision_amount', 'store_mobile',
        'order_number_prefix',
        'online_status', 'store_qrcode', 'profile_image', 'upi_id',
        'longitude', 'latitude', 'place_id', 'service_area', 'is_pgActivated', 'gst'

    ];




    public function country()
    {
        return $this->belongsTo('App\Models\admin\Country', 'store_country_id', 'country_id');
    }

    public function state()
    {
        return $this->belongsTo('App\Models\admin\State', 'store_state_id', 'state_id');
    }
    public function district()
    {
        return $this->belongsTo('App\Models\admin\District', 'store_district_id', 'district_id');
    }

    public function town() //town district relation
    {
        return $this->belongsTo('App\Models\admin\Town', 'town_id', 'town_id');
    }

    public function store_doc() //town district relation
    {
        return $this->belongsTo('App\Models\admin\Mst_store_documents', 'store_id', 'store_id');
    }



    public function business_type()
    {
        return $this->belongsTo('App\Models\admin\Mst_business_types', 'business_type_id', 'business_type_id')->withTrashed();
    }

    public function subadmin()
    {
        return $this->belongsTo('App\User', 'subadmin_id', 'id');
    }

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new StoreResetPasswordNotification($token));
    }
}
