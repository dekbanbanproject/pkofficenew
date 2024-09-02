@extends('layouts.support_prs_airback')
@section('title', 'PK-OFFICE || Air-Service')

@section('content')
    <script>
        function TypeAdmin() {
            window.location.href = '{{ route('index') }}';
        }
        function air_main_repaire_destroy(air_repaire_id) {
            Swal.fire({
                position: "top-end",
                title: 'ต้องการลบใช่ไหม?',
                text: "ข้อมูลนี้จะถูกลบไปเลย !!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, ลบเดี๋ยวนี้ !',
                cancelButtonText: 'ไม่, ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('air_main_repaire_destroy') }}" + '/' + air_repaire_id,
                        type: 'POST',
                        data: {
                            _token: $("input[name=_token]").val()
                        },
                        success: function(response) {
                            if (response.status == 200 ) {
                                Swal.fire({
                                    position: "top-end",
                                    title: 'ลบข้อมูล!',
                                    text: "You Delet data success",
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: '#06D177',
                                    // cancelButtonColor: '#d33',
                                    confirmButtonText: 'เรียบร้อย'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $("#sid" + air_repaire_id).remove();
                                        window.location.reload();
                                        // window.location = "{{ url('air_main') }}";
                                    }
                                })
                            } else {  
                            }
                        }
                    })
                }
            })
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
    
    ?>

    
    <?php
    $ynow = date('Y') + 543;
    $yb = date('Y') + 542;
    ?>

<div class="tabs-animation">
{{-- <div class="containner-fluid"> --}}
    <div id="preloader">
        <div id="status">
            <div id="container_spin">
                <svg viewBox="0 0 100 100">
                    <defs>
                        <filter id="shadow">
                        <feDropShadow dx="0" dy="0" stdDeviation="2.5" 
                            flood-color="#fc6767"/>
                        </filter>
                    </defs>
                    <circle id="spinner" style="fill:transparent;stroke:#dd2476;stroke-width: 7px;stroke-linecap: round;filter:url(#shadow);" cx="50" cy="50" r="45"/>
                </svg>
            </div>
        </div>
    </div>
    <form action="{{ url('air_report_building') }}" method="GET">
        @csrf
        <div class="row"> 
            <div class="col-md-10">
                <h4 style="color:rgb(255, 255, 255)">แผนการบำรุงรักษาเครื่องปรับอากาศโรงพยาบาลภูเขียวเฉลิมพะเกียรติ ปีงบประมาณ {{$bg_yearnow}} </h4>
   
            </div>
             
            {{-- <div class="col"></div> --}}
            <div class="col-md-2 text-end"> 
                
                {{-- <a href="{{url('air_report_building_excel')}}" class="ladda-button btn-pill btn btn-sm btn-success bt_prs">
                    <span class="ladda-label"> <i class="fa-solid fa-file-excel text-white me-2"></i>Export To Excel</span>  
                </a> --}}
            
            </div>
        </div>  
    </form>
 
