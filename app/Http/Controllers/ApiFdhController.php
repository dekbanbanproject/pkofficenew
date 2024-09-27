<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
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
use GuzzleHttp\Psr7\Request;
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
    
    public function account_pkucs216_sendapi()
    { 
      
        $data_token_ = DB::connection('mysql')->select('SELECT * FROM api_neweclaim WHERE active_mini = "Y" AND user_id = "'.Auth::user()->id.'"');
        foreach ($data_token_ as $key => $val_to) {
            $token_   = $val_to->api_neweclaim_token;
        }
        $fdh_jwt = $token_;

        $dataexport_ = DB::connection('mysql')->select('SELECT folder_name from fdh_sesion where d_anaconda_id = "WALKIN"');
        foreach ($dataexport_ as $key => $v_export) {
            $folder = $v_export->folder_name;
        }
        // $pathdir_fdh_utf8 = "EXPORT_WALKIN_API/".$folder."_fdh_utf8/";
        $pathdir_fdh_utf8 = "EXPORT_WALKIN_API/".$folder. "/";

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
     //    $postResponse = $client->post($url, [
     //        'headers' => $headers,
     //        'json'    => $options,
     //    ]);
        // Get the response code
     //    $responseCode = $postResponse->getStatusCode();
     //    return response()->json(['response_code' => $responseCode]);
            #สำหรับทดสอบ  https://uat-fdh.inet.co.th/api/v1/data_hub/16_files
            #Product      https://fdh.moph.go.th/api/v1/data_hub/16_files
            #v1 = API นำเข้า 16 แฟ้ม แบบไม่มีหัวคอลัมน์
            #v2 = API นำเข้า 16 แฟ้ม แบบมีหัวคอลัมน์
            #$request = new Request('POST', 'https://uat-fdh.inet.co.th/api/v1/data_hub/16_files', $headers);
            $request = new Request('POST', 'https://fdh.moph.go.th/api/v2/data_hub/16_files', $headers);
            try {
                $response = $client->send($request, $options); 
                $response_gb = $response->getBody();
                $result_send_fame_outp = json_decode($response_gb, true);
               
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                if ($e->hasResponse()) {
                    $errorResponse = json_decode($e->getResponse()->getBody(), true);
                    json_encode($errorResponse, JSON_PRETTY_PRINT);
                    #echo "if";
                } else {
                    json_encode(['error' => 'Unknown error occurred'], JSON_PRETTY_PRINT);
                    #echo "else";
                }
            }
            $status = $result_send_fame_outp['status'];

          //    dd($status);
             if ($status = '200') {
                      return response()->json([
                              'status'    => '200'
                    ]);
             } else {
               return response()->json([
                    'status'    => '100'
               ]);
             }
             

           
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