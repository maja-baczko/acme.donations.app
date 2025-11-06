# Routes Testing Guide

This guide provides comprehensive test cases for the donations API, specifically focusing on:
- Audit log immutability
- Payment immutability
- Donation exports with permissions
- Receipt generation and authorization

## Prerequisites

1. Docker containers running: `docker compose up -d`
2. Database migrated and seeded
3. Postman or similar API client
4. Valid authentication token

## Getting Authentication Token

**POST** `http://localhost:8000/api/v1/login`

Headers:
```
Content-Type: application/json
Accept: application/json
```

Body:
```json
{
    "email": "admin@example.com",
    "password": "password"
}
```

**Expected Response:**
```json
{
    "message": "Login successful",
    "access_token": "1|xxxxxxxxxx",
    "token_type": "Bearer"
}
```

Save the `access_token` for subsequent requests.

---

## Test 1: Audit Log Immutability ❌ SHOULD FAIL

### Test 1.1: Attempt to Update Audit Log (Should Fail with 405)

**PUT** `http://localhost:8000/api/v1/audit-logs/1`

Headers:
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

Body:
```json
{
    "action": "hacked"
}
```

**Expected Result:** ❌ **405 Method Not Allowed** or **404 Not Found** (route doesn't exist)

**Verification:** The route should not exist in `routes/api.php`

---

### Test 1.2: Verify Audit Logs Are Read-Only

**GET** `http://localhost:8000/api/v1/audit-logs`

Headers:
```
Authorization: Bearer {your_token}
Accept: application/json
```

**Expected Result:** ✅ **200 OK** with audit logs list

**POST/PUT/DELETE should not be available for audit logs except:**
- POST `/api/v1/audit-logs` - Only for system logging (rare)
- No PUT route
- DELETE only for admin purge operations

---

## Test 2: Payment Immutability ❌ SHOULD FAIL

### Test 2.1: Attempt to Update Payment (Should Fail with 405)

**PUT** `http://localhost:8000/api/v1/payments/1`

Headers:
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

Body:
```json
{
    "amount": 99999.99
}
```

**Expected Result:** ❌ **405 Method Not Allowed** or **404 Not Found** (route doesn't exist)

**Verification:** The route should not exist in `routes/api.php`

---

### Test 2.2: Verify Payment Create Works

**POST** `http://localhost:8000/api/v1/payments`

Headers:
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

Body:
```json
{
    "donation_id": 1,
    "amount": 50.00,
    "status": "processing",
    "gateway": "mock"
}
```

**Expected Result:** ✅ **201 Created** with payment data

**Note:** Once created, the payment amount cannot be changed. Only status transitions are allowed through specific service methods.

---

## Test 3: Donation Export with Permissions ✅ SHOULD WORK

### Test 3.1: Export Donations as JSON (Basic)

**GET** `http://localhost:8000/api/v1/donations/export?format=json`

Headers:
```
Authorization: Bearer {your_token}
Accept: application/json
```

**Expected Result:** ✅ **200 OK** with JSON export data

**Response Format:**
```json
{
    "data": [
        {
            "date": "2025-11-06 14:30:00",
            "donation_id": 1,
            "campaign": "Campaign Title",
            "donor_name": "John Doe",
            "donor_email": "john@example.com",
            "amount": 50.00,
            "status": "completed",
            "payment_method": "credit_card",
            "payment_reference": "TXN-ABC123-1699281234",
            "payment_status": "completed",
            "comment": "Happy to help!"
        }
    ],
    "summary": {
        "total_donations": 10,
        "total_amount": 500.00,
        "completed_count": 8,
        "pending_count": 1,
        "failed_count": 1
    }
}
```

---

### Test 3.2: Export Donations as CSV

**GET** `http://localhost:8000/api/v1/donations/export?format=csv`

Headers:
```
Authorization: Bearer {your_token}
Accept: text/csv
```

**Expected Result:** ✅ **200 OK** with CSV file download

**File Name:** `donations_export_2025-11-06_143000.csv`

**CSV Content:**
```
SUMMARY
Total Donations,10
Total Amount,500.00
Completed,8
Pending,1
Failed,1

date,donation_id,campaign,donor_name,donor_email,amount,status,payment_method,payment_reference,payment_status,comment
2025-11-06 14:30:00,1,Campaign Title,John Doe,john@example.com,50.00,completed,credit_card,TXN-ABC123,completed,Happy to help!
...
```

---

### Test 3.3: Export Filtered Donations

**GET** `http://localhost:8000/api/v1/donations/export`

Query Parameters:
```
format=json
status=completed
campaign_id=1
date_from=2025-11-01
date_to=2025-11-30
with_payment_proof=true
```

Full URL:
```
http://localhost:8000/api/v1/donations/export?format=json&status=completed&campaign_id=1&date_from=2025-11-01&date_to=2025-11-30&with_payment_proof=true
```

Headers:
```
Authorization: Bearer {your_token}
Accept: application/json
```

**Expected Result:** ✅ **200 OK** with filtered donations

**Verification:**
- Only completed donations
- Only from campaign ID 1
- Only within date range
- All should have payment references

---

### Test 3.4: Export Without Permission (Should Fail)

Login as a user without 'view donations' permission (e.g., regular employee).

**GET** `http://localhost:8000/api/v1/donations/export?format=json`

Headers:
```
Authorization: Bearer {employee_token}
Accept: application/json
```

**Expected Result:** ❌ **403 Forbidden**

**Response:**
```json
{
    "message": "This action is unauthorized."
}
```

---

## Test 4: Receipt Generation and Authorization ✅ SHOULD WORK

### Test 4.1: Generate Receipt for Own Payment

**GET** `http://localhost:8000/api/v1/payments/1/receipt`

Headers:
```
Authorization: Bearer {your_token}
Accept: application/json
```

**Expected Result:** ✅ **200 OK** with receipt data

**Response Format:**
```json
{
    "success": true,
    "receipt": {
        "receipt_number": "REC-00000001",
        "payment": {
            "id": 1,
            "transaction_reference": "TXN-ABC123DEF456-1699281234",
            "amount": 50.00,
            "gateway": "mock",
            "status": "completed",
            "payment_date": "2025-11-06 14:30:45"
        },
        "donation": {
            "id": 1,
            "amount": 50.00,
            "status": "completed",
            "is_anonymous": false,
            "donation_date": "2025-11-06 14:30:00",
            "comment": "Happy to help!"
        },
        "campaign": {
            "title": "Help Build Schools",
            "beneficiary": "Education Foundation"
        },
        "donor": {
            "name": "John Doe",
            "email": "john@example.com"
        },
        "generated_at": "2025-11-06 15:00:00"
    }
}
```

---

### Test 4.2: Attempt to View Someone Else's Receipt (Should Fail)

Login as User A, try to access User B's payment receipt.

**GET** `http://localhost:8000/api/v1/payments/5/receipt` (payment belongs to another user)

Headers:
```
Authorization: Bearer {userA_token}
Accept: application/json
```

**Expected Result:** ❌ **403 Forbidden**

**Response:**
```json
{
    "message": "You are not authorized to view this receipt."
}
```

---

### Test 4.3: Admin Can View Any Receipt

Login as admin with 'view donations' permission.

**GET** `http://localhost:8000/api/v1/payments/5/receipt` (payment belongs to another user)

Headers:
```
Authorization: Bearer {admin_token}
Accept: application/json
```

**Expected Result:** ✅ **200 OK** with receipt data

**Verification:** Admin with 'view donations' permission can view any receipt.

---

### Test 4.4: Generate Receipt for Pending Payment (Should Fail)

**GET** `http://localhost:8000/api/v1/payments/10/receipt` (payment with status='pending')

Headers:
```
Authorization: Bearer {your_token}
Accept: application/json
```

**Expected Result:** ❌ **422 Unprocessable Entity**

**Response:**
```json
{
    "success": false,
    "message": "Failed to generate receipt",
    "error": "Can only generate receipts for completed payments."
}
```

---

## Test 5: Integration Test - Complete Donation Flow

### Step 1: Create Donation

**POST** `http://localhost:8000/api/v1/donations`

Headers:
```
Authorization: Bearer {your_token}
Content-Type: application/json
Accept: application/json
```

Body:
```json
{
    "campaign_id": 1,
    "amount": 100.00,
    "status": "pending",
    "payment_method": "credit_card",
    "comment": "Great cause!",
    "is_anonymous": false
}
```

**Expected Result:** ✅ **201 Created**

Save the `donation.id` from response.

---

### Step 2: Create Payment

**POST** `http://localhost:8000/api/v1/payments`

Body:
```json
{
    "donation_id": {donation_id_from_step_1},
    "amount": 100.00,
    "status": "processing",
    "gateway": "mock"
}
```

**Expected Result:** ✅ **201 Created**

Save the `payment.id` from response.

---

### Step 3: Mark Payment as Completed (via Service)

This would normally be done internally by the PaymentService, but for testing:

Use `php artisan tinker` in Docker:
```bash
docker compose exec backend php artisan tinker
```

```php
$payment = \App\Modules\Payment\Models\Payment::find({payment_id});
$service = app(\App\Modules\Payment\Services\PaymentService::class);
$service->markAsCompleted($payment, 'TXN-TEST-' . time());
```

**Expected:**
- Payment status = 'completed'
- Donation status = 'completed'
- Campaign current_amount updated
- Events dispatched (check logs)

---

### Step 4: Generate Receipt

**GET** `http://localhost:8000/api/v1/payments/{payment_id}/receipt`

**Expected Result:** ✅ **200 OK** with complete receipt

---

### Step 5: Export Donation

**GET** `http://localhost:8000/api/v1/donations/export?format=csv`

**Expected Result:** ✅ **200 OK** with CSV containing the new donation

---

## Testing with Different User Roles

### Admin User
- Can export donations (has 'view donations' permission)
- Can view all receipts
- Can view all audit logs

### Manager User
- Can export donations if has 'view donations' permission
- Can view receipts for their team's donations
- Limited audit log access

### Employee/Donor User
- Cannot export donations
- Can only view their own receipts
- Cannot view audit logs

---

## Common Issues and Solutions

### Issue: Export returns empty data
**Solution:** Check if there are donations in the database. Run seeders if needed.

### Issue: Receipt shows "unauthorized"
**Solution:** Ensure the payment belongs to the authenticated user or user has 'view donations' permission.

### Issue: CSV file not downloading
**Solution:** Check Accept header is set correctly and browser doesn't block downloads.

### Issue: Payment update returns 404
**Solution:** This is correct behavior - payment update route doesn't exist (immutable).
