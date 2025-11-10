# Student Dashboard - Quick User Guide

## Accessing the Dashboard

**URL**: `http://127.0.0.1:8000/student/dashboard`

**Login Credentials** (from seeders):
- Email: `student1@example.com` / Password: `password`
- Email: `student2@example.com` / Password: `password`
- Email: `student3@example.com` / Password: `password`

## Dashboard Sections

### 1. Profile Information Card (Left Side)
**What you'll see:**
- Your profile picture icon
- Full name
- Email address
- Phone number
- Date of birth
- Address
- Current status (Active/Inactive/Suspended/Rejected)

**Color-coded Status Badges:**
- 🟢 Green (Active) - You can access all features
- ⚪ Gray (Inactive) - Account temporarily inactive
- 🟡 Yellow (Suspended) - Account suspended
- 🔴 Red (Rejected) - Account rejected

### 2. Fee Summary Card (Right Side)
**Three main metrics displayed:**

1. **Total Fees** (Purple card)
   - Shows the total amount you need to pay for all enrolled courses

2. **Amount Paid** (Pink card)
   - Shows how much you've already paid

3. **Balance Due** (Yellow card)
   - Shows remaining amount to be paid

**Progress Bar:**
- Visual representation of payment completion
- Shows percentage paid
- Animated and color-coded

**Refresh Button:**
- Click to reload latest fee information
- Icon spins while loading

**Record New Payment Button:**
- Opens payment form modal
- Disabled if all fees are paid

### 3. Enrolled Courses Section
**For each course, you'll see:**
- 📚 Course name and fee per month
- ⏱️ Course duration (in months)
- 💵 Total course fee
- ✅ Amount you've paid
- ❗ Balance remaining
- Progress bar showing payment status

**Visual Indicators:**
- Green text = Fully paid
- Red text = Payment pending
- Progress bar shows percentage completed

**Hover Effect:**
- Cards lift up when you hover over them
- Background changes slightly

### 4. Payment Recording Modal
**How to record a new payment:**

1. Click the **"Record New Payment"** button in the Fee Summary card
2. A popup form will appear
3. **Select Course**: Choose from dropdown (shows remaining balance)
4. **Enter Amount**: Type payment amount
   - Maximum limited to course balance
   - Minimum ₹0.01
5. **Select Date**: Choose payment date (cannot be future date)
6. Click **"Submit Payment"** button

**Validation:**
- ⚠️ All fields are required (marked with red asterisk *)
- ⚠️ Amount cannot exceed course balance
- ⚠️ Date cannot be in the future
- ⚠️ You cannot pay for already fully-paid courses

**After Submission:**
- ✅ Success message appears
- 💰 Fee details automatically refresh
- 📊 Progress bars update
- ❌ Modal closes automatically

### 5. Payment History Section
**View all your past payments:**
- Serial number
- Payment date
- Course name
- Amount paid (in green badge)
- Payment status (Paid)

**Table Features:**
- Hover over rows for highlighting
- Responsive design (scrolls on mobile)
- Total amount shown at bottom

## Interactive Features

### 🔄 Auto-Refresh
- Fee details refresh after every payment
- Click refresh button for manual update

### 🎨 Visual Feedback
- Loading spinners during data fetch
- Hover effects on cards
- Animated progress bars
- Color-coded status indicators

### ✅ Success Messages
- Appear at top of page
- Auto-dismiss after 5 seconds
- Can be manually closed

### ❌ Error Messages
- Appear at top of page
- Show what went wrong
- Can be manually closed

### 📱 Responsive Design
- Works on desktop, tablet, and mobile
- Layout adjusts to screen size
- Touch-friendly buttons on mobile

## Tips & Tricks

### 💡 Quick Actions
1. **Check Total Balance**: Look at "Balance Due" card
2. **View Course-wise Balance**: Scroll to "Enrolled Courses"
3. **Record Payment**: Click big blue button in Fee Summary
4. **Refresh Data**: Click refresh button with sync icon
5. **View Payment History**: Scroll to bottom section

