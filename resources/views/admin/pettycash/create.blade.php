@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Petty Cash Request</h6>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.pettycash.store') }}" enctype="multipart/form-data"
            id="petty-cash-form">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <div class="form-floating">
                            <!-- Payee select dropdown -->
                            <select class="form-control" id="payee" name="payee">
                                <option value="">Select Payee</option>
                                @foreach($payees as $payee)
                                <option value="{{ $payee->id }}" data-phone="{{ $payee->ref_one }}"
                                    data-store="{{ $payee->ref_two }}">
                                    {{ $payee->name }}
                                </option>
                                @endforeach
                            </select>
                            <!-- Payee input field (hidden by default) -->
                            <input type="text" class="form-control" id="new-payee" name="new_payee" placeholder="Enter New Payee Name" style="display: none;">
                            <label for="payment_medium">Phone/Agent/Paybill/Till</label>
                        </div>
                        <div class="input-group-append p-2">
                            <span class="input-group-text btn bg-success" id="basic-addon2" onclick="togglePayeeInput()"> <i class="fa fa-plus"></i> </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="payment_medium" name="payment_medium"
                            value="{{ old('payment_medium') }}" placeholder="Phone/Agent/Paybill/Till">
                        <label for="payment_medium">Phone/Agent/Paybill/Till</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="store_account" name="store_account"
                            value="{{ old('store_account') }}" placeholder="Store Number/Account">
                        <label for="store_account">Store Number/Account</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount') }}"
                            placeholder="Amount Requested">
                        <label for="amount">Amount Requested</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="purpose" name="purpose" value="{{ old('purpose') }}"
                            placeholder="Purpose of Request">
                        <label for="purpose">Purpose of Request</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <h6>Breakdown</h6>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-bordered" id="breakdown-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Unit Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="items[0][item]" class="form-control" required></td>
                                    <td><input type="number" name="items[0][unit_price]"
                                            class="form-control breakdown-unit-price" required></td>
                                    <td><input type="number" name="items[0][qty]" class="form-control breakdown-qty"
                                            required></td>
                                    <td><input type="number" name="items[0][total]" class="form-control breakdown-total"
                                            readonly></td>
                                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" id="add-row" class="btn btn-success btn-sm mt-2">Add Row</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <textarea class="form-control" id="details" name="details" placeholder="Additional Details"
                            rows="3">{{ old('details') }}</textarea>
                        <label for="details">Additional Details</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" id="receipt" name="receipt">
                        <label for="receipt">Upload Receipt (if any)</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block">Submit Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    #breakdown-table tbody {
        display: block;
        max-height: 300px;
        overflow-y: auto;
    }

    #breakdown-table thead,
    #breakdown-table tbody tr {
        display: table;
        width: 100%;
        table-layout: fixed;
    }
</style>

<script>
    function togglePayeeInput() {
        const payeeSelect = document.getElementById('payee');
        const newPayeeInput = document.getElementById('new-payee');
        
        // Toggle visibility between select and input
        if (payeeSelect.style.display !== 'none') {
            // Hide the select dropdown and show the input field in the same position
            payeeSelect.style.display = 'none';
            newPayeeInput.style.display = 'block';
            newPayeeInput.focus();  // Focus on the new payee input
        } else {
            // Show the select dropdown and hide the input field
            payeeSelect.style.display = 'block';
            newPayeeInput.style.display = 'none';
            payeeSelect.focus();  // Focus on the select dropdown
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Autofill payment_medium and store_account when payee is selected
        document.getElementById('payee').addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const phone = selectedOption.getAttribute('data-phone') || '';
            const store = selectedOption.getAttribute('data-store') || '';
            document.getElementById('payment_medium').value = phone;
            document.getElementById('store_account').value = store;
        });

        // Initialize breakdown row logic
        let rowIndex = 1;

        document.getElementById('add-row').addEventListener('click', function () {
            const tableBody = document.querySelector('#breakdown-table tbody');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="items[${rowIndex}][item]" class="form-control" required></td>
                <td><input type="number" name="items[${rowIndex}][unit_price]" class="form-control breakdown-unit-price" required></td>
                <td><input type="number" name="items[${rowIndex}][qty]" class="form-control breakdown-qty" required></td>
                <td><input type="number" name="items[${rowIndex}][total]" class="form-control breakdown-total" disabled readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
            `;
            tableBody.appendChild(newRow);
            rowIndex++;
        });

        document.querySelector('#breakdown-table').addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
            }
        });

        document.querySelector('#breakdown-table').addEventListener('input', function (e) {
            const row = e.target.closest('tr');
            const unitPrice = row.querySelector('.breakdown-unit-price').value || 0;
            const qty = row.querySelector('.breakdown-qty').value || 0;
            row.querySelector('.breakdown-total').value = unitPrice * qty;
        });

        document.getElementById('petty-cash-form').addEventListener('submit', function (e) {
            const requestedAmount = parseFloat(document.getElementById('amount').value) || 0;
            const breakdownTotals = Array.from(document.querySelectorAll('.breakdown-total')).reduce((sum, input) => sum + parseFloat(input.value || 0), 0);

            if (requestedAmount !== breakdownTotals) {
                e.preventDefault();
                alert('The total breakdown amount must match the requested amount.');
            }
        });
    });
</script>
@endsection
