<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\support\Facades\Hash;
use Illuminate\support\Facades\Validator;
use App\Models\User;
use App\Models\Acc_debtor;
use App\Models\Pttype_eclaim;
use App\Models\Account_listpercen;
use App\Models\Leave_month;
use App\Models\Acc_debtor_stamp;
use App\Models\Acc_debtor_sendmoney;
use App\Models\Pttype;
use App\Models\Pttype_acc; 
use App\Models\Department;
use App\Models\Departmentsub;
use App\Models\Air_stock_month;
use App\Models\Fire;
use App\Models\Product_spyprice;
use App\Models\Products;
use App\Models\Products_type;
use App\Models\Product_group;
use App\Models\Product_unit;
use App\Models\Products_category;
use App\Models\Air_report_ploblems_sub;
use App\Models\Product_prop;
use App\Models\Product_decline;
use App\Models\Department_sub_sub;
use App\Models\Products_vendor;
use App\Models\Status;
use App\Models\Air_plan_excel;
use App\Models\Air_plan;
use App\Models\Cctv_list;
use App\Models\Air_report_ploblems;
use App\Models\Air_repaire_supexcel;
use App\Models\Air_repaire_excel;
use App\Models\Article_status;
use App\Models\Air_repaire;
use App\Models\Air_list;
use App\Models\Gas_check;
use App\Models\Air_repaire_sub;
use App\Models\Air_repaire_ploblem;
use App\Models\Air_repaire_ploblemsub;
use App\Models\Air_maintenance;
use App\Models\Air_maintenance_list;
use App\Models\Product_budget;
use App\Models\Air_plan_month;
use App\Models\Air_temp_ploblem;
use App\Models\Air_edit_log;
use PDF;
use setasign\Fpdi\Fpdi;
use App\Models\Budget_year;
use Illuminate\Support\Facades\File;
use DataTables;
use Intervention\Image\ImageManagerStatic as Image;
use App\Mail\DissendeMail;
use Mail;
use Illuminate\Support\Facades\Storage;
use Auth;
use Http;
use SoapClient;
use Str;
// use SplFileObject;
use Arr;
// use Storage;
use GuzzleHttp\Client;

use App\Imports\ImportAcc_stm_ti;
use App\Imports\ImportAcc_stm_tiexcel_import;
use App\Imports\ImportAcc_stm_ofcexcel_import;
use App\Imports\ImportAcc_stm_lgoexcel_import;
use App\Models\Acc_1102050101_217_stam;
use App\Models\Acc_opitemrece_stm;

use SplFileObject;
use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;

date_default_timezone_set("Asia/Bangkok");


