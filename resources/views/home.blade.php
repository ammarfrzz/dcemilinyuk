@extends('layouts.app')

@section('content')
    {{-- Urutan section sesuai README versi React --}}
    @include('partials.hero')
    <div class="section-divider"></div>

    @include('partials.categories')
    <div class="section-divider"></div>

    @include('partials.products')
    <div class="section-divider"></div>

    @include('partials.peel-reveal')
    <div class="section-divider"></div>

    @include('partials.featured')
    <div class="section-divider"></div>

    @include('partials.specialty-drinks')
    <div class="section-divider"></div>

    @include('partials.gallery')
    <div class="section-divider"></div>

    @include('partials.horizontal-scroll')
    <div class="section-divider"></div>

    @include('partials.about')
    <div class="section-divider"></div>

    @include('partials.how-to-order')
    <div class="section-divider"></div>

    @include('partials.testimonials')

    @include('partials.cta')
    @include('partials.footer')
@endsection
