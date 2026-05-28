@extends('layouts.landing')

@section('content')
    @include('landing.partials.hero')
    @include('landing.partials.services')
    @include('landing.partials.why-us')
    @include('landing.partials.stats')
    @include('landing.partials.team')
    @include('landing.partials.testimonials')
    @include('landing.partials.contact')
@endsection
