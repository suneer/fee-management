# Fee Management System

A comprehensive Laravel-based Fee Management System with role-based authentication for managing students, courses, and payments with EMI support.

## Features

### 🔐 Authentication & Authorization
- **Role-based Access Control**: Admin and Student roles
- **Laravel Breeze**: Built-in authentication system
- **Protected Routes**: Middleware-based route protection

### 👨‍💼 Admin Features
- **Student Management**
  - Add, edit, delete students
  - Assign courses to students
  - Manage student status (Active/Inactive/Suspended/Rejected)
  - View student fee details and payment history

- **Course Management**
  - Add, edit, delete courses
  - Set duration and monthly fees
  - Track enrolled students per course
  - View revenue projections

- **Payment Management**
  - Record payments for students
  - Automatic fee calculation (duration × fee_per_month)
  - Track remaining balances
  - EMI calculator for installment planning
  - Complete payment history

### 👨‍🎓 Student Features
- **Personal Dashboard**
  - View enrolled courses
  - Check fee summary (Total, Paid, Balance)
  - View payment history
  - Access profile settings

## Installation

### Prerequisites
- PHP >= 8.0
- Composer
- MySQL/MariaDB
- Node.js & NPM

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/suneer/fee-management.git
   cd fee-management
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database** (Edit `.env` file)
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=fee_management
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Run migrations and seeders**
   ```bash
   php artisan migrate:fresh
   php artisan db:seed --class=CourseSeeder
   php artisan db:seed --class=UserSeeder
   ```

7. **Build assets**
   ```bash
   npm run build
   ```

8. **Start the development server**
   ```bash
   php artisan serve
   ```

9. **Access the application**
   - URL: `http://127.0.0.1:8000`

## Default Login Credentials

### Admin Account
- **Email**: admin@feemanagement.com
- **Password**: admin123

### Student Accounts
- **Email**: student1@example.com / student2@example.com / student3@example.com
- **Password**: student123 (for all students)

## Database Schema

### Tables
- **users** - User authentication with roles (admin/student)
- **students** - Student profiles and information
- **courses** - Course details (name, duration, fees)
- **payments** - Payment records
- **course_student** - Pivot table for student-course enrollment

### Relationships
- One User → One Student (for student role)
- Many Students → Many Courses (many-to-many)
- One Student → Many Payments
- One Course → Many Payments

## Technologies Used

- **Backend**: Laravel 10.x
- **Frontend**: Bootstrap 5, Blade Templates
- **Authentication**: Laravel Breeze
- **Database**: MySQL
- **Icons**: Bootstrap Icons

## Key Functionalities

### Fee Calculation
```
Total Course Fee = Duration (months) × Fee per Month
Remaining Balance = Total Course Fee - Total Paid
```

### EMI Calculator
- Divide remaining balance into monthly installments
- Flexible terms (2-24 months)
- Automatic due date calculation
- Visual payment schedule

### Payment Tracking
- Per-course payment tracking
- Automatic balance updates
- Complete audit trail
- Date-wise payment history

## Project Structure

```
fee-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── StudentController.php
│   │   │   ├── CourseController.php
│   │   │   └── PaymentController.php
│   │   └── Middleware/
│   │       └── IsAdmin.php
│   └── Models/
│       ├── User.php
│       ├── Student.php
│       ├── Course.php
│       └── Payment.php
├── database/
│   ├── migrations/
│   └── seeders/
│       ├── CourseSeeder.php
│       └── UserSeeder.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── admin.blade.php
│       ├── students/
│       ├── courses/
│       ├── payments/
│       └── student/
│           └── dashboard.blade.php
└── routes/
    └── web.php
```

## Security Features

- Password hashing with bcrypt
- CSRF protection
- SQL injection prevention
- XSS protection
- Authentication middleware
- Role-based authorization

## Screenshots

### Admin Dashboard
- Student list with fee details
- Course management
- Payment recording

### Student Dashboard  
- Personal profile
- Enrolled courses
- Fee summary
- Payment history

## Future Enhancements

- [ ] Email notifications for payments
- [ ] PDF receipt generation
- [ ] SMS reminders for pending fees
- [ ] Advanced reporting and analytics
- [ ] Bulk payment import
- [ ] Multi-currency support

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please create an issue on GitHub or contact the administrator.

## Credits

Developed by Suneer
- GitHub: [@suneer](https://github.com/suneer)
- Repository: [fee-management](https://github.com/suneer/fee-management)
