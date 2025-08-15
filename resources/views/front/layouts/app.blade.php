@include('front.layouts.head')

<body>
    @include('front.layouts.nav')

    @yield('content')

    @include('front.layouts.footer')

    @include('front.layouts.scripts')
</body>

</html>