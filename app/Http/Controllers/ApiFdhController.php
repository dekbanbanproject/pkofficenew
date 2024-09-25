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
use App\Models\Acc_stm_ti;
use App\Models\Acc_stm_ti_total;
use App\Models\Acc_opitemrece;
use App\Models\Acc_1102050101_202;
use App\Models\Acc_1102050101_217;
use App\Models\Acc_1102050101_216;
use App\Models\Acc_stm_ucs;
use App\Models\Acc_1102050101_301;
use App\Models\Acc_1102050101_304;
use App\Models\Acc_1102050101_308;
use App\Models\Acc_1102050101_4011;
use App\Models\Acc_1102050101_3099;
use App\Models\Acc_1102050101_401;
use App\Models\Acc_1102050101_402;
use App\Models\Acc_1102050102_801;
use App\Models\Acc_1102050102_802;
use App\Models\Acc_1102050102_803;
use App\Models\Acc_1102050102_804;
use App\Models\Acc_1102050101_4022;
use App\Models\Acc_1102050102_602;
use App\Models\Acc_1102050102_603;
use App\Models\Acc_stm_prb;
use App\Models\Acc_stm_ti_totalhead;
use App\Models\Acc_stm_ti_excel;
use App\Models\Acc_stm_ofc;
use App\Models\acc_stm_ofcexcel;
use App\Models\Acc_stm_lgo;
use App\Models\Acc_stm_lgoexcel;
use App\Models\Acc_function;
use App\Models\D_walkin_drug;
use App\Models\Fdh_sesion;
use App\Models\Departmentsub;
use App\Models\Departmentsubsub;
use App\Models\Fdh_mini_dataset;

use App\Models\Fdh_api_ins;
use App\Models\Fdh_api_adp;
use App\Models\Fdh_api_aer;
use App\Models\Fdh_api_orf;
use App\Models\Fdh_api_odx;
use App\Models\Fdh_api_cht;
use App\Models\Fdh_api_cha;
use App\Models\Fdh_api_oop;
use App\Models\Fdh_api_dru;
use App\Models\Fdh_api_idx;
use App\Models\Fdh_api_iop;
use App\Models\Fdh_api_pat;
use App\Models\Fdh_api_opd;
use App\Models\Fdh_api_lvd;
use App\Models\Fdh_api_irf;
use App\Models\Fdh_api_ipd;

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
use App\Models\D_fdh;
use App\Models\D_walkin_report;



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
// use File;
// use SplFileObject;
use Arr;
// use Storage;
 
use GuzzleHttp\Client;
// use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Utils;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;

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
use ZipArchive;  
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Stmt\If_;
use Stevebauman\Location\Facades\Location; 
use Illuminate\Filesystem\Filesystem;
date_default_timezone_set("Asia/Bangkok");


class ApiFdhController extends Controller
 {   
    // ************ Claim Api FDH ******************************
    
    public function account_pkucs216_sendapi(Request $request)
    { 
        #delete file in folder ทั้งหมด
        $file = new Filesystem;
        $file->cleanDirectory('EXPORT_WALKIN'); //ทั้งหมด 
        $file->cleanDirectory('EXPORT_WALKIN_API'); //ทั้งหมด 
        $dataexport_ = DB::connection('mysql')->select('SELECT folder_name from fdh_sesion where d_anaconda_id = "WALKIN"');
        foreach ($dataexport_ as $key => $v_export) {
            $folder_ = $v_export->folder_name;
        }
        $folder = $folder_;
         mkdir ('EXPORT_WALKIN/'.$folder, 0777, true);  //Web
         mkdir ('EXPORT_WALKIN_API/'.$folder, 0777, true);  //Web
        //  mkdir ('C:Export/'.$folder, 0777, true); //localhost

        header("Content-type: text/txt");
        header("Cache-Control: no-store, no-cache");
        header('Content-Disposition: attachment; filename="content.txt"; charset=tis-620″ ;');
         
         //********** 1 ins.txt *****************//
        $file_d_ins         = "EXPORT_WALKIN/".$folder."/INS.txt";
        $file_fdh_ins       = "EXPORT_WALKIN_API/".$folder."/INS.txt";
        $objFopen_opd_ins   = fopen($file_d_ins, 'w');
        $fdh_ins            = fopen($file_fdh_ins, 'w');
        // $opd_head_ins = 'HN|INSCL|SUBTYPE|CID|DATEIN|DATEEXP|HOSPMAIN|HOSPSUB|GOVCODE|GOVNAME|PERMITNO|DOCNO|OWNRPID|OWNNAME|AN|SEQ|SUBINSCL|RELINSCL|HTYPE';  // สปสช
        $opd_head_ins       = 'HN|INSCL|SUBTYPE|CID|HCODE|DATEEXP|HOSPMAIN|HOSPSUB|GOVCODE|GOVNAME|PERMITNO|DOCNO|OWNRPID|OWNNAME|AN|SEQ|SUBINSCL|RELINSCL|HTYPE';   // FDH
        fwrite($objFopen_opd_ins, $opd_head_ins);
        fwrite($fdh_ins, $opd_head_ins);
        $ins = DB::connection('mysql')->select('SELECT * from fdh_ins where d_anaconda_id = "WALKIN"');
        foreach ($ins as $key => $value1) {
            $a1 = $value1->HN;
            $a2 = $value1->INSCL;
            $a3 = $value1->SUBTYPE;
            $a4 = $value1->CID;
            // $a5 = $value1->DATEIN;
            $a5 = $value1->HCODE;
            $a6 = $value1->DATEEXP;
            $a7 = $value1->HOSPMAIN;
            $a8 = $value1->HOSPSUB;
            $a9 = $value1->GOVCODE;
            $a10 = $value1->GOVNAME;
            $a11 = $value1->PERMITNO;
            $a12 = $value1->DOCNO;
            $a13 = $value1->OWNRPID;
            $a14 = $value1->OWNNAME;
            $a15 = $value1->AN;
            $a16 = $value1->SEQ;
            $a17 = $value1->SUBINSCL;
            $a18 = $value1->RELINSCL;
            $a19 = $value1->HTYPE;
            $strText_ins  ="\r\n".$a1."|".$a2."|".$a3."|".$a4."|".$a5."|".$a6."|".$a7."|".$a8."|".$a9."|".$a10."|".$a11."|".$a12."|".$a13."|".$a14."|".$a15."|".$a16."|".$a17."|".$a18."|".$a19;
            $ansitxt_ins  = iconv('UTF-8', 'TIS-620', $strText_ins);
            $apifdh_ins   = iconv('UTF-8', 'UTF-8', $strText_ins); 
            fwrite($objFopen_opd_ins, $ansitxt_ins);
            fwrite($fdh_ins, $apifdh_ins);
        }
        fclose($objFopen_opd_ins);
        fclose($fdh_ins);

        //**********2 pat.txt ******************//
        $file_pat         = "EXPORT_WALKIN/".$folder."/PAT.txt";
        $file_fdh_pat     = "EXPORT_WALKIN_API/".$folder."/PAT.txt";
        $objFopen_opd_pat = fopen($file_pat, 'w');
        $fdh_pat          = fopen($file_fdh_pat, 'w');
        $opd_head_pat     = 'HCODE|HN|CHANGWAT|AMPHUR|DOB|SEX|MARRIAGE|OCCUPA|NATION|PERSON_ID|NAMEPAT|TITLE|FNAME|LNAME|IDTYPE';
        fwrite($objFopen_opd_pat, $opd_head_pat);
        fwrite($fdh_pat, $opd_head_pat);
        $pat = DB::connection('mysql')->select('SELECT * from fdh_pat where d_anaconda_id = "WALKIN"');
        foreach ($pat as $key => $value2) {
            $i1 = $value2->HCODE;
            $i2 = $value2->HN;
            $i3 = $value2->CHANGWAT;
            $i4 = $value2->AMPHUR;
            $i5 = $value2->DOB;
            $i6 = $value2->SEX;
            $i7 = $value2->MARRIAGE;
            $i8 = $value2->OCCUPA;
            $i9 = $value2->NATION;
            $i10 = $value2->PERSON_ID;
            $i11 = $value2->NAMEPAT;
            $i12 = $value2->TITLE;
            $i13 = $value2->FNAME;
            $i14 = $value2->LNAME;
            $i15 = $value2->IDTYPE;      
            $strText_pat  ="\r\n".$i1."|".$i2."|".$i3."|".$i4."|".$i5."|".$i6."|".$i7."|".$i8."|".$i9."|".$i10."|".$i11."|".$i12."|".$i13."|".$i14."|".$i15;
            $ansitxt_pat  = iconv('UTF-8', 'TIS-620', $strText_pat);
            $apifdh_pat   = iconv('UTF-8', 'UTF-8', $strText_pat);
            fwrite($objFopen_opd_pat, $ansitxt_pat);
            fwrite($fdh_pat, $apifdh_pat);
        }
        fclose($objFopen_opd_pat);
        fclose($fdh_pat);

        //************ 3 opd.txt *****************//
        $file_d_opd       = "EXPORT_WALKIN/".$folder."/OPD.txt";
        $file_fdh_opd     = "EXPORT_WALKIN_API/".$folder."/OPD.txt";
        $objFopen_opd_opd = fopen($file_d_opd, 'w');
        $fdh_opd          = fopen($file_fdh_opd, 'w');
        $opd_head_opd     = 'HN|CLINIC|DATEOPD|TIMEOPD|SEQ|UUC|DETAIL|BTEMP|SBP|DBP|PR|RR|OPTYPE|TYPEIN|TYPEOUT';
        fwrite($objFopen_opd_opd, $opd_head_opd);
        fwrite($fdh_opd, $opd_head_opd);
        $opd = DB::connection('mysql')->select('SELECT * from fdh_opd where d_anaconda_id = "WALKIN"');
        foreach ($opd as $key => $value3) {
            $o1 = $value3->HN;
            $o2 = $value3->CLINIC;
            $o3 = $value3->DATEOPD; 
            $o4 = $value3->TIMEOPD; 
            $o5 = $value3->SEQ; 
            $o6 = $value3->UUC;  
            $strText_opd ="\r\n".$o1."|".$o2."|".$o3."|".$o4."|".$o5."|".$o6;
            $ansitxt_opd = iconv('UTF-8', 'TIS-620', $strText_opd);
            $apifdh_opd  = iconv('UTF-8', 'UTF-8', $strText_opd);
            fwrite($objFopen_opd_opd, $ansitxt_opd);
            fwrite($fdh_opd, $apifdh_opd);
        }
        fclose($objFopen_opd_opd);
        fclose($fdh_opd);

        //****************** 4 orf.txt **************************//
        $file_d_orf       = "EXPORT_WALKIN/".$folder."/ORF.txt";
        $file_fdh_orf     = "EXPORT_WALKIN_API/".$folder."/ORF.txt";
        $objFopen_opd_orf = fopen($file_d_orf, 'w');
        $fdh_orf          = fopen($file_fdh_orf, 'w');
        $opd_head_orf = 'HN|DATEOPD|CLINIC|REFER|REFERTYPE|SEQ';
        fwrite($objFopen_opd_orf, $opd_head_orf);
        fwrite($fdh_orf, $opd_head_orf);
        $orf = DB::connection('mysql')->select('SELECT * from fdh_orf where d_anaconda_id = "WALKIN"');
        foreach ($orf as $key => $value4) {
            $p1 = $value4->HN;
            $p2 = $value4->DATEOPD;
            $p3 = $value4->CLINIC; 
            $p4 = $value4->REFER; 
            $p5 = $value4->REFERTYPE; 
            $p6 = $value4->SEQ;  
            $strText_orf  ="\r\n".$p1."|".$p2."|".$p3."|".$p4."|".$p5."|".$p6;
            $ansitxt_orf  = iconv('UTF-8', 'TIS-620', $strText_orf);
            $apifdh_orf   = iconv('UTF-8', 'UTF-8', $strText_orf);
            fwrite($objFopen_opd_orf, $ansitxt_orf);
            fwrite($fdh_orf, $apifdh_orf);
        }
        fclose($objFopen_opd_orf);
        fclose($fdh_orf);

        //****************** 5 odx.txt **************************//
        $file_d_odx       = "EXPORT_WALKIN/".$folder."/ODX.txt";
        $file_fdh_odx     = "EXPORT_WALKIN_API/".$folder."/ODX.txt";
        $objFopen_opd_odx = fopen($file_d_odx, 'w');
        $fdh_odx          = fopen($file_fdh_odx, 'w');
        $opd_head_odx = 'HN|DATEDX|CLINIC|DIAG|DXTYPE|DRDX|PERSON_ID|SEQ';
        fwrite($objFopen_opd_odx, $opd_head_odx);
        fwrite($fdh_odx, $opd_head_odx);
        $odx = DB::connection('mysql')->select('SELECT * from fdh_odx where d_anaconda_id = "WALKIN"');
        foreach ($odx as $key => $value5) {
            $m1 = $value5->HN;
            $m2 = $value5->DATEDX;
            $m3 = $value5->CLINIC; 
            $m4 = $value5->DIAG; 
            $m5 = $value5->DXTYPE; 
            $m6 = $value5->DRDX; 
            $m7 = $value5->PERSON_ID; 
            $m8 = $value5->SEQ; 
            $strText_odx  ="\r\n".$m1."|".$m2."|".$m3."|".$m4."|".$m5."|".$m6."|".$m7."|".$m8;
            $ansitxt_odx  = iconv('UTF-8', 'TIS-620', $strText_odx);
            $apifdh_odx   = iconv('UTF-8', 'UTF-8', $strText_odx);
            fwrite($objFopen_opd_odx, $ansitxt_odx);
            fwrite($fdh_odx, $apifdh_odx);
        }
        fclose($objFopen_opd_odx);
        fclose($fdh_odx);

        //****************** 6.oop.txt ******************************//
        $file_d_oop       = "EXPORT_WALKIN/".$folder."/OOP.txt";
        $file_fdh_oop     = "EXPORT_WALKIN_API/".$folder."/OOP.txt";
        $objFopen_opd_oop = fopen($file_d_oop, 'w');
        $fdh_oop          = fopen($file_fdh_oop, 'w');
        $opd_head_oop     = 'HN|DATEOPD|CLINIC|OPER|DROPID|PERSON_ID|SEQ';
        fwrite($objFopen_opd_oop, $opd_head_oop);
        fwrite($fdh_oop, $opd_head_oop);
        $oop = DB::connection('mysql')->select('SELECT * from fdh_oop where d_anaconda_id = "WALKIN"');
        foreach ($oop as $key => $value6) {
            $n1 = $value6->HN;
            $n2 = $value6->DATEOPD;
            $n3 = $value6->CLINIC; 
            $n4 = $value6->OPER; 
            $n5 = $value6->DROPID; 
            $n6 = $value6->PERSON_ID; 
            $n7 = $value6->SEQ;  
            $strText_oop  ="\r\n".$n1."|".$n2."|".$n3."|".$n4."|".$n5."|".$n6."|".$n7;
            $ansitxt_oop  = iconv('UTF-8', 'TIS-620', $strText_oop);
            $apifdh_oop   = iconv('UTF-8', 'UTF-8', $strText_oop);
            fwrite($objFopen_opd_oop, $ansitxt_oop);
            fwrite($fdh_oop, $apifdh_oop);
        }
        fclose($objFopen_opd_oop);
        fclose($fdh_oop);

        //******************** 7.ipd.txt **************************//
        $file_d_ipd       = "EXPORT_WALKIN/".$folder."/IPD.txt";
        $file_fdh_ipd     = "EXPORT_WALKIN_API/".$folder."/IPD.txt";
        $objFopen_opd_ipd = fopen($file_d_ipd, 'w');
        $fdh_ipd          = fopen($file_fdh_ipd, 'w');
        $opd_head_ipd     = 'HN|AN|DATEADM|TIMEADM|DATEDSC|TIMEDSC|DISCHS|DISCHT|WARDDSC|DEPT|ADM_W|UUC|SVCTYPE';
        fwrite($objFopen_opd_ipd, $opd_head_ipd);
        fwrite($fdh_ipd, $opd_head_ipd);
        $ipd = DB::connection('mysql')->select('SELECT * from fdh_ipd where d_anaconda_id = "WALKIN"');
        foreach ($ipd as $key => $value7) {
            $j1  = $value7->HN;
            $j2  = $value7->AN;
            $j3  = $value7->DATEADM;
            $j4  = $value7->TIMEADM;
            $j5  = $value7->DATEDSC;
            $j6  = $value7->TIMEDSC;
            $j7  = $value7->DISCHS;
            $j8  = $value7->DISCHT;
            $j9  = $value7->WARDDSC;
            $j10 = $value7->DEPT;
            $j11 = $value7->ADM_W;
            $j12 = $value7->UUC;
            $j13 = $value7->SVCTYPE;    
            $strText_ipd     ="\r\n".$j1."|".$j2."|".$j3."|".$j4."|".$j5."|".$j6."|".$j7."|".$j8."|".$j9."|".$j10."|".$j11."|".$j12."|".$j13;
            $ansitxt_pat_ipd = iconv('UTF-8', 'TIS-620', $strText_ipd);
            $apifdh_ipd      = iconv('UTF-8', 'UTF-8', $strText_ipd);
            fwrite($objFopen_opd_ipd, $ansitxt_pat_ipd);
            fwrite($fdh_ipd, $apifdh_ipd);
        }
        fclose($objFopen_opd_ipd);
        fclose($fdh_ipd);

         //********************* 8.irf.txt ***************************//
         $file_d_irf       = "EXPORT_WALKIN/".$folder."/IRF.txt";
         $file_fdh_irf     = "EXPORT_WALKIN_API/".$folder."/IRF.txt";
         $objFopen_opd_irf = fopen($file_d_irf, 'w');
         $fdh_irf          = fopen($file_fdh_irf, 'w');
         $opd_head_irf     = 'AN|REFER|REFERTYPE';
         fwrite($objFopen_opd_irf, $opd_head_irf);
         fwrite($fdh_irf, $opd_head_irf);
         $irf = DB::connection('mysql')->select('SELECT * from fdh_irf where d_anaconda_id = "WALKIN"');
         foreach ($irf as $key => $value8) {
             $k1 = $value8->AN;
             $k2 = $value8->REFER;
             $k3 = $value8->REFERTYPE; 
             $strText_irf     ="\r\n".$k1."|".$k2."|".$k3;
             $ansitxt_pat_irf = iconv('UTF-8', 'TIS-620', $strText_irf);
             $apifdh_ipd      = iconv('UTF-8', 'UTF-8', $strText_irf);
             fwrite($objFopen_opd_irf, $ansitxt_pat_irf);
             fwrite($fdh_irf, $apifdh_ipd);
         }
         fclose($objFopen_opd_irf);
         fclose($fdh_irf);

        //********************** 9.idx.txt ***************************//
        $file_d_idx       = "EXPORT_WALKIN/".$folder."/IDX.txt";
        $file_fdh_idx     = "EXPORT_WALKIN_API/".$folder."/IDX.txt";
        $objFopen_opd_idx = fopen($file_d_idx, 'w');
        $fdh_idx          = fopen($file_fdh_idx, 'w');
        $opd_head_idx     = 'AN|DIAG|DXTYPE|DRDX';
        fwrite($objFopen_opd_idx, $opd_head_idx);
        fwrite($fdh_idx, $opd_head_idx);
        $idx = DB::connection('mysql')->select('SELECT * from fdh_idx where d_anaconda_id = "WALKIN"');
        foreach ($idx as $key => $value9) {
            $h1 = $value9->AN;
            $h2 = $value9->DIAG;
            $h3 = $value9->DXTYPE;
            $h4 = $value9->DRDX; 
            $strText_idx     ="\r\n".$h1."|".$h2."|".$h3."|".$h4;
            $ansitxt_pat_idx = iconv('UTF-8', 'TIS-620', $strText_idx);
            $apifdh_ipd      = iconv('UTF-8', 'UTF-8', $strText_idx);
            fwrite($objFopen_opd_idx, $ansitxt_pat_idx);
            fwrite($fdh_idx, $apifdh_ipd);
        }
        fclose($objFopen_opd_idx);
        fclose($fdh_idx);

        //********************** 10 iop.txt ***************************//
        $file_d_iop       = "EXPORT_WALKIN/".$folder."/IOP.txt";
        $file_fdh_iop     = "EXPORT_WALKIN_API/".$folder."/IOP.txt";
        $objFopen_opd_iop = fopen($file_d_iop, 'w');
        $fdh_iop          = fopen($file_fdh_iop, 'w');
        $opd_head_iop     = 'AN|OPER|OPTYPE|DROPID|DATEIN|TIMEIN|DATEOUT|TIMEOUT';
        fwrite($objFopen_opd_iop, $opd_head_iop);
        fwrite($fdh_iop, $opd_head_iop);
        $iop = DB::connection('mysql')->select('SELECT * from fdh_iop where d_anaconda_id = "WALKIN"');
        foreach ($iop as $key => $value10) {
            $b1 = $value10->AN;
            $b2 = $value10->OPER;
            $b3 = $value10->OPTYPE;
            $b4 = $value10->DROPID;
            $b5 = $value10->DATEIN;
            $b6 = $value10->TIMEIN;
            $b7 = $value10->DATEOUT;
            $b8 = $value10->TIMEOUT;           
            $strText_iop     ="\r\n".$b1."|".$b2."|".$b3."|".$b4."|".$b5."|".$b6."|".$b7."|".$b8;
            $ansitxt_pat_iop = iconv('UTF-8', 'TIS-620', $strText_iop);
            $apifdh_iop      = iconv('UTF-8', 'UTF-8', $strText_iop);
            fwrite($objFopen_opd_iop, $ansitxt_pat_iop);
            fwrite($fdh_iop, $apifdh_iop);
        }
        fclose($objFopen_opd_iop);
        fclose($fdh_iop);

        //********************** .11 cht.txt *****************************//
        $file_d_cht       = "EXPORT_WALKIN/".$folder."/CHT.txt";
        $file_fdh_cht     = "EXPORT_WALKIN_API/".$folder."/CHT.txt";
        $objFopen_opd_cht = fopen($file_d_cht, 'w');
        $fdh_cht          = fopen($file_fdh_cht, 'w');
        $opd_head_cht     = 'HN|AN|DATE|TOTAL|PAID|PTTYPE|PERSON_ID|SEQ|OPD_MEMO|INVOICE_NO|INVOICE_LT';
        fwrite($objFopen_opd_cht, $opd_head_cht);
        fwrite($fdh_cht, $opd_head_cht);
        $cht = DB::connection('mysql')->select('SELECT * from fdh_cht where d_anaconda_id = "WALKIN"');
        foreach ($cht as $key => $value11) {
            $f1  = $value11->HN;
            $f2  = $value11->AN;
            $f3  = $value11->DATE;
            $f4  = $value11->TOTAL;
            $f5  = $value11->PAID;
            $f6  = $value11->PTTYPE;
            $f7  = $value11->PERSON_ID; 
            $f8  = $value11->SEQ;
            $f9  = $value11->OPD_MEMO;
            $f10 = $value11->INVOICE_NO;
            $f11 = $value11->INVOICE_LT;
            $strText_cht     ="\r\n".$f1."|".$f2."|".$f3."|".$f4."|".$f5."|".$f6."|".$f7."|".$f8."|".$f9."|".$f10."|".$f11;
            $ansitxt_pat_cht = iconv('UTF-8', 'TIS-620', $strText_cht);
            $apifdh_cht      = iconv('UTF-8', 'UTF-8', $strText_cht);
            fwrite($objFopen_opd_cht, $ansitxt_pat_cht);
            fwrite($fdh_cht, $apifdh_cht);
        }
        fclose($objFopen_opd_cht);
        fclose($fdh_cht);

        //********************** .12 cha.txt *****************************//
        $file_d_cha       = "EXPORT_WALKIN/".$folder."/CHA.txt";
        $file_fdh_cha     = "EXPORT_WALKIN_API/".$folder."/CHA.txt";
        $objFopen_opd_cha = fopen($file_d_cha, 'w');
        $fdh_cha          = fopen($file_fdh_cha, 'w');
        $opd_head_cha     = 'HN|AN|DATE|CHRGITEM|AMOUNT|PERSON_ID|SEQ';
        fwrite($objFopen_opd_cha, $opd_head_cha);
        fwrite($fdh_cha, $opd_head_cha);
        $cha = DB::connection('mysql')->select('SELECT * from fdh_cha where d_anaconda_id = "WALKIN"');
        foreach ($cha as $key => $value12) {
            $e1 = $value12->HN;
            $e2 = $value12->AN;
            $e3 = $value12->DATE;
            $e4 = $value12->CHRGITEM;
            $e5 = $value12->AMOUNT;
            $e6 = $value12->PERSON_ID;
            $e7 = $value12->SEQ; 
            $strText_cha     ="\r\n".$e1."|".$e2."|".$e3."|".$e4."|".$e5."|".$e6."|".$e7;
            $ansitxt_pat_cha = iconv('UTF-8', 'TIS-620', $strText_cha);
            $apifdh_cha      = iconv('UTF-8', 'UTF-8', $strText_cha);
            fwrite($objFopen_opd_cha, $ansitxt_pat_cha);
            fwrite($fdh_cha, $apifdh_cha);
        }
        fclose($objFopen_opd_cha);
        fclose($fdh_cha);

        //************************ .13 aer.txt **********************************//
        $file_d_aer       = "EXPORT_WALKIN/".$folder."/AER.txt";
        $file_fdh_aer     = "EXPORT_WALKIN_API/".$folder."/AER.txt";
        $objFopen_opd_aer = fopen($file_d_aer, 'w');
        $fdh_aer          = fopen($file_fdh_aer, 'w');
        $opd_head_aer     = 'HN|AN|DATEOPD|AUTHAE|AEDATE|AETIME|AETYPE|REFER_NO|REFMAINI|IREFTYPE|REFMAINO|OREFTYPE|UCAE|EMTYPE|SEQ|AESTATUS|DALERT|TALERT';
        fwrite($objFopen_opd_aer, $opd_head_aer);
        fwrite($fdh_aer, $opd_head_aer);
        $aer = DB::connection('mysql')->select('SELECT * from fdh_aer where d_anaconda_id = "WALKIN"');
        foreach ($aer as $key => $value13) {
            $d1 = $value13->HN;
            $d2 = $value13->AN;
            $d3 = $value13->DATEOPD;
            $d4 = $value13->AUTHAE;
            $d5 = $value13->AEDATE;
            $d6 = $value13->AETIME;
            $d7 = $value13->AETYPE;
            $d8 = $value13->REFER_NO;
            $d9 = $value13->REFMAINI;
            $d10 = $value13->IREFTYPE;
            $d11 = $value13->REFMAINO;
            $d12 = $value13->OREFTYPE;
            $d13 = $value13->UCAE;
            $d14 = $value13->EMTYPE;
            $d15 = $value13->SEQ;
            $d16 = $value13->AESTATUS;
            $d17 = $value13->DALERT;
            $d18 = $value13->TALERT;        
            $strText_aer     ="\r\n".$d1."|".$d2."|".$d3."|".$d4."|".$d5."|".$d6."|".$d7."|".$d8."|".$d9."|".$d10."|".$d11."|".$d12."|".$d13."|".$d14."|".$d15."|".$d16."|".$d17."|".$d18;
            $ansitxt_pat_aer = iconv('UTF-8', 'TIS-620', $strText_aer);
            $apifdh_aer      = iconv('UTF-8', 'UTF-8', $strText_aer);
            fwrite($objFopen_opd_aer, $ansitxt_pat_aer);
            fwrite($fdh_aer, $apifdh_aer);
        }
        fclose($objFopen_opd_aer);
        fclose($fdh_aer);

        //************************ .14 adp.txt **********************************//
        $file_d_adp       = "EXPORT_WALKIN/".$folder."/ADP.txt";
        $file_fdh_adp     = "EXPORT_WALKIN_API/".$folder."/ADP.txt";
        $objFopen_opd_adp = fopen($file_d_adp, 'w');
        $fdh_adp          = fopen($file_fdh_adp, 'w');
        $opd_head_adp     = 'HN|AN|DATEOPD|TYPE|CODE|QTY|RATE|SEQ|CAGCODE|DOSE|CA_TYPE|SERIALNO|TOTCOPAY|USE_STATUS|TOTAL|QTYDAY|TMLTCODE|STATUS1|BI|CLINIC|ITEMSRC|PROVIDER|GRAVIDA|GA_WEEK|DCIP|LMP|SP_ITEM';
        fwrite($objFopen_opd_adp, $opd_head_adp);
        fwrite($fdh_adp, $opd_head_adp);
        $adp = DB::connection('mysql')->select('SELECT * from fdh_adp where d_anaconda_id = "WALKIN"');
        foreach ($adp as $key => $value14) {
            $c1 = $value14->HN;
            $c2 = $value14->AN;
            $c3 = $value14->DATEOPD;
            $c4 = $value14->TYPE;
            $c5 = $value14->CODE;
            $c6 = $value14->QTY;
            $c7 = $value14->RATE;
            $c8 = $value14->SEQ;
            $c9 = $value14->CAGCODE;
            $c10 = $value14->DOSE;
            $c11 = $value14->CA_TYPE;
            $c12 = $value14->SERIALNO;
            $c13 = $value14->TOTCOPAY;
            $c14 = $value14->USE_STATUS;
            $c15 = $value14->TOTAL;
            $c16 = $value14->QTYDAY;
            $c17 = $value14->TMLTCODE;
            $c18 = $value14->STATUS1;
            $c19 = $value14->BI;
            $c20 = $value14->CLINIC;
            $c21 = $value14->ITEMSRC;
            $c22 = $value14->PROVIDER;
            $c23 = $value14->GRAVIDA;
            $c24 = $value14->GA_WEEK;
            $c25 = $value14->DCIP;
            $c26 = $value14->LMP;
            $c27 = $value14->SP_ITEM;           
            $strText_adp ="\r\n".$c1."|".$c2."|".$c3."|".$c4."|".$c5."|".$c6."|".$c7."|".$c8."|".$c9."|".$c10."|".$c11."|".$c12."|".$c13."|".$c14."|".$c15."|".$c16."|".$c17."|".$c18."|".$c19."|".$c20."|".$c21."|".$c22."|".$c23."|".$c24."|".$c25."|".$c26."|".$c27;
            $ansitxt_adp = iconv('UTF-8', 'TIS-620', $strText_adp);
            $apifdh_adp  = iconv('UTF-8', 'UTF-8', $strText_adp);
            fwrite($objFopen_opd_adp, $ansitxt_adp);
            fwrite($fdh_adp, $apifdh_adp);
        }
        fclose($objFopen_opd_adp); 
        fclose($fdh_adp); 
        //*********************** 15.dru.txt ****************************//
        $file_d_dru       = "EXPORT_WALKIN/".$folder."/DRU.txt";
        $file_fdh_dru     = "EXPORT_WALKIN_API/".$folder."/DRU.txt";
        $objFopen_opd_dru = fopen($file_d_dru, 'w');
        $fdh_dru          = fopen($file_fdh_dru, 'w');
        $opd_head_dru     = 'HCODE|HN|AN|CLINIC|PERSON_ID|DATE_SERV|DID|DIDNAME|AMOUNT|DRUGPRIC|DRUGCOST|DIDSTD|UNIT|UNIT_PACK|SEQ|DRUGREMARK|PA_NO|TOTCOPAY|USE_STATUS|TOTAL|SIGCODE|SIGTEXT|PROVIDER|SP_ITEM';
        fwrite($objFopen_opd_dru, $opd_head_dru);
        fwrite($fdh_dru, $opd_head_dru);
        $dru = DB::connection('mysql')->select('SELECT * from fdh_dru where d_anaconda_id = "WALKIN"');
        foreach ($dru as $key => $value15) {
            $g1 = $value15->HCODE;
            $g2 = $value15->HN;
            $g3 = $value15->AN;
            $g4 = $value15->CLINIC;
            $g5 = $value15->PERSON_ID;
            $g6 = $value15->DATE_SERV;
            $g7 = $value15->DID;
            $g8 = $value15->DIDNAME;
            $g9 = $value15->AMOUNT;
            $g10 = $value15->DRUGPRIC;
            $g11 = $value15->DRUGCOST;
            $g12 = $value15->DIDSTD;
            $g13 = $value15->UNIT;
            $g14 = $value15->UNIT_PACK;
            $g15 = $value15->SEQ;
            $g16 = $value15->DRUGREMARK;
            $g17 = $value15->PA_NO;
            $g18 = $value15->TOTCOPAY;
            $g19 = $value15->USE_STATUS;
            $g20 = $value15->TOTAL;
            $g21 = $value15->SIGCODE;
            $g22 = $value15->SIGTEXT;  
            $g23 = $value15->PROVIDER;   
            $g24 = $value15->SP_ITEM;   
            $strText_dru ="\r\n".$g1."|".$g2."|".$g3."|".$g4."|".$g5."|".$g6."|".$g7."|".$g8."|".$g9."|".$g10."|".$g11."|".$g12."|".$g13."|".$g14."|".$g15."|".$g16."|".$g17."|".$g18."|".$g19."|".$g20."|".$g21."|".$g22."|".$g23."|".$g24;
            $ansitxt_dru = iconv('UTF-8', 'TIS-620', $strText_dru);
            $apifdh_dru  = iconv('UTF-8', 'UTF-8', $strText_dru);
            fwrite($objFopen_opd_dru, $ansitxt_dru);
            fwrite($fdh_dru, $apifdh_dru);
        }
        fclose($objFopen_opd_dru);
        fclose($fdh_dru);
        
        //************************* 16.lvd.txt *****************************//
        $file_d_lvd       = "EXPORT_WALKIN/".$folder."/LVD.txt";
        $file_fdh_lvd     = "EXPORT_WALKIN_API/".$folder."/LVD.txt";
        $objFopen_opd_lvd = fopen($file_d_lvd, 'w');
        $fdh_lvd          = fopen($file_fdh_lvd, 'w');
        $opd_head_lvd     = 'SEQLVD|AN|DATEOUT|TIMEOUT|DATEIN|TIMEIN|QTYDAY';
        fwrite($objFopen_opd_lvd, $opd_head_lvd);
        fwrite($fdh_lvd, $opd_head_lvd);
        $lvd = DB::connection('mysql')->select('SELECT * from fdh_lvd where d_anaconda_id = "WALKIN"');
        foreach ($lvd as $key => $value16) {
            $L1 = $value16->SEQLVD;
            $L2 = $value16->AN;
            $L3 = $value16->DATEOUT; 
            $L4 = $value16->TIMEOUT; 
            $L5 = $value16->DATEIN; 
            $L6 = $value16->TIMEIN; 
            $L7 = $value16->QTYDAY; 
            $strText_lvd ="\r\n".$L1."|".$L2."|".$L3."|".$L4."|".$L5."|".$L6."|".$L7;
            $ansitxt_pat_lvd = iconv('UTF-8', 'TIS-620', $strText_lvd);
            $apifdh_lvd  = iconv('UTF-8', 'UTF-8', $strText_lvd);
            fwrite($objFopen_opd_lvd, $ansitxt_pat_lvd);
            fwrite($fdh_lvd, $apifdh_lvd);
        }
        fclose($objFopen_opd_lvd); 
        fclose($fdh_lvd);

        //*********************** 17.lab.txt **********************************//
        $file_d_lab = "EXPORT_WALKIN/".$folder."/LAB.txt";
        $file_fdh_lab = "EXPORT_WALKIN_API/".$folder."/LAB.txt";
        $objFopen_opd_lab = fopen($file_d_lab, 'w');
        $fdh_lab = fopen($file_fdh_lab, 'w');
        $opd_head_lab = 'HCODE|HN|PERSON_ID|DATESERV|SEQ|LABTEST|LABRESULT';
        fwrite($objFopen_opd_lab, $opd_head_lab);
        fwrite($fdh_lab, $opd_head_lab);
        fclose($objFopen_opd_lab);
        fclose($fdh_lab);

        $data_token_ = DB::connection('mysql')->select('SELECT * FROM api_neweclaim WHERE active_mini = "Y" AND user_id = "'.Auth::user()->id.'"');
        foreach ($data_token_ as $key => $val_to) {
            $token_   = $val_to->api_neweclaim_token;
        }
        $fdh_jwt = $token_;

        $pathdir_fdh_utf8 = "EXPORT_WALKIN_API/".$folder."_fdh_utf8/";
        $files_fdh_utf8 = glob($pathdir_fdh_utf8 . '/*'); 
        
        //Create Client object to deal with
        $client = new Client();
        

        $options = [
            'multipart' => [
                 [
                      'name' => 'type',
                      'contents' => 'txt'
                 ],
                      
                      [
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'OPD.txt'),
                           'filename' => 'OPD.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],
                      [
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'ORF.txt'),
                           'filename' => 'ORF.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'ODX.txt'),
                           'filename' => 'ODX.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'OOP.txt'),
                           'filename' => 'OOP.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'CHA.txt'),
                           'filename' => 'CHA.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'ADP.txt'),
                           'filename' => 'ADP.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'INS.txt'),
                           'filename' => 'INS.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'PAT.txt'),
                           'filename' => 'PAT.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'CHT.txt'),
                           'filename' => 'CHT.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'AER.txt'),
                           'filename' => 'AER.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ],[
                           'name' => 'file',
                           'contents' => file_get_contents($pathdir_fdh_utf8.'DRU.txt'),
                           'filename' => 'DRU.txt',
                           'headers' => [
                                'Content-Type' => 'text/plain'
                           ]
                      ]
                 ]
            ];
        // Define the request parameters
        $url = 'https://fdh.moph.go.th/api/v2/data_hub/16_files';
        // $headers = [
        //     'Content-Type' => 'application/json',
        // ];
        $headers = [
            'Authorization' => 'Bearer '.$fdh_jwt
        ];
        // $data = [
        // 'name' => $request->input('name'),
        // 'job' => $request->input('job'),
        // ];    
        // POST request using the created object
        $postResponse = $client->post($url, [
            'headers' => $headers,
            'json'    => $options,
        ]);
        // Get the response code
        $responseCode = $postResponse->getStatusCode();
        return response()->json(['response_code' => $responseCode]);
            #สำหรับทดสอบ  https://uat-fdh.inet.co.th/api/v1/data_hub/16_files
            #Product      https://fdh.moph.go.th/api/v1/data_hub/16_files
            #v1 = API นำเข้า 16 แฟ้ม แบบไม่มีหัวคอลัมน์
            #v2 = API นำเข้า 16 แฟ้ม แบบมีหัวคอลัมน์
            #$request = new Request('POST', 'https://uat-fdh.inet.co.th/api/v1/data_hub/16_files', $headers);
            // $request = new Request("POST", "https://fdh.moph.go.th/api/v2/data_hub/16_files", $headers);
            // try {
            //     $response = $client->send($request, $options); 
            //     $response_gb = $response->getBody();
            //     $result_send_fame_outp = json_decode($response_gb, true);
            //     #echo "try";
            // } catch (\GuzzleHttp\Exception\RequestException $e) {
            //     if ($e->hasResponse()) {
            //         $errorResponse = json_decode($e->getResponse()->getBody(), true);
            //         json_encode($errorResponse, JSON_PRETTY_PRINT);
            //         #echo "if";
            //     } else {
            //         json_encode(['error' => 'Unknown error occurred'], JSON_PRETTY_PRINT);
            //         #echo "else";
            //     }
            // }
            
             

           
            #ส่ง api
            // if (@$result_send_fame_outp['status']=='200') {
            //     return response()->json([
            //         'status'    => '200'
            //     ]);
            // } else {
            //     # code...
            // }            
 
        // return response()->json([
        //     'status'    => '200'
        // ]);
   } 
 }