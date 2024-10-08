<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\support\Facades\Hash;
use Illuminate\support\Facades\Validator;
use App\Models\User;
use App\Models\Department;
use App\Models\Departmentsub;
use App\Models\Departmentsubsub;
use App\Models\Products_vendor;
use App\Models\Status;
use App\Models\Position;
use App\Models\Product_spyprice;
use App\Models\Products;
use App\Models\Products_type;
use App\Models\Product_group;
use App\Models\Product_unit;
use App\Models\Products_category;
use App\Models\Article;
use App\Models\Product_prop;
use App\Models\Product_decline;
use App\Models\Department_sub_sub;
use App\Models\Products_request;
use App\Models\Products_request_sub;
use App\Models\Leave_leader;
use App\Models\Leave_leader_sub;
use App\Models\Book_type;
use App\Models\Book_import_fam;
use App\Models\Book_signature;
use App\Models\Bookrep;
use App\Models\Book_objective;
use App\Models\Book_senddep;
use App\Models\Book_senddep_sub;
use App\Models\Book_send_person;
use App\Models\Book_sendteam;
use App\Models\Bookrepdelete;
use App\Models\Car_status;
use App\Models\Car_index;
use App\Models\Article_status;
use App\Models\Car_type;
use App\Models\Product_brand;
use App\Models\Product_color;
use App\Models\Land;
use App\Models\Building;
use App\Models\Product_budget;
use App\Models\Product_method;
use App\Models\Product_buy;
use App\Models\Warehouse_inven;
use App\Models\Warehouse_inven_person;
use App\Models\Warehouse_rep;
use App\Models\Warehouse_rep_sub;
use App\Models\Warehouse_recieve;
use App\Models\Warehouse_recieve_sub;
use App\Models\Warehouse_stock;
use App\Models\Wh_unit;
use App\Models\Wh_product;
use Illuminate\Support\Facades\File;
use DataTables;
use PDF;
use Auth;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Intervention\Image\ImageManagerStatic as Image;

class WhController extends Controller
{
    public static function refnumber()
    {
        $year = date('Y');
        $maxnumber = DB::table('warehouse_rep')->max('warehouse_rep_id');
        if ($maxnumber != '' ||  $maxnumber != null) {
            $refmax = DB::table('warehouse_rep')->where('warehouse_rep_id', '=', $maxnumber)->first();
            if ($refmax->warehouse_rep_code != '' ||  $refmax->warehouse_rep_code != null) {
                $maxref = substr($refmax->warehouse_rep_code, -5) + 1;
            } else {
                $maxref = 1;
            }
            $ref = str_pad($maxref, 6, "0", STR_PAD_LEFT);
        } else {
            $ref = '000001';
        }
        $ye = date('Y') + 543;
        $y = substr($ye, -2);
        $refnumber = $ye . '-' . $ref;
        return $refnumber;


    }
    public function wh_dashboard(Request $request)
    {
        $startdate = $request->datepicker;
        $enddate = $request->datepicker2;
        $data['q'] = $request->query('q');
        $query = User::select('users.*')
            ->where(function ($query) use ($data) {
                $query->where('pname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('fname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('lname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('tel', 'like', '%' . $data['q'] . '%');
                $query->orwhere('username', 'like', '%' . $data['q'] . '%');
            });
        $data['users'] = $query->orderBy('id', 'DESC')->paginate(10);
        $data['department'] = Department::get();
        $data['department_sub'] = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position'] = Position::get();
        $data['status'] = Status::get();
        return view('wh.wh_dashboard', $data);
    }
    public function wh_plan(Request $request)
    {
        $startdate  = $request->datepicker;
        $enddate    = $request->datepicker2;
        $data['q']  = $request->query('q');
        $query = User::select('users.*')
            ->where(function ($query) use ($data) {
                $query->where('pname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('fname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('lname', 'like', '%' . $data['q'] . '%');
                $query->orwhere('tel', 'like', '%' . $data['q'] . '%');
                $query->orwhere('username', 'like', '%' . $data['q'] . '%');
            });
        $data['users']              = $query->orderBy('id', 'DESC')->paginate(10);
        $data['department']         = Department::get();
        $data['department_sub']     = Departmentsub::get();
        $data['department_sub_sub'] = Departmentsubsub::get();
        $data['position']           = Position::get();
        $data['status']             = Status::get();
        $data['wh_product']         = Wh_product::get();
        $yy1                        = date('Y') + 543;
        $yy2                        = date('Y') + 542;
        $yy3                        = date('Y') + 541;

        $data['wh_product']         = DB::select(
            'SELECT a.pro_id,a.pro_num,a.pro_year,a.pro_code,a.pro_name,b.wh_type_name,c.wh_unit_name ,a.active
                ,IFNULL(d.wh_unit_pack_qty,"1") as wh_unit_pack_qty
                ,IFNULL(d.wh_unit_pack_name,c.wh_unit_name) as unit_name
                ,e.*
                ,(SELECT total_plan FROM wh_plan WHERE pro_id = a.pro_id AND wh_plan_year = "'. $yy3.'") plan_65
                ,(SELECT total_plan FROM wh_plan WHERE pro_id = a.pro_id AND wh_plan_year = "'. $yy2.'") plan_66
                ,(SELECT total_plan FROM wh_plan WHERE pro_id = a.pro_id AND wh_plan_year = "'. $yy1.'") plan_67
                FROM wh_product a
                LEFT JOIN wh_type b ON b.wh_type_id = a.pro_type
                LEFT JOIN wh_unit c ON c.wh_unit_id = a.unit_id
                LEFT JOIN wh_unit_pack d ON d.wh_unit_id = a.pro_id
                LEFT JOIN wh_plan e ON e.pro_id = a.pro_id
            WHERE a.active ="Y" 
            GROUP BY a.pro_id
        ');
        
        return view('wh.wh_plan', $data,[
            'startdate'  => $startdate,
            'enddate'    => $enddate,
        ]);
    }
    public function warehouse_index(Request $request)
    {
        $data['product_category'] = Products_category::get();
        $data['product_type'] = Products_type::get();
        $data['product_group'] = Product_group::where('product_group_id', '=', 1)->orWhere('product_group_id', '=', 2)->get();
        $data['product_unit'] = Product_unit::get();
        $data['product_data'] = Products::where('product_groupid', '=', 1)->orwhere('product_groupid', '=', 2)->orderBy('product_id', 'DESC')->get();
        $data['countsttus'] = DB::table('warehouse_rep_sub')->where('warehouse_rep_sub_status', '=','2')->count();
        $data['warehouse_rep'] = DB::connection('mysql')
            ->select('select wr.warehouse_rep_id,wr.warehouse_rep_code,wr.warehouse_rep_no_bill,wr.warehouse_rep_po,wr.warehouse_rep_year,wr.warehouse_rep_send
        ,wr.warehouse_rep_user_id,wr.warehouse_rep_user_name,wr.warehouse_rep_inven_id,wr.warehouse_rep_inven_name,wr.warehouse_rep_total
        ,wr.warehouse_rep_vendor_id,wr.warehouse_rep_vendor_name,wr.warehouse_rep_date,wr.warehouse_rep_status
        ,wr.warehouse_rep_type
        from warehouse_rep wr
        order by wr.warehouse_rep_id desc
        ');
        // left outer join warehouse_rep_sub wrs on wrs.warehouse_rep_id = wr.warehouse_rep_id
        $data['users'] = User::get();
        $data['budget_year'] = DB::table('budget_year')->where('active','=','True')->get();
        $data['products_vendor'] = Products_vendor::get();
        $data['warehouse_inven'] = DB::table('warehouse_inven')->get();

        return view('warehouse.warehouse_index', $data);
    }
    // public function warehouse_add(Request $request)
    // {
    //     $data['budget_year'] = DB::table('budget_year')->get();
    //     $data['users'] = User::get();
    //     $data['products_vendor'] = Products_vendor::get();
    //     $data['warehouse_inven'] = DB::table('warehouse_inven')->get();

    //     $data['product_data'] = Products::where('product_groupid', '=', 1)->orwhere('product_groupid', '=', 2)->orderBy('product_id', 'DESC')->get();
    //     $data['products_typefree'] = DB::table('products_typefree')->get();
    //     $data['product_unit'] = DB::table('product_unit')->get();

    //     return view('warehouse.warehouse_add', $data);
    // }

    // public function warehouse_billsave(Request $request)
    // {
    //     $invenid = $request->warehouse_rep_inven_id;
    //     $vendorid = $request->warehouse_rep_vendor_id;
    //     $sendid = $request->warehouse_rep_send;
    //     // $proid = $request->product_id;

    //     if ($invenid == '') {
    //         return response()->json([
    //             'status'     => '100'
    //         ]);
    //     }else if ($vendorid == ''){
    //         return response()->json([
    //             'status'     => '150'
    //         ]);
    //     } else {

    //         // 2565-000002

    //         $add = new Warehouse_rep();
    //         $add->warehouse_rep_code = $request->warehouse_rep_code;
    //         $add->warehouse_rep_no_bill = $request->warehouse_rep_no_bill;
    //         $add->warehouse_rep_po = $request->warehouse_rep_po;
    //         $add->warehouse_rep_year = $request->warehouse_rep_year;
    //         $add->warehouse_rep_date = $request->warehouse_rep_date;
    //         $add->warehouse_rep_status = 'recieve';
    //         $add->warehouse_rep_send = '';
    //         $add->store_id = $request->store_id;

    //         $iduser = $request->warehouse_rep_user_id;
    //         if ($iduser != '') {
    //             $usersave = DB::table('users')->where('id', '=', $iduser)->first();
    //             $add->warehouse_rep_user_id = $usersave->id;
    //             $add->warehouse_rep_user_name = $usersave->fname . '  ' . $usersave->lname;
    //         } else {
    //             $add->warehouse_rep_user_id = '';
    //             $add->warehouse_rep_user_name = '';
    //         }


    //         if ($invenid != '') {
    //             $invensave = DB::table('warehouse_inven')->where('warehouse_inven_id', '=', $invenid)->first();
    //             $add->warehouse_rep_inven_id = $invensave->warehouse_inven_id;
    //             $add->warehouse_rep_inven_name = $invensave->warehouse_inven_name;
    //         } else {
    //             $add->warehouse_rep_inven_id = '';
    //             $add->warehouse_rep_inven_name = '';
    //         }


    //         if ($vendorid != '') {
    //             $vendorsave = DB::table('products_vendor')->where('vendor_id', '=', $vendorid)->first();
    //             $add->warehouse_rep_vendor_id = $vendorsave->vendor_id;
    //             $add->warehouse_rep_vendor_name = $vendorsave->vendor_name;
    //         } else {
    //             $add->warehouse_rep_vendor_id = '';
    //             $add->warehouse_rep_vendor_name = '';
    //         }

    //         $add->save();


    //         return response()->json([
    //             'status'     => '200'
    //         ]);

    //     }

    // }
     

    //======================ฟังชั่น====================

    // function checksummoney(Request $request)
    // {
    //     $SUP_TOTAL = $request->get('SUP_TOTAL');
    //     $PRICE_PER_UNIT = $request->get('PRICE_PER_UNIT');

    //     $sum = $SUP_TOTAL * $PRICE_PER_UNIT;

    //     $output = '<input type="hidden" type="text" name="sum" value="' . $sum . '" /><div style="text-align: right; margin-right: 10px;font-size: 14px;">' . number_format($sum, 2) . '</div>';
    //     echo $output;
    // }
 
    // function checkunitref(Request $request)
    // {

    //     $unitn  = $request->unitnew;
    //     // $SUP_UNIT_ID_H = $request->get('SUP_UNIT_ID_H');

    //     $infoproduct = DB::table('product_data')->where('product_id', '=', $unitn)->first();

    //     $infounits = DB::table('product_unit')->where('unit_id', '=', $infoproduct->product_unit_subid)->get();

    //     $output = '
    //                 <select name="product_unit_subid[]" id="product_unit_subid[]"  class="form-control form-control-sm" style="width: 100%;" >
    //             ';
    //     foreach ($infounits as $infounit) {
    //         $output .= ' <option value="' . $infounit->unit_id . '" selected>' . $infounit->unit_name . '</option>';

    //         $output .= '</select> ';
    //         echo $output;
    //     }
    // }

    
 
}