class MedicalgasController extends Controller
 {  
    public function medicalgas_db(Request $request)
    {        
        $months = date('m');
        $year = date('Y'); 
        $startdate   = $request->startdate;
        $enddate     = $request->enddate;
        $date_now    = date('Y-m-d');
        $y           = date('Y') + 543;  
        $data['budget_year'] = DB::table('budget_year')->get();
        $newdays     = date('Y-m-d', strtotime($date_now . ' -1 days')); //ย้อนหลัง 1 วัน
        $newweek     = date('Y-m-d', strtotime($date_now . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate     = date('Y-m-d', strtotime($date_now . ' -1 months')); //ย้อนหลัง 1 เดือน
        $newyear     = date('Y-m-d', strtotime($date_now . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew     = date('Y');
        $year_old    = date('Y')-1; 
        $startdate   = (''.$year_old.'-10-01');
        $enddate     = (''.$yearnew.'-09-30'); 
        $iduser      = Auth::user()->id;
        // dd($years);
        $datashow = DB::select(
            'SELECT s.air_supplies_id,s.supplies_name,COUNT(air_repaire_id) as c_repaire           
                FROM air_repaire a
                LEFT JOIN air_list al ON al.air_list_id = a.air_list_id
                LEFT JOIN users p ON p.id = a.air_staff_id 
                LEFT JOIN air_supplies s ON s.air_supplies_id = a.air_supplies_id
                GROUP BY a.air_supplies_id
        '); 
  
        $data['count_air'] = Air_list::where('active','Y')->count();
        

        return view('support_prs.gas.medicalgas_db',$data,[
            'startdate'     => $startdate,
            'enddate'       => $enddate, 
            'datashow'      => $datashow,
        ]);
    }
    public function medicalgas_list(Request $request)
    {
        $datenow = date('Y-m-d');
        $months = date('m');
        $year = date('Y'); 
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        $datashow = DB::select('SELECT * FROM gas_list WHERE gas_year = "'.$bg_yearnow.'" AND active ="Ready" ORDER BY gas_list_id DESC'); 
        // WHERE active="Y"
        return view('support_prs.gas.medicalgas_list',[
            'startdate'     => $startdate,
            'enddate'       => $enddate, 
            'datashow'      => $datashow,
        ]);
    }
    public function gas_check_list(Request $request)
    {
        $datenow   = date('Y-m-d');
        $months    = date('m');
        $year      = date('Y'); 
        $startdate = $request->startdate;
        $enddate   = $request->enddate;
        $newweek   = date('Y-m-d', strtotime($datenow . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate   = date('Y-m-d', strtotime($datenow . ' -1 months')); //ย้อนหลัง 1 เดือน
        $newyear   = date('Y-m-d', strtotime($datenow . ' -1 year')); //ย้อนหลัง 1 ปี 
        if ($startdate =='') {
            $datashow = DB::select(
                'SELECT a.gas_list_num,a.gas_list_name,a.detail,a.size
                ,b.check_year,b.check_date,b.check_time,b.gas_check_body,b.gas_check_body_name,b.gas_check_valve,b.gas_check_valve_name
                ,b.gas_check_pressure,b.gas_check_pressure_name,b.gas_check_pressure_min,b.gas_check_pressure_max,b.standard_value
                ,b.standard_value_min,b.standard_value_max,concat(p.fname," ",p.lname) as ptname 
                FROM gas_check b
                LEFT JOIN gas_list a ON a.gas_list_id = b.gas_list_id
                LEFT JOIN users p ON p.id = a.user_id 
                WHERE b.check_date BETWEEN "'.$newDate.'" AND "'.$datenow.'"
                ORDER BY b.gas_check_id DESC 
            '); 
        } else {
            $datashow = DB::select(
                'SELECT a.gas_list_num,a.gas_list_name,a.detail,a.size
                ,b.check_year,b.check_date,b.check_time,b.gas_check_body,b.gas_check_body_name,b.gas_check_valve,b.gas_check_valve_name
                ,b.gas_check_pressure,b.gas_check_pressure_name,b.gas_check_pressure_min,b.gas_check_pressure_max,b.standard_value
                ,b.standard_value_min,b.standard_value_max,concat(p.fname," ",p.lname) as ptname 
                FROM gas_check b
                LEFT JOIN gas_list a ON a.gas_list_id = b.gas_list_id
                LEFT JOIN users p ON p.id = a.user_id 
                WHERE b.check_date BETWEEN "'.$startdate.'" AND "'.$enddate.'"
                ORDER BY b.gas_check_id DESC  
            '); 
        }
     
        return view('support_prs.gas.gas_check_list',[
            'startdate'     => $startdate,
            'enddate'       => $enddate, 
            'datashow'      => $datashow,
        ]);
    }
    public function gas_check_tank(Request $request)
    {
        $datenow   = date('Y-m-d');
        $months    = date('m');
        $year      = date('Y'); 
        $startdate = $request->startdate;
        $enddate   = $request->enddate;
        $newweek   = date('Y-m-d', strtotime($datenow . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate   = date('Y-m-d', strtotime($datenow . ' -1 months')); //ย้อนหลัง 1 เดือน
        $newyear   = date('Y-m-d', strtotime($datenow . ' -1 year')); //ย้อนหลัง 1 ปี 
        // if ($startdate =='') {
        //     $datashow = DB::select(
        //         'SELECT a.gas_list_num,a.gas_list_name,a.detail,a.size
        //         ,b.check_year,b.check_date,b.check_time,b.gas_check_body,b.gas_check_body_name,b.gas_check_valve,b.gas_check_valve_name
        //         ,b.gas_check_pressure,b.gas_check_pressure_name,b.gas_check_pressure_min,b.gas_check_pressure_max,b.standard_value
        //         ,b.standard_value_min,b.standard_value_max,concat(p.fname," ",p.lname) as ptname 
        //         FROM gas_check b
        //         LEFT JOIN gas_list a ON a.gas_list_id = b.gas_list_id
        //         LEFT JOIN users p ON p.id = a.user_id 
        //         WHERE b.check_date BETWEEN "'.$newDate.'" AND "'.$datenow.'"
        //         ORDER BY b.gas_check_id DESC 
        //     '); 
        // } else {
        //     $datashow = DB::select(
        //         'SELECT a.gas_list_num,a.gas_list_name,a.detail,a.size
        //         ,b.check_year,b.check_date,b.check_time,b.gas_check_body,b.gas_check_body_name,b.gas_check_valve,b.gas_check_valve_name
        //         ,b.gas_check_pressure,b.gas_check_pressure_name,b.gas_check_pressure_min,b.gas_check_pressure_max,b.standard_value
        //         ,b.standard_value_min,b.standard_value_max,concat(p.fname," ",p.lname) as ptname 
        //         FROM gas_check b
        //         LEFT JOIN gas_list a ON a.gas_list_id = b.gas_list_id
        //         LEFT JOIN users p ON p.id = a.user_id 
        //         WHERE b.check_date BETWEEN "'.$startdate.'" AND "'.$enddate.'"
        //         ORDER BY b.gas_check_id DESC  
        //     '); 
        // }
     
        return view('support_prs.gas.gas_check_tank',[
            'startdate'     => $startdate,
            'enddate'       => $enddate, 
            // 'datashow'      => $datashow,
        ]);
    }

    public function gas_check_tank_save(Request $request)
    {
        $datenow       = date('Y-m-d');
        $months        = date('m');
        $year          = date('Y'); 
        $m             = date('H');
        $mm            = date('H:m:s');
        $datefull      = date('Y-m-d H:m:s');
        $bgs_year      = DB::table('budget_year')->where('years_now','Y')->first();
        $bg_yearnow    = $bgs_year->leave_year_id;
        $startdate     = $request->startdate;
        $enddate       = $request->enddate;
        $iduser        = Auth::user()->id;
        $name_         = User::where('id', '=',$iduser)->first();
        $name_check    = $name_->fname. '  '.$name_->lname;

        Gas_check::insert([
            'check_year'      =>  $bg_yearnow,
            'check_date'      =>  $request->check_date,
            'check_time'      =>  $mm,
            'gas_type'        =>  $request->gas_type,
            'standard_value'  =>  $request->standard_value,
            'check_time'      =>  $request->standard_value_min,
            'pressure_value'  =>  $request->pressure_value,
            'pariman_value'   =>  $request->pariman_value,
        ]);
         
        //แจ้งเตือนไลน์
        // if ($request->pariman_value < '50') {
            //แจ้งเตือน 
            function DateThailine($strDate)
            {
                $strYear = date("Y", strtotime($strDate)) + 543;
                $strMonth = date("n", strtotime($strDate));
                $strDay = date("j", strtotime($strDate));

                $strMonthCut = array("", "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.", "ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค.");
                $strMonthThai = $strMonthCut[$strMonth];
                return "$strDay $strMonthThai $strYear";
            }
            $header = "ตรวจสอบออกซิเจนเหลว(Main)";                                    
                $message =  $header. 
                "\n" . "วันที่ตรวจสอบ: " . DateThailine($request->check_date).
                "\n" . "เวลา : " . $mm ."". 
                "\n" . "ปริมาณมาตรฐาน : 124 inH2O". 
                "\n" . "ปริมาณวัดได้ : " . $request->pariman_value . 
                "\n" . "แรงดันมาตรฐาน : 5-12 bar". 
                "\n" . "ค่าแรงดันวัดได้ : " . $request->pressure_value;
                "\n" . "ผู้ตรวจสอบ : " . $name_check;
                // $linesend = "YNWHjzi9EA6mr5myMrcTvTaSlfOMPHMOiCyOfeSJTHr"; //ช่างซ่อม
                $linesend = "ZKL5cZS30sn2MYsP4ZD5x9REjJZkodKhQNrzD7y4GBK";  // พรส
                
                if ($linesend == null) {
                    $test = '';
                } else {
                    $test = $linesend;
                }

                if ($test !== '' && $test !== null) {
                    $chOne = curl_init();
                    curl_setopt($chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify");
                    curl_setopt($chOne, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($chOne, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($chOne, CURLOPT_POST, 1);
                    curl_setopt($chOne, CURLOPT_POSTFIELDS, $message);
                    curl_setopt($chOne, CURLOPT_POSTFIELDS, "message=$message");
                    curl_setopt($chOne, CURLOPT_FOLLOWLOCATION, 1);
                    $headers = array('Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $test . '',);
                    curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($chOne, CURLOPT_RETURNTRANSFER, 1);
                    $result = curl_exec($chOne);
                    if (curl_error($chOne)) {
                        echo 'error:' . curl_error($chOne);
                    } else {
                        $result_ = json_decode($result, true);                        
                    }
                    curl_close($chOne); 
                }
        // }
        //แจ้งเตือนไลน์
        // if ($request->pressure_value < '5') {
            
        // }
        

    return response()->json([
        'status'     => '200'
    ]);
}
  

 }