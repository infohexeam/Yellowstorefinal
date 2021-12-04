<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Trn_CustomerFeedback extends Model
{
    protected $table = "trn__customer_feedback";
    protected $primaryKey = "feedback_id";

    protected $fillable = [
    					     'customer_id','product_varient_id','feedback','feedback_question_id'

    						  ];
      public function question()
      {
        return $this->belongsTo('App\Models\admin\Mst_FeedbackQuestion', 'feedback_question_id', 'feedback_question_id');
      }
						  
}
