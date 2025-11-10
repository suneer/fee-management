# Fee Management System - API Documentation

## Base URL
```
http://127.0.0.1:8000/api
```

## API Endpoints

### 1. Students API

#### 1.1 Get All Students
**Endpoint:** `GET /api/students`

**Description:** Retrieve a list of all students with their enrolled courses.

**Response Example:**
```json
{
    "success": true,
    "message": "Students retrieved successfully",
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "1234567890",
            "dob": "2000-01-15",
            "address": "123 Main St",
            "status": "active",
            "courses": [
                {
                    "id": 1,
                    "name": "Web Development",
                    "duration": 6,
                    "fee_per_month": 5000,
                    "total_fee": 30000
                }
            ],
            "created_at": "2025-11-10T10:00:00.000000Z",
            "updated_at": "2025-11-10T10:00:00.000000Z"
        }
    ]
}
```

#### 1.2 Get Specific Student
**Endpoint:** `GET /api/students/{id}`

**Description:** Retrieve details of a specific student by ID.

**URL Parameters:**
- `id` (required) - Student ID

**Response Example:**
```json
{
    "success": true,
    "message": "Student retrieved successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "1234567890",
        "dob": "2000-01-15",
        "address": "123 Main St",
        "status": "active",
        "courses": [...],
        "payments": [...],
        "created_at": "2025-11-10T10:00:00.000000Z",
        "updated_at": "2025-11-10T10:00:00.000000Z"
    }
}
```

#### 1.3 Create New Student
**Endpoint:** `POST /api/students`

**Description:** Add a new student to the system.

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "dob": "2000-01-15",
    "address": "123 Main St",
    "status": "active"
}
```

**Required Fields:**
- `name` (string, max 255)
- `email` (string, valid email, unique)
- `phone` (string, max 20)
- `dob` (date)
- `address` (string)

**Optional Fields:**
- `status` (enum: active, inactive, suspended, rejected) - Default: active

**Response Example:**
```json
{
    "success": true,
    "message": "Student created successfully",
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "1234567890",
        "dob": "2000-01-15",
        "address": "123 Main St",
        "status": "active",
        "created_at": "2025-11-10T10:00:00.000000Z",
        "updated_at": "2025-11-10T10:00:00.000000Z"
    }
}
```

#### 1.4 Update Student
**Endpoint:** `PUT /api/students/{id}`

**Description:** Update an existing student's information.

**URL Parameters:**
- `id` (required) - Student ID

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "phone": "9876543210",
    "status": "inactive"
}
```

**Response Example:**
```json
{
    "success": true,
    "message": "Student updated successfully",
    "data": {
        "id": 1,
        "name": "John Doe Updated",
        "email": "john@example.com",
        "phone": "9876543210",
        "status": "inactive",
        ...
    }
}
```

#### 1.5 Delete Student
**Endpoint:** `DELETE /api/students/{id}`

**Description:** Delete a student from the system.

**URL Parameters:**
- `id` (required) - Student ID

**Response Example:**
```json
{
    "success": true,
    "message": "Student deleted successfully"
}
```

#### 1.6 Get Student Fee Details
**Endpoint:** `GET /api/students/{id}/fee-details`

**Description:** Fetch comprehensive fee details for a specific student including all courses, payments, and balances.

**URL Parameters:**
- `id` (required) - Student ID

**Response Example:**
```json
{
    "success": true,
    "message": "Fee details retrieved successfully",
    "data": {
        "student_id": 1,
        "student_name": "John Doe",
        "student_email": "john@example.com",
        "total_fee": 30000,
        "total_paid": 15000,
        "balance": 15000,
        "courses": [
            {
                "course_id": 1,
                "course_name": "Web Development",
                "duration": 6,
                "fee_per_month": 5000,
                "total_fee": 30000,
                "amount_paid": 15000,
                "balance": 15000
            }
        ],
        "payments": [
            {
                "id": 1,
                "course_id": 1,
                "course_name": "Web Development",
                "amount_paid": 5000,
                "date_of_payment": "2025-11-01",
                "created_at": "2025-11-01T10:00:00.000000Z"
            }
        ]
    }
}
```

#### 1.7 Assign Courses to Student
**Endpoint:** `POST /api/students/{id}/assign-courses`

**Description:** Assign one or more courses to a student.

**URL Parameters:**
- `id` (required) - Student ID

**Request Body:**
```json
{
    "course_ids": [1, 2, 3]
}
```

**Response Example:**
```json
{
    "success": true,
    "message": "Courses assigned successfully",
    "data": {
        "student_id": 1,
        "assigned_courses": [
            {
                "id": 1,
                "name": "Web Development",
                "duration": 6,
                "fee_per_month": 5000
            }
        ]
    }
}
```

#### 1.8 Get Student Payments
**Endpoint:** `GET /api/students/{id}/payments`

**Description:** Get all payments made by a specific student.

**URL Parameters:**
- `id` (required) - Student ID

**Response Example:**
```json
{
    "success": true,
    "message": "Student payments retrieved successfully",
    "data": {
        "student_id": 1,
        "student_name": "John Doe",
        "total_payments": 3,
        "total_amount_paid": 15000,
        "payments": [
            {
                "id": 1,
                "course_id": 1,
                "course_name": "Web Development",
                "amount_paid": 5000,
                "date_of_payment": "2025-11-01",
                "created_at": "2025-11-01T10:00:00.000000Z"
            }
        ]
    }
}
```

---

### 2. Payments API

#### 2.1 Get All Payments
**Endpoint:** `GET /api/payments`

**Description:** Retrieve a list of all payment records.

**Response Example:**
```json
{
    "success": true,
    "message": "Payments retrieved successfully",
    "data": [
        {
            "id": 1,
            "student_id": 1,
            "student_name": "John Doe",
            "student_email": "john@example.com",
            "course_id": 1,
            "course_name": "Web Development",
            "amount_paid": 5000,
            "date_of_payment": "2025-11-01",
            "created_at": "2025-11-01T10:00:00.000000Z",
            "updated_at": "2025-11-01T10:00:00.000000Z"
        }
    ]
}
```

#### 2.2 Get Specific Payment
**Endpoint:** `GET /api/payments/{id}`

**Description:** Retrieve details of a specific payment.

**URL Parameters:**
- `id` (required) - Payment ID

**Response Example:**
```json
{
    "success": true,
    "message": "Payment retrieved successfully",
    "data": {
        "id": 1,
        "student": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "course": {
            "id": 1,
            "name": "Web Development",
            "duration": 6,
            "fee_per_month": 5000
        },
        "amount_paid": 5000,
        "date_of_payment": "2025-11-01",
        "created_at": "2025-11-01T10:00:00.000000Z",
        "updated_at": "2025-11-01T10:00:00.000000Z"
    }
}
```

#### 2.3 Record New Payment
**Endpoint:** `POST /api/payments`

**Description:** Record a new payment for a student's course.

**Request Body:**
```json
{
    "student_id": 1,
    "course_id": 1,
    "amount_paid": 5000,
    "date_of_payment": "2025-11-01"
}
```

**Required Fields:**
- `student_id` (integer, must exist in students table)
- `course_id` (integer, must exist in courses table)
- `amount_paid` (numeric, minimum 0)
- `date_of_payment` (date)

**Response Example:**
```json
{
    "success": true,
    "message": "Payment recorded successfully",
    "data": {
        "payment": {
            "id": 1,
            "student_id": 1,
            "course_id": 1,
            "amount_paid": 5000,
            "date_of_payment": "2025-11-01",
            "created_at": "2025-11-01T10:00:00.000000Z"
        },
        "fee_summary": {
            "total_course_fee": 30000,
            "total_paid": 5000,
            "remaining_balance": 25000,
            "payment_status": "Pending"
        }
    }
}
```

#### 2.4 Update Payment
**Endpoint:** `PUT /api/payments/{id}`

**Description:** Update an existing payment record.

**URL Parameters:**
- `id` (required) - Payment ID

**Request Body:**
```json
{
    "amount_paid": 6000,
    "date_of_payment": "2025-11-02"
}
```

**Response Example:**
```json
{
    "success": true,
    "message": "Payment updated successfully",
    "data": {
        "id": 1,
        "student_id": 1,
        "course_id": 1,
        "amount_paid": 6000,
        "date_of_payment": "2025-11-02",
        ...
    }
}
```

#### 2.5 Delete Payment
**Endpoint:** `DELETE /api/payments/{id}`

**Description:** Delete a payment record.

**URL Parameters:**
- `id` (required) - Payment ID

**Response Example:**
```json
{
    "success": true,
    "message": "Payment deleted successfully"
}
```

---

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "name": ["The name field is required."]
    }
}
```

### Not Found Error (404)
```json
{
    "success": false,
    "message": "Student not found"
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Failed to create student",
    "error": "Error details here"
}
```

### Bad Request (400)
```json
{
    "success": false,
    "message": "Student is not enrolled in this course"
}
```

---

## Testing the API

### Using cURL

**Get all students:**
```bash
curl -X GET http://127.0.0.1:8000/api/students
```

**Create a new student:**
```bash
curl -X POST http://127.0.0.1:8000/api/students \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "dob": "2000-01-15",
    "address": "123 Main St"
  }'
```

**Get fee details:**
```bash
curl -X GET http://127.0.0.1:8000/api/students/1/fee-details
```

**Record a payment:**
```bash
curl -X POST http://127.0.0.1:8000/api/payments \
  -H "Content-Type: application/json" \
  -d '{
    "student_id": 1,
    "course_id": 1,
    "amount_paid": 5000,
    "date_of_payment": "2025-11-01"
  }'
```

### Using Postman

1. Import the base URL: `http://127.0.0.1:8000/api`
2. Set headers: `Content-Type: application/json`
3. Use the endpoints listed above
4. View formatted JSON responses

---

## HTTP Status Codes

- `200 OK` - Successful GET, PUT, DELETE
- `201 Created` - Successful POST
- `400 Bad Request` - Invalid request data
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Validation failed
- `500 Internal Server Error` - Server error

---

## Notes

1. All endpoints return JSON responses
2. Date format: `YYYY-MM-DD`
3. Currency amounts are in decimal format (e.g., 5000.00)
4. Student must be enrolled in a course before recording payments
5. All API routes are prefixed with `/api`