### 📊 Understanding Progress Bars
- **Green**: Percentage of fees paid
- **Gray**: Remaining unpaid portion
- **100%**: All fees paid for that course

### 🎯 Common Actions

**To pay for a specific course:**
1. Note the course name and balance from "Enrolled Courses"
2. Click "Record New Payment"
3. Select that course from dropdown
4. Enter amount (up to balance shown)
5. Submit

**To check payment history:**
1. Scroll to "Payment History" section
2. All payments listed with dates
3. Total amount shown at bottom

**To see overall progress:**
1. Look at Fee Summary card
2. Check progress bar percentage
3. Compare "Paid" vs "Balance Due"

## Keyboard Shortcuts

- `Esc` - Close payment modal
- `Tab` - Navigate between form fields
- `Enter` - Submit payment form (when focused)

## Mobile Usage

### Portrait Mode (Phone)
- Cards stack vertically
- Full-width layout
- Large touch-friendly buttons

### Landscape Mode (Tablet)
- Side-by-side cards
- Better space utilization
- Optimized table view

## Troubleshooting

### ⚠️ "Loading dashboard data..." stuck?
- Check internet connection
- Refresh page (F5)
- Check if server is running

### ⚠️ Error messages appearing?
- Read the error message carefully
- Common issues:
  - Network error: Server might be down
  - Validation error: Check form inputs
  - Authentication error: Try logging in again

### ⚠️ Payment modal not opening?
- Check if "Record New Payment" button is enabled
- If disabled, all fees might be paid already
- Refresh the page

### ⚠️ Data not updating?
- Click the refresh button
- Hard refresh page (Ctrl+F5)
- Clear browser cache

## Feature Limitations

### ❌ Cannot do:
- Edit past payments (contact admin)
- Delete payments (contact admin)
- Change payment dates (contact admin)
- Enroll in new courses (contact admin)
- Edit profile information (contact admin)

### ✅ Can do:
- View all your information
- Record new payments
- Track payment progress
- View payment history
- Refresh fee details

## Security Notes

🔒 **Your Data is Secure:**
- All connections encrypted
- Only you can see your data
- Cannot access other students' information
- Automatic logout after inactivity

🔐 **Best Practices:**
- Logout when finished
- Don't share login credentials
- Use strong password
- Clear browser history on shared computers

## Support & Help

### Need Help?
- Contact system administrator
- Email: admin@example.com
- Check documentation files
- Report bugs to admin

### Found a Bug?
- Note what you were doing
- Take screenshot if possible
- Report to administrator
- Include error message

## Browser Recommendations

### ✅ Recommended Browsers:
- Google Chrome (latest)
- Mozilla Firefox (latest)
- Microsoft Edge (latest)
- Safari (latest)

### ❌ Not Supported:
- Internet Explorer 11 or older
- Very old browser versions
- Browsers with JavaScript disabled

## Performance Tips

### 🚀 For Best Performance:
- Use updated browser
- Good internet connection
- Clear cache regularly
- Close unused tabs
- Enable JavaScript

## Privacy

### 🔐 Your Privacy:
- Only your data is visible
- Admin can view for management
- Data not shared with third parties
- Secure storage and transmission

## Updates & Changes

### 📢 Stay Updated:
- Check for announcements
- New features added regularly
- System may be updated
- Refresh page for latest version

---

**Dashboard Version**: 1.0.0  
**Last Updated**: November 10, 2025  
**Need Help?**: Contact your administrator

## Quick Reference Card

```
┌─────────────────────────────────────────┐
│         STUDENT DASHBOARD               │
├─────────────────────────────────────────┤
│                                         │
│  📊 VIEW FEES       → Fee Summary Card  │
│  💳 PAY FEES        → Record Payment    │
│  📚 VIEW COURSES    → Enrolled Courses  │
│  📜 VIEW HISTORY    → Payment History   │
│  🔄 REFRESH DATA    → Refresh Button    │
│  👤 VIEW PROFILE    → Profile Card      │
│                                         │
└─────────────────────────────────────────┘
```

**Happy Learning! 🎓**
