@extends('layouts.app')

@section('content')
<x-header></x-header>
<x-navbar></x-navbar>

<div class="faq-area pt-100 mt-5 pb-70">
    <div class="container">
        <!-- Mailchimp Campaigns -->
        <div class="card">
            <div class="card-header">
                <h3>Latest Newsletters</h3>
            </div>
            <div class="card-body">
                <div class="display_archive">
                    <style type="text/css">
                        .display_archive {
                            font-family: Arial, Verdana;
                            font-size: 16px; /* Increased font size */
                        }
                        .campaign {
                            line-height: 150%; /* Increased line height */
                            margin: 10px; /* Increased margin */
                        }
                    </style>
                    <script
                        language="javascript"
                        src="https://teulekenya.us8.list-manage.com/generate-js/?u=faccd5067c49396b3faf54e8f&show=10&fid=7961"
                        type="text/javascript"
                    ></script>
                </div>
            </div>
        </div>
        <!-- End of Mailchimp Campaigns -->
    </div>
</div>

<x-footer></x-footer>
@endsection
