# Contact Form PHP Backend

This backend handles contact form submissions using PHPMailer to send emails via SMTP with app password authentication.

## Setup Instructions

### 1. Prerequisites
- PHP 7.4 or higher
- A web server (Apache/Nginx) or PHP built-in server
- Gmail account with app password enabled

### 2. Gmail App Password Setup
1. Go to your Google Account settings
2. Enable 2-factor authentication if not already enabled
3. Go to Security → App passwords
4. Generate a new app password for "Mail"
5. Copy the 16-character password (without spaces)

### 3. Configuration
Edit `contact-form.php` and update these settings:

```php
// Replace with your Gmail credentials
$mail->Username = 'your-email@gmail.com'; // Your Gmail address
$mail->Password = 'your-app-password'; // The 16-character app password

// Replace with recipient email
$mail->addAddress('recipient@example.com', 'Recipient Name'); // Where emails should be sent
```

### 4. File Permissions
Make sure the PHP files have appropriate permissions:
```bash
chmod 644 contact-form.php
chmod 644 .htaccess
```

### 5. Testing Locally
You can test the backend locally using PHP's built-in server:

```bash
cd backend
php -S localhost:8000
```

Then update the frontend to use: `http://localhost:8000/contact-form.php`

### 6. Deployment
When deploying to a live server:

1. **Update the frontend URL**: Change the `PHP_BACKEND_ENDPOINT` in `src/pages/Contact.js` to your live backend URL
2. **Ensure HTTPS**: Make sure your backend is served over HTTPS for security
3. **Server Requirements**: Ensure your hosting provider supports PHP and outgoing SMTP connections
4. **Firewall**: Some hosting providers block SMTP ports - contact them if needed

### 7. Troubleshooting

#### Common Issues:

**"SMTP connect() failed"**
- Check if your hosting provider allows SMTP connections on port 587
- Verify Gmail credentials are correct
- Ensure app password is properly generated

**"Authentication failed"**
- Double-check the app password (should be 16 characters, no spaces)
- Make sure you're using the app password, not your regular Gmail password
- Verify 2FA is enabled on your Google account

**"CORS errors"**
- Make sure the `.htaccess` file is being processed by your web server
- Check that `mod_headers` is enabled in Apache
- For Nginx, you'll need to add CORS headers in the server configuration

**"Connection timed out"**
- Some hosting providers block SMTP ports for security
- Consider using a transactional email service like SendGrid or Mailgun
- Check with your hosting provider about SMTP restrictions

### 8. Alternative Email Services

If Gmail SMTP doesn't work with your hosting, consider these alternatives:

#### SendGrid (Recommended)
```php
$mail->isSMTP();
$mail->Host = 'smtp.sendgrid.net';
$mail->SMTPAuth = true;
$mail->Username = 'apikey';
$mail->Password = 'your-sendgrid-api-key';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
```

#### Mailgun
```php
$mail->isSMTP();
$mail->Host = 'smtp.mailgun.org';
$mail->SMTPAuth = true;
$mail->Username = 'your-mailgun-smtp-username';
$mail->Password = 'your-mailgun-smtp-password';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port = 587;
```

### 9. Security Considerations

- Never commit your actual credentials to version control
- Use environment variables for sensitive data in production
- Consider implementing rate limiting to prevent spam
- Add CAPTCHA verification for additional security
- Regularly rotate app passwords

### 10. Email Template Customization

The email template is defined in the `$emailBody` variable. You can customize:
- Colors and styling
- Layout and formatting
- Additional fields to display
- Branding elements

### 11. Monitoring

Consider adding logging to track:
- Successful email deliveries
- Failed attempts
- Form submission statistics
- Error patterns

This will help you monitor the health of your contact form system.

