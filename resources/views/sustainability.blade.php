@extends('layouts.app')
@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="faq-area pt-100 mt-5 pb-70">
    <div class="container">
        <div class="row align-items-justified">

            <div class="sust-reports-container col-lg-7">
                <div class="sust-reports">
                    <h4><b> {{ $sustainabilityItem->title }} {{$sustainabilityItem->img_links }} Project</b></h4>
                    <p class="sust-brief">{!! $sustainabilityItem->brief !!}</p>
                    @if(count($sustainabilityItem->reports) > 0)
                    
                    <div class=" col-md-12 reports-container">
                    <h5>Project Reports</h5>
                        @foreach($sustainabilityItem->reports as $report)
                        <div class="report-row-container text-center row col-md-12">
                            <div class="col-md-8  report-row-title text-sm-center">{{$report->report_title}} </div>

                            <a class="col-md-4 report-row-btn text-sm-center" href="{{ asset('uploads/sust_projects_reports/' . $report->report_url) }}" target="_blank" onclick="openPDF(event, this)">


                                <i class="fa fa-download"></i>

                                Download
                            </a>
                        </div>
                        @endforeach


                    </div>
                    @endif



                </div>

            </div>
            <div class="col-lg-5">
                <div class="testimonial-slider owl-theme owl-carousel">
                    @if(is_array($sustainabilityItem->image_links) && count($sustainabilityItem->image_links) > 0)
                    @foreach($sustainabilityItem->image_links as $image)
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('uploads/sust_projects/' . $image) }}" alt="Testimonial" style="height: 300px; width: 100%; object-fit: cover;">
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>

            <div class=" mt-4 col-lg-12 ">
                <h4><b>More about {{ $sustainabilityItem->title }}</b></h4>
                <hr>
                {!! $sustainabilityItem->description !!}
            </div>
        </div>
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