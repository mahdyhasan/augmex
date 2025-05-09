@extends('layouts.app')

@section('title', 'Add New Lead')

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="text-primary">Add New Lead</h3>
            <button class="btn btn-outline-primary" data-bs-toggle="offcanvas" data-bs-target="#importLeadModal">
                <i class="fas fa-file-import me-2"></i>Import Leads (Excel)
            </button>
        </div>

        <div class="d-flex justify-content-center">
            <div class="card shadow-sm" style="width: 60%;">
                <div class="card-body">
                <form id="addLeadForm" action="{{ route('divanj.crm.leads.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="check_phone" class="form-label">Enter Mobile or Landline *</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="check_phone" placeholder="Enter 9-11 digit number (no country code, do not start with 0)">
                            <select class="form-select" id="phone_type">
                                <option value="mobile">Mobile</option>
                                <option value="landline">Landline</option>
                            </select>
                            <button type="button" class="btn btn-outline-primary" id="checkPhoneBtn">Check Number</button>
                        </div>
                        <small id="phoneCheckResult" class="form-text"></small>
                    </div>

                    <div id="leadFields" class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="name" class="form-label">Name *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>

                            <div class="form-group mb-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="tel" class="form-control phone-input" id="mobile" name="mobile" maxlength="20">
                            </div>

                            <div class="form-group mb-3">
                                <label for="landline" class="form-label">Landline</label>
                                <input type="tel" class="form-control phone-input" id="landline" name="landline" maxlength="20">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="source" class="form-label">Source *</label>
                                <select class="form-control" id="source" name="source" required>
                                    <option value="">Select Source</option>
                                    <option value="sam">SAM</option>
                                    <option value="pm">PM</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            @if (Auth::user()->isSuperAdmin())
                            <div class="form-group mb-3">
                                <label for="agent_id" class="form-label">Assign To Agent</label>
                                <select class="form-control" id="agent_id" name="agent_id">
                                    <option value="">Select Agent</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}">{{ $agent->employee->stage_name ?? $agent->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <div class="form-group mb-4">
                                <label for="note" class="form-label">Notes</label>
                                <textarea class="form-control" id="note" name="note" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('divanj.crm.leads.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Leads
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Lead
                            </button>
                        </div>   
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Offcanvas Modal for Bulk Import --}}
<div class="offcanvas offcanvas-end" tabindex="-1" id="importLeadModal" aria-labelledby="importLeadModalLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="importLeadModalLabel">Import Leads via Excel</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="importForm" action="{{ route('divanj.crm.leads.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
                <label for="excel_file" class="form-label">Excel File (.xlsx, .xls, .csv)</label>
                <input type="file" class="form-control" name="excel_file" accept=".xlsx,.xls,.csv" required>
            </div>

            @if (Auth::user()->isSuperAdmin())
            <div class="form-group mb-3">
                <label for="agent_id_import" class="form-label">Assign To Agent</label>
                <select class="form-control" name="agent_id">
                    <option value="">Select Agent</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}">{{ $agent->employee->stage_name ?? $agent->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-upload me-2"></i>Upload & Import
            </button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const phoneInput = document.getElementById('check_phone');
    const phoneType = document.getElementById('phone_type');
    const checkBtn = document.getElementById('checkPhoneBtn');
    const resultBox = document.getElementById('phoneCheckResult');
    const leadFields = document.getElementById('leadFields');
    const mobileField = document.getElementById('mobile');
    const landlineField = document.getElementById('landline');

    function toggleFormFields(enable) {
        [...leadFields.querySelectorAll('input, select, textarea')].forEach(el => el.disabled = !enable);
    }

    toggleFormFields(false);

    checkBtn.addEventListener('click', function () {
        const rawValue = phoneInput.value.replace(/\D/g, '');
        const type = phoneType.value;

        if (rawValue.length < 9 || rawValue.length > 11) {
            resultBox.textContent = 'Enter a valid number (9–11 digits)';
            resultBox.className = 'text-danger';
            toggleFormFields(false);
            return;
        }

        fetch('{{ route('divanj.crm.leads.checkPhone') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ phone: rawValue })
        })
        .then(res => res.json())
        .then(data => {
            if (data.exists) {
                resultBox.textContent = `Already in system. Agent ${data.name} is working on it.`;
                resultBox.className = 'text-danger';
                toggleFormFields(false);
            } else {
                resultBox.textContent = 'Number is available. You can proceed.';
                resultBox.className = 'text-success';
                toggleFormFields(true);

                if (type === 'mobile') {
                    mobileField.value = rawValue;
                } else {
                    landlineField.value = rawValue;
                }
            }
        })
        .catch(err => {
            console.error(err);
            resultBox.textContent = 'Server error while checking number.';
            resultBox.className = 'text-danger';
            toggleFormFields(false);
        });
    });
});



$('#addLeadForm').submit(function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (response) {
            // 👇 Redirect after successful lead creation
            if (response.success) {
                window.location.href = "{{ route('divanj.crm.leads.index') }}";
            }
        },
        error: function (xhr) {
            const errors = xhr.responseJSON.errors || {};
            for (const field in errors) {
                $(`#${field}`).addClass('is-invalid');
                $(`#${field}`).next('.invalid-feedback').text(errors[field][0]);
            }
        }
    });
});

</script>
@endsection


