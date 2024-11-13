@php
use Carbon\Carbon;
@endphp

@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>

<div class="page-title-area title-bg-eleven">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="container">
                <div class="title-item">
                    <h2>{{ strtoupper($child->name) }} PROFILE </h2>
                    <ul>
                        <li>
                            <a href="{{ route('home') }}">Home</a>
                        </li>
                        <li>
                            <span>{{ $child->name }} Profile</span>
                        </li>
                    </ul>
                    <!-- The hidden HTML content that will appear in the popover -->
                    <div class="col-md-6" id="spons-content" style="display: none;">
                        <div class="popover-content-inner">
                            <h4 class="title"><b><u>WHAT YOU NEED TO KNOW ABOUT CHILD SPONSORSHIP</u></b></h4>
                            <p>
                                Child Sponsorship is one of the many ways for partners like you
                                to support Teule in serving vulnerable children and their families.
                                When you sponsor a child, you will receive updates on your child's progress and
                                will have the opportunity to communicate with them.
                                Sponsorship funds are pooled together to provide for the basic needs
                                of all the children in our program and assist in stabilizing vulnerable
                                families in the community. In addition to providing access to quality
                                education, your contributions also cover psychosocial support from our
                                counselors and social workers. Each child needs several sponsors to cover the cost of
                                holistic care.
                            </p>
                        </div>
                    </div>
                    <div id="sposn-fancybox" data-fancybox="popover" data-src="#spons-content"
                        class="sponsorship-nature">
                        <p style="color: white !important;" class="text-center">Click here to learn more about our
                            sponsorship
                            <i class="icon  fa fa-info-circle "></i>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="team-area ptb-100">
    <div class="container">
        <section style="background-image: url('{{ asset('/assets/img/profilebg_2.jpg') }}'); background-color: #eee;">

            <div class="container py-5">
                <div class="row">
                    <div class="col">
                        <nav aria-label="breadcrumb" class="bg-body-tertiary rounded-3 p-3 mb-4">
                            <ol class="breadcrumb mb-0">
                                <h5>{{ strtoupper($child->name) }} PROFILE</h5>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-4">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card mb-4">
                                    <div class="card-body text-center">
                                        <a href="{{ asset($child->img_url) }}" data-lightbox="profile-image">
                                            <img src="{{ asset($child->img_url) }}" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
                                        </a>
                                        <h5 class="my-3">{{ $child->name }}</h5>
                                       
                                        <div class="d-flex justify-content-center mb-2">
                                            <a href="{{ route('donate') }}"><button type="button"
                                                    data-mdb-button-init data-mdb-ripple-init
                                                    class="btn btn-primary">SPONSOR {{ strtoupper($child->name) }}
                                                </button></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12 ">
                                <div class="card mb-4">
                                    <div class="card-body text-center">
                                        <b><u>
                                                <h6>CONTACT DETAILS : TEULE USA</h6>
                                            </u></b>
                                        <p>
                                            <b>Maple Avenue Unit 137 Lisle, IL 60532</b><br>
                                            <b>EMAIL:</b> teuleusa@teulekenya.org<br>
                                            <b>EIN #:</b> 47-5102209<br>
                                        </p>
                                        <hr>
                                        <h6><u>CONTACT DETAILS : TEULE KENYA</u></h6>
                                        <p>
                                            <b>EMAIL:</b> info@teulekenya.org<br>
                                            <b>TEL:</b> +254 721 582323<br>
                                            <b>P.O BOX</b> 184-00209 Oloitokitok, Kenya<br>
                                            <b>NGO Registration Code:</b> OP.218/051/9518/531
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">FULL NAME</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0">{{ $child->name}}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">D.O.B</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0">{{ $child->dob}}</p>
                                    </div>
                                </div>
                                <hr>

                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">AGE</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0">{{ $child->age}}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">CURRENT GRADE</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0">{{ $child->details->firstWhere('key', 'current_grade')->value ?? '---' }}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">PROGRAM</p>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0">{{ $child->status}}</p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0">OTHER DETAILS</p>
                                    </div>
                                    <div class="col-sm-9">
                                        @if ($child->details)
                                        @foreach ($child->details as $detail)
                                        @if ($detail->key !== 'current_grade' && $detail->key !== 'sponsors')
                                        <p><b>{{ $detail->key }}:</b> {{ $detail->value }}</p>
                                        @endif
                                        @endforeach
                                        @else
                                        <p>no details</p>
                                        @endif
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <p class="mb-0"> <b>HOW TO SPONSOR</b></p>
                                    </div>
                                    <div class="col-sm-9">
                                      
                                        <ul>
                                            <li>Via PayPal (click this link) <a
                                                    href="https://www.paypal.com/donate/?hosted_button_id=Q4SSDAS2YDSXJ">PayPal
                                                    Donation</a></li>
                                            <li>Check/Cheque to Teule USA with <b>"{{ $child->name}} Sponsorship"</b> in the
                                                memo line. The Check/Cheque can be sent to: <b>1042 Maple Avenue Unit
                                                    137 Lisle, IL 60532</b></li>
                                            <li>Safaricom Mpesa Pay bill <b>910450</b> Account name <b>{{
                                                    $child->name}}<b></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</section>

<x-footer></x-footer>
@endsection
