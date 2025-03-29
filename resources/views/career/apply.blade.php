@extends('layouts.invoice')

@section('title', 'Apply for Position - Lemon Infosys')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-2">
                    <div class="card-header ">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="fw-bold mb-0">
                                <i class="fas fa-briefcase me-2"></i> Apply for the Position
                            </h3>
                        <div>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-sm btn-info view-circular-btn">
                                <i class="fas fa-eye me-1"></i> View Circular
                            </button>
                        </div>
                    </div>
                </div>
            </div>

                <div class="card-body p-4">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <form action="{{ route('career.store') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                        @csrf

                        <!-- Basic Information -->
                        <div class="form-section mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-user-tie mr-2"></i> Basic Information
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">Position</label>
                                    <input type="text" class="form-control bg-light" value="International Sales Executive" readonly>
                                    <input type="hidden" name="position" value="1">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">Full Name</label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">Email Address</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Personal Details -->
                        <div class="form-section mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-info-circle mr-2"></i> Personal Details
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">Phone Number</label>
                                    <input type="tel" 
                                        name="phone" 
                                        class="form-control @error('phone') is-invalid @enderror" 
                                        value="{{ old('phone') }}" 
                                        pattern="0\d{10}"
                                        required>
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    <small class="text-muted">Must be 11 digits starting with 0</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">Age</label>
                                    <input type="number" name="age" class="form-control @error('age') is-invalid @enderror" value="{{ old('age') }}" required>
                                    @error('age')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">Current Location</label>
                                    <select name="area" class="form-control select2 @error('area') is-invalid @enderror" required>
                                        <option value="">Select Area</option>
                                        @foreach(['Badda', 'Banani', 'Baridhara DOHS', 'Baridhara J Block', 'Bashundhara R/A', 
                                                'Dhanmondi', 'Farmgate', 'Gulshan', 'Khilgaon', 'Lalmatia', 'Malibagh', 
                                                'Mirpur', 'Mohakhali', 'Mohammadpur', 'Moghbazar', 'Motijheel', 'Niketan',
                                                'Paltan', 'Rampura', 'Shantinagar', 'Shyamoli', 'Tejgaon', 'Uttara', 
                                                'Old Town', 'Others'] as $areaOption)
                                            <option value="{{ $areaOption }}" {{ old('area') == $areaOption ? 'selected' : '' }}>
                                                {{ $areaOption }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Education Background -->
                        <div class="form-section mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-graduation-cap mr-2"></i> Education Background
                            </h5>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">Highest Education Level</label>
                                    <select name="last_education" class="form-control @error('last_education') is-invalid @enderror" required>
                                        <option value="">Select Education Level</option>
                                        @foreach(['High School', 'Diploma', 'Bachelor\'s Degree', 'Master\'s Degree', 'Others'] as $education)
                                            <option value="{{ $education }}" {{ old('last_education') == $education ? 'selected' : '' }}>
                                                {{ $education }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('last_education')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">University/College</label>
                                    <input type="text" name="last_education_institute" class="form-control @error('last_education_institute') is-invalid @enderror" value="{{ old('last_education_institute') }}" required>
                                    @error('last_education_institute')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="font-weight-bold">Graduation Year</label>
                                    <input type="number" name="last_education_year" class="form-control @error('last_education_year') is-invalid @enderror" value="{{ old('last_education_year') }}" min="1900" max="{{ date('Y') }}" required>
                                    @error('last_education_year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Work Experience -->
                        <div class="form-section mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-briefcase mr-2"></i> Work Experience
                            </h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Most Recent Position</label>
                                    <input type="text" name="last_experience" class="form-control" value="{{ old('last_experience') }}" placeholder="E.g., Telemarketer at ABC Ltd.">
                                    <small class="text-muted">Optional</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="font-weight-bold">Total Experience</label>
                                    <select name="total_experience" class="form-control @error('total_experience') is-invalid @enderror" required>
                                        <option value="">Select Experience Level</option>
                                        @foreach(['Fresher', '1-3 years', '3-5 years', '5+ years'] as $experience)
                                            <option value="{{ $experience }}" {{ old('total_experience') == $experience ? 'selected' : '' }}>
                                                {{ $experience }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('total_experience')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div class="form-section mb-5">
                            <h5 class="text-primary mb-4">
                                <i class="fas fa-file-upload mr-2"></i> Documents
                            </h5>
                            <div class="custom-file">
                                <input type="file" name="resume_upload" class="custom-file-input @error('resume_upload') is-invalid @enderror" id="resume_upload" accept=".pdf,.doc,.docx" required>
                                <label class="custom-file-label" for="resume_upload">Choose file</label>
                                @error('resume_upload')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <small class="text-muted d-block mt-2">Accepted formats: PDF, DOC, DOCX (Max 3MB)</small>
                        </div>

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-5">
                            <a href="{{ url('/') }}" class="btn btn-outline-secondary px-4">
                                <i class="fas fa-arrow-left mr-2"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Application
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Add this at the end of your blade file, before the closing </div> of your content wrapper -->
        <div id="circularDetails" class="card shadow-lg mt-4">
        <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0">Job Circular Details</h4>
                        <p class="mb-0 small">Tech Cloud Ltd. - Global Marketing Executive</p>
                    </div>
                    <button id="closeCircular" class="btn btn-sm btn-light">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            <div class="card-body">

            
                <!-- Basic Information -->
                <div class="mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Job Summary</h6>
                    <p>Tech Cloud Ltd. is seeking enthusiastic and proactive Global Marketing Executives to join our team. We're looking for 5 to 6 individuals with strong spoken English skills to sell to Australian customers.</p>
                </div>

                <!-- Employment Details -->
                <div class="mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Employment Details</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Employment Status:</strong>
                            <p class="mb-0">Contractual</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Location:</strong>
                            <p class="mb-0">Baridhara DOHS</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Working Hours (Male):</strong>
                            <p class="mb-0">6 AM to 2 PM (Bangladesh Time)</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Working Hours (Female):</strong>
                            <p class="mb-0">7 AM to 3 PM (Bangladesh Time)</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <strong>Working Days:</strong>
                            <p class="mb-0">Monday to Friday</p>
                        </div>
                    </div>
                </div>

                <!-- Responsibilities -->
                <div class="mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Job Responsibilities</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Make outbound calls, texts and emails to clients in Australia</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Meet daily calls and sales target</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Address and resolve customer inquiries, complaints, and requests</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Follow up on online sales and track orders until delivery completion</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Maintain records of customer interactions</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-primary me-2"></i> Escalate unresolved issues to relevant team members</li>
                    </ul>
                </div>

                <!-- Qualifications -->
                <div class="mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Qualifications</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 1-5 years experience in call center or customer service roles</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Super fluency in Spoken English</li>
                    </ul>
                </div>

                <!-- Additional Requirements -->
                <div class="mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Additional Requirements</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-circle-notch text-info me-2 small"></i> Proficient in spoken and written English</li>
                        <li class="mb-2"><i class="fas fa-circle-notch text-info me-2 small"></i> Computer literacy and CRM tools familiarity</li>
                        <li class="mb-2"><i class="fas fa-circle-notch text-info me-2 small"></i> Ability to work under pressure</li>
                        <li class="mb-2"><i class="fas fa-circle-notch text-info me-2 small"></i> Willingness to work on Fridays/public holidays</li>
                    </ul>
                </div>

                <!-- Salary & Benefits -->
                <div class="mb-4">
                    <h6 class="fw-bold border-bottom pb-2 mb-3">Salary & Benefits</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Salary Range:</strong>
                            <p class="mb-0">22,000 - 30,000 BDT</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Sales Commission:</strong>
                            <p class="mb-0">16K to 80K BDT</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Weekends:</strong>
                            <p class="mb-0">Saturday and Sunday</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Meals:</strong>
                            <p class="mb-0">Fully subsidized lunch and breakfast</p>
                        </div>
                        <div class="col-12 mb-3">
                            <strong>Other Benefits:</strong>
                            <p class="mb-0">Unlimited tea and coffee</p>
                        </div>
                    </div>
                </div>

                <!-- Apply Button at Bottom -->
                <!-- <div class="text-center mt-4">
                    <button id="applyNowBtn" class="btn btn-primary btn-lg px-4">
                        <i class="fas fa-paper-plane me-2"></i> Apply Now
                    </button>
                </div> -->
            </div>
        </div>








@endsection

@section('css')
<style>
    /* Custom styling */
    body {
        background-color: #f8f9fa;
    }
    
    .card {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }
    
    .form-control {
        border-radius: 0.25rem;
        border: 1px solid #ced4da;
        padding: 0.5rem 0.75rem;
    }
    
    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    .select2-container .select2-selection--single {
        height: calc(1.5em + 1rem + 2px);
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-weight: 500;
    }
    
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    
    .btn-primary:hover {
        background-color: #0069d9;
        border-color: #0062cc;
    }
    
    .form-section {
        position: relative;
        padding-bottom: 2rem;
    }
    
    .form-section:not(:last-child)::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        border-bottom: 1px solid #dee2e6;
    }
    
    .custom-file-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Alert customization */
    .alert {
        border-radius: 0.25rem;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
    }

    /* Offcanvas Custom Styles */
#viewCircular {
    max-width: 600px;
}

.offcanvas-header.bg-primary {
    background: linear-gradient(135deg, #4e73df, #224abe);
}

.list-unstyled li {
    padding-left: 1.5rem;
    text-indent: -1.5rem;
    margin-bottom: 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #viewCircular {
        max-width: 100%;
    }
}

#circularDetails {
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}

#circularDetails .card-header {
    background: linear-gradient(135deg, #4e73df, #224abe);
}


</style>
@endsection

@section('js')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "Search or select area",
            allowClear: true
        });

        // Custom file input
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // Phone number validation
        $('input[name="phone"]').on('input', function() {
            let phone = $(this).val().replace(/\D/g, '');
            $(this).val(phone);
            
            if (phone && (!phone.startsWith('0') || phone.length !== 11)) {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Phone must be 11 digits starting with 0</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Email duplicate check
        $('input[name="email"]').on('blur', function() {
            let email = $(this).val();
            let position = $('input[name="position"]').val();
            
            if (email && position) {
                $.ajax({
                    url: '/check-application',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        email: email,
                        position: position
                    },
                    success: function(response) {
                        if (response.exists) {
                            $('input[name="email"]')
                                .addClass('is-invalid')
                                .after('<div class="invalid-feedback">You have already applied for this position</div>');
                        }
                    }
                });
            }
        });

        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    });


</script>



<script>
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Script Loaded'); // Check if script loads

            // View Circular Button
            document.querySelectorAll('.view-circular-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    console.log('👁️ View button clicked'); // Check if button is clicked
                    
                    const details = document.getElementById('circularDetails');
                    if (details.classList.contains('d-none')) {
                        details.classList.remove('d-none');
                    }

                    details.scrollIntoView({ behavior: 'smooth', block: 'start' });
                });
            });

            // Close Button
            document.getElementById('closeCircular')?.addEventListener('click', function () {
                console.log('❌ Close button clicked'); // Check if close button works
                const details = document.getElementById('circularDetails');
                details.classList.add('d-none');
                window.scrollBy({ top: -200, behavior: 'smooth' });
            });

            // Apply Button
            document.getElementById('applyNowBtn')?.addEventListener('click', function () {
                console.log('📨 Apply button clicked'); // Check if apply button works
                const details = document.getElementById('circularDetails');
                details.classList.add('d-none');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    </script>
@endsection