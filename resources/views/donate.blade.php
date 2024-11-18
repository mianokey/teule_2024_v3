@extends('layouts.app')
@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="faq-area pt-100 mt-5 pb-70">
    <div class="container">
        <div class="row align-items-justified">

            <div class="col-lg-6 order-md-2">
                

                
                <ul class="accordion">
                    <li>

                        <a>
                            DONATE VIA PAYPAL
                            <i class="icofont-plus"></i>
                            <i class="icofont-minus two"></i>
                        </a>
                        <p class="text-center">
                            <a href="https://www.paypal.com/donate/?hosted_button_id=Q4SSDAS2YDSXJ" id="open-in-new-tab-link" style="background-color: white;">
                                <img width="390" src="{{ asset('assets/img/paypal.png') }}" alt="">
                            </a>
                        </p>
                    </li>
                    <li>

                        <a>
                            VIA ZELLE
                            <i class="icofont-plus"></i>
                            <i class="icofont-minus two"></i>
                        </a>
                        <p class="text-center">
                            1. Open Zelle in your banking app, scan the QR code or search recipient "teuleusa@teulekenya.org"</br>
                            2. Enter amount</br>
                            3. Add message for specific contributions</br>
                            
                            <a href="https://www.paypal.com/donate/?hosted_button_id=Q4SSDAS2YDSXJ" id="open-in-new-tab-link" style="background-color: white;">
                                <img width="290" src="{{ asset('assets/img/chasebank.jpg') }}" alt="">
                            </a>
                        </p>
                    </li>
                    
                    <li>

                        <a>
                            VENMO
                            <i class="icofont-plus"></i>
                            <i class="icofont-minus two"></i>
                        </a>
                        <p class="text-center">
                            1. Open Venmo App</br>
                            2. Select pay/request and search "Teule@Teule" in Charities</br>
                            3.  Enter amount </br>
                            4.  Donate </br>
                            
                        </p>
                    </li>
                    
                    <li>
                        <a>
                            VIA MPESA
                            <i class="icofont-plus"></i>
                            <i class="icofont-minus two"></i>
                        </a>
                        <div class="text-center method-details">
                            <p>Paybill:: <span>910450</span></p>
                            <p>Account Number:: <span>PURPOSE</span></p>
                            <p>Mpesa Line:: <span>0721582323-JOHN T HIGH</span></p>
                        </div>
                    </li>
                    <li>
                        <a>
                            VIA I&M BANK
                            <i class="icofont-plus"></i>
                            <i class="icofont-minus two"></i>
                        </a>
                        <div class="text-center method-details">
                            <p class="text-dark"><u>TEULE KENYA-SPONSORSHIP</u></p>
                            <p>A/C Number: <span>00200205951202</span></p>
                            <p>A/C Name: <span>Teule Kenya-Sponsorship Ac</span></p>
                            <p class="text-dark"><u>TEULE KENYA-EURO ACCOUNT</u></p>
                            <p>A/C Number: <span>00200205951212</span></p>
                            <p>A/C Name: <span>Teule Kenya-General Ac</span></p>
                        </div>
                    </li>
                    <li>
                        <a>
                            EQUITY BANK
                            <i class="icofont-plus"></i>
                            <i class="icofont-minus two"></i>
                        </a>
                        <div class="text-center method-details">
                            <p class="text-dark"><u>TEULE LEADERSHIP ACADEMY</u></p>
                            <p>A/C Number: <span>0740295914240</span></p>
                            <p>A/C Name: <span>Teule Leadership Academy</span></p>
                            <p class="text-dark"><u>TEULE KENYA-LTK BUSINESS UNITS</u></p>
                            <p>A/C Number: <span>0740295914287</span></p>
                            <p>A/C Name: <span>Teule Kenya-Ltk Business Units</span></p>
                        </div>
                    </li>
                    
                    <li>
                        <a>
                            KCB BANK KENYA
                            <i class="icofont-plus"></i>
                            <i class="icofont-minus two"></i>
                        </a>
                        <div class="text-center method-details">
                            <p class="text-dark"><u>TEULE KENYA</u></p>
                            <p>A/C Number: <span>1103831364</span></p>
                            <p>A/C Name: <span>Teule Kenya</span></p>
                        </div>
                    </li>
                    <li>
                        <a>
                            SEND CHECKS
                            <i class="icofont-plus"></i>
                            <i class="icofont-minus two"></i>
                        </a>
                        <div class="text-center method-details">
                            <p class="text-dark"><u>SEND CHECKS TO</u></p>
                            <p><span>1042 Maple Ave Unit 137 Lisle, IL 60532</span></p>
                            <p>CHECKS PAYABLE TO: <span> "Teule"</span></p>
                        </div>
                    </li>
                    <!--<li>-->
                    <!--    <a>-->
                    <!--        SENDWAVE-->
                    <!--        <i class="icofont-plus"></i>-->
                    <!--        <i class="icofont-minus two"></i>-->
                    <!--    </a>-->
                    <!--    <div class="text-center method-details">-->
                    <!--        <p class="text-dark"><u>SEND TO</u></p>-->
                    <!--        <p>MOBILE NUMBER: <span>0721582323</span></p>-->
                    <!--    </div>-->
                    <!--</li>-->
                </ul>
            </div>
            <div class="col-lg-6 order-md-1">
                <div class="testimonial-slider owl-theme owl-carousel">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('assets/img/faq-main.jpg') }}" alt="Testimonial">
                            </div>
                        </div>

                    </div>
                    <!--<div class="row align-items-center">-->
                    <!--    <div class="col-lg-12">-->
                    <!--        <div class="faq-img">-->
                    <!--            <img src="{{ asset('assets/img/faq-main-2.jpg') }}" alt="Testimonial">-->
                    <!--        </div>-->
                    <!--    </div>-->

                    <!--</div>-->
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('assets/img/faq-main-3.jpg') }}" alt="Testimonial">
                            </div>
                        </div>

                    </div>
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <div class="faq-img">
                                <img src="{{ asset('assets/img/faq-main-4.jpg') }}" alt="Testimonial">
                            </div>
                        </div>

                    </div>
                    <!--<div class="row align-items-center">-->
                    <!--    <div class="col-lg-12">-->
                    <!--        <div class="faq-img">-->
                    <!--            <img src="{{ asset('assets/img/faq-main-5.jpg') }}" alt="Testimonial">-->
                    <!--        </div>-->
                    <!--    </div>-->

                    <!--</div>-->



                </div>

            </div>
        </div>
    </div>
</div>
<x-footer></x-footer>
@endsection