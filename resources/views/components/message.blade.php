 <!-- Success Message -->
 <div class="">
 @if(session('success'))
 <div class="alert alert-success">
     {{ session('success') }}
 </div>
@endif
<!-- End Success Message -->

<!-- Error Div -->
@if ($errors->any())
 <div class="alert alert-danger">
     <ul>
         @foreach ($errors->all() as $error)
             <li>{{ $error }}</li>
         @endforeach
     </ul>
 </div>
@endif
<!-- End Error Div -->