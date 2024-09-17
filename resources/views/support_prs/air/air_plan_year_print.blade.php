<!doctype html>
<html lang="en">
  <head>
    {{-- <meta charset="utf-8"> --}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
<title>QrCode All</title>

<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
<link href='https://fonts.googleapis.com/css?family=Kanit&subset=thai,latin' rel='stylesheet' type='text/css'>
{{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
<style type="text/css">
    @font-face {
        font-family: 'THSarabunNew';  
        src: url({{ storage_path('fonts/THSarabunNew.ttf') }}) format('truetype');
        font-weight: 300; // use the matching font-weight here ( 100, 200, 300, 400, etc).
        font-style: normal; // use the matching font-style here
    }        
    body{
        font-family: "THSarabunNew",  //set your font name u can set custom font name also which u set in @font-face css rule
      }
</style>

</head>
 
 
<?php
use SimpleSoftwareIO\QrCode\Facades\QrCode;
?>

<body>

<div class="container-fluid text-center">
  
        <table class="table table-sm table-striped">
          <thead>
            <tr>
              <th scope="col">ลำดับ</th>
              <th scope="col">รหัส</th>
              <th scope="col">อาคาร</th>
              <th scope="col">หน่วยงาน</th>
              <th scope="col">ยี่ห้อ</th>
              <th scope="col">btu</th>
            </tr>
          </thead>
          <tbody>
              <?php $i = 1; ?>
              @foreach ($data_air as $item)
                  <?php $i++; ?>
                  <tr>
                    <th scope="row">{{$i}}</th>
                    <td>{{$item->air_list_num}}</td>
                    <td>{{$item->air_location_name}}</td>
                    <td>{{$item->detail}}</td>
                    <td>{{$item->brand_name}}</td>
                    <td>{{$item->btu}}</td>
                  </tr> 
              @endforeach
          </tbody>
        </table>
 
  

</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>

{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script> --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script> --}}

</body>

</html>
