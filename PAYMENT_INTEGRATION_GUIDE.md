# Payment Integration Guide

This document provides a comprehensive guide for integrating payment gateways into the Kitabasan LMS platform, including how to add payment methods, configure payment APIs, and set up webhook/callback URLs.

---

## Table of Contents

1. [Overview](#overview)
2. [Payment System Architecture](#payment-system-architecture)
3. [Adding Payment Methods](#adding-payment-methods)
4. [Payment API URLs and Endpoints](#payment-api-urls-and-endpoints)
5. [Payment Flow](#payment-flow)
6. [Gateway-Specific Integration](#gateway-specific-integration)
7. [Testing Payment Integration](#testing-payment-integration)
8. [Troubleshooting](#troubleshooting)

---

## Overview

The Kitabasan LMS payment system supports multiple payment gateways including JazzCash, EasyPaisa, Stripe, PayPal, and other providers. The system automatically:

- Creates payment and transaction records
- Activates course enrollment upon successful payment
- Handles payment callbacks and webhooks
- Manages refunds and cancellations
- Prevents duplicate transactions

---

## Payment System Architecture

### Database Tables

1. **`payments`** - Stores payment records
2. **`transactions`** - Stores transaction logs
3. **`payment_methods`** - Stores payment gateway configurations
4. **`course_enrollments`** - Links payments to course access

### Key Components

- **PaymentController** (`app/Http/Controllers/Student/PaymentController.php`) - Handles payment requests
- **PaymentService** (`app/Services/PaymentService.php`) - Business logic for payments
- **PaymentMethod Model** (`app/Models/PaymentMethod.php`) - Payment gateway configurations

---

## Adding Payment Methods

### Method 1: Via Admin Panel (Recommended)

1. Navigate to **Admin Panel** → **Settings** → **Payment Methods**
2. Click **"Add New Payment Method"**
3. Fill in the form:

   - **Name**: Display name (e.g., "JazzCash", "EasyPaisa")
   - **Code**: Unique code (e.g., "jazzcash", "easypaisa") - lowercase, no spaces
   - **Description**: Brief description of the payment method
   - **Icon**: Upload payment method logo/icon (optional)
   - **Credentials** (JSON format):
     ```json
     {
       "merchant_id": "your_merchant_id",
       "password": "your_password",
       "integrity_salt": "your_salt_key"
     }
     ```
   - **Config** (JSON format):
     ```json
     {
       "sandbox_url": "https://sandbox.gateway.com",
       "production_url": "https://payments.gateway.com"
     }
     ```
   - **Transaction Fee Percentage**: Percentage fee (e.g., 2.5 for 2.5%)
   - **Transaction Fee Fixed**: Fixed amount fee (e.g., 10.00)
   - **Is Active**: Enable/disable payment method
   - **Is Sandbox**: Use test mode

4. Click **"Save"**

### Method 2: Via Database

```sql
INSERT INTO payment_methods (
    name, slug, code, description, 
    credentials, config, 
    is_active, is_sandbox, `order`,
    transaction_fee_percentage, transaction_fee_fixed,
    created_at, updated_at
) VALUES (
    'Stripe',
    'stripe',
    'stripe',
    'Stripe payment gateway integration',
    '{"api_key": "sk_test_...", "public_key": "pk_test_..."}',
    '{"sandbox_url": "https://api.stripe.com", "production_url": "https://api.stripe.com"}',
    1,
    1,
    3,
    2.9,
    0.30,
    NOW(),
    NOW()
);
```

### Method 3: Via Migration (For Default Methods)

Edit `database/migrations/2025_12_31_000012_create_payment_methods_table.php` and add your payment method to the insert statement.

---

## Payment API URLs and Endpoints

### Base URLs

**Production:**
```
https://yourdomain.com
```

**Development/Local:**
```
http://localhost:8000
http://yourdomain.test
```

### Payment Endpoints

#### 1. Payment Page (Student)
```
GET /student/payments?course_id={course_id}
```
**Purpose**: Display payment page for a specific course

**Authentication**: Required (Student)

**Example:**
```
https://yourdomain.com/student/payments?course_id=5
```

---

#### 2. Process Payment (Submit Payment Request)
```
POST /student/payments
```
**Purpose**: Create payment record and initiate payment gateway redirect

**Authentication**: Required (Student)

**Request Body:**
```json
{
  "course_id": 5,
  "payment_method_id": 1,
  "terms": true
}
```

**Response**: Redirects to payment gateway or callback URL

---

#### 3. Payment Callback (Return URL)
```
GET /student/payments/callback?transaction_id={transaction_id}&status={status}
```
**Purpose**: Handle user return from payment gateway

**Authentication**: Required (Student)

**Query Parameters:**
- `transaction_id`: Unique transaction identifier
- `status`: Payment status (`success`, `failed`, `cancelled`)

**Example:**
```
https://yourdomain.com/student/payments/callback?transaction_id=TXNABC1234567890&status=success
```

**⚠️ IMPORTANT**: This URL should be provided to your payment gateway as the **Return URL** or **Redirect URL**.

---

#### 4. Payment Webhook (Server-to-Server)
```
POST /payments/webhook
```
**Purpose**: Receive payment status updates from payment gateway (server-to-server)

**Authentication**: None (Public endpoint, should be secured with signature verification)

**Request Body** (varies by gateway):
```json
{
  "transaction_id": "TXNABC1234567890",
  "status": "success",
  "amount": "5000.00",
  "currency": "PKR",
  "payment_status": "completed"
}
```

**Expected Status Values:**
- `success` or `completed` - Payment successful
- `failed` - Payment failed
- `cancelled` or `refunded` - Payment cancelled/refunded

**Response:**
```json
{
  "message": "Webhook processed successfully"
}
```

**⚠️ CRITICAL**: This URL must be provided to your payment gateway as the **Webhook URL**, **IPN URL**, or **Notification URL**.

**⚠️ SECURITY NOTE**: Currently, webhook signature verification is not implemented. It's recommended to:
1. Verify webhook signatures from the gateway
2. Implement IP whitelisting
3. Use HTTPS only
4. Validate request source

---

### Complete URL Examples

#### For JazzCash Integration

**Return URL (Callback):**
```
https://yourdomain.com/student/payments/callback?transaction_id={PP_TRAN_REF}&status={pp_ResponseCode}
```

**Webhook URL (IPN):**
```
https://yourdomain.com/payments/webhook
```

**Note**: Replace `{PP_TRAN_REF}` and `{pp_ResponseCode}` with actual JazzCash parameters.

---

#### For EasyPaisa Integration

**Return URL:**
```
https://yourdomain.com/student/payments/callback?transaction_id={transactionId}&status={status}
```

**Webhook URL:**
```
https://yourdomain.com/payments/webhook
```

---

#### For Stripe Integration

**Return URL (Success):**
```
https://yourdomain.com/student/payments/callback?transaction_id={CHECKOUT_SESSION_ID}&status=success
```

**Return URL (Cancel):**
```
https://yourdomain.com/student/payments/callback?transaction_id={CHECKOUT_SESSION_ID}&status=cancelled
```

**Webhook URL:**
```
https://yourdomain.com/payments/webhook
```

---

## Payment Flow

### Complete Payment Flow Diagram

```
┌─────────────┐
│   Student   │
│   Selects   │
│   Course    │
└──────┬──────┘
       │
       ▼
┌─────────────────────┐
│  CourseController   │
│  enroll() method    │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ PaymentController   │
│  index() - Shows    │
│  Payment Page       │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Student Selects    │
│  Payment Method     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│ PaymentController   │
│  store() - Creates  │
│  Payment Record     │
└──────┬──────────────┘
       │
       ▼
┌─────────────────────┐
│  Redirect to        │
│  Payment Gateway    │
└──────┬──────────────┘
       │
       ├──────────────────┐
       │                  │
       ▼                  ▼
┌──────────────┐  ┌──────────────┐
│   User       │  │  Gateway     │
│   Completes  │  │  Sends       │
│   Payment    │  │  Webhook     │
└──────┬───────┘  └──────┬───────┘
       │                  │
       │                  │
       ▼                  ▼
┌─────────────────────────────────┐
│  PaymentController              │
│  callback() OR webhook()        │
└──────────┬──────────────────────┘
           │
           ▼
┌─────────────────────┐
│  PaymentService     │
│  handlePaymentSuccess│
│  activateEnrollment │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Course Enrollment  │
│  Activated          │
│  Student Can Access │
└─────────────────────┘
```

### Step-by-Step Flow

1. **Student Initiates Purchase**
   - Student clicks "Enroll" on a paid course
   - `CourseController::enroll()` redirects to payment page

2. **Payment Page Display**
   - `PaymentController::index()` displays payment methods
   - Student selects payment method and agrees to terms

3. **Payment Request Submission**
   - `PaymentController::store()` creates:
     - `Payment` record with status `pending`
     - `Transaction` record with status `pending`
   - Generates unique `transaction_id` (e.g., `TXNABC1234567890`)

4. **Redirect to Payment Gateway**
   - System redirects user to payment gateway (JazzCash, EasyPaisa, etc.)
   - Includes return URL and transaction details

5. **Payment Processing**
   - User completes payment on gateway
   - Gateway processes payment

6. **Return/Webhook Handling**
   - **Return URL (Callback)**: User redirected back to `callback()` method
   - **Webhook URL**: Gateway sends server-to-server notification to `webhook()` method

7. **Payment Verification**
   - System verifies payment status
   - Updates `Payment` record status (`completed`, `failed`, `cancelled`)
   - Updates `Transaction` record status

8. **Enrollment Activation**
   - If payment successful: `PaymentService::handlePaymentSuccess()` is called
   - Creates/updates `CourseEnrollment` with:
     - `status` = `active`
     - `payment_status` = `paid`
     - `payment_id` = payment record ID
     - `expires_at` = calculated based on course duration

9. **Access Granted**
   - Student redirected to course learning page
   - Course is now accessible

---

## Gateway-Specific Integration

### JazzCash Integration

#### Required Credentials
- Merchant ID
- Password
- Integrity Salt

#### Configuration in Admin Panel

**Credentials JSON:**
```json
{
  "merchant_id": "your_merchant_id",
  "password": "your_password",
  "integrity_salt": "your_integrity_salt"
}
```

**Config JSON:**
```json
{
  "sandbox_url": "https://sandbox.jazzcash.com.pk",
  "production_url": "https://payments.jazzcash.com.pk"
}
```

#### URLs to Provide to JazzCash

**Return URL:**
```
https://yourdomain.com/student/payments/callback?transaction_id={PP_TRAN_REF}&status={pp_ResponseCode}
```

**Webhook URL (IPN):**
```
https://yourdomain.com/payments/webhook
```

**Note**: Map JazzCash response codes:
- `000` = success
- `001` = failed
- Other codes = failed/cancelled

---

### EasyPaisa Integration

#### Required Credentials
- Merchant ID
- Password
- Store ID

#### Configuration in Admin Panel

**Credentials JSON:**
```json
{
  "merchant_id": "your_merchant_id",
  "password": "your_password",
  "store_id": "your_store_id"
}
```

**Config JSON:**
```json
{
  "sandbox_url": "https://easypay.easypaisa.com.pk",
  "production_url": "https://easypay.easypaisa.com.pk"
}
```

#### URLs to Provide to EasyPaisa

**Return URL:**
```
https://yourdomain.com/student/payments/callback?transaction_id={transactionId}&status={status}
```

**Webhook URL:**
```
https://yourdomain.com/payments/webhook
```

---

### Stripe Integration

#### Required Credentials
- API Key (Secret Key)
- Public Key (Publishable Key)

#### Configuration in Admin Panel

**Credentials JSON:**
```json
{
  "api_key": "sk_live_...",
  "public_key": "pk_live_..."
}
```

**Config JSON:**
```json
{
  "sandbox_url": "https://api.stripe.com",
  "production_url": "https://api.stripe.com"
}
```

#### URLs to Provide to Stripe

**Success URL:**
```
https://yourdomain.com/student/payments/callback?transaction_id={CHECKOUT_SESSION_ID}&status=success
```

**Cancel URL:**
```
https://yourdomain.com/student/payments/callback?transaction_id={CHECKOUT_SESSION_ID}&status=cancelled
```

**Webhook URL:**
```
https://yourdomain.com/payments/webhook
```

**Stripe Webhook Events to Subscribe:**
- `checkout.session.completed`
- `payment_intent.succeeded`
- `payment_intent.payment_failed`
- `charge.refunded`

---

### PayPal Integration

#### Required Credentials
- Client ID
- Client Secret

#### Configuration in Admin Panel

**Credentials JSON:**
```json
{
  "client_id": "your_client_id",
  "client_secret": "your_client_secret"
}
```

**Config JSON:**
```json
{
  "sandbox_url": "https://api.sandbox.paypal.com",
  "production_url": "https://api.paypal.com"
}
```

#### URLs to Provide to PayPal

**Return URL:**
```
https://yourdomain.com/student/payments/callback?transaction_id={token}&status=success
```

**Cancel URL:**
```
https://yourdomain.com/student/payments/callback?transaction_id={token}&status=cancelled
```

**Webhook URL (IPN):**
```
https://yourdomain.com/payments/webhook
```

---

### GoPayFast (Custom Redirect Integration)

#### Required Credentials (example)
- Merchant ID
- Secret Key (for signature)

#### Configuration in Admin Panel

**Credentials JSON:**
```json
{
  "merchant_id": "your_merchant_id",
  "secret_key": "your_secret_key"
}
```

**Config JSON (redirect + fields):**
```json
{
  "redirect_url": "https://sandbox.gopayfast.com/checkout",
  "method": "POST",
  "fields": {
    "merchant_id": "{merchant_id}",
    "transaction_id": "{transaction_id}",
    "amount": "{amount}",
    "currency": "{currency}",
    "callback_url": "{callback_url}",
    "customer_name": "{user_name}",
    "customer_email": "{user_email}",
    "course_title": "{course_title}"
  },
  "signature": {
    "field": "signature",
    "type": "hmac_sha256",
    "key": "secret_key",
    "fields": ["merchant_id","transaction_id","amount","currency","callback_url"]
  }
}
```

#### URLs to Provide to GoPayFast

**Return URL (Callback):**
```
https://yourdomain.com/student/payments/callback?transaction_id={transaction_id}&status={status}
```

**Webhook URL (IPN):**
```
https://yourdomain.com/payments/webhook
```

---

## Testing Payment Integration

### Test Payment Flow

1. **Enable Sandbox Mode**
   - Set payment method `is_sandbox` = `true` in admin panel
   - Use test credentials from payment gateway

2. **Create Test Course**
   - Create a course with a price (e.g., Rs. 1000)
   - Set course as paid (not free)

3. **Test Payment**
   - Login as student
   - Navigate to course
   - Click "Enroll"
   - Select payment method
   - Use test card/account from gateway
   - Complete payment

4. **Verify Results**
   - Check `payments` table: payment status should be `completed`
   - Check `transactions` table: transaction status should be `completed`
   - Check `course_enrollments` table: enrollment should exist with `status` = `active`
   - Verify student can access course content

### Test Webhook Locally

Use tools like:
- **ngrok**: `ngrok http 8000` to expose local server
- **Webhook.site**: Test webhook endpoints
- **Postman**: Simulate webhook requests

**Example ngrok URL:**
```
https://abc123.ngrok.io/payments/webhook
```

### Test Cases

✅ **Success Payment**
- Payment status = `completed`
- Transaction status = `completed`
- Enrollment created with `status` = `active`
- Course accessible

✅ **Failed Payment**
- Payment status = `failed`
- Transaction status = `failed`
- No enrollment created
- Course not accessible

✅ **Cancelled Payment**
- Payment status = `cancelled`
- Transaction status = `failed`
- No enrollment created
- Course not accessible

✅ **Duplicate Webhook**
- First webhook: Processes payment
- Second webhook: Returns "already processed" (idempotent)

✅ **Refund**
- Payment status = `cancelled`
- Enrollment status = `cancelled`
- Course access revoked

---

## Troubleshooting

### Common Issues

#### 1. Payment Not Activating Enrollment

**Problem**: Payment is successful but course not accessible

**Solutions**:
- Check `payments` table: Verify payment `status` = `completed`
- Check `course_enrollments` table: Verify enrollment exists
- Check logs: `storage/logs/laravel.log`
- Manually activate via admin panel: Update payment status to `completed`

#### 2. Webhook Not Receiving Data

**Problem**: Payment gateway not sending webhook notifications

**Solutions**:
- Verify webhook URL is correct and accessible (HTTPS required)
- Check gateway webhook settings/configuration
- Test webhook URL with tool like Webhook.site
- Check server logs for incoming requests
- Verify firewall/security settings allow incoming requests

#### 3. Duplicate Payments

**Problem**: Multiple payment records for same transaction

**Solutions**:
- Verify `transaction_id` is unique (database constraint exists)
- Check idempotency logic in webhook handler
- Verify payment gateway is not sending duplicate webhooks

#### 4. Callback URL Not Working

**Problem**: User redirected but payment not processed

**Solutions**:
- Verify callback URL parameters match expected format
- Check `transaction_id` exists in database
- Verify callback URL is accessible (not blocked)
- Check browser console for errors
- Review callback handler logs

#### 5. Payment Gateway Error

**Problem**: Gateway returns error when redirecting

**Solutions**:
- Verify credentials are correct (check admin panel)
- Check if sandbox/production mode matches credentials
- Verify transaction amount format (should be decimal)
- Check gateway API documentation for required fields
- Review gateway response/error logs

---

## Security Best Practices

1. **Webhook Verification**
   - Implement signature verification for webhooks
   - Validate request source IP
   - Use HTTPS only

2. **Credentials Storage**
   - Store credentials encrypted in database
   - Use environment variables for sensitive data
   - Never commit credentials to version control

3. **Transaction Security**
   - Always use HTTPS for payment pages
   - Implement CSRF protection
   - Validate all input data
   - Use unique transaction IDs

4. **Logging**
   - Log all payment attempts
   - Log webhook/callback requests
   - Monitor for suspicious activity

---

## Support and Documentation

### Internal Documentation
- Payment Service: `app/Services/PaymentService.php`
- Payment Controller: `app/Http/Controllers/Student/PaymentController.php`
- Payment Method Model: `app/Models/PaymentMethod.php`

### Gateway Documentation
- [JazzCash Documentation](https://developer.jazzcash.com.pk/)
- [EasyPaisa Documentation](https://easypay.easypaisa.com.pk/documentation)
- [Stripe Documentation](https://stripe.com/docs)
- [PayPal Documentation](https://developer.paypal.com/docs)

### Contact
For integration support, contact the development team or refer to the codebase documentation.

---

## Quick Reference

### Essential URLs

| Purpose | URL |
|---------|-----|
| Payment Page | `https://yourdomain.com/student/payments?course_id={id}` |
| Payment Callback | `https://yourdomain.com/student/payments/callback` |
| Payment Webhook | `https://yourdomain.com/payments/webhook` |

### Status Codes

| Payment Status | Enrollment Action |
|---------------|-------------------|
| `completed` | Enrollment activated |
| `failed` | No enrollment |
| `cancelled` | Enrollment revoked |
| `pending` | No enrollment (waiting) |

### Database Fields

**payments table:**
- `transaction_id` (unique)
- `status` (pending, completed, failed, cancelled)
- `amount`
- `gateway_response` (JSON)

**course_enrollments table:**
- `payment_id` (links to payment)
- `status` (active, expired, cancelled)
- `payment_status` (pending, paid, free)
- `expires_at` (calculated from course duration)

---

**Last Updated**: January 2025  
**Version**: 1.0
