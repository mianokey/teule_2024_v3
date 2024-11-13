@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>
<div class="page-title-area title-bg-eight">
  <div class="d-table">
    <div class="d-table-cell">
      <div class="container">
        <div class="title-item">
          <h2>THE EDUCATION FUND</h2>
          <ul>
            <li>
              <a href="{{route('home')}}">Home</a>
            </li>
            <li>
              <span>Education fund</span>
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

    <div class="col-lg-8 order-md-2">
    <div class="contact-area pb-70">
          <div class="container">
          <h4>Teule Education Fund</h4>
          <hr>
          <p style="text-align: justify;">
          Teule is committed to having every child in our program have access to quality education and complete secondary school or
           earn a certificate at a trade school program. As education continues past primary it becomes more expensive, especially 
           when a child can attend a boarding school.  Many children do not have sponsorship for secondary school. While some
            children have sponsors who will commit to taking the child through college, most children in the program do not have a
             sponsor who can do so.  Through our Educational  Fund, we are able to provide for the educational needs of all our
              children at least through secondary school and beyond when funds allow.  When you give to the Education Fund, your
               money goes directly to educational expenses like school fees, tuition, set books, school supplies, dorm necessities,
                transport school uniform and shoes. Partner with us in giving the gift of education by donating today.
          </p>
          <a href="{{route('donate')}}"><button class="btn btn-primary btn-custom">Support Education Fund</button></a>
             
          </div>
    </div>

    </div>


    </div>
  </div>
</div>
<x-footer></x-footer>
@endsection