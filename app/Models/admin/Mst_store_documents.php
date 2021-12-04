<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;

class Mst_store_documents extends Model
{
  	protected $table ="mst_store_documents";
	protected $primaryKey = "store_document_id";

    protected $fillable = [
    					'store_document_id','store_document_license','store_document_gstin','store_document_other_file','store_document_file_head','store_id',
    					  ];



	public function store()
	{
		return $this->belongsTo('App\Models\admin\Mst_store','store_id','store_id');
	}

}
