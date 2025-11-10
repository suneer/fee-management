@extends('layouts.admin')

@section('title', 'API Demo - RESTful Operations')
@section('page-title', 'RESTful API Demonstration')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
    [v-cloak] {
        display: none;
    }
    .api-card {
        border-left: 4px solid #007bff;
        transition: all 0.3s;
    }
    .api-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    .method-badge {
        font-weight: bold;
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }
    .json-output {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 5px;
        padding: 15px;
        max-height: 300px;
        overflow-y: auto;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
    }
    .loading-spinner {
        width: 20px;
        height: 20px;
        border: 3px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
        display: inline-block;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div id="apiDemo" v-cloak>
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h4><i class="fas fa-info-circle"></i> RESTful API Operations Demo</h4>
                <p class="mb-0">This page demonstrates all RESTful API endpoints for Student Management and Payment Recording using Vue.js.</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Alerts -->
    <transition name="fade">
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <strong>Success:</strong> @{{ successMessage }}
            <button type="button" class="btn-close" @click="successMessage = null"></button>
        </div>
    </transition>

    <transition name="fade">
        <div v-if="errorMessage" class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <strong>Error:</strong> @{{ errorMessage }}
            <button type="button" class="btn-close" @click="errorMessage = null"></button>
        </div>
    </transition>

    <!-- API Operations Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#students-tab">
                <i class="fas fa-users"></i> Student Operations
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#fees-tab">
                <i class="fas fa-dollar-sign"></i> Fee Details
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#payments-tab">
                <i class="fas fa-credit-card"></i> Payment Recording
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Students Tab -->
        <div id="students-tab" class="tab-pane fade show active">
            <div class="row">
                <!-- Get All Students -->
                <div class="col-md-6 mb-4">
                    <div class="card api-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <span class="method-badge badge bg-success">GET</span>
                                Retrieve All Students
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Endpoint:</strong> <code>/api/students</code></p>
                            <p><strong>Description:</strong> Fetches all students with their enrolled courses</p>
                            
                            <button class="btn btn-primary" @click="getAllStudents" :disabled="loading.students">
                                <span v-if="loading.students" class="loading-spinner"></span>
                                <i v-else class="fas fa-download"></i>
                                Fetch All Students
                            </button>

                            <div v-if="apiResponses.allStudents" class="mt-3">
                                <h6>Response:</h6>
                                <div class="json-output">
                                    <pre>@{{ JSON.stringify(apiResponses.allStudents, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get Single Student -->
                <div class="col-md-6 mb-4">
                    <div class="card api-card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <span class="method-badge badge bg-success">GET</span>
                                Retrieve Single Student
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Endpoint:</strong> <code>/api/students/{id}</code></p>
                            <p><strong>Description:</strong> Fetches specific student by ID</p>
                            
                            <div class="input-group mb-3">
                                <span class="input-group-text">Student ID</span>
                                <input type="number" class="form-control" v-model.number="studentId" placeholder="Enter student ID">
                            </div>

                            <button class="btn btn-info text-white" @click="getStudentById" :disabled="loading.student">
                                <span v-if="loading.student" class="loading-spinner"></span>
                                <i v-else class="fas fa-user"></i>
                                Fetch Student
                            </button>

                            <div v-if="apiResponses.singleStudent" class="mt-3">
                                <h6>Response:</h6>
                                <div class="json-output">
                                    <pre>@{{ JSON.stringify(apiResponses.singleStudent, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add New Student -->
                <div class="col-md-12 mb-4">
                    <div class="card api-card border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <span class="method-badge badge bg-warning text-dark">POST</span>
                                Add New Student
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Endpoint:</strong> <code>/api/students</code></p>
                            <p><strong>Description:</strong> Creates a new student record</p>
                            
                            <form @submit.prevent="addNewStudent">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Name *</label>
                                        <input type="text" class="form-control" v-model="newStudent.name" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Email *</label>
                                        <input type="email" class="form-control" v-model="newStudent.email" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Phone *</label>
                                        <input type="text" class="form-control" v-model="newStudent.phone" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Date of Birth *</label>
                                        <input type="date" class="form-control" v-model="newStudent.dob" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Address *</label>
                                        <input type="text" class="form-control" v-model="newStudent.address" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Status</label>
                                        <select class="form-select" v-model="newStudent.status">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="suspended">Suspended</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success" :disabled="loading.addStudent">
                                    <span v-if="loading.addStudent" class="loading-spinner"></span>
                                    <i v-else class="fas fa-plus"></i>
                                    Add Student
                                </button>
                            </form>

                            <div v-if="apiResponses.addedStudent" class="mt-3">
                                <h6>Response:</h6>
                                <div class="json-output">
                                    <pre>@{{ JSON.stringify(apiResponses.addedStudent, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fee Details Tab -->
        <div id="fees-tab" class="tab-pane fade">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <div class="card api-card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0">
                                <span class="method-badge badge bg-success">GET</span>
                                Fetch Fee Details for Student
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Endpoint:</strong> <code>/api/students/{id}/fee-details</code></p>
                            <p><strong>Description:</strong> Fetches comprehensive fee breakdown including courses, payments, and balances</p>
                            
                            <div class="input-group mb-3">
                                <span class="input-group-text">Student ID</span>
                                <input type="number" class="form-control" v-model.number="feeStudentId" placeholder="Enter student ID">
                                <button class="btn btn-warning" @click="getFeeDetails" :disabled="loading.fees">
                                    <span v-if="loading.fees" class="loading-spinner"></span>
                                    <i v-else class="fas fa-dollar-sign"></i>
                                    Fetch Fee Details
                                </button>
                            </div>

                            <div v-if="apiResponses.feeDetails" class="mt-3">
                                <h6>Response:</h6>
                                
                                <!-- Summary Cards -->
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="card bg-primary text-white">
                                            <div class="card-body text-center">
                                                <h6>Total Fees</h6>
                                                <h3>₹@{{ apiResponses.feeDetails.data?.total_fee || 0 }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-success text-white">
                                            <div class="card-body text-center">
                                                <h6>Total Paid</h6>
                                                <h3>₹@{{ apiResponses.feeDetails.data?.total_paid || 0 }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card bg-danger text-white">
                                            <div class="card-body text-center">
                                                <h6>Balance</h6>
                                                <h3>₹@{{ apiResponses.feeDetails.data?.balance || 0 }}</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full JSON Response -->
                                <div class="json-output">
                                    <pre>@{{ JSON.stringify(apiResponses.feeDetails, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payments Tab -->
        <div id="payments-tab" class="tab-pane fade">
            <div class="row">
                <!-- Record Payment -->
                <div class="col-md-12 mb-4">
                    <div class="card api-card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0">
                                <span class="method-badge badge bg-warning text-dark">POST</span>
                                Record New Payment
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Endpoint:</strong> <code>/api/payments</code></p>
                            <p><strong>Description:</strong> Records a new payment for a student's course</p>
                            
                            <form @submit.prevent="recordPayment">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Student ID *</label>
                                        <input type="number" class="form-control" v-model.number="newPayment.student_id" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Course ID *</label>
                                        <input type="number" class="form-control" v-model.number="newPayment.course_id" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Amount (₹) *</label>
                                        <input type="number" step="0.01" min="0.01" class="form-control" v-model.number="newPayment.amount_paid" required>
                                        <small class="text-muted">Must be positive</small>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Payment Date *</label>
                                        <input type="date" class="form-control" v-model="newPayment.date_of_payment" required>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-danger" :disabled="loading.payment">
                                    <span v-if="loading.payment" class="loading-spinner"></span>
                                    <i v-else class="fas fa-credit-card"></i>
                                    Record Payment
                                </button>
                            </form>

                            <div v-if="apiResponses.recordedPayment" class="mt-3">
                                <h6>Response:</h6>
                                <div class="json-output">
                                    <pre>@{{ JSON.stringify(apiResponses.recordedPayment, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get All Payments -->
                <div class="col-md-6 mb-4">
                    <div class="card api-card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">
                                <span class="method-badge badge bg-success">GET</span>
                                Get All Payments
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Endpoint:</strong> <code>/api/payments</code></p>
                            <p><strong>Description:</strong> Retrieves all payment records</p>
                            
                            <button class="btn btn-secondary" @click="getAllPayments" :disabled="loading.allPayments">
                                <span v-if="loading.allPayments" class="loading-spinner"></span>
                                <i v-else class="fas fa-list"></i>
                                Fetch All Payments
                            </button>

                            <div v-if="apiResponses.allPayments" class="mt-3">
                                <h6>Response (@{{ apiResponses.allPayments.data?.length || 0 }} payments):</h6>
                                <div class="json-output">
                                    <pre>@{{ JSON.stringify(apiResponses.allPayments, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Get Student Payments -->
                <div class="col-md-6 mb-4">
                    <div class="card api-card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0">
                                <span class="method-badge badge bg-success">GET</span>
                                Get Student Payments
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Endpoint:</strong> <code>/api/students/{id}/payments</code></p>
                            <p><strong>Description:</strong> Retrieves all payments for a specific student</p>
                            
                            <div class="input-group mb-3">
                                <span class="input-group-text">Student ID</span>
                                <input type="number" class="form-control" v-model.number="paymentStudentId" placeholder="Enter student ID">
                                <button class="btn btn-dark" @click="getStudentPayments" :disabled="loading.studentPayments">
                                    <span v-if="loading.studentPayments" class="loading-spinner"></span>
                                    <i v-else class="fas fa-receipt"></i>
                                    Fetch Payments
                                </button>
                            </div>

                            <div v-if="apiResponses.studentPayments" class="mt-3">
                                <h6>Response:</h6>
                                <div class="json-output">
                                    <pre>@{{ JSON.stringify(apiResponses.studentPayments, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- API Summary -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> API Endpoints Summary</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Method</th>
                                    <th>Endpoint</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>/api/students</code></td>
                                    <td>Retrieve all students</td>
                                    <td><span class="badge bg-success">✓ Working</span></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>/api/students/{id}</code></td>
                                    <td>Retrieve single student</td>
                                    <td><span class="badge bg-success">✓ Working</span></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning text-dark">POST</span></td>
                                    <td><code>/api/students</code></td>
                                    <td>Add new student</td>
                                    <td><span class="badge bg-success">✓ Working</span></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>/api/students/{id}/fee-details</code></td>
                                    <td>Fetch fee details for student</td>
                                    <td><span class="badge bg-success">✓ Working</span></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-warning text-dark">POST</span></td>
                                    <td><code>/api/payments</code></td>
                                    <td>Record new payment</td>
                                    <td><span class="badge bg-success">✓ Working</span></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>/api/payments</code></td>
                                    <td>Get all payments</td>
                                    <td><span class="badge bg-success">✓ Working</span></td>
                                </tr>
                                <tr>
                                    <td><span class="badge bg-success">GET</span></td>
                                    <td><code>/api/students/{id}/payments</code></td>
                                    <td>Get student payments</td>
                                    <td><span class="badge bg-success">✓ Working</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const { createApp } = Vue;

createApp({
    data() {
        return {
            loading: {
                students: false,
                student: false,
                addStudent: false,
                fees: false,
                payment: false,
                allPayments: false,
                studentPayments: false
            },
            successMessage: null,
            errorMessage: null,
            studentId: '',
            feeStudentId: '',
            paymentStudentId: '',
            apiResponses: {
                allStudents: null,
                singleStudent: null,
                addedStudent: null,
                feeDetails: null,
                recordedPayment: null,
                allPayments: null,
                studentPayments: null
            },
            newStudent: {
                name: '',
                email: '',
                phone: '',
                dob: '',
                address: '',
                status: 'active'
            },
            newPayment: {
                student_id: '',
                course_id: '',
                amount_paid: '',
                date_of_payment: new Date().toISOString().split('T')[0]
            }
        };
    },
    methods: {
        async getAllStudents() {
            try {
                this.loading.students = true;
                this.errorMessage = null;
                
                const response = await axios.get('/api/students');
                this.apiResponses.allStudents = response.data;
                this.showSuccess('Successfully retrieved ' + (response.data.data?.length || 0) + ' students');
            } catch (error) {
                this.showError('Failed to fetch students: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loading.students = false;
            }
        },

        async getStudentById() {
            try {
                this.loading.student = true;
                this.errorMessage = null;
                
                const response = await axios.get(`/api/students/${this.studentId}`);
                this.apiResponses.singleStudent = response.data;
                this.showSuccess('Successfully retrieved student data');
            } catch (error) {
                this.showError('Failed to fetch student: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loading.student = false;
            }
        },

        async addNewStudent() {
            try {
                this.loading.addStudent = true;
                this.errorMessage = null;
                
                const response = await axios.post('/api/students', this.newStudent);
                this.apiResponses.addedStudent = response.data;
                this.showSuccess('Student added successfully! ID: ' + response.data.data?.id);
                
                // Reset form
                this.newStudent = {
                    name: '',
                    email: '',
                    phone: '',
                    dob: '',
                    address: '',
                    status: 'active'
                };
            } catch (error) {
                this.showError('Failed to add student: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loading.addStudent = false;
            }
        },

        async getFeeDetails() {
            try {
                this.loading.fees = true;
                this.errorMessage = null;
                
                const response = await axios.get(`/api/students/${this.feeStudentId}/fee-details`);
                this.apiResponses.feeDetails = response.data;
                this.showSuccess('Successfully retrieved fee details');
            } catch (error) {
                this.showError('Failed to fetch fee details: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loading.fees = false;
            }
        },

        async recordPayment() {
            try {
                this.loading.payment = true;
                this.errorMessage = null;
                
                // Client-side validation: Check if amount is positive
                if (!this.newPayment.amount_paid || this.newPayment.amount_paid <= 0) {
                    this.showError('Payment amount must be a positive number greater than zero.');
                    this.loading.payment = false;
                    return;
                }
                
                const response = await axios.post('/api/payments', this.newPayment);
                this.apiResponses.recordedPayment = response.data;
                this.showSuccess('Payment recorded successfully!');
                
                // Reset form after successful payment
                this.newPayment.amount_paid = 0;
            } catch (error) {
                this.showError('Failed to record payment: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loading.payment = false;
            }
        },

        async getAllPayments() {
            try {
                this.loading.allPayments = true;
                this.errorMessage = null;
                
                const response = await axios.get('/api/payments');
                this.apiResponses.allPayments = response.data;
                this.showSuccess('Successfully retrieved ' + (response.data.data?.length || 0) + ' payments');
            } catch (error) {
                this.showError('Failed to fetch payments: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loading.allPayments = false;
            }
        },

        async getStudentPayments() {
            try {
                this.loading.studentPayments = true;
                this.errorMessage = null;
                
                const response = await axios.get(`/api/students/${this.paymentStudentId}/payments`);
                this.apiResponses.studentPayments = response.data;
                this.showSuccess('Successfully retrieved student payments');
            } catch (error) {
                this.showError('Failed to fetch student payments: ' + (error.response?.data?.message || error.message));
            } finally {
                this.loading.studentPayments = false;
            }
        },

        showSuccess(message) {
            this.successMessage = message;
            setTimeout(() => {
                this.successMessage = null;
            }, 5000);
        },

        showError(message) {
            this.errorMessage = message;
            setTimeout(() => {
                this.errorMessage = null;
            }, 7000);
        }
    },
    mounted() {
        console.log('API Demo loaded successfully');
        console.log('All RESTful API endpoints are ready to test');
    }
}).mount('#apiDemo');
</script>
@endsection
