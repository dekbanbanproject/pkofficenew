<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\support\Facades\Hash;
use Illuminate\support\Facades\Validator;
use App\Models\User;
use App\Models\Ot_one;
use PDF;
use setasign\Fpdi\Fpdi;
use App\Models\Budget_year;
use Illuminate\Support\Facades\File;
use DataTables;
use Intervention\Image\ImageManagerStatic as Image;
// use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OtExport;
// use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Department;
use App\Models\Departmentsub;
use App\Models\Departmentsubsub;
use App\Models\Position; 
use App\Models\D_apiwalkin_ins;  
use App\Models\D_apiwalkin_adp;
use App\Models\D_apiwalkin_aer;
use App\Models\D_apiwalkin_orf;
use App\Models\D_apiwalkin_odx;
use App\Models\D_apiwalkin_cht;
use App\Models\D_apiwalkin_cha;
use App\Models\D_apiwalkin_oop;
use App\Models\D_claim; 
use App\Models\D_apiwalkin_dru;
use App\Models\D_apiwalkin_idx;
use App\Models\D_apiwalkin_iop;
use App\Models\D_apiwalkin_ipd;
use App\Models\D_apiwalkin_pat;
use App\Models\D_apiwalkin_opd;
use App\Models\D_walkin;
use App\Models\D_walkin_drug;
use App\Models\Audit_approve_code;

use App\Models\Fdh_sesion;
use App\Models\Fdh_ins;
use App\Models\Fdh_pat;
use App\Models\Fdh_opd;
use App\Models\Fdh_orf;
use App\Models\Fdh_odx;
use App\Models\Fdh_cht;
use App\Models\Fdh_cha;
use App\Models\Fdh_oop; 
use App\Models\Fdh_adp;
use App\Models\Fdh_dru;
use App\Models\Fdh_idx;
use App\Models\Fdh_iop;
use App\Models\Fdh_ipd;
use App\Models\Fdh_aer;
use App\Models\Fdh_irf;
use App\Models\Fdh_lvd;
use App\Models\D_ofc_401;
use App\Models\D_dru_out;
use App\Models\D_ofc_repexcel;
use App\Models\D_fdh;

use Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http; 
use SoapClient;
use Arr;   
use SplFileObject;
use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx; 
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory; 
use ZipArchive;  
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Stmt\If_;
use Stevebauman\Location\Facades\Location; 
use Illuminate\Filesystem\Filesystem;

use Mail;
use Illuminate\Support\Facades\Storage;
  
 
date_default_timezone_set("Asia/Bangkok");

