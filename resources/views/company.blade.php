@extends('layouts.app')

@section('title', 'Company')

@section('content')
    <div class="row">
        <div class="col-md-12">

            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-8">
                        <h4 class="page-title">Companies<span class="count-title">123</span></h4>
                    </div>
                    <div class="col-4 text-end">
                        <div class="head-icons">
                            <a href="companies.html" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Refresh"><i class="ti ti-refresh-dot"></i></a>
                            <a href="javascript:void(0);" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Collapse" id="collapse-header"><i class="ti ti-chevrons-up"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Page Header -->

            <div class="card ">
                <div class="card-header">
                    <!-- Search -->
                    <div class="row align-items-center">
                        <div class="col-sm-4">
                            <div class="icon-form mb-3 mb-sm-0">
                                <span class="form-icon"><i class="ti ti-search"></i></span>
                                <input type="text" class="form-control" placeholder="Search Companies">
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div
                                    class="d-flex align-items-center flex-wrap row-gap-2 justify-content-sm-end">
                                <div class="dropdown me-2">
                                    <a href="javascript:void(0);" class="dropdown-toggle"
                                       data-bs-toggle="dropdown"><i
                                                class="ti ti-package-export me-2"></i>Export</a>
                                    <div class="dropdown-menu  dropdown-menu-end">
                                        <ul>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item"><i
                                                            class="ti ti-file-type-pdf text-danger me-1"></i>Export
                                                                                                             as PDF</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item"><i
                                                            class="ti ti-file-type-xls text-green me-1"></i>Export
                                                                                                            as Excel </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <a href="javascript:void(0);" class="btn btn-primary"
                                   data-bs-toggle="offcanvas" data-bs-target="#offcanvas_add"><i
                                            class="ti ti-square-rounded-plus me-2"></i>Add Company</a>
                            </div>
                        </div>
                    </div>
                    <!-- /Search -->
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2 mb-4">
                        <div class="d-flex align-items-center flex-wrap row-gap-2">
                            <div class="dropdown me-2">
                                <a href="javascript:void(0);" class="dropdown-toggle"
                                   data-bs-toggle="dropdown"><i
                                            class="ti ti-sort-ascending-2 me-2"></i>Sort </a>
                                <div class="dropdown-menu  dropdown-menu-start">
                                    <ul>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                <i class="ti ti-circle-chevron-right me-1"></i>Ascending
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                <i class="ti ti-circle-chevron-right me-1"></i>Descending
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                <i class="ti ti-circle-chevron-right me-1"></i>Recently
                                                                                               Viewed
                                            </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item">
                                                <i class="ti ti-circle-chevron-right me-1"></i>Recently
                                                                                               Added
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="icon-form">
                                <span class="form-icon"><i class="ti ti-calendar"></i></span>
                                <input type="text" class="form-control bookingrange" placeholder="">
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap row-gap-2">
                            <div class="dropdown me-2">
                                <a href="javascript:void(0);" class="btn bg-soft-purple text-purple"
                                   data-bs-toggle="dropdown" data-bs-auto-close="outside"><i
                                            class="ti ti-columns-3 me-2"></i>Manage Columns</a>
                                <div class="dropdown-menu  dropdown-menu-md-end dropdown-md p-3">
                                    <h4 class="mb-2 fw-semibold">Want to manage datatables?</h4>
                                    <p class="mb-3">Please drag and drop your column to reorder your table
                                                    and enable see option as you want.</p>
                                    <div class="border-top pt-3">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Name</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-name" class="check">
                                                <label for="col-name" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Phone</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-phone" class="check">
                                                <label for="col-phone" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Email</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-email" class="check">
                                                <label for="col-email" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Tags</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-tag" class="check">
                                                <label for="col-tag" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Location</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-loc" class="check">
                                                <label for="col-loc" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Rating</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-rate" class="check">
                                                <label for="col-rate" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Owner</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-owner" class="check">
                                                <label for="col-owner" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Contact</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-contact" class="check"
                                                       checked="">
                                                <label for="col-contact" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Status</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-status" class="check">
                                                <label for="col-status" class="checktoggle"></label>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <p class="mb-0 d-flex align-items-center"><i
                                                        class="ti ti-grip-vertical me-2"></i>Action</p>
                                            <div class="status-toggle">
                                                <input type="checkbox" id="col-action" class="check">
                                                <label for="col-action" class="checktoggle"></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-sorts dropdown">
                                <a href="javascript:void(0);" data-bs-toggle="dropdown"
                                   data-bs-auto-close="outside"><i
                                            class="ti ti-filter-share"></i>Filter</a>
                                <div class="filter-dropdown-menu dropdown-menu  dropdown-menu-md-end p-3">
                                    <div class="filter-set-view">
                                        <div class="filter-set-head">
                                            <h4><i class="ti ti-filter-share"></i>Filter</h4>
                                        </div>
                                        <div class="accordion" id="accordionExample">
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" data-bs-toggle="collapse"
                                                       data-bs-target="#collapseTwo" aria-expanded="true"
                                                       aria-controls="collapseTwo">Country</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse show"
                                                     id="collapseTwo" data-bs-parent="#accordionExample">
                                                    <div class="filter-content-list">
                                                        <div class="mb-2 icon-form">
																		<span class="form-icon"><i
                                                                                    class="ti ti-search"></i></span>
                                                            <input type="text" class="form-control"
                                                                   placeholder="Search Country">
                                                        </div>
                                                        <ul>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        India
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        USA
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        France
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        United Kingdom
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        UAE
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Italy
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Martin Lewis
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Germany
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                       data-bs-target="#owner" aria-expanded="false"
                                                       aria-controls="owner">Owner</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                     id="owner" data-bs-parent="#accordionExample">
                                                    <div class="filter-content-list">
                                                        <div class="mb-2 icon-form">
																		<span class="form-icon"><i
                                                                                    class="ti ti-search"></i></span>
                                                            <input type="text" class="form-control"
                                                                   placeholder="Search Owner">
                                                        </div>
                                                        <ul>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox" checked>
                                                                        <span class="checkmarks"></span>
                                                                        Hendry Milner
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Guilory Berggren
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Jami Carlile
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Theresa Nelson
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Smith Cooper
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                       data-bs-target="#Status" aria-expanded="false"
                                                       aria-controls="Status">Status</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                     id="Status" data-bs-parent="#accordionExample">
                                                    <div class="filter-content-list">
                                                        <ul>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox" checked>
                                                                        <span class="checkmarks"></span>
                                                                        Active
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Inactive
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                       data-bs-target="#collapseOne" aria-expanded="false"
                                                       aria-controls="collapseOne">Rating</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                     id="collapseOne" data-bs-parent="#accordionExample">
                                                    <div class="filter-content-list">
                                                        <ul>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox" checked>
                                                                        <span class="checkmarks"></span>
                                                                        <span class="rating">
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<span>5.0</span>
																					</span>
                                                                    </label>
                                                                </div>

                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        <span class="rating">
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<span>4.0</span>
																					</span>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        <span class="rating">
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>3.0</span>
																					</span>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        <span class="rating">
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>2.0</span>
																					</span>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        <span class="rating">
																						<i
                                                                                                class="fa fa-star filled"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<i class="fa fa-star"></i>
																						<span>1.0</span>
																					</span>
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="filter-set-content">
                                                <div class="filter-set-content-head">
                                                    <a href="#" class="collapsed" data-bs-toggle="collapse"
                                                       data-bs-target="#collapseThree"
                                                       aria-expanded="false"
                                                       aria-controls="collapseThree">Tags</a>
                                                </div>
                                                <div class="filter-set-contents accordion-collapse collapse"
                                                     id="collapseThree" data-bs-parent="#accordionExample">
                                                    <div class="filter-content-list">
                                                        <ul>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox" checked>
                                                                        <span class="checkmarks"></span>
                                                                        Promotion
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Rated
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Rejected
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Collab
                                                                    </label>
                                                                </div>
                                                            </li>
                                                            <li>
                                                                <div class="filter-checks">
                                                                    <label class="checkboxs">
                                                                        <input type="checkbox">
                                                                        <span class="checkmarks"></span>
                                                                        Calls
                                                                    </label>
                                                                </div>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter-reset-btns">
                                            <div class="row">
                                                <div class="col-6">
                                                    <a href="#" class="btn btn-light">Reset</a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('company') }}"
                                                       class="btn btn-primary">Filter</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /Filter -->

                    <!-- Contact List -->
                    <div class="table-responsive custom-table">
                        <table class="table datatable">
                            <thead class="thead-light">
                                <tr>
                                    <th class="no-sort">
                                        <label class="checkboxs"><input type="checkbox" id="select-all"><span class="checkmarks"></span></label>
                                    </th>
                                    <th class="no-sort"></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Account URL</th>
                                    <th>Plan</th>
                                    <th>Created Dated</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-01.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">NovaWave LLC</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f5879a97908781869a9bb59298949c99db969a98">[email&#160;protected]</a></td>
                                    <td>nw.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Advanced (Monthly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>25 Sep 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-02.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">BlueSky Industries</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="5e2d363f2c31301e3b263f332e323b703d3133">[email&#160;protected]</a></td>
                                    <td>bsi.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Basic (Yearly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>29 Sep 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-danger">Inctive</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-03.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">Silver Hawk</a>
                                        </div>
                                    </td>
                                    <td>Vaughan <a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="551930223c266467153238343c397b363a38">[email&#160;protected]</a></td>
                                    <td>sh.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Advanced (Monthly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>04 Oct 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-04.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">Summit  Peak</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="3c56594f4f555f5d0d0f7c5b515d5550125f5351">[email&#160;protected]</a></td>
                                    <td>sp.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Advanced (Monthly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>04 Oct 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-05.svg') }}a" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">RiverStone Ventur</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="3f5c5e4d50536b57500c7f58525e5653115c5052">[email&#160;protected]</a></td>
                                    <td>rsv.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Enterprise (Monthly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>27 Oct 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-06.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">Bright Bridge Grp</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="9afefbedf4f7ffe8f9f2fbdafdf7fbf3f6b4f9f5f7">[email&#160;protected]</a></td>
                                    <td>bbg.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Advanced (Monthly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>28 Oct 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-07.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">CoastalStar Co.</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6f1d0e0c070a032f08020e0603410c0002">[email&#160;protected]</a></td>
                                    <td>csc.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Enterprise (Yearly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>29 Oct 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-08.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">HarborView</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="3d575253585151587d5a505c5451135e5250">[email&#160;protected]</a></td>
                                    <td>hv.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Basic (Monthly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>30 Oct 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-09.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">Golden Gate Ltd</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="6a0005040b1e020b042a0d070b030644090507">[email&#160;protected]</a></td>
                                    <td>ggl.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Basic (Monthly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>11 Dec 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><label class="checkboxs"><input type="checkbox"><span class="checkmarks"></span></label></td>
                                    <td>
                                        <div class="set-star rating-select"><i class="fa fa-star"></i></div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <a href="javascript:void(0);" class="avatar avatar-sm border rounded p-1 me-2">
                                                <img class="w-auto h-auto" src="{{ asset('public/assets/img/icons/company-icon-10.svg') }}" alt="User Image">
                                            </a>
                                            <a href="javascript:void(0);" class="d-flex flex-column fw-medium">Redwood Inc</a>
                                        </div>
                                    </td>
                                    <td><a href="https://crms.dreamstechnologies.com/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="1371617c7c7853747e727a7f3d707c7e">[email&#160;protected]</a></td>
                                    <td>ri.example.com</td>
                                    <td>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span>Advanced (Monthly)</span>
                                            <a href="javascript:void(0);" class="badge bg-green badge-lg text-white ms-3" data-bs-toggle="offcanvas"data-bs-target="#upgrade_plan">Upgrade</a>
                                        </div>
                                    </td>
                                    <td>15 Dec 2023</td>
                                    <td><span class="badge badge-pill badge-status bg-success">Active</span></td>
                                    <td>
                                        <div class="dropdown table-action">
                                            <a href="#" class="action-icon " data-bs-toggle="dropdown"aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal"data-bs-target="#company_detail">
                                                    <i class="ti ti-eye text-info"></i>View
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="offcanvas" data-bs-target="#offcanvas_edit">
                                                    <i class="ti ti-edit text-blue"></i>Edit
                                                </a>
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#delete_company">
                                                    <i class="ti ti-trash text-danger"></i>Delete
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="datatable-length"></div>
                        </div>
                        <div class="col-md-6">
                            <div class="datatable-paginate"></div>
                        </div>
                    </div>
                    <!-- /Contact List -->

                </div>
            </div>

        </div>
    </div>

    <!-- Add Company -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_add">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Add New Company</h5>
            <button type="button"
                    class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                    data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('company') }}">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="profile-upload">
                                        <div class="profile-upload-img">
                                            <span><i class="ti ti-photo"></i></span>
                                            <img src="{{ asset('public/assets/img/profiles/avatar-20.jpg') }}" alt="img"
                                                 class="preview1">
                                            <button type="button" class="profile-remove">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                        <div class="profile-upload-content">
                                            <label class="profile-upload-btn">
                                                <i class="ti ti-file-broken"></i> Upload File
                                                <input type="file" class="input-img">
                                            </label>
                                            <p>JPG, GIF or PNG. Max size of 800K</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Company Name <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Email Address</label>
                                    <input type="email" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="col-form-label">Account URL</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Phone Number <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Website</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Password <span class="text-danger"> *</span></label>
                                    <div class="pass-group">
                                        <input type="password" class="pass-input form-control">
                                        <span class="ti toggle-password ti-eye-off"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Confirm Password <span class="text-danger"> *</span></label>
                                    <div class="pass-group">
                                        <input type="password" class="pass-inputs form-control">
                                        <span class="ti toggle-passwords ti-eye-off"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="col-form-label">Address</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Plan Name <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option>Advanced</option>
                                        <option>Basic</option>
                                        <option>Enterprise</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Plan Type <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option>Monthly</option>
                                        <option>Yearly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Currency <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Choose</option>
                                        <option>Dollar</option>
                                        <option>Euro</option>
                                        <option>Pound</option>
                                        <option>Rupee</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Language <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Choose</option>
                                        <option>English</option>
                                        <option>Arabic</option>
                                        <option>French</option>
                                        <option>German</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-0">
                                    <div class="radio-wrap">
                                        <label class="col-form-label">Status</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="me-2">
                                                <input type="radio" class="status-radio" id="person" name="leave" checked="">
                                                <label for="person">Active</label>
                                            </div>
                                            <div>
                                                <input type="radio" class="status-radio" id="Organization" name="leave">
                                                <label for="Organization">Inactive</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
    <!-- /Add Company -->

    <!-- Edit Company -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="offcanvas_edit">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Edit Company</h5>
            <button type="button"
                    class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                    data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('company') }}">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="profile-upload">
                                        <div class="profile-upload-img">
                                            <span><i class="ti ti-photo"></i></span>
                                            <img src="{{ asset('public/assets/img/profiles/avatar-20.jpg') }}" alt="img"
                                                 class="preview1">
                                            <button type="button" class="profile-remove">
                                                <i class="ti ti-x"></i>
                                            </button>
                                        </div>
                                        <div class="profile-upload-content">
                                            <label class="profile-upload-btn">
                                                <i class="ti ti-file-broken"></i> Upload File
                                                <input type="file" class="input-img">
                                            </label>
                                            <p>JPG, GIF or PNG. Max size of 800K</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Company Name <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" value="NovaWave LLC">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Email Address</label>
                                    <input type="email" class="form-control" value="robertson@gmail.com">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="col-form-label">Account URL</label>
                                    <input type="text" class="form-control" value="nw.example.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Phone Number <span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Website</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Password <span class="text-danger"> *</span></label>
                                    <div class="pass-group">
                                        <input type="password" class="pass-input form-control">
                                        <span class="ti toggle-password ti-eye-off"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Confirm Password <span class="text-danger"> *</span></label>
                                    <div class="pass-group">
                                        <input type="password" class="pass-inputs form-control">
                                        <span class="ti toggle-passwords ti-eye-off"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="col-form-label">Address</label>
                                    <input type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Plan Name <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option selected>Advanced</option>
                                        <option>Basic</option>
                                        <option>Enterprise</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3 ">
                                    <label class="col-form-label">Plan Type <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option>Monthly</option>
                                        <option>Yearly</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Currency <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Choose</option>
                                        <option selected>Dollar</option>
                                        <option>Euro</option>
                                        <option>Pound</option>
                                        <option>Rupee</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="col-form-label">Language <span class="text-danger"> *</span></label>
                                    <select class="select">
                                        <option>Choose</option>
                                        <option selected>English</option>
                                        <option>Arabic</option>
                                        <option>French</option>
                                        <option>German</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-0">
                                    <div class="radio-wrap">
                                        <label class="col-form-label">Status</label>
                                        <div class="d-flex flex-wrap">
                                            <div class="me-2">
                                                <input type="radio" class="status-radio" id="person2" name="leave" checked="">
                                                <label for="person2">Active</label>
                                            </div>
                                            <div>
                                                <input type="radio" class="status-radio" id="Organization2" name="leave">
                                                <label for="Organization2">Inactive</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
    <!-- /Edit Company -->

    <!-- Upgrade Plane -->
    <div class="offcanvas offcanvas-end offcanvas-large" tabindex="-1" id="upgrade_plan">
        <div class="offcanvas-header border-bottom">
            <h5 class="fw-semibold">Upgrade Package</h5>
            <button type="button"
                    class="btn-close custom-btn-close border p-1 me-0 d-flex align-items-center justify-content-center rounded-circle"
                    data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('company') }}">
                <div class="card">
                    <div class="card-body pb-0">
                        <div class="mb-1">
                            <div class="">
                                <h5 class="mb-3">Current Plan Details</h5>
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <p class="fs-12 mb-0">Company Name</p>
                                            <p class="text-gray-9">NovaWave LLC</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <p class="fs-12 mb-0">Plan Name</p>
                                            <p class="text-gray-9">Advanced</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <p class="fs-12 mb-0">Plan Type</p>
                                            <p class="text-gray-9">Monthly</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <p class="fs-12 mb-0">Price</p>
                                            <p class="text-gray-9">200</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <p class="fs-12 mb-0">Register Date</p>
                                            <p class="text-gray-9">12 Sep 2024</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <p class="fs-12 mb-0">Expiring On</p>
                                            <p class="text-gray-9">11 Oct 2024</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div>
                            <h5 class="mb-3">Change Plan</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Plan Name <span class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Advanced</option>
                                            <option>Basic</option>
                                            <option>Enterprise</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3 ">
                                        <label class="col-form-label">Plan Type <span class="text-danger">*</span></label>
                                        <select class="select">
                                            <option>Select</option>
                                            <option>Monthly</option>
                                            <option>Yearly</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Ammount<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Payment Date <span class="text-danger">*</span></label>
                                        <div class="input-icon-end position-relative">
                                            <input type="text" class="form-control datetimepicker" placeholder="dd/mm/yyyy">
                                            <span class="input-icon-addon">
													<i class="ti ti-calendar text-gray-7"></i>
												</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Next Payment Date <span class="text-danger">*</span></label>
                                        <div class="input-icon-end position-relative">
                                            <input type="text" class="form-control datetimepicker" placeholder="dd/mm/yyyy">
                                            <span class="input-icon-addon">
													<i class="ti ti-calendar text-gray-7"></i>
												</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="col-form-label">Expiring On <span class="text-danger">*</span></label>
                                        <div class="input-icon-end position-relative">
                                            <input type="text" class="form-control datetimepicker" placeholder="dd/mm/yyyy">
                                            <span class="input-icon-addon">
													<i class="ti ti-calendar text-gray-7"></i>
												</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-end">
                    <button type="button" data-bs-dismiss="offcanvas" class="btn btn-light me-2">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create</button>
                </div>
            </form>
        </div>
    </div>
    <!-- /Upgrade Plane -->

    <!-- Delete Company -->
    <div class="modal fade" id="delete_company" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <div class="avatar avatar-xl bg-danger-light rounded-circle mb-3">
                            <i class="ti ti-trash-x fs-36 text-danger"></i>
                        </div>
                        <h4 class="mb-2">Remove Company?</h4>
                        <p class="mb-0">Are you sure you want to remove <br> company you selected.</p>
                        <div class="d-flex align-items-center justify-content-center mt-4">
                            <a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
                            <a href="{{ route('company') }}" class="btn btn-danger">Yes, Delete it</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Company -->

    <!-- Company Detail -->
    <div class="modal fade custom-modal" id="company_detail">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Company Detail</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <div class="modal-body pb-0">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center rounded bg-light-400 p-3">
                            <div class="file-name-icon d-flex align-items-center">
                                <a href="#" class="avatar avatar-sm border rounded p-1 me-2 flex-shrink-0">
                                    <img src="{{ asset('public/assets/img/icons/company-icon-01.svg') }}" class="w-auto h-auto" alt="img">
                                </a>
                                <div>
                                    <p class="text-gray-9 fw-medium mb-0">NovaWave LLC</p>
                                </div>
                            </div>
                            <span class="badge badge-success">Active</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-gray-9 fw-medium">Basic Info</p>
                        <div class="border-bottom mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Account URL</p>
                                        <p class="text-gray-9">nw.example.com</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Phone Number</p>
                                        <p class="text-gray-9">+1 875455453</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Website</p>
                                        <p class="text-gray-9">www.exmple.com</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Currency</p>
                                        <p class="text-gray-9">United Stated Dollar (USD)</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Language</p>
                                        <p class="text-gray-9">English</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Addresss</p>
                                        <p class="text-gray-9">3705 Lynn Avenue, Phelps, WI 54554</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-9 fw-medium">Plan Details</p>
                        <div>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Plan Name</p>
                                        <p class="text-gray-9">Advanced</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Plan Type</p>
                                        <p class="text-gray-9">Monthly</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Price</p>
                                        <p class="text-gray-9">$200</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Register Date</p>
                                        <p class="text-gray-9">12 Sep 2024</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <p class="fs-12 mb-0">Expiring On</p>
                                        <p class="text-gray-9">11 Oct 2024</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>


            </div>
        </div>
    </div>
    <!-- /Company Detail -->
@endsection

@section('js')

@endsection