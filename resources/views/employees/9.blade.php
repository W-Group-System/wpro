
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
  bottom: 45px;
  left: 150px;
}
.next-bottom-left {
  position: absolute;
  top: 270px;
  left: 360px;
}
.email {
  position: absolute;
  top: 330px;
  left:360px;
}
.contact_person {
  position: absolute;
  top: 380px;
  left:360px;
}
.contact_number {
  position: absolute;
  top: 440px;
  left:360px;
}
.contact_person_d {
  position: absolute;
  top: 800px;
  left:360px;
}
.contact_number_d {
  position: absolute;
  top: 850px;
  left:360px;
}
.website {
  position: absolute;
  top: 500px;
  left:360px;
}
.qr-code {
  position: absolute;
  top: 822px;
  left:40px;
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
  bottom: 270px;
  left: 10px;
}
.position {
  position: absolute;
  bottom: 230px;
  left: 10px;
}

/* Centered text */
.centered {
  position: absolute;
  top: 47.5%;
  left: 50%;
  transform: translate(-50%, -50%);
}

    </style>
</head>
<body style="margin: 0; padding: 0; text-align: left; ">
    <div class="container" style='height:100%;'>
   
        <img style='border:1px;' src="{{asset('/images/FMTCCFront.png')}}" width="100%" height="100%"/>
        <div class="bottom-left" style='font-size: 40px;'>{{$employee->employee_code}}</div>
        <div class="name" style='  font-size: 50px;color:#134d70;text-align: center;width:100`%;'><b style='text-transform: ;'>{{$employee->last_name}}</b>, {{$employee->first_name}} </div>
        <div class="position" style='  font-size: 35px;color:#134d70;text-align: center;width:100%;'>{{$employee->position}}</div>
        <div class="centered"><img   src='{{asset($employee->avatar)}}' style='width:460px;' ></div>
    </div>
    <div class="container" style='height:100%;'>
   
        <img style='border:1px;' src="{{asset('/images/FMTCCBack.png')}}" width="100%" height="100%"/>
        <div class="next-bottom-left" style='font-size: 30px;'>{{$employee->tax_number}}</div>
        <div class="email" style='font-size: 30px;'>{{$employee->phil_number}}</div>
        <div class="contact_person" style='font-size: 30px;'>{{$employee->sss_number}}</div>
        <div class="contact_number" style='font-size: 30px;'>{{$employee->hdmf_number}}</div>
        <div class="website" style='font-size: 30px;'>{{date('F d, Y',strtotime($employee->original_date_hired))}}</div>
        {{-- <div class="qr-code" ><img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(250)->errorCorrection('H')->generate('https://hris.wsystem.online/calling-card/'.$employee->employee_code)) !!}"></div> --}}

        <div class="contact_person_d" style='font-size: 30px;'>{{$employee->contact_person->name ?? null}}</div>
        <div class="contact_number_d" style='font-size: 30px;'>{{$employee->contact_person->contact_number ?? null}}</div>
    </div>
    
    
</body>
</html>


