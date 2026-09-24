@extends('layouts.default')

@section('content')

      <!-- Swiper-->
        @if ($sliders->isNotEmpty())
@include('layouts.partials.slider')
@endif
      <!-- What We Offer-->
        @include('layouts.partials.categories')

      <!-- Section CTA-->
        @if ($banner)
@include('layouts.partials.banner')
@endif

      <!-- Our Shop-->
        @include('layouts.partials.products-home')


      <!-- What We Offer-->
        {{-- @include('layouts.partials.comments')--}}

        {{-- Gallery --}}
       {{-- @include('layouts.partials.gallery')--}}


      <!-- Section Services  Last section-->
        @include('layouts.partials.services')
@endsection
