@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<!-- <div class="page-title-area title-bg-eight">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="container">
                <div class="title-item">
                    <h2>Gallery</h2>
                    <ul>
                        <li>
                            <a href="{{route('home')}}">Home</a>
                        </li>
                        <li>
                            <span>gallery </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div> -->
<div class="gallery-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-md-2 d-none d-md-block">
                <!-- Year Folders section (Visible on Medium and Large screens) -->
                <div class="custom-links">
                    <h5 class="text-center"><i class="fa fa-filter"></i> Filter by years</h5>
                    <ul>
                        @foreach ($yearLinks as $year => $link)
                        <li class="text-center">
                            <a href="{{ $link }}">{{ $year }} PHOTOS</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="col-md-10">
                <!-- Image Gallery section -->
                <div class="row">
                    @foreach ($paginatedImageFiles as $imageUrl)
                    <div class="col-sm-6 col-lg-4">
                        <div class="gallery-item">
                            <a href="{{ asset($imageUrl) }}" data-lightbox="roadtrip">
                                <img src="{{ asset($imageUrl) }}" style="height: 300px; width: 100%; object-fit: cover;" alt="Gallery" />
                                <i class="icofont-eye"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div style="text-align: center;" class="pagination d-flex justify-content-center">
                    {{ $paginatedImageFiles->links() }}
                </div>
            </div>
        </div>
        <div class="d-sm-block d-md-none d-lg-none d-xl-none" style="position: fixed; top: 20px; left: 20px;">
            <!-- Floating button for Year Folders section (Visible on Small and Extra Small screens) -->
            <div class="btn-float floated_div">
            <div class="custom-links">
                    <ul>
                        @foreach ($yearLinks as $year => $link)
                        <li>
                            <a href="{{ $link }}">{{ $year }} PHOTOS</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
</div>
            
           
        </div>
    </div>
</div>
<x-footer></x-footer>