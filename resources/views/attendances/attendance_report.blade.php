
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
  bottom: 85px;
  left: 200px;
}
.next-bottom-left {
  position: absolute;
  top: 485px;
  left: 280px;
}
.email {
  position: absolute;
  top: 545px;
  left:280px;
}
.contact_person {
  position: absolute;
  top: 622px;
  left:280px;
}
.contact_number {
  position: absolute;
  top: 692px;
  left:280px;
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
  color: white;
}
.page_break { page-break-before: always; }

    </style>
</head>
<body style="margin: 0; padding: 0; text-align: left; ">
  
</body>
</html>


