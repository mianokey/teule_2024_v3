@extends('layouts.app')
@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="faq-area pt-100 mt-5 pb-70">
    <div class="container">
    @foreach($latests as $latest)
        <div class="row align-items-justified">

            <div class="sust-reports-container col-lg-7">
                <div class="sust-reports">
                    <h4><b> {!! $latest->title !!}</b></h4>
                    <h6>Update on:{{ \Carbon\Carbon::parse($latest->updated_at)->format('d-M-y H:i:s') }}</h6>
                    <div style="text-align: justify;" class="mt-3">
                        {!! $latest->description !!}
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="testimonial-slider owl-theme owl-carousel">

                @if(is_array($latest->image_links) && count($latest->image_links) > 0)
                    @foreach($latest->image_links as $image)
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('uploads/latest/' . $image) }}" alt="Testimonial" style="height: 300px; width: 100%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
         </div>
    @endforeach

    {{ $latests->links() }}
         
    </div>
</div>
<x-footer></x-footer>
<script>
    function openPDF(event, link) {
        event.preventDefault();
        const pdfUrl = link.href;
        window.open(pdfUrl, '_blank');
    }
</script>
@endsection