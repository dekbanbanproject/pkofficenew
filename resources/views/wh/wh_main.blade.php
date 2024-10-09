@extends('layouts.wh')
@section('title', 'PK-OFFICE || Where House')

@section('content')
    <script>
        function TypeAdmin() {
            window.location.href = '{{ route('index') }}';
        }
    </script>
    <?php
    if (Auth::check()) {
        $type = Auth::user()->type;
        $iduser = Auth::user()->id;
    } else {
        echo "<body onload=\"TypeAdmin()\"></body>";
        exit();
    }
    $url = Request::url();
    $pos = strrpos($url, '/') + 1;
    $ynow = date('Y') + 543;
    $yb = date('Y') + 542;
    ?>

    <style>
        #button {
            display: block;
            margin: 20px auto;
            padding: 30px 30px;
            background-color: #eee;
            border: solid #ccc 1px;
            cursor: pointer;
        }

        #overlay {
            position: fixed;
            top: 0;
            z-index: 100;
            width: 100%;
            height: 100%;
            display: none;
            background: rgba(0, 0, 0, 0.6);
        }

        .cv-spinner {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            width: 250px;
            height: 250px;
            border: 5px #ddd solid;
            border-top: 10px rgb(252, 101, 1) solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(360deg);
            }
        }

        .is-hide {
            display: none;
        }
    </style>

<div class="tabs-animation">

    <div class="row text-center">
        <div id="overlay">
            <div class="cv-spinner">
                <span class="spinner"></span>
            </div>
        </div> 
    </div> 
    <div id="preloader">
        <div id="status">
            <div class="spinner"> 
            </div>
        </div>
    </div>
        
       
        <div class="row">
            <div class="col-md-12">
                
                <div class="card card_audit_4c">
   
                            <div class="card-body">
                                <form action="{{ URL('wh_plan') }}" method="GET">
                                    @csrf

                                <div class="row"> 
                                    <div class="col-md-6"> 
                                        <h5 class="card-title" style="color:green">คลัง {{$stock_name}}</h5>
                                        <p class="card-title-desc">หน่วยงาน  กลุ่มงานพัสดุ โรงพยาบาลภูเขียวเฉลิมพระเกียรติ จังหวัด ชัยภูมิ ประจำปีงบประมาณ 2568</p>
                                    </div>
                                    <div class="col"></div>
                                   
                                </div>

                                </form>
                                <div class="row"> 
                                    <div class="col-xl-12">
                                        {{-- <table id="scroll-vertical-datatable" class="table table-sm table-striped table-bordered nowrap w-100" style="width: 100%;">  --}}
                                        <table class="table table-bordered " style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                            <thead> 
                                                <tr style="font-size: 10px;">
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;">ลำดับ</th>
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 12px;">รายการ</th>
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;">ประเภท</th>
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;">ขนาดบรรจุ /<br>หน่วยนับ</th>
                                                    <th class="text-center" style="background-color: rgb(255, 237, 117);font-size: 11px;">รับเข้า</th> 
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;">จ่ายออก</th> 
                                                    <th class="text-center" style="background-color: rgb(255, 251, 228);font-size: 11px;"> คงเหลือ</th> 
                                                </tr> 
                                            </thead>
                                            <tbody>
                                                <?php $i = 0; ?>
                                                @foreach ($wh_product as $item)
                                                <?php $i++ ?>
                                                <tr>
                                                    <td class="text-center">{{$i}}</td>
                                                    <td class="text-start" width="10%">{{$item->pro_name}}</td>
                                                    <td class="text-center">{{$item->wh_type_name}}</td>
                                                    <td class="text-center">{{$item->wh_unit_pack_qty}}/{{$item->unit_name}}</td> 
                                                    <td class="text-center">0</td>
                                                    <td class="text-center">0</td>
                                                    <td class="text-center">0</td>
                                                    {{-- <td class="text-center" width="5%">{{number_format($item->total_plan, 0)}}</td> --}}
                                                    {{-- <td class="text-center" width="5%">{{number_format($item->total_plan_price, 2)}}</td>   --}}
                                                </tr>
                                                    
                                                @endforeach                                                
                                            </tbody>
                                        </table>

                                    </div>
                                </div>  
                            </div>
                        
                </div>
                 
            </div>
        </div>
    </div>


    </div>
 
@endsection
@section('footer')
 
    <script>
        var Linechart;
        $(document).ready(function() {
            $('#example').DataTable();
            $('#example2').DataTable();
            
            $('#p4p_work_month').select2({
                placeholder: "--เลือก--",
                allowClear: true
            });
            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd'
            });
  
        });
    </script>
  

@endsection
