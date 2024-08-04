@extends('layouts.mobile')
@section('title', 'PK-OFFICE || Suplieser')
@section('content')
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
            border: 10px #ddd solid;
            border-top: 10px #d22cf3 solid;
            border-radius: 50%;
            animation: sp-anime 0.8s infinite linear;
        }

        @keyframes sp-anime {
            100% {
                transform: rotate(390deg);
            }
        }

        .is-hide {
            display: none;
        }
    </style>
    <script>
        function TypeAdmin() {
            window.location.href = '{{ route('index') }}';
        }

       
    </script>

    <?php
    if (Auth::check()) {
        $type = Auth::user()->type;
        $iduser = Auth::user()->id;
        $iddep = Auth::user()->dep_subsubtrueid;
    } else {
        echo "<body onload=\"TypeAdmin()\"></body>";
        exit();
    }
    $url = Request::url();
    $pos = strrpos($url, '/') + 1;
    $datenow = date('Y-m-d');
    $y = date('Y') + 543;
    $newweek = date('Y-m-d', strtotime($datenow . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
    $newDate = date('Y-m-d', strtotime($datenow . ' -1 months')); //ย้อนหลัง 1 เดือน
    
    use App\Http\Controllers\StaticController;
    use App\Models\Products_request_sub;
    $countpermiss_ot = StaticController::countpermiss_ot($iduser);
    ?>
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
        <form action="{{ url('home_supplies_mobile') }}" method="GET">
            @csrf
        <div class="row mt-5 ms-3 me-3 text-center"> 
            <div class="col"> 
                <h4 style="color:green">รายการซ่อมตามใบแจ้งซ่อม</h4> 
            </div>
        </div>
        <div class="row mt-3 ms-3 me-3"> 
            <div class="col">
                <select class="form-control cardair" id="air_repaire_type" name="air_repaire_type" style="width: 100%">
                    <option value="" class="text-center">เลือกประเภททั้งหมด</option>
                        @foreach ($air_repaire_type as $item_t)
                        @if ($repaire_type == $item_t->air_repaire_type_code)
                            <option value="{{ $item_t->air_repaire_type_code }}" class="text-center" selected> {{ $item_t->air_repaire_typename }}</option>
                        @else
                            <option value="{{ $item_t->air_repaire_type_code }}" class="text-center"> {{ $item_t->air_repaire_typename }}</option>
                        @endif 
                        @endforeach 
                </select>
            </div>
        </div> 
        <div class="row mt-3 ms-3 me-3"> 
            <div class="col"> 
                <div class="input-daterange input-group" id="datepicker1" data-date-format="dd M, yyyy" data-date-autoclose="true" data-provide="datepicker" data-date-container='#datepicker1'>
                    <input type="text" class="form-control cardair" name="startdate" id="datepicker" placeholder="Start Date" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $startdate }}" required/>
                    <input type="text" class="form-control cardair" name="enddate" placeholder="End Date" id="datepicker2" data-date-container='#datepicker1' data-provide="datepicker" data-date-autoclose="true" autocomplete="off"
                        data-date-language="th-th" value="{{ $enddate }}"/>  
                        <button type="submit" class="ladda-button btn-pill btn btn-primary cardair" data-style="expand-left">
                            <span class="ladda-label"> <i class="fa-solid fa-magnifying-glass text-white me-2"></i>ค้นหา</span> 
                        </button> 
                      
                </div> 
            </div> 
        </div>
    </form>
        <div class="row mt-3 ms-3 me-3">
            <div class="col">
              
                    <div class="card-body cardair">    
                        
                      
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" style="width: 100%;"> 
                                    <thead>
                                        <tr style="font-size:13px">
                                          
                                            {{-- <th width="5%" class="text-center">ลำดับ</th>    --}}
                                            <th class="text-center" width="10%">วันที่ซ่อม</th>  
                                            <th class="text-center" width="10%">เวลา</th>
                                            <th class="text-center" width="10%">เลขที่แจ้งซ่อม</th>
                                            <th class="text-center">รหัส</th>   
                                            {{-- <th class="text-center">รายการ</th>    --}}
                                            {{-- <th class="text-center" width="12%">เจ้าหน้าที่</th>  --}}
                                            {{-- <th class="text-center" width="12%">ช่าง รพ.</th>  --}}
                                            {{-- <th class="text-center" width="12%">ช่างนอก</th>   --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; ?>
                                        @foreach ($datashow as $item) 
                                            <tr id="tr_{{$item->air_repaire_id}}" style="font-size:13px">                                                  
                                                {{-- <td class="text-center" width="5%">{{ $i++ }}</td>    --}}
                                                <td class="text-center">{{ DateThai($item->repaire_date )}}</td>  
                                                <td class="text-center">{{ $item->repaire_time }}</td> 
                                                <td class="text-center">{{ $item->air_repaire_no }}</td> 
                                                <td class="text-start">{{ $item->air_list_num }}</td>  
                                                {{-- <td class="text-start" width="10%">{{ $item->staff_name }}</td>  --}}
                                                {{-- <td class="text-start" width="10%">{{ $item->tect_name }}</td>  --}}
                                                {{-- <td class="text-start" width="10%">{{ $item->air_techout_name }}</td>  --}}
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
 

@endsection
@section('footer') 
    <script>
        $(document).ready(function() {
            
 
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '#printBtn', function() {
                var month_id = $(this).val();
                alert(month_id);

            });

            $("#spinner-div").hide(); //Request is complete so hide spinner
         
         $('#Pulldata').click(function() {
             var data_insert = $('#data_insert').val(); 
            //  var datepicker2 = $('#datepicker2').val(); 
             Swal.fire({
                 position: "top-end",
                     title: 'ต้องการประมวลผลใช่ไหม ?',
                     text: "You Warn Process Data!",
                     icon: 'warning',
                     showCancelButton: true,
                     confirmButtonColor: '#3085d6',
                     cancelButtonColor: '#d33',
                     confirmButtonText: 'Yes, Process it!'
                     }).then((result) => {
                         if (result.isConfirmed) {
                             $("#overlay").fadeIn(300);　
                             $("#spinner").show(); //Load button clicked show spinner 
                             
                             $.ajax({
                                 url: "{{ route('d.nurse_index_process') }}",
                                 type: "POST",
                                 dataType: 'json',
                                 data: {
                                    data_insert                       
                                 },
                                 success: function(data) {
                                     if (data.status == 200) { 
                                         Swal.fire({
                                             position: "top-end",
                                             title: 'ประมวลผลสำเร็จ',
                                    text: "You Process data success",
                                             icon: 'success',
                                             showCancelButton: false,
                                             confirmButtonColor: '#06D177',
                                             confirmButtonText: 'เรียบร้อย'
                                         }).then((result) => {
                                             if (result
                                                 .isConfirmed) {
                                                 console.log(
                                                     data);
                                                 window.location.reload();
                                                 $('#spinner').hide();//Request is complete so hide spinner
                                                     setTimeout(function(){
                                                         $("#overlay").fadeOut(300);
                                                     },500);
                                             }
                                         })
                                     } else {
                                         
                                     }
                                 },
                             });
                             
                         }
             })
         });
 
 
        });
    </script>

@endsection
