<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from themewagon.github.io/cozastore/home-03.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 31 May 2025 16:58:55 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=utf-8" /><!-- /Added by HTTrack -->
<head>
	<title>Shop|@yield('title')</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
@include('component.client.component.linkcss')
@yield('css')


<!--===============================================================================================-->
</head>
<body class="animsition">

@include('component.client.component.header')



@yield('content')


	<!-- Footer -->
@include('component.client.component.footer')


@include('component.client.component.setting')
@include('component.client.component.linkjs')
@yield('js')
</body>

<!-- Mirrored from themewagon.github.io/cozastore/home-03.html by HTTrack Website Copier/3.x [XR&CO'2014], Sat, 31 May 2025 16:58:58 GMT -->
</html>
