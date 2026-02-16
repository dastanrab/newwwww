<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<link rel="stylesheet" href="{{asset('assets/css/style.bootstrap.css')}}">
<link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
<link rel="stylesheet" href="{{asset('assets/css/style.icons.css')}}">
<body dir="rtl">
<div class="container-fluid">
    <div class="row mt-5">
        <div class="d-flex justify-content-center">
        <figure><img src="{{$logo}}" width="200px" alt=""></figure>
        </div>
        <div class="d-flex justify-content-center mt-2">
            <div class="alert alert-{{$alertClass}}">{{$description}}</div>
        </div>
        <div class="d-flex justify-content-center mt-2">
            <span><a href="{{$link}}" class="btn btn-{{$alertClass}} btn-lg"><i class='{{$icon}}'></i> {{$textBtn}}</a></span>
        </div>
    </div>
</div>
</body>
</html>
