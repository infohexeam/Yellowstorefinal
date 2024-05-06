<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mst_FeedbackQuestion extends Model
{
     use SoftDeletes;
   protected $table = "mst__feedback_questions";

    protected $primaryKey = "feedback_question_id";


    protected $fillable = [
        'feedback_question',
        'deleted_at',
        'category_id'

    ];
    
        public function category()
       {
            return $this->belongsTo('App\Models\admin\Mst_categories','category_id','category_id');
       }
    
}
