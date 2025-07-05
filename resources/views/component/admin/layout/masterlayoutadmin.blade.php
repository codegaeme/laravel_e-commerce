<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from zoyothemes.com/tapeli/html/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 16 Jul 2024 08:33:02 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>

        <meta charset="utf-8" />
       <title>Shop|@yield('title', 'Shop')</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc."/>
        <meta name="author" content="Zoyothemes"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('component.admin.component.linkcss')
        @yield('css')
    </head>

    <!-- body start -->
    <body data-menu-color="light" data-sidebar="default">

        <!-- Begin page -->
        <div id="app-layout">


            <!-- Topbar Start -->
            @include('component.admin.component.header')
            <!-- end Topbar -->

            <!-- Left Sidebar Start -->

            <!-- Left Sidebar End -->
            @include('component.admin.component.sidebar')
            <!-- ============================================================== -->
            <!-- Start Page Content here -->
            <!-- ============================================================== -->

            @yield('content')
            <!-- ============================================================== -->
            <!-- End Page content -->
            <!-- ============================================================== -->

        </div>
        <!-- END wrapper -->

        <!-- Vendor -->
       @include('component.admin.component.linkjs')
       @yield('js')

    </body>

<!-- Mirrored from zoyothemes.com/tapeli/html/ by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 16 Jul 2024 08:34:03 GMT -->
</html>
