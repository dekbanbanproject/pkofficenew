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
                                        <th rowspan="2" class="text-center" style="background-color: rgb(228, 255, 255)" width= "12%">อาคาร</th>  
                                        {{-- <th rowspan="2" class="text-center" style="background-color: rgb(228, 255, 255);width: 7%">อาคาร</th>   --}}
                                        <th rowspan="2" class="text-center" style="background-color: rgb(228, 255, 255);" width= "5%">จำนวน</th>  
                                        <th colspan="12" class="text-center" style="background-color: rgb(239, 228, 255);">ระยะเวลาการดำเนินงาน</th>   
                                    </tr> 
                                    <tr style="font-size:11px">  
                                        <th class="text-center" width="5%">ต.ค</th> 
                                        <th class="text-center" width="5%">พ.ย</th>   
                                        <th class="text-center" width="5%">ธ.ค</th> 
                                        <th class="text-center" width="5%">ม.ค</th>
                                        <th class="text-center" width="5%">ก.พ</th>
                                        <th class="text-center" width="5%">มี.ค</th>

                                        <th class="text-center" width="5%">เม.ย</th>
                                        <th class="text-center" width="5%">พ.ค</th>
                                        <th class="text-center" width="5%">มิ.ย</th>
                                        <th class="text-center" width="5%">ก.ค</th>
                                        <th class="text-center" width="5%">ส.ค</th>
                                        <th class="text-center" width="5%">ก.ย</th>
                                    </tr> 
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach ($datashow as $item) 
                                <?php $i++ ?>   
                                <?php
                                        // $budget_year = DB::select('SELECT leave_year_id FROM budget_year WHERE years_now = "Y"');
                                        // foreach ($budget_year as $key => $va_y) {
                                        //     $budgetyear   = $va_y->leave_year_id;
                                        // }
                                
                                
                                ?>                             
                                    <tr>                                                  
                                        {{-- <td class="text-center" style="font-size:13px;color: rgb(13, 134, 185)" width="5%">{{$i}}</td> --}}
                                        <td class="text-start" style="font-size:13px;color: rgb(2, 95, 182)">{{$item->building_name}}</td>
                                        {{-- <td class="text-center" style="font-size:13px;color: rgb(4, 117, 117)">{{$item->building_id}}</td> --}}
                                        <td class="text-center" style="font-size:13px;color: rgb(228, 15, 86)">
                                           {{-- <a href="{{url('air_report_building_sub/'.$item->building_id)}}" target="_blank">  --}}
                                                <span class="badge bg-success"> {{$item->qtyall}}</span> 
                                            {{-- </a>  --}}
                                        </td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->tula}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->plusji}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->tanwa}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->makkara}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->gumpa}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->mena}}</td>

                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->mesa}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->plussapa}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->mituna}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->karakada}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->singha}}</td>
                                        <td class="text-center" style="font-size:13px;color: rgb(50, 3, 68)">{{$item->kanya}}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </p>
            </div>
        </div>
    </div>
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
