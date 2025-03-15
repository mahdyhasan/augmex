<!DOCTYPE html>
<html lang="en">

    @include('layouts.partials.head')

    <body>

        <!-- Main Wrapper -->
        <div class="main-wrapper">

            <div class="preloader">
                <span class="loader"></span>
            </div>

            @include('layouts.partials.header')

            @include('layouts.partials.sidebar')

            <!-- Page Wrapper -->
            <div class="page-wrapper">
                <div class="content">
                    @yield('content')
                </div>
            </div>
            <!-- /Page Wrapper -->

        </div>
        <!-- /Main Wrapper -->

        @include('layouts.partials.script')

    </body>

</html>