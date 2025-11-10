 Login Credentials

  ADMIN LOGIN
- Email: admin@example.com
- Password: password

 STUDENT LOGIN 
- Email: qjohns@example.org 
- Password: password

 Data Seeding :I have used seeders and factories for data listing..so use artisan db seed 


 API Endpoints i have used.

Students API
- GET /api/students - Get all students (paginated)
- GET /api/students/{id} - Get single student
- POST /api/students - Create new student
- PUT /api/students/{id} - Update student
- DELETE /api/students/{id} - Delete student
- GET /api/students/{id}/fee-details - Get fee breakdown
- POST /api/students/{id}/assign-courses - Enroll in courses
- GET /api/students/{id}/payments - Get student payments

 Payments API
- GET /api/payments - Get all payments (paginated)
- GET /api/payments/{id} - Get single payment
- POST /api/payments - Record new payment
- PUT /api/payments/{id} - Update payment
- DELETE /api/payments/{id} - Delete payment
- GET /api/students/{id}/payments - Get payments by student