class PreauditController extends Controller
{  
    public function pre_audit(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
 
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $yy = date('Y');
        $m = date('m');
        $newweek = date('Y-m-d', strtotime($date . ' -3 week')); //ย้อนหลัง 3 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -1 months')); //ย้อนหลัง 3 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y')+1;
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        if ($startdate == '') { 
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn
                    ,count(DISTINCT authen) as countauthen
                    ,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve,sum(debit) as sum_total 
                    FROM d_fdh WHERE vstdate BETWEEN "'.$start.'" AND "'.$end.'" 
                    AND projectcode ="OFC" AND debit > 0
                    AND an IS NULL
                    GROUP BY month(vstdate)
            ');  
            $data['fdh_ofc_m']    = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE month(vstdate) ="'.$m.'" AND projectcode ="OFC" AND debit > 0 AND authen IS NULL AND an IS NULL GROUP BY vn'); 
            // ,(SELECT sum(debit) FROM d_fdh WHERE month(vstdate)= "'.$newDate.'" AND "'.$date.'" AND authen IS NULL AND projectcode ="OFC") as no_total
            // ,(SELECT sum(debit) FROM d_fdh WHERE vstdate BETWEEN "'.$newDate.'" AND "'.$date.'" AND authen IS NOT NULL AND projectcode ="OFC") as sum_total            
            
        } else {
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn,count(DISTINCT authen) as countauthen,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve ,sum(debit) as sum_total  
                    FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND projectcode ="OFC" AND debit > 0 
                    AND an IS NULL
                    GROUP BY month(vstdate)
            '); 
            // ,(SELECT sum(debit) FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND authen IS NULL AND projectcode ="OFC") as no_total
            // ,(SELECT sum(debit) FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND authen IS NOT NULL AND projectcode ="OFC") as sum_total  
                       
                
            }   
                     
        return view('audit.pre_audit',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate, 
        ]);
    }  
    public function audit_approve_codenew(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
 
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $yy = date('Y');
        $m = date('m');
        // dd($m);
        $newweek = date('Y-m-d', strtotime($date . ' -3 week')); //ย้อนหลัง 3 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -3 months')); //ย้อนหลัง 3 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y');
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        
            
            $data['ofc_data']  = DB::connection('mysql')->select(
                'SELECT * FROM audit_approve_code  
            ');   
           
                
        return view('audit.audit_approve_codenew',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate, 
        ]);
    }   
    public function audit_approve_codenew_process(Request $request)
    {
        $startdate = $request->startdate;
        $enddate   = $request->enddate; 
        $date = date('Y-m-d');
  
        if ($startdate == '') { 
            return response()->json([
                'status'    => '100'
           ]);
        } else {
            Audit_approve_code::truncate();
                $iduser = Auth::user()->id;  
                $data_main_ = DB::connection('mysql2')->select(
                    'SELECT concat(p.pname,p.fname," ",p.lname) as ptname,  r.*,o.vstdate,o.vsttime,t.name as pttype_name   
                    FROM rcpt_debt r  
                    left outer join ovst o on o.vn=r.vn  
                    left outer join patient p on p.hn=o.hn  
                    left outer join pttype t on t.pttype = r.pttype  
                    where r.pt_type="OPD" and r.debt_date 
                     BETWEEN "'.$startdate.'" and "'.$enddate.'"
                    AND r.pttype IN("O1","O2","O3","O4","O5")
                    order by r.debt_id 
                ');  
                       
                foreach ($data_main_ as $key => $value) {    
                    
                    Audit_approve_code::insert([
                            'vn'                  => $value->vn,
                            'hn'                  => $value->hn, 
                            'ptname'              => $value->ptname,
                            'staff'               => $value->staff, 
                            'debt_date'           => $value->debt_date,
                            'debt_time'           => $value->debt_time,
                            'amount'              => $value->amount,
                            'total_amount'        => $value->total_amount,
                            'sss_approval_code'   => $value->sss_approval_code,
                            'sss_amount'          => $value->sss_amount,
                            
                        ]);
                    
                }   
            }   
                     
            return response()->json([
                'status'    => '200'
           ]);
    }   
    function audit_approve_codenew_excel(Request $request)
    {  
        Audit_approve_code::truncate();
                $the_file = $request->file('files'); 
                // $file_ = $request->file('files')->getClientOriginalName(); //ชื่อไฟล์ 
   
                $spreadsheet = IOFactory::load($the_file->getRealPath()); 
                $sheet        = $spreadsheet->setActiveSheetIndex(0);
                $row_limit    = $sheet->getHighestDataRow(); 
                $row_range    = range('1',$row_limit ); 
                $startcount = '1';
                $data = array();
                foreach ($row_range as $row ) {
                    $vst = $sheet->getCell( 'G' . $row )->getValue();  
                    // $day = substr($vst,0,2);
                    // $mo = substr($vst,3,2);
                    // $year = substr($vst,6,4);
                    // $vstdate = $year.'-'.$mo.'-'.$day;
                  
                    // $timestamp = strtotime($vst);
                    // $originalDate = "2023-05-31"; 
                    // Unix time = 1685491200
                    // $unixTime = strtotime($originalDate);
                    // Pass the new date format as a string and the original date in Unix time
                    $newDate = date("Y-m-d", strtotime($vst));
                    // echo $newDate;
                    // date($format,strtotime($vst));

                    // dd($newDate);
                    $data[] = [
                        'ECLAIM_NO'         =>$sheet->getCell( 'B' . $row )->getValue(),
                        'CID_SPSCH'         =>$sheet->getCell( 'D' . $row )->getValue(),
                        'PTNAME_SPSCH'      =>$sheet->getCell( 'E' . $row )->getValue(), 
                        // 'HN_SPSCH'          =>$sheet->getCell( 'F' . $row )->getValue(), 
                        'VSTDATE_SPSCH'     =>$newDate,
                        'VSTTIME_SPSCH'     =>$sheet->getCell( 'H' . $row )->getValue(), 
                        'STATUS_SPSCH'      =>$sheet->getCell( 'I' . $row )->getValue(), 
                        'Tran_ID'           =>$sheet->getCell( 'K' . $row )->getValue(), 
                        'CLAIM'             =>$sheet->getCell( 'L' . $row )->getValue(), 
                        'REP'               =>$sheet->getCell( 'M' . $row )->getValue(), 
                        'ERROR_C'           =>$sheet->getCell( 'N' . $row )->getValue(), 
                        'Deny'              =>$sheet->getCell( 'O' . $row )->getValue(), 
                        'Channel'           =>$sheet->getCell( 'P' . $row )->getValue()  
                    ];
                    $startcount++;  
                } 
                $for_insert = array_chunk($data, length:1000);
                foreach ($for_insert as $key => $datasert) {
                    Audit_approve_code::insert($datasert); 
                } 
                return response()->json([
                    'status'     => '200'
                ]);  
    }
    public function importplan_send(Request $request)
    {
        try{
            $data_ = DB::connection('mysql')->select('SELECT * FROM air_plan_excel');
            foreach ($data_ as $key => $value) {
              
                    $check = Air_plan::where('air_plan_year','=',$value->air_plan_year)->where('air_list_num','=',$value->air_list_num)->where('air_plan_month_id','=',$value->air_plan_month_id)->count();
                    if ($check > 0) {
                    } else {
                        $add = new Air_plan();
                        $add->air_plan_year         = $value->air_plan_year;
                        $add->air_list_num          = $value->air_list_num;
                        $add->air_plan_month_id     = $value->air_plan_month_id;
                        $add->PlanDOC               = $value->PlanDOC; 
                        $add->save(); 
                    } 
                
            }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return back()->withErrors('There was a problem uploading the data!');
            }
            Air_plan_excel::truncate();

            return response()->json([
                'status'     => '200'
            ]); 
        // return redirect()->back();
    }
    public function audit_approve_code(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
 
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $yy = date('Y');
        $m = date('m');
        // dd($m);
        $newweek = date('Y-m-d', strtotime($date . ' -3 week')); //ย้อนหลัง 3 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -3 months')); //ย้อนหลัง 3 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y');
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        if ($startdate == '') { 
            // $data['fdh_ofc']    = DB::connection('mysql')->select(
            //     'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
            //         ,count(DISTINCT vn) as countvn
            //         ,count(DISTINCT authen) as countauthen
            //         ,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve
            //         ,sum(debit) as sum_total 
            //         FROM d_fdh WHERE vstdate BETWEEN "'.$start.'" AND "'.$end.'" 
            //         AND projectcode ="OFC" AND debit > 0 
            //         AND (an IS NULL OR an ="") 
            //         AND (hn IS NOT NULL OR hn <>"")
            //         GROUP BY month(vstdate)
            // ');  
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn  
                    ,sum(debit) as sum_total 
                    FROM d_fdh   
                    WHERE projectcode ="OFC" AND debit > 0 
                    AND (an IS NULL OR an ="") 
                   
                    AND vstdate BETWEEN "'.$start.'" AND "'.$end.'"  
                    GROUP BY month(vstdate)
            '); 
            // AND (an IS NULL OR an ="")     AND hn <>"" 
            // $data['fdh_ofc_m']    = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE month(vstdate) BETWEEN "'.$newDate.'" AND "'.$m.'" AND projectcode ="OFC" AND authen IS NULL AND an IS NULL GROUP BY vn'); 
            // $data['fdh_ofc_m']       = DB::connection('mysql')->select(
            //     'SELECT * FROM d_fdh 
            //     WHERE projectcode ="OFC" AND debit > 0 
            //     AND hn<>"" AND (authen IS NULL OR authen ="") 
            //     AND (an IS NULL OR an ="") AND vstdate BETWEEN "'.$start.'" AND "'.$end.'"           
            //     '); 
            $data['fdh_ofc_all']  = DB::connection('mysql')->select(
                'SELECT * FROM d_fdh 
                    WHERE projectcode ="OFC"  
                    AND hn <>"" 
                    AND (authen IS NULL OR authen ="") 
                    AND (an IS NULL OR an ="") AND debit > 0
                    AND vstdate BETWEEN "'.$start.'" AND "'.$end.'" 
                   
            '); 
                // $data['fdh_ofc_momth']    = DB::connection('mysql')->select(
                //     'SELECT * FROM d_fdh WHERE month(vstdate) ="'.$m.'" AND year(vstdate) ="'.$yy.'" 
                //     AND projectcode ="OFC" AND debit > 0 AND hn<>"" 
                //     AND (authen IS NULL OR authen ="") 
                //     AND (an IS NULL OR an ="")  
                // '); 
                $data['fdh_ofc_momth']    = DB::connection('mysql')->select(
                    'SELECT * FROM d_fdh 
                    WHERE projectcode ="OFC" 
                    AND hn <>""
                    AND (authen IS NULL OR authen ="") 
                    AND (an IS NULL OR an ="")
                    AND month(vstdate) ="'.$m.'" AND year(vstdate) ="'.$yy.'" AND debit > 0
     
                '); 
            // ,(SELECT sum(debit) FROM d_fdh WHERE month(vstdate)= "'.$newDate.'" AND "'.$date.'" AND authen IS NULL AND projectcode ="OFC") as no_total
            // ,(SELECT sum(debit) FROM d_fdh WHERE vstdate BETWEEN "'.$newDate.'" AND "'.$date.'" AND authen IS NOT NULL AND projectcode ="OFC") as sum_total            
            
        } else {
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn,count(DISTINCT authen) as countauthen,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve ,sum(debit) as sum_total  
                    FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND projectcode ="OFC" 
                    AND (an IS NULL OR an ="") AND (hn IS NOT NULL OR hn <>"") AND debit > 0
                    GROUP BY month(vstdate)
            '); 
            // ,(SELECT sum(debit) FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND authen IS NULL AND projectcode ="OFC") as no_total
            // ,(SELECT sum(debit) FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND authen IS NOT NULL AND projectcode ="OFC") as sum_total  
                       
                
            }   
                     
        return view('audit.audit_approve_code',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate, 
        ]);
    } 
    public function audit_approve_detail(Request $request,$month,$year)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
 
        $date = date('Y-m-d');
        // $y = date('Y') + 543;
        // $yy = date('Y');
        // $m = date('m');
 
        // $newweek = date('Y-m-d', strtotime($date . ' -3 week')); //ย้อนหลัง 3 สัปดาห์
        // $newDate = date('Y-m-d', strtotime($date . ' -3 months')); //ย้อนหลัง 3 เดือน
        // $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y')+1;
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        // if ($startdate == '') { 
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn
                    ,count(DISTINCT authen) as countauthen
                    ,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve,sum(debit) as sum_total 
                    FROM d_fdh WHERE vstdate BETWEEN "'.$start.'" AND "'.$end.'" 
                    AND projectcode ="OFC" AND debit > 0 AND hn<>""
                    AND an IS NULL
                    GROUP BY month(vstdate)
            ');  
            // $data['fdh_ofc_m']    = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE month(vstdate) BETWEEN "'.$newDate.'" AND "'.$m.'" AND projectcode ="OFC" AND authen IS NULL AND an IS NULL GROUP BY vn'); 
            $data['fdh_ofc_m']       = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE projectcode ="OFC" AND debit > 0 AND hn<>"" AND authen IS NULL AND an IS NULL GROUP BY vn'); 
            $data['fdh_ofc_momth']    = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE month(vstdate) ="'.$month.'" AND year(vstdate) ="'.$year.'" AND projectcode ="OFC" AND debit > 0 AND hn<>"" AND authen IS NULL AND an IS NULL GROUP BY vn'); 
           
        // } else {
        //     $data['fdh_ofc']    = DB::connection('mysql')->select(
        //         'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
        //             ,count(DISTINCT vn) as countvn,count(DISTINCT authen) as countauthen,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve ,sum(debit) as sum_total  
        //             FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND projectcode ="OFC" 
        //             AND an IS NULL
        //             GROUP BY month(vstdate)
        //     '); 
                      
                
        //     }   
                     
        return view('audit.audit_approve_detail',$data,[
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'month'         => $month,
            'year'          => $year, 
        ]);
    } 
    public function approve_destroy(Request $request,$id)
    {
        $del = D_fdh::find($id);   
        $del->delete();  
        return response()->json(['status' => '200']);
    }    
    public function pre_audit_process_a(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate; 
        $date = date('Y-m-d');
  
        if ($startdate == '') { 
            return response()->json([
                'status'    => '100'
           ]);
        } else {
                $iduser = Auth::user()->id;  
                $data_main_ = DB::connection('mysql2')->select(
                    'SELECT v.vn,o.an,v.cid,v.hn,concat(pt.pname,pt.fname," ",pt.lname) ptname
                            ,v.vstdate,v.pttype,IFNULL(rd.sss_approval_code,k.approval_code) as Apphos,v.inc04 as xray,h.hospcode,h.name as hospcode_name
                            ,rd.amount AS price_ofc,v.income,ptt.hipdata_code,group_concat(distinct k.amount) as edc,r.rcpno,rd.amount as rramont,v.paid_money,op.cc
                            ,group_concat(DISTINCT hh.appr_code,":",hh.transaction_amount,"/") AS AppKTB 
                            ,GROUP_CONCAT(DISTINCT ov.icd10 order by ov.diagtype) AS icd10,v.pdx,ovv.name as active_status,v.income-v.discount_money-v.rcpt_money as debit
                            FROM vn_stat v
                            LEFT OUTER JOIN patient pt ON v.hn=pt.hn
                            LEFT OUTER JOIN ovstdiag ov ON v.vn=ov.vn
                            LEFT OUTER JOIN ovst o ON v.vn=o.vn
                            LEFT OUTER JOIN hospcode h on h.hospcode = v.hospmain
                            LEFT OUTER JOIN opdscreen op ON v.vn = op.vn
                            LEFT OUTER JOIN pttype ptt ON v.pttype=ptt.pttype 
                            LEFT OUTER JOIN rcpt_print r on r.vn =v.vn
                            LEFT OUTER JOIN rcpt_debt rd ON v.vn = rd.vn
                            LEFT OUTER JOIN hpc11_ktb_approval hh on hh.pid = pt.cid and hh.transaction_date = v.vstdate                        
                            LEFT OUTER JOIN ktb_edc_transaction k on k.vn = v.vn  
                            LEFT OUTER JOIN ovst ot on ot.vn = v.vn
                            LEFT OUTER JOIN ovstost ovv on ovv.ovstost = ot.ovstost

                        WHERE o.vstdate BETWEEN "'.$startdate.'" and "'.$enddate.'"
                        AND v.pttype in("O1","O2","O3","O4","O5")                    
                        AND v.pttype not in ("OF","FO")                          
                        AND o.an is null 
                        GROUP BY v.vn 
                ');  
                       
                foreach ($data_main_ as $key => $value) {    
                    $check_ofc = D_fdh::where('vn',$value->vn)->where('projectcode','OFC')->count(); 
                    if ($check_ofc > 0) { 
                        D_fdh::where('vn',$value->vn)->where('projectcode','OFC')->update([ 
                            'an'             => $value->an,    
                            'pdx'            => $value->pdx,  
                            'icd10'          => $value->icd10, 
                            'debit'          => $value->debit,
                            'pttype'         => $value->pttype,
                            'price_ofc'      => $value->price_ofc,
                            'active_status'  => $value->active_status,
                            'authen'         => $value->Apphos,
                            'AppKTB'         => $value->AppKTB,
                            'edc'            => $value->edc,
                            'rcpno'          => $value->rcpno,
                            'paid_money'     => $value->paid_money,
                            'cc'             => $value->cc
                        ]);
                    } else { 
                        D_fdh::insert([
                            'vn'           => $value->vn,
                            'hn'           => $value->hn,
                            'an'           => $value->an, 
                            'cid'          => $value->cid,
                            'pttype'       => $value->pttype,                           
                            'ptname'       => $value->ptname,
                            'vstdate'      => $value->vstdate,
                            'authen'       => $value->Apphos,
                            'AppKTB'       => $value->AppKTB,
                            'edc'          => $value->edc,
                            'rcpno'        => $value->rcpno,
                            'paid_money'   => $value->paid_money,
                            'projectcode'  => 'OFC', 
                            'pdx'          => $value->pdx,  
                            'icd10'        => $value->icd10,
                            'hospcode'     => $value->hospcode, 
                            'debit'        => $value->debit,
                            'price_ofc'      => $value->price_ofc,
                            'active_status'  => $value->active_status,
                            'cc'             => $value->cc
                        ]);
                    }  
                }   
            }   
                     
            return response()->json([
                'status'    => '200'
           ]);
    } 
    public function pre_audit_chart(Request $request)
    {
        $date = date("Y-m-d"); 
        $y = date('Y');
        
        $labels = [
            1 => "ม.ค", "ก.พ", "มี.ค", "เม.ย", "พ.ย", "มิ.ย", "ก.ค","ส.ค","ก.ย","ต.ค","พ.ย","ธ.ค"
          ];
        $chart = DB::connection('mysql')->select(' 
            SELECT
            MONTH(c.vstdate) as month
            ,YEAR(c.vstdate) as year
            ,DAY(c.vstdate) as day
            ,COUNT(DISTINCT c.vn) as countvn
            ,COUNT(c.claimcode) as Authen
            ,COUNT(c.vn)-COUNT(c.claimcode) as Noauthen
            from check_sit_auto c
            LEFT JOIN kskdepartment k ON k.depcode = c.main_dep
            WHERE year(c.vstdate) = "'.$y.'" 
            AND c.pttype NOT IN("M1","M2","M3","M4","M5","M6","13","23","91","X7")
            AND c.main_dep NOT IN("011","036","107")
            GROUP BY month
        ');
        foreach ($chart as $key => $value) {
            
            if ($value->countvn > 0) {
                $dataset[] = [
                    'label'     => $labels,
                    'count'     => $value->countvn,
                    'Authen'    => $value->Authen,
                    'Noauthen'  => $value->Noauthen
                ];
            }
        }
 
        $Dataset1 = $dataset;
        // $Dataset2 = $dataset_2; 
        return response()->json([
            'status'                    => '200', 
            'Dataset1'                  => $Dataset1,
            // 'Dataset2'                  => $Dataset2
        ]);
    }
    public function audit_pdx(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
 
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $yy = date('Y');
        $m = date('m');
        // dd($m);
        $newweek = date('Y-m-d', strtotime($date . ' -3 week')); //ย้อนหลัง 3 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -3 months')); //ย้อนหลัง 3 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y');
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30');
        // dd($end);
        if ($startdate == '') { 
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn  
                    ,sum(debit) as sum_total 
                    FROM d_fdh   
                    WHERE projectcode ="OFC" AND debit > 0 
                    AND hn <>""
                    AND (an IS NULL OR an ="")
                    AND vstdate BETWEEN "'.$start.'" AND "'.$end.'"  
                    GROUP BY month(vstdate)
            ');  
            // AND (pdx IS NULL OR pdx ="") 
            // AND projectcode ="OFC"  
            // AND (an IS NULL OR an ="") AND (hn IS NOT NULL OR hn <>"")  
            $data['fdh_ofc_all']       = DB::connection('mysql')->select(
                'SELECT * FROM d_fdh 
                    WHERE projectcode ="OFC" AND debit > 0 
                    AND hn <>"" AND (pdx IS NULL OR pdx ="") 
                    AND (an IS NULL OR an ="") 
                    AND vstdate BETWEEN "'.$start.'" AND "'.$end.'" 
                   
            '); 
            // $data['fdh_ofc_m']        = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE projectcode ="OFC" AND (pdx IS NULL OR pdx ="") AND (an IS NULL OR an ="") AND (hn IS NOT NULL OR hn <>"") GROUP BY vn'); 
            $data['fdh_ofc_momth']    = DB::connection('mysql')->select(
                'SELECT * FROM d_fdh 
                WHERE projectcode ="OFC" AND debit > 0
                AND hn <>"" AND (pdx IS NULL OR pdx ="") 
                AND (an IS NULL OR an ="")
                AND month(vstdate) ="'.$m.'" AND year(vstdate) ="'.$yy.'" 
 
            '); 
            
        } else {
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn,count(pdx) as countpdx,count(DISTINCT vn)-count(pdx) as count_no_diag ,sum(debit) as sum_total  
                    FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND projectcode ="OFC" 
                    AND (an IS NULL OR an ="") AND (hn IS NOT NULL OR hn <>"")
                    GROUP BY month(vstdate)
            '); 
                
            }   
            // AND (pdx IS NULL OR pdx ="")        
        return view('audit.audit_pdx',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate, 
        ]);
    } 
    public function audit_pdx_detail(Request $request,$month,$year)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
 
        $date = date('Y-m-d');
    
        $yearnew = date('Y')+1;
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        // if ($startdate == '') { 
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn
                    ,count(DISTINCT authen) as countauthen
                    ,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve,sum(debit) as sum_total 
                    FROM d_fdh WHERE vstdate BETWEEN "'.$start.'" AND "'.$end.'" 
                    AND projectcode ="OFC" AND debit > 0
                    AND (an IS NULL OR an ="")
                    GROUP BY month(vstdate)
            ');  
         
            $data['fdh_ofc_m']       = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE projectcode ="OFC" AND (pdx IS NULL OR pdx ="") AND (an IS NULL OR an ="") AND debit > 0 GROUP BY vn'); 
            $data['fdh_ofc_momth']    = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE month(vstdate) ="'.$month.'" AND debit > 0 AND year(vstdate) ="'.$year.'" AND projectcode ="OFC" AND (pdx IS NULL OR pdx ="") AND (an IS NULL OR an ="") GROUP BY vn'); 
       
                     
        return view('audit.audit_pdx_detail',$data,[
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'month'         => $month,
            'year'          => $year, 
        ]);
    } 
    public function audit_pdx_detail_print(Request $request,$month,$year)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
 
        $date = date('Y-m-d');
    
        $yearnew = date('Y')+1;
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        // if ($startdate == '') { 
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn
                    ,count(DISTINCT authen) as countauthen
                    ,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve,sum(debit) as sum_total 
                    FROM d_fdh WHERE vstdate BETWEEN "'.$start.'" AND "'.$end.'" 
                    AND projectcode ="OFC" AND debit > 0
                    AND (an IS NULL OR an ="")
                    GROUP BY month(vstdate)
            ');  
         
            $data['fdh_ofc_m']       = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE projectcode ="OFC" AND (pdx IS NULL OR pdx ="") AND (an IS NULL OR an ="") AND debit > 0 GROUP BY vn'); 
            $data['fdh_ofc_momth']    = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE month(vstdate) ="'.$month.'" AND debit > 0 AND year(vstdate) ="'.$year.'" AND projectcode ="OFC" AND (pdx IS NULL OR pdx ="") AND (an IS NULL OR an ="") GROUP BY vn'); 
       
                     
        return view('audit.audit_pdx_detail_print',$data,[
            'startdate'     => $startdate,
            'enddate'       => $enddate,
            'month'         => $month,
            'year'          => $year, 
        ]);
    } 
    public function talassemaie(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
        $budget_year = DB::table('budget_year')->where('active','=',true)->first();
        $leave_month_year = DB::table('leave_month')->orderBy('MONTH_ID', 'ASC')->get();
        $by_startnew = $budget_year->date_begin;
        $by_endnew = $budget_year->date_end;
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $yy = date('Y');
        $m = date('m');
        // dd($m);
        $newweek = date('Y-m-d', strtotime($date . ' -3 week')); //ย้อนหลัง 3 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -3 months')); //ย้อนหลัง 3 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y')+1;
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        // if ($startdate == '') { 
            $data['datashow']    = DB::connection('mysql2')->select(
                'SELECT year(v.vstdate) as years ,month(v.vstdate) as months,year(v.vstdate) as days 
                    ,count(DISTINCT v.vn) as countvn 
                    ,(SELECT SUM(qty) qty FROM opitemrece WHERE vn = v.vn AND icode IN("1590015","1520001")) as total_qty
                    ,(SELECT SUM(sum_price) sum_price FROM opitemrece WHERE vn = v.vn AND icode IN("1590015","1520001")) as sum_total                    
                    FROM vn_stat v
                    LEFT JOIN visit_pttype vs on vs.vn = v.vn
                    LEFT JOIN ovst o on o.vn = v.vn
                    LEFT JOIN opdscreen s ON s.vn = v.vn
                    LEFT JOIN opitemrece ot ON ot.vn = v.vn
                    LEFT JOIN drugitems d ON d.icode = ot.icode
                    LEFT JOIN patient p on p.hn=v.hn
                    LEFT JOIN pttype pt on pt.pttype = v.pttype  
                    LEFT JOIN opduser op on op.loginname = o.staff
                    WHERE v.vstdate BETWEEN "'.$by_startnew.'" AND "'.$by_endnew.'" 
                    AND pt.hipdata_code ="UCS" AND ot.icode IN("1590015","1520001")  
                    AND (o.an IS NULL OR o.an = "")
                    GROUP BY month(v.vstdate)
            ');   
            $data['datashow_m']    = DB::connection('mysql2')->select(
                'SELECT v.vn,ot.an,v.hn,v.cid,concat(p.pname,p.fname," ",p.lname) as ptname,v.pttype,v.vstdate,v.age_y,l.lab_items_name AS lab_name,d.name as drugname 
                    ,(SELECT SUM(qty) qty FROM opitemrece WHERE vn = v.vn AND icode IN("1590015","1520001")) as total_qty
                    ,(SELECT SUM(sum_price) sum_price FROM opitemrece WHERE vn = v.vn AND icode IN("1590015","1520001")) as total_drug                    
                    FROM vn_stat v
                    LEFT JOIN visit_pttype vs on vs.vn = v.vn  
                    LEFT JOIN opitemrece ot ON ot.vn = v.vn 
                    LEFT JOIN drugitems d ON d.icode = ot.icode
                    LEFT JOIN patient p on p.hn=v.hn
                    LEFT JOIN pttype pt on pt.pttype = v.pttype   
                    LEFT OUTER JOIN lab_head lh ON lh.vn = v.vn  
                    LEFT OUTER JOIN lab_order lo on lo.lab_order_number=lh.lab_order_number 
                    LEFT OUTER JOIN lab_order lo1 on lo1.lab_order_number=lh.lab_order_number  
                    LEFT OUTER JOIN lab_items l on l.lab_items_code=lo.lab_items_code  
                    LEFT OUTER JOIN lab_items l1 on l1.lab_items_code=lo1.lab_items_code  
                    WHERE month(v.vstdate) ="'.$m.'" AND year(v.vstdate) ="'.$yy.'"
                    AND pt.hipdata_code ="UCS" AND ot.icode IN("1590015","1520001")   
                    AND (ot.an IS NULL OR ot.an = "")
                    GROUP BY v.vn ORDER BY v.age_y ASC
            ');  
            // -- and v.age_y between "35" and "59"  
            // $data['fdh_ofc_m']        = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE projectcode ="OFC" AND (pdx IS NULL OR pdx ="") AND (an IS NULL OR an ="") AND (hn IS NOT NULL OR hn <>"") GROUP BY vn'); 
            // $data['fdh_ofc_momth']    = DB::connection('mysql')->select('SELECT * FROM d_fdh WHERE month(vstdate) ="'.$m.'" AND projectcode ="OFC" AND (pdx IS NULL OR pdx ="") AND (an IS NULL OR an ="") AND (hn IS NOT NULL OR hn <>"") GROUP BY vn'); 
          
              
        return view('audit.talassemaie',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate, 
        ]);
    } 
    public function talassemaie_detail(Request $request,$month,$year)
    {
        $startdate              = $request->startdate;
        $enddate                = $request->enddate;
        $budget_year            = DB::table('budget_year')->where('active','=',true)->first();
        $month_year             = DB::table('leave_month')->where('MONTH_ID', $month)->first();
        $by_startnew            = $budget_year->date_begin;
        $by_endnew              = $budget_year->date_end;
        $data['month_year']     = $month_year->MONTH_NAME;
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $yy = date('Y');
        $m = date('m');
        // dd($m);
        $newweek = date('Y-m-d', strtotime($date . ' -3 week')); //ย้อนหลัง 3 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -3 months')); //ย้อนหลัง 3 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y')+1;
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        // if ($startdate == '') { 
            $data['datashow']    = DB::connection('mysql2')->select(
                'SELECT year(v.vstdate) as years ,month(v.vstdate) as months,year(v.vstdate) as days 
                    ,count(DISTINCT v.vn) as countvn
                    ,(SELECT SUM(qty) qty FROM opitemrece WHERE vn = v.vn AND icode IN("1590015","1520001")) as total_qty
                    ,(SELECT SUM(sum_price) sum_price FROM opitemrece WHERE vn = v.vn AND icode IN("1590015","1520001")) as sum_total                      
                    FROM vn_stat v
                    LEFT JOIN visit_pttype vs on vs.vn = v.vn
                    LEFT JOIN ovst o on o.vn = v.vn
                    LEFT JOIN opdscreen s ON s.vn = v.vn
                    LEFT JOIN opitemrece ot ON ot.vn = v.vn
                    LEFT JOIN drugitems d ON d.icode = ot.icode
                    LEFT JOIN patient p on p.hn=v.hn
                    LEFT JOIN pttype pt on pt.pttype = v.pttype  
                    LEFT JOIN opduser op on op.loginname = o.staff
                    WHERE v.vstdate BETWEEN "'.$by_startnew.'" AND "'.$by_endnew.'" 
                    AND pt.hipdata_code ="UCS" AND ot.icode IN("1590015","1520001")  
                    AND (o.an IS NULL OR o.an = "")
                    GROUP BY month(v.vstdate)
            ');   
            $data['datashow_m']    = DB::connection('mysql2')->select(
                'SELECT v.vn,ot.an,v.hn,v.cid,concat(p.pname,p.fname," ",p.lname) as ptname,v.pttype,v.vstdate,v.age_y,l.lab_items_name AS lab_name,d.name as drugname 
                 
                    ,(SELECT SUM(qty) qty FROM opitemrece WHERE vn = v.vn AND icode IN("1590015","1520001")) as total_qty
                    ,(SELECT SUM(sum_price) sum_price FROM opitemrece WHERE vn = v.vn AND icode IN("1590015","1520001")) as total_drug                       
                    FROM vn_stat v
                    LEFT JOIN visit_pttype vs on vs.vn = v.vn  
                    LEFT JOIN opitemrece ot ON ot.vn = v.vn 
                    LEFT JOIN drugitems d ON d.icode = ot.icode
                    LEFT JOIN patient p on p.hn=v.hn
                    LEFT JOIN pttype pt on pt.pttype = v.pttype   
                    LEFT OUTER JOIN lab_head lh ON lh.vn = v.vn  
                    LEFT OUTER JOIN lab_order lo on lo.lab_order_number=lh.lab_order_number 
                    LEFT OUTER JOIN lab_order lo1 on lo1.lab_order_number=lh.lab_order_number  
                    LEFT OUTER JOIN lab_items l on l.lab_items_code=lo.lab_items_code  
                    LEFT OUTER JOIN lab_items l1 on l1.lab_items_code=lo1.lab_items_code  
                    WHERE month(v.vstdate) ="'.$month.'" AND year(v.vstdate) ="'.$year.'"
                    AND pt.hipdata_code ="UCS" AND ot.icode IN("1590015","1520001")   
                    AND (ot.an IS NULL OR ot.an = "")
                    GROUP BY v.vn ORDER BY v.age_y ASC
            ');  
          
              
        return view('audit.talassemaie_detail',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate, 
        ]);
    } 










    public function audit_only(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;
 
        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $yy = date('Y');
        $m = date('m');
        // dd($m);
        $newweek = date('Y-m-d', strtotime($date . ' -3 week')); //ย้อนหลัง 3 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -3 months')); //ย้อนหลัง 3 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
        $yearnew = date('Y');
        $yearold = date('Y')-1;
        $start = (''.$yearold.'-10-01');
        $end = (''.$yearnew.'-09-30'); 
        if ($startdate == '') { 
            
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn  
                    ,sum(debit) as sum_total 
                    FROM d_fdh   
                    WHERE projectcode ="OFC" AND debit > 0 
                    AND (an IS NULL OR an ="") 
                   
                    AND vstdate BETWEEN "'.$start.'" AND "'.$end.'"  
                    GROUP BY month(vstdate)
            '); 
           
            $data['fdh_ofc_all']  = DB::connection('mysql')->select(
                'SELECT * FROM d_fdh 
                    WHERE projectcode ="OFC"  
                    AND hn <>"" 
                    AND (authen IS NULL OR authen ="") 
                    AND (an IS NULL OR an ="") AND debit > 0
                    AND vstdate BETWEEN "'.$start.'" AND "'.$end.'" 
                   
            '); 
                
                $data['fdh_ofc_momth']    = DB::connection('mysql')->select(
                    'SELECT * FROM d_fdh 
                    WHERE projectcode ="OFC" 
                    AND hn <>""
                    AND (authen IS NULL OR authen ="") 
                    AND (an IS NULL OR an ="")
                    AND month(vstdate) ="'.$m.'" AND year(vstdate) ="'.$yy.'" AND debit > 0
     
                '); 
           
        } else {
            $data['fdh_ofc']    = DB::connection('mysql')->select(
                'SELECT year(vstdate) as years ,month(vstdate) as months,year(vstdate) as days 
                    ,count(DISTINCT vn) as countvn,count(DISTINCT authen) as countauthen,count(DISTINCT vn)-count(DISTINCT authen) as count_no_approve ,sum(debit) as sum_total  
                    FROM d_fdh WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND projectcode ="OFC" 
                    AND (an IS NULL OR an ="") AND (hn IS NOT NULL OR hn <>"") AND debit > 0
                    GROUP BY month(vstdate)
            '); 
               
            }   
                     
        return view('audit.audit_only',$data,[
            'startdate'     =>     $startdate,
            'enddate'       =>     $enddate, 
        ]);
    } 

   
   
     
}
