@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eight">
    <div class="d-table">
        <div class="d-table-cell">
            <div class="container">
                <div class="title-item">
                    <h2>MORE ABOUT TEULE</h2>
                    <ul>
                        <li>
                            <a href="{{route('home')}}">Home</a>
                        </li>
                        <li>
                            <span>more.. </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="about-area pt-100 pb-70">
    <div class="container">
        <div class="row d-flex justify-content-center align-items-center">

            <div class="col-lg-12 order-md-2">
                <div class="contact-area pb-70">
                    <div class="container">
                        <h4>More about Teule</h4>
                        <hr> <p style="text-align:justify;"> 
                            Teule Kenya is a registered Christian Non-Government Organization (NGO) that was established to rescue, 
                            support, and integrate orphaned and vulnerable children. Teule does this by operating a charitable 
                            children’s home, a private school, and working with vulnerable families in the community. The 
                            organization receives funding from Teule USA, a registered 501c non profit organization in the 
                            United States and other partners from Kenya and abroad. Teule Kenya programs in Kajiado South 
                            Sub-County include our Charitable Children Institution (CCI) christened Chombo Cha Upendo 
                            (meaning “Vessel of Love”) and primary school, Teule Leadership Academy, (formerly Kibo Slopes Academy).
                             Both institutions are located in Loitokitok, (LTK) town near the Kenya- Tanzania border 
                             overlooking Mt Kilimanjaro. Teule Kenya has four core activities in which to meet its mandate. 
                             These activities are providing holistic childcare, quality education, family empowerment, and 
                             spiritual discipleship to raise a generation of leaders who will serve their communities and 
                             the world. Our core activities are closely anchored on the spirit of Sustainable Development 
                             Goals, SDGs, in addressing the root causes of poverty and hunger. Additionally, these 
                             activities increase access to clean water, quality education, gender equality for 
                             orphaned and vulnerable children and their families. The realization of SDGs is 
                             Teule Kenya’s chosen path towards citizen responsibility and contribution to the 
                             achievement of national goals.
                        </p>
                       
                        <div class="col-md-6" id="spons-content" style="display: none;">
                            <div class="popover-content-inner">
                                <h4 class="title"><b><u>What is required?</u></b></h4>
                                <p>
                                    In order to apply for internship/Attachment kindly attach the following documents :
                                <ul>
                                    <li>Colored National ID</li>
                                    <li>Official letter from school</li>
                                    <li>Official letter from church</li>
                                    <li>Medical certificate</li>
                                </ul>
                                </p>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<x-footer></x-footer>
@endsection