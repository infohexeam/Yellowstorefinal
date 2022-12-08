<?php

namespace App\Imports;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\admin\Mst_GlobalProducts;
use App\Models\admin\Mst_business_types;
use App\Models\admin\Mst_Tax;
use App\Models\admin\Mst_attribute_value;
use App\Models\admin\Mst_attribute_group;
use App\Models\admin\Mst_categories;
use App\Models\admin\Mst_store_agencies;
use App\Models\admin\Mst_SubCategory;
use Illuminate\Validation\Rule;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Throwable;

class GlobalProductsImport implements ToCollection, WithHeadingRow, SkipsOnError, WithValidation
{
    use Importable, SkipsErrors;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function collection(Collection $rows)
    {
    //dd($rows);
        foreach($rows as $row) {

            
            $tax_data = Mst_Tax::where('tax_value',$row['tax'])->first();
          //  $business_type = Mst_business_types::where('business_type_name',$row['product_type'])->first();
           // $color_data = Mst_attribute_value::where('group_value',$row['color'])->first();
          //  $att_grp_data = Mst_attribute_group::where('group_name',$row['attribute_group'])->first();
         //   $att_val_data = Mst_attribute_value::where('group_value',$row['attribute_value'])->first();
            $pro_categ = Mst_categories::where('category_name',$row['product_category'])->first();
            $vendor_data = Mst_store_agencies::where('agency_name',$row['vendor'])->first();
    
            $pro_Subcateg = Mst_SubCategory::where('sub_category_name',$row['sub_category_name'])->first();
            
            // $business_type_id = $business_type->business_type_id;
            // if(!isset($business_type))
            //         $business_type_id = 0;

            // if(!isset($color_data->attr_value_id))
            //     $color_data->attr_value_id = 0;

            // if(!isset($att_grp_data->attr_group_id))
            //     $att_grp_data->attr_group_id = 0;

            // if(!isset($att_val_data->attr_value_id))
            //     $att_val_data->attr_value_id = 0;   
            
            // if(!isset($pro_categ->category_id))
            //     $pro_categ->category_id = 0;      
          
            // if(!isset($vendor_data->agency_id))
            //     $vendor_data->agency_id = 0;   

            $dataz = [
                    'product_name' => $row['product_name'],
                    'product_name_slug' => Str::of($row['product_name'])->slug('-'),
                    'product_description' => $row['product_description'],
                    'regular_price' => $row['regular_price'],
                    'sale_price' => $row['sale_price'],
                    'tax_id' => @$tax_data->tax_id ,
                    'min_stock' => $row['minstock'] ,
                    'product_code' => $row['product_code'],
                    'business_type_id' => 0 ,
                  //  'color_id' => @$color_data->attr_value_id ,
                    'product_brand' => $row['product_brand'],
                    'attr_group_id' => 0 ,
                    'attr_value_id' => 0 ,
                    'product_cat_id' => @$pro_categ->category_id ,
                    'sub_category_id' => @$pro_Subcateg->sub_category_id,
                    'vendor_id' =>  @$vendor_data->agency_id ,
                    'product_base_image' => null,
                    'created_date' =>  Carbon::now()->format('Y-m-d'),
                    // 'created_by' => auth()->user()->id, // since store id and admin id seems to be 1 to solve jira issue 476
                    'created_by' => 0,
            ];

            // echo "<pre>";
            // print_r($dataz);die;
            

            $global_products = Mst_GlobalProducts::create($dataz);
        }

    }
    public function rules(): array
    {
        return [
            '*.product_name' => ['required'],
            '*.product_description' => ['required'],
            '*.regular_price' => ['required','numeric'],
            '*.sale_price' => ['required','numeric'],
            '*.product_category' => ['required'],
            '*.product_code' => ['required',Rule::unique('mst__global_products')],
        ];
    }
        

    public function onError(Throwable $e)
    {
        
    }
}