<div class="row mt-2">
    <div class="col-xl-12">
        <div class="card card_prs_4">
            <div class="card-body">    
                

                <p class="mb-0">
                    <div class="table-responsive">
                        <table id="example" class="table table-hover table-sm dt-responsive nowrap myTable" style=" border-spacing: 0; width: 100%;">
                        {{-- <table id="example" class="table table-hover table-sm dt-responsive" style="width: 100%;"> --}}
                            {{-- <table id="example" class="table table-borderless table-hover table-bordered" style="width: 100%;"> --}}
                                {{-- <table id="example" class="table table-borderless table-hover table-bordered" style="width: 100%;"> --}}
                            <thead>                             
                                    <tr style="font-size:13px"> 
                                        {{-- <th rowspan="2" width="3%" class="text-center" style="background-color: rgb(228, 255, 255);">ลำดับ</th>   --}}
                                        <th rowspan="2" class="text-center" style="background-color: rgb(255, 156, 110);color:#FFFFFF" width= "12%">อาคาร</th>  
                                        {{-- <th rowspan="2" class="text-center" style="background-color: rgb(228, 255, 255);width: 7%">อาคาร</th>   --}}
                                        <th rowspan="2" class="text-center" style="background-color: #06b78b;color:#FFFFFF;" width= "5%">จำนวน</th>  
                                        <th colspan="12" class="text-center" style="background-color: rgb(154, 86, 255);color:#FFFFFF">ระยะเวลาการดำเนินงาน</th>   
                                    </tr> 
                                    <tr style="font-size:11px">  
                                        <th class="text-center" width="5%" style="background-color: rgb(185, 140, 253);color:#FFFFFF">ต.ค</th> 
                                        <th class="text-center" width="5%" style="background-color: rgb(209, 178, 255);color:#FFFFFF">พ.ย</th>   
                                        <th class="text-center" width="5%" style="background-color: rgb(185, 140, 253);color:#FFFFFF">ธ.ค</th> 
                                        <th class="text-center" width="5%" style="background-color: rgb(209, 178, 255);color:#FFFFFF">ม.ค</th>
                                        <th class="text-center" width="5%" style="background-color: rgb(185, 140, 253);color:#FFFFFF">ก.พ</th>
                                        <th class="text-center" width="5%" style="background-color: rgb(209, 178, 255);color:#FFFFFF">มี.ค</th> 
                                        <th class="text-center" width="5%" style="background-color: rgb(185, 140, 253);color:#FFFFFF">เม.ย</th>
                                        <th class="text-center" width="5%" style="background-color: rgb(209, 178, 255);color:#FFFFFF">พ.ค</th>
                                        <th class="text-center" width="5%" style="background-color: rgb(185, 140, 253);color:#FFFFFF">มิ.ย</th>
                                        <th class="text-center" width="5%" style="background-color: rgb(209, 178, 255);color:#FFFFFF">ก.ค</th>
                                        <th class="text-center" width="5%" style="background-color: rgb(185, 140, 253);color:#FFFFFF">ส.ค</th>
                                        <th class="text-center" width="5%" style="background-color: rgb(209, 178, 255);color:#FFFFFF">ก.ย</th>
                                    </tr> 
                            </thead>
                            <tbody>
                                <?php $i = 0;
                                    $total1 = 0; $total2 = 0; $total3 = 0; $total4 = 0; $total5 = 0; $total6 = 0; $total7 = 0; $total8 = 0; $total9 = 0; $total10 = 0; $total11 = 0; $total12 = 0;$total13 = 0;  
                                    $total14 = 0; $total15 = 0; $total16 = 0;$total17 = 0; $total18 = 0; $total19 = 0; $total20 = 0;$total21 = 0;$total22 = 0;$total23 = 0;$total24 = 0;$total25 = 0;
                                ?>
                                @foreach ($datashow as $item) 
                                <?php $i++ ?>               
                                    <tr>     
                                        <td class="text-start" style="font-size:13px;color: rgb(2, 95, 182)">{{$item->building_name}}</td>
                                        {{-- <td class="text-center" style="font-size:13px;color: rgb(4, 117, 117)">{{$item->building_id}}</td> --}}
                                        <td class="text-center" style="font-size:13px;color: rgb(228, 15, 86)">
                                           {{-- <a href="{{url('air_report_building_sub/'.$item->building_id)}}" target="_blank">  --}}
                                                <span class="badge bg-success me-2"> {{$item->qtyall}}</span> <span class="badge bg-danger"> {{$item->qty_noall}}</span>
                                            {{-- </a>  --}}
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                            <span class="badge bg-info me-2"> {{$item->tula_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->tula_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                         <span class="badge bg-info me-2"> {{$item->plusji_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->plusji_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                            <span class="badge bg-info me-2"> {{$item->tanwa_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->tanwa_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                             <span class="badge bg-info me-2"> {{$item->makkara_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->makkara_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                            <span class="badge bg-info me-2"> {{$item->gumpa_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->gumpa_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                            <span class="badge bg-info me-2"> {{$item->mena_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->mena_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                            <span class="badge bg-info me-2"> {{$item->mesa_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->mesa_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                             <span class="badge bg-info me-2"> {{$item->plussapa_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->plussapa_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                             <span class="badge bg-info me-2"> {{$item->mituna_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->mituna_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                             <span class="badge bg-info me-2"> {{$item->karakada_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->karakada_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                            <span class="badge bg-info me-2"> {{$item->singha_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->singha_bt}}</span>
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">
                                             <span class="badge bg-info me-2"> {{$item->kanya_saha}}</span> <span class="badge" style="background: #ba0890"> {{$item->kanya_bt}}</span>
                                        </td>
                                    </tr>
                                    <?php
                                            $total1 = $total1 + $item->qtyall;

                                            $total2 = $total2 + $item->tula_saha;
                                            $total14 = $total14 + $item->tula_bt;

                                            $total3 = $total3 + $item->plusji_saha; 
                                            $total15 = $total15 + $item->plusji_bt; 

                                            $total4 = $total4 + $item->tanwa_saha; 
                                            $total16 = $total16 + $item->tanwa_bt; 

                                            $total5 = $total5 + $item->makkara_saha; 
                                            $total17 = $total17 + $item->makkara_bt; 

                                            $total6 = $total6 + $item->gumpa_saha; 
                                            $total18 = $total18 + $item->gumpa_bt; 

                                            $total7 = $total7 + $item->mena_saha; 
                                            $total19 = $total19 + $item->mena_bt; 

                                            $total8 = $total8 + $item->mesa_saha; 
                                            $total20 = $total20 + $item->mesa_bt;

                                            $total9 = $total9 + $item->plussapa_saha; 
                                            $total21 = $total21 + $item->plussapa_bt; 

                                            $total10 = $total10 + $item->mituna_saha; 
                                            $total22 = $total22 + $item->mituna_bt;

                                            $total11 = $total11 + $item->karakada_saha; 
                                            $total23 = $total23 + $item->karakada_bt; 

                                            $total12 = $total12 + $item->singha_saha; 
                                            $total24 = $total24 + $item->singha_bt;
                                            
                                            $total13 = $total13 + $item->kanya_saha; 
                                            $total25 = $total25 + $item->kanya_bt;
                                    ?>
                                @endforeach
                            </tbody>
                            <tr style="background-color: #f3fca1">
                                <td colspan="1" class="text-end" style="background-color: #fabcd7"></td>
                                <td class="text-center" style="background-color: #06b78b"><label for="" style="color: #FFFFFF">{{$total1 }}</label></td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total2}}</span> <span class="badge" style="background: #ba0890"> {{$total14}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total3}}</span> <span class="badge" style="background: #ba0890"> {{$total15}}</span>
                                    </label></td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total4}}</span> <span class="badge" style="background: #ba0890"> {{$total16}}</span>
                                    </label>
                                    </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total5}}</span> <span class="badge" style="background: #ba0890"> {{$total17}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total6}}</span> <span class="badge" style="background: #ba0890"> {{$total18}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total7}}</span> <span class="badge" style="background: #ba0890"> {{$total19}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total8}}</span> <span class="badge" style="background: #ba0890"> {{$total20}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total9}}</span> <span class="badge" style="background: #ba0890"> {{$total21}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total10}}</span> <span class="badge" style="background: #ba0890"> {{$total22}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total11}}</span> <span class="badge" style="background: #ba0890"> {{$total23}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total12}}</span> <span class="badge" style="background: #ba0890"> {{$total24}}</span>
                                    </label>
                                </td>
                                <td class="text-center" style="background-color: #fabcd7" >
                                    <label for="" style="color: #FFFFFF">
                                        <span class="badge bg-info me-2"> {{$total13}}</span> <span class="badge" style="background: #ba0890"> {{$total25}}</span>
                                    </label>
                                </td>
                                
                            </tr>  
                        </table>
                    </div>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-1">
        <div class="card"> 
            <span class="badge bg-info me-2 p-2"> บริษัทสหรัตน์แอร์</span> 
        </div>
    </div>
    <div class="col-xl-1">
        <div class="card">
            <span class="badge p-2" style="background: #ba0890">บริษัทบีทีแอร์</span> 
        </div>
    </div>
    <div class="col"></div>
</div>

</div>
</div>

@endsection
@section('footer')
    <script>
        $(document).ready(function() {
           
            // $('select').select2();
     
        
            $('#example2').DataTable();
            var table = $('#example').DataTable({
                scrollY: '60vh',
                scrollCollapse: true,
                scrollX: true,
                "autoWidth": false,
                "pageLength": 10,
                "lengthMenu": [10,25,30,31,50,100,150,200,300],
            });
        
            $('#datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd'
            });

            $('#datepicker3').datepicker({
                format: 'yyyy-mm-dd'
            });
            $('#datepicker4').datepicker({
                format: 'yyyy-mm-dd'
            });

        });
    </script>

@endsection
