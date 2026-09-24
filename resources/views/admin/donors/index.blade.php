@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">
            All Donors
        </h6>

        <a href="{{ route('admin.donors.create') }}"
           class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i>
            Add Donor
        </a>
    </div>

    <x-message></x-message>

    <div class="table-responsive p-4">

        <table id="datatable"
               class="table data-table table-striped table-bordered">

            <thead>
                <tr>
                    <th>Donor Number</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Organization</th>
                    <th>Donations</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($donors as $donor)

                    <tr>

                        <td>
                            {{ $donor->donor_number }}
                        </td>

                        <td>
                            {{ $donor->name }}
                        </td>

                        <td>
                            {{ $donor->phone ?: 'NOT STATED' }}
                        </td>

                        <td>
                            {{ $donor->email ?: 'NOT STATED' }}
                        </td>

                        <td>
                            {{ $donor->organization ?: 'NOT STATED' }}
                        </td>

                        <td>
                            {{ $donor->donations_count }}
                        </td>

                        <td>

                            <a href="{{ route('admin.donors.show', $donor) }}"
                               class="btn btn-info btn-sm">
                                View
                            </a>

                            <a href="{{ route('admin.donors.edit', $donor) }}"
                               class="btn btn-primary btn-sm">
                                Edit
                            </a>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection
