<!DOCTYPE html>
<html lang="en">
@include('layouts.partials.head')
<body>
<!-- Start wrapper -->
<div id="wrapper">

    @include('layouts.partials.sidebar')

    @include('layouts.partials.header')

    @yield('content')

</div><!--End wrapper-->

<!--Start Back To Top Button-->
<a href="javaScript:void(0);" class="back-to-top"><i class="fa fa-angle-double-up"></i></a>
<!--End Back To Top Button-->

@include('layouts.partials.footer')

<div class="modal fade" id="darkmodal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-dark"></div>
    </div>
</div>

@include('layouts.partials.script')
</body>
</html>
