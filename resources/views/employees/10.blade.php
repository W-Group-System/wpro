
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="icon" type="image/png" href="{{ asset('/images/icon.png')}}"/>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    <style>
        table { 
            border-spacing: 0;
            border-collapse: collapse;
        }
        body{
          font-family: sans-serif;
            border-collapse: collapse;
        }
        .page-break {
            page-break-after: always;
        }
        .green {
            width:10px;
            border: 10px solid green;
        }
        .violet {
            width:10px;
            border: 10px solid blueviolet;
        }
        .grey {
            width:10px;
            border: 10px solid gray;
        }
        @page {
  margin: 0;
}
@font-face {
        font-family: 'Helvetica';
        font-weight: normal;
        font-style: normal;
        font-variant: normal;
        /* src: url("font url"); */
      }

/* Bottom left text */
.bottom-left {
  position: absolute;
  bottom: 90px;
  left: 200px;
}
.next-bottom-left {
  position: absolute;
  top: 180px;
  left: 120px;
}
.philhealth-number {
  position: absolute;
  top: 180px;
  left: 520px;
}
.sss-number {
  position: absolute;
  top: 300px;
  left: 150px;
}
.pagibig-number {
  position: absolute;
  top: 300px;
  left: 550px;
}
.datehired {
  position: absolute;
  top: 420px;
  left: 570px;
}
.email {
  position: absolute;
  top: 545px;
  left:280px;
}
.contact_person {
  position: absolute;
  top: 645px;
  left:180px;
}
.contact_number {
  position: absolute;
  top: 715px;
  left:330px;
}
.website {
  position: absolute;
  top: 752px;
  left:280px;
}
.qr-code {
  position: absolute;
  top: 1022px;
  left:40;
}

/* Top left text */
.top-left {
  position: absolute;
  top: 8px;
  left: 16px;
}

/* Top right text */
.top-right {
  position: absolute;
  top: 8px;
  right: 16px;
}

/* Bottom right text */
.name {
  position: absolute;
  bottom: 300px;
  left: 10px;
}
.nickname {
  position: absolute;
  bottom: 370px;
  left: 10px;
}
.position {
  position: absolute;
  bottom: 235px;
  left: 10px;
}

/* Centered text */
.centered {
  position: absolute;
  top: 46.5%;
  left: 50.5%;
  transform: translate(-50%, -50%);
}
.container {
  position: relative;
  text-align: center;
  color: black;
}
.page_break { page-break-before: always; }

    </style>
</head>
<body style="margin: 0; padding: 0; text-align: left; ">
    <div class="container" style='height:100%;'>
   
        <img src="{{asset('/images/PBIFront.png')}}" width="100%" height="100%"/>
        <div class="bottom-left" style='font-size: 40px;color:black;'>{{$employee->employee_code}}</div>
        <div class="nickname" style='  font-size: 50px;text-align: center;width:100%;color:black;'>{{$employee->nick_name}}</div>
        <div class="name" style='  font-size: 50px;text-align: center;width:100%;color:black;'><b>{{$employee->last_name}}, {{$employee->first_name}}</b></div>
        <div class="position" style='  font-size: 45px;text-align: center;width:100%;color:#2789c6;'><b>{{$employee->position}}</b></div>
        <div class="centered"><img   src='{{asset($employee->avatar)}}' style='width:440px;height:440px; border-radius: 50%;' ></div>
    </div>
    <div class="page_break"></div>
    <div class="container" style='height:100%;'>
   
        <img style='border:1px;' src="{{asset('/images/PBIBack.png')}}" width="100%" height="100%"/>
        <div class="next-bottom-left" style='font-size: 30px;'>{{$employee->tax_number}}</div>
        <div class="philhealth-number" style='font-size: 30px;'>{{$employee->phil_number}}</div>
        <div class="sss-number" style='font-size: 30px;'>{{$employee->sss_number}}</div>
        <div class="pagibig-number" style='font-size: 30px;'>{{$employee->hdmf_number}}</div>
        <div class="datehired" style='font-size: 30px;'>{{date('m-d-Y',strtotime($employee->original_date_hired))}}</div>
        {{-- <div class="email" style='font-size: 30px;'>{{$employee->user_info->email}}</div> --}}
        <div class="contact_person" style='font-size: 30px;'>{{$employee->contact_person->name ?? null}}</div>
        <div class="contact_number" style='font-size: 30px;'>{{$employee->contact_person->contact_number ?? null}}</div>
        {{-- <div class="qr-code" ><img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate('https://hris.wsystem.online/calling-card/'.$employee->employee_code)) !!}"></div> --}}
    </div>
</body>
</html>


