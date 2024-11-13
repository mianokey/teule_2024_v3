@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="about-area pt-100 pb-70">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4">
                <div class="about-img">
                    <img src="assets/img/about/about-main1.jpg" alt="About">
                    <div class="video-wrap">
                        <button class="js-modal-btn" data-video-id="189686304">
                            <i class="icofont-ui-play"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="about-content">
                    <div class="section-title">
                        <span class="sub-title">Who we are</span>
                        <h2>Our History</h2>
                    </div>
                    <span class="about-span"><u>HUMBLE BEGINNINGS </u></span>
                    <p style="text-align: justify;">
                        Teule started as a humble project to serve the street children of Nairobi. Registered in 1996 as Homeless Children
                        International, HCI-Kenya. By December 1998, the organization expanded its services from the street children in Nairobi
                        to operating a Children's Home and Primary School in Loitokitok. In addition to serving the street boys, we began serving
                        other orphaned and vulnerable children, including girls rescued from harmful cultural practices like FGM and child marriage.
                        In 2005 the boys in the Nairobi program were moved to a boy’s dorm on the Loitokitok property. As part of the project in
                        Loitokitok, Teule formally registered a primary school called Kibo Slopes Academy in 2006.
                    </p>
                    <span class="about-span"><u>NEW NAME</u></span>
                    <p>Later on, HCI Kenya registered as a Non-Governmental Organization under the Kenyan NGO Act. At that time, the organization
                        realized the original name of “Homeless Children” bore a negative stigma, so we asked the children to select a name for
                        themselves; thus, Teule was born. In Kiswahili, “teule” is the word for “chosen”. As spoken by Jesus,
                    <blockquote class="cust-blockquote">“You did not choose me, but I chose you and appointed you so that you may go and bear fruit - fruit that will last
                        - and so that whatever you ask in my name the Father will give you.”-John 15:16</blockquote>
                    The name “Teule” speaks to the potential and fruitfulness of each child. It encourages the children that come through the program
                    to view themselves as “chosen” with a purpose, rather than rejected and alone.
                    </p>

                    <div class="about-btn-area">
                        <a class="common-btn" href="{{route('gallery')}}">Check our gallery</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<x-footer></x-footer>
@endsection