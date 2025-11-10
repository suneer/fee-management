@extends('layouts.admin')

@section('title', 'Student Dashboard')
@section('page-title', 'My Interactive Dashboard')

@section('styles')
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<style>
    [v-cloak] {
        display: none;
    }
    .loading-spinner {
        display: inline-block;
        width: 60px;
        height: 60px;
        border: 6px solid rgba(0, 0, 0, 0.1);
        border-radius: 50%;
        border-top-color: #007bff;
        animation: spin 1s ease-in-out infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    .stat-card {
        transition: all 0.3s;
        border: none;
        border-radius: 10px;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .course-card {
        transition: all 0.3s;
        border-left: 5px solid #007bff;
        margin-bottom: 15px;
    }
    .course-card:hover {
        background-color: #f8f9fa;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .payment-badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
    .icon-box {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 24px;
    }
    .fade-enter-active, .fade-leave-active {
        transition: opacity 0.5s;
    }
    .fade-enter-from, .fade-leave-to {
        opacity: 0;
    }
</style>
@endsection

@section('content')
<div id="studentDashboard" v-cloak>
    <!-- Loading State -->
    <div v-if="loading" class="text-center py-5">
        <div class="loading-spinner"></div>
        <p class="mt-3 text-muted fs-5">Loading dashboard data...</p>
    </div>

    <!-- Error Alert -->
    <transition name="fade">
        <div v-if="error" class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> <strong>Error:</strong> @{{ error }}
            <button type="button" class="btn-close" @click="error = null"></button>
        </div>
    </transition>

    <!-- Success Alert -->
    <transition name="fade">
        <div v-if="successMessage" class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> <strong>Success:</strong> @{{ successMessage }}
            <button type="button" class="btn-close" @click="successMessage = null"></button>
        </div>
    </transition>

    <!-- Main Dashboard Content -->
    <div v-if="!loading && studentData">
        <!-- Welcome Banner -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-primary text-white stat-card">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-box bg-white bg-opacity-25 text-white me-3">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <div>
                                <h3 class="mb-1">Welcome back, @{{ studentData.name }}! 👋</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Row -->
        <div class="row mb-4">
            <!-- Profile Card -->
            <div class="col-lg-4 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 text-white"><i class="fas fa-user-circle"></i> Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary mx-auto">
                                <i class="fas fa-user-circle fa-3x"></i>
                            </div>
                        </div>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="fw-bold text-muted"><i class="fas fa-user"></i> Name:</td>
                                <td class="text-end">@{{ studentData.name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted"><i class="fas fa-envelope"></i> Email:</td>
                                <td class="text-end"><small>@{{ studentData.email }}</small></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted"><i class="fas fa-phone"></i> Phone:</td>
                                <td class="text-end">@{{ studentData.phone }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted"><i class="fas fa-birthday-cake"></i> DOB:</td>
                                <td class="text-end">@{{ formatDate(studentData.dob) }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted"><i class="fas fa-map-marker-alt"></i> Address:</td>
                                <td class="text-end"><small>@{{ studentData.address }}</small></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted"><i class="fas fa-info-circle"></i> Status:</td>
                                <td class="text-end">
                                    <span :class="'badge payment-badge bg-' + getStatusColor(studentData.status)">
                                        @{{ studentData.status.toUpperCase() }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Fee Summary Card -->
            <div class="col-lg-8 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-header bg-gradient text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Fee Summary</h5>
                        <button class="btn btn-light btn-sm" @click="refreshFeeDetails" :disabled="refreshing">
                            <i class="fas fa-sync-alt" :class="{'fa-spin': refreshing}"></i> Refresh
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <div class="icon-box bg-white bg-opacity-25 text-white mx-auto mb-2">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <h6 class="text-white mb-2">Total Fees</h6>
                                    <h3 class="text-white mb-0 fw-bold">₹@{{ formatCurrency(feeDetails.total_fees) }}</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <div class="icon-box bg-white bg-opacity-25 text-white mx-auto mb-2">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <h6 class="text-white mb-2">Amount Paid</h6>
                                    <h3 class="text-white mb-0 fw-bold">₹@{{ formatCurrency(feeDetails.total_paid) }}</h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded stat-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                                    <div class="icon-box bg-white bg-opacity-25 text-white mx-auto mb-2">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <h6 class="text-white mb-2">Balance Due</h6>
                                    <h3 class="text-white mb-0 fw-bold">₹@{{ formatCurrency(feeDetails.balance) }}</h3>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold"><i class="fas fa-chart-line"></i> Payment Progress</span>
                                <span class="badge bg-primary fs-6">@{{ paymentPercentage }}% Complete</span>
                            </div>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" 
                                     role="progressbar" 
                                     :style="'width: ' + paymentPercentage + '%'"
                                     :aria-valuenow="paymentPercentage" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <strong>@{{ paymentPercentage }}% Paid</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="text-center">
                            <button class="btn btn-lg btn-primary px-5" @click="showPaymentModal" :disabled="feeDetails.balance <= 0">
                                <i class="fas fa-plus-circle"></i> Record New Payment
                            </button>
                            <p v-if="feeDetails.balance <= 0" class="text-success mt-2 mb-0">
                                <i class="fas fa-check-circle"></i> All fees are paid!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrolled Courses Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card stat-card">
                    <div class="card-header bg-info text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-book"></i> My Enrolled Courses (@{{ feeDetails.courses ? feeDetails.courses.length : 0 }})</h5>
                            <span class="badge bg-light text-dark">Total: ₹@{{ formatCurrency(feeDetails.total_fees) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="feeDetails.courses && feeDetails.courses.length > 0">
                            <div v-for="course in feeDetails.courses" :key="course.id" class="course-card card mb-3">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-3">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-box bg-primary bg-opacity-10 text-primary me-3">
                                                    <i class="fas fa-graduation-cap"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0">@{{ course.name }}</h5>
                                                    <small class="text-muted">₹@{{ formatCurrency(course.fee_per_month) }}/month</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <small class="text-muted d-block">Duration</small>
                                            <strong>@{{ course.duration }} months</strong>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <small class="text-muted d-block">Total Fee</small>
                                            <strong class="text-primary">₹@{{ formatCurrency(course.total_fee) }}</strong>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <small class="text-muted d-block">Paid</small>
                                            <strong class="text-success">₹@{{ formatCurrency(course.paid) }}</strong>
                                        </div>
                                        <div class="col-md-2 text-center">
                                            <small class="text-muted d-block">Balance</small>
                                            <strong :class="course.balance > 0 ? 'text-danger' : 'text-success'">
                                                ₹@{{ formatCurrency(course.balance) }}
                                            </strong>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <span v-if="course.balance > 0" class="badge bg-warning text-dark">Pending</span>
                                            <span v-else class="badge bg-success">Paid</span>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" 
                                                 :style="'width: ' + getCourseProgress(course) + '%'">
                                                @{{ getCourseProgress(course) }}%
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-5">
                            <i class="fas fa-book text-muted" style="font-size: 4rem;"></i>
                            <p class="text-muted mt-3 fs-5">No courses enrolled yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Section -->
        <div class="row">
            <div class="col-12">
                <div class="card stat-card">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-receipt"></i> Payment History (@{{ feeDetails.payments ? feeDetails.payments.length : 0 }})</h5>
                            <span class="badge bg-light text-dark">Total Paid: ₹@{{ formatCurrency(feeDetails.total_paid) }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div v-if="feeDetails.payments && feeDetails.payments.length > 0" class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th><i class="fas fa-calendar"></i> Date</th>
                                        <th><i class="fas fa-book"></i> Course</th>
                                        <th class="text-end"><i class="fas fa-money-bill"></i> Amount Paid</th>
                                        <th><i class="fas fa-info-circle"></i> Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(payment, index) in feeDetails.payments" :key="payment.id">
                                        <td>@{{ index + 1 }}</td>
                                        <td>
                                            <i class="fas fa-calendar-check text-muted"></i>
                                            @{{ formatDate(payment.date_of_payment) }}
                                        </td>
                                        <td>
                                            <strong>@{{ payment.course_name }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge bg-success payment-badge">₹@{{ formatCurrency(payment.amount_paid) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Paid</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-5">
                            <i class="fas fa-receipt text-muted" style="font-size: 4rem;"></i>
                            <p class="text-muted mt-3 fs-5">No payment history available</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-plus-circle"></i> Record New Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form @submit.prevent="submitPayment">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="courseSelect" class="form-label fw-bold">
                                <i class="fas fa-book"></i> Select Course <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg" id="courseSelect" v-model="paymentForm.course_id" required>
                                <option value="">-- Choose a course --</option>
                                <option v-for="course in feeDetails.courses" 
                                        :key="course.id" 
                                        :value="course.id"
                                        :disabled="course.balance <= 0">
                                    @{{ course.name }} - Balance: ₹@{{ formatCurrency(course.balance) }}
                                    <span v-if="course.balance <= 0">(Paid)</span>
                                </option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="amountInput" class="form-label fw-bold">
                                <i class="fas fa-money-bill"></i> Amount to Pay <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">₹</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="amountInput" 
                                       v-model.number="paymentForm.amount_paid" 
                                       step="0.01" 
                                       min="0.01"
                                       :max="getSelectedCourseBalance()"
                                       required
                                       placeholder="Enter amount">
                            </div>
                            <small class="text-muted">Maximum: ₹@{{ formatCurrency(getSelectedCourseBalance()) }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="dateInput" class="form-label fw-bold">
                                <i class="fas fa-calendar"></i> Payment Date <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control form-control-lg" 
                                   id="dateInput" 
                                   v-model="paymentForm.date_of_payment" 
                                   :max="today"
                                   required>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Note:</strong> Payment will be recorded and your balance will be updated automatically.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" :disabled="submitting">
                            <span v-if="submitting">
                                <span class="spinner-border spinner-border-sm me-2"></span>
                                Processing...
                            </span>
                            <span v-else>
                                <i class="fas fa-check"></i> Submit Payment
                            </span>
                        </button>
                    </div>
                </form>
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
            loading: true,
            refreshing: false,
            submitting: false,
            error: null,
            successMessage: null,
            studentData: null,
            feeDetails: {
                total_fees: 0,
                total_paid: 0,
                balance: 0,
                courses: [],
                payments: []
            },
            paymentForm: {
                student_id: {{ $student->id }},
                course_id: '',
                amount_paid: '',
                date_of_payment: new Date().toISOString().split('T')[0]
            },
            today: new Date().toISOString().split('T')[0]
        };
    },
    computed: {
        paymentPercentage() {
            if (this.feeDetails.total_fees === 0) return 0;
            return Math.round((this.feeDetails.total_paid / this.feeDetails.total_fees) * 100);
        }
    },
    methods: {
        async fetchStudentData() {
            try {
                this.loading = true;
                this.error = null;
                
                // Fetch student data with courses
                const response = await axios.get('/api/students/{{ $student->id }}');
                
                if (response.data.success) {
                    this.studentData = response.data.data;
                    await this.fetchFeeDetails();
                } else {
                    this.error = response.data.message || 'Failed to load student data';
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Error loading student data: ' + err.message;
            } finally {
                this.loading = false;
            }
        },
        
        async fetchFeeDetails() {
            try {
                const response = await axios.get('/api/students/{{ $student->id }}/fee-details');
                
                if (response.data.success) {
                    this.feeDetails = response.data.data;
                } else {
                    this.error = response.data.message || 'Failed to load fee details';
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Error loading fee details: ' + err.message;
            }
        },
        
        async refreshFeeDetails() {
            this.refreshing = true;
            await this.fetchFeeDetails();
            this.refreshing = false;
            this.showSuccess('Fee details refreshed successfully!');
        },
        
        async submitPayment() {
            try {
                this.submitting = true;
                this.error = null;
                
                // Client-side validation: Check if amount is positive
                if (!this.paymentForm.amount_paid || this.paymentForm.amount_paid <= 0) {
                    this.error = 'Payment amount must be a positive number greater than zero.';
                    this.submitting = false;
                    return;
                }
                
                // Client-side validation: Check if amount exceeds remaining balance
                const selectedCourse = this.feeDetails.courses.find(c => c.course_id == this.paymentForm.course_id);
                if (selectedCourse && this.paymentForm.amount_paid > selectedCourse.balance) {
                    this.error = `Payment amount (₹${this.paymentForm.amount_paid}) exceeds the remaining balance (₹${selectedCourse.balance.toFixed(2)}) for this course.`;
                    this.submitting = false;
                    return;
                }
                
                const response = await axios.post('/api/payments', this.paymentForm);
                
                if (response.data.success) {
                    this.showSuccess('Payment recorded successfully! Your balance has been updated.');
                    
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
                    modal.hide();
                    
                    // Reset form
                    this.paymentForm.course_id = '';
                    this.paymentForm.amount_paid = '';
                    this.paymentForm.date_of_payment = this.today;
                    
                    // Refresh fee details
                    await this.fetchFeeDetails();
                } else {
                    this.error = response.data.message || 'Failed to record payment';
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Error recording payment: ' + err.message;
            } finally {
                this.submitting = false;
            }
        },
        
        showPaymentModal() {
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        },
        
        showSuccess(message) {
            this.successMessage = message;
            setTimeout(() => {
                this.successMessage = null;
            }, 5000);
        },
        
        formatCurrency(amount) {
            return parseFloat(amount || 0).toFixed(2);
        },
        
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-IN', { 
                day: '2-digit', 
                month: 'short', 
                year: 'numeric' 
            });
        },
        
        getStatusColor(status) {
            const colors = {
                'active': 'success',
                'inactive': 'secondary',
                'suspended': 'warning',
                'rejected': 'danger'
            };
            return colors[status] || 'secondary';
        },
        
        getCourseProgress(course) {
            if (course.total_fee === 0) return 0;
            return Math.round((course.paid / course.total_fee) * 100);
        },
        
        getSelectedCourseBalance() {
            if (!this.paymentForm.course_id) return 0;
            const course = this.feeDetails.courses.find(c => c.id == this.paymentForm.course_id);
            return course ? course.balance : 0;
        }
    },
    mounted() {
        this.fetchStudentData();
    }
}).mount('#studentDashboard');
</script>
@endsection
