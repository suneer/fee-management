<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 0 0 5px 5px;
        }
        .payment-details {
            background-color: white;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            color: #333;
        }
        .amount {
            font-size: 24px;
            color: #4CAF50;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 14px;
        }
        .success-icon {
            font-size: 48px;
            color: #4CAF50;
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Payment Confirmation</h1>
    </div>
    
    <div class="content">
        <div class="success-icon">✓</div>
        
        <p>Dear {{ $payment->student->name }},</p>
        
        <p>Thank you for your payment! We have successfully received and recorded your payment for the course <strong>{{ $payment->course->name }}</strong>.</p>
        
        <div class="payment-details">
            <h2 style="margin-top: 0; color: #4CAF50;">Payment Details</h2>
            
            <div class="detail-row">
                <span class="label">Receipt Number:</span>
                <span class="value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Student Name:</span>
                <span class="value">{{ $payment->student->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Course Name:</span>
                <span class="value">{{ $payment->course->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Amount Paid:</span>
                <span class="value amount">₹{{ number_format($payment->amount_paid, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Payment Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($payment->date_of_payment)->format('F d, Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Course Duration:</span>
                <span class="value">{{ $payment->course->duration }} months</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Total Course Fee:</span>
                <span class="value">₹{{ number_format($payment->course->duration * $payment->course->fee_per_month, 2) }}</span>
            </div>
            
            @php
                $totalPaid = $payment->student->payments()
                    ->where('course_id', $payment->course_id)
                    ->sum('amount_paid');
                $totalFee = $payment->course->duration * $payment->course->fee_per_month;
                $remaining = $totalFee - $totalPaid;
            @endphp
            
            <div class="detail-row">
                <span class="label">Total Paid So Far:</span>
                <span class="value">₹{{ number_format($totalPaid, 2) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Remaining Balance:</span>
                <span class="value" style="color: {{ $remaining > 0 ? '#ff9800' : '#4CAF50' }};">
                    ₹{{ number_format(max(0, $remaining), 2) }}
                    @if($remaining <= 0)
                        <strong>(Fully Paid)</strong>
                    @endif
                </span>
            </div>
        </div>
        
        @if($remaining > 0)
        <p><strong>Note:</strong> You have a remaining balance of ₹{{ number_format($remaining, 2) }} for this course. Please continue making payments to complete your enrollment.</p>
        @else
        <p><strong>Congratulations!</strong> You have completed all payments for this course. Thank you for your commitment to your education!</p>
        @endif
        
        <p>If you have any questions regarding this payment or your account, please contact our administration office.</p>
        
        <p>Best regards,<br>
        <strong>Fee Management System</strong></p>
    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply to this message.</p>
        <p>&copy; {{ date('Y') }} Fee Management System. All rights reserved.</p>
    </div>
</body>
</html>
