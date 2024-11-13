@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="about-area page-title-area title-bg-fifteen pt-100 pb-70">
    <div style="z-index: 1050" class="container">
        <div class="row align-items-center">

            <div class="col-lg-7 mt-5">
                <div class="about-content">
                    <div class="section-title">
                        <h3 style="font-weight:500;font-family:tahoma" class="text-white">Hello,welcome to</h3>
                        <h1 style="font-weight:900;font-size:50px;width:100%;font-family:tahoma" class="text-white">TEULE LEADERSHIP ACADEMY</h1>
                    

                    

                    <blockquote class="cust-blockquote">
                        Enroll today for playgroup, grade 1-6 and Junior secondary classes.Teule Kenya is the oasis of learning
                    </blockquote>


                    <div class="about-btn-area">
                        <a class="common-btn">For enquiries <br> <i class="fa fa-phone"></i>+254 721582323/+254 741541038</a>
                    </div>
                </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
<div class="about-area pt-100 pb-70">
  <div class="container">
    <div class="row d-flex justify-content-center align-items-center">

    <div class="col-lg-8 order-md-1">
    <div class="contact-area pb-70">
          <div class="container">
          <h4>Overview</h4>
          <hr>
          <p style="text-align: justify;">
          Teule Leadership Academy, formerly known as Kibo Slopes Academy, was officially registered as aprivate primary school in the Oloitoktok District in 2006.
          The schoo, has excelled in extracurricular activities like athletics and drama with students competing at sub-county and county level. The school was recently re-registered
          as Teule Leadership Academy. In addition to serving the children at chombo cha upendo children's home, the school is open to the community.<br>
          Teule Kenya believes in the importance of quality Christian education and the holistic development of each learner.We know that each learner has different gifts and talents
          Our qualified teachers are trained to identify and meet the unique educational needs of our learners. Teule Leadership Academy strives to be an oasis of learning where all children
          can discover the leader inside.
          </p>  
          </div>
    </div>

    </div>
    <div class="col-lg-4 order-md-2">
        <div class="contact-area pb-70">
              <div class="container">
              <img src="{{ asset('assets/img/school/graduation.jpg') }}">
              <p style="text-align: justify;">
              
            </p>   
              </div>
        </div>
    
        </div>


    </div>
    <div class="row d-flex justify-content-center align-items-center">

        <div class="col-lg-8 order-md-2">
        <div class="contact-area pb-70">
              <div class="container">
              <h4>SCHOOL FEES STRUCTURE</h4>
              <hr>
              <table class="table table-bordered">
                <thead>
                    <th>GRADE</th>
                    <th>FEES AMOUNT</th>
                </thead>
                <tbody>
                    <tr>
                        <td>Playgroup-PP2</td>
                        <td>Ksh. 4,200</td>
                    </tr>
                    <tr>
                        <td>Grade 1-3</td>
                        <td>Ksh. 4,800</td>
                    </tr>
                    <tr>
                        <td>Grade 4-5</td>
                        <td>Ksh. 5,400</td>
                    </tr>
                    <tr>
                        <td>Grade 6</td>
                        <td>Ksh. 6,000</td>
                    </tr>
                    <tr>
                        <td>Junior Secondary</td>
                        <td>Ksh. 10,000</td>
                    </tr>
                    <tr>
                        <td>School Van</td>
                        <td>Ksh. 4,500</td>
                    </tr>
                    
                </tbody>
              </table>
              <p style="text-align: justify;">
              Fees is paybale to:<b>Equity Bank </b> account name:<b>Teule Leadership Academy</b> Account number:<b>0740295914240</b>
              </p>  
              </div>
        </div>
    
        </div>
        <div class="col-lg-4 order-md-1">
            <div class="contact-area pb-70">
                  <div class="container">
                  <img src="{{ asset('assets/img/school/sch_002.jpg') }}">
                  <img src="{{ asset('assets/img/school/sch_004.jpg') }}">
                  <p style="text-align: justify;">
                  
                </p>   
                  </div>
            </div>
        
            </div>
    
    
        </div>
  </div>
</div>
<x-footer></x-footer>
@endsection