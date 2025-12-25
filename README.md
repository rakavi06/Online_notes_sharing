# Online Notes Sharing System (ONSS)

A web-based application for managing and sharing notes online. Users can register, upload notes with multiple files, manage their profile, and share their notes with others.

## Features

- **User Registration & Authentication**: Secure user registration and login system
- **User Dashboard**: View statistics about uploaded notes and files
- **Notes Management**: 
  - Add notes with title, subject, and description
  - Upload multiple files (PDF, DOC, DOCX, TXT, JPG, PNG, GIF)
  - Edit existing notes
  - Delete notes and associated files
- **Profile Management**: Update user profile information (name, mobile, email)
- **Change Password**: Secure password change functionality

## Technical Specifications

- **Frontend**: HTML, CSS, Bootstrap 5, jQuery
- **Backend**: PHP
- **Database**: MySQL
- **IDE**: VS Code
- **Software Required**: XAMPP

## Installation

### Prerequisites

1. Install XAMPP (includes Apache, MySQL, PHP)
2. Start Apache and MySQL services from XAMPP Control Panel

### Setup Steps

1. **Clone or download the project** to your XAMPP `htdocs` folder:
   ```
   C:\xampp\htdocs\online_notes_sharing
   ```

2. **Create the database**:
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the SQL file: `database/schema.sql`
   - Or manually create database `onss_db` and import the schema

3. **Configure database connection**:
   - Edit `config/database.php` if needed
   - Default settings:
     - Host: localhost
     - User: root
     - Password: (empty)
     - Database: onss_db

4. **Set permissions**:
   - Ensure `assets/uploads/` directory has write permissions
   - On Windows, this is usually automatic
   - On Linux/Mac: `chmod 755 assets/uploads/`

5. **Access the application**:
   - Open browser and navigate to: `http://localhost/online_notes_sharing/`
   - Or: `http://localhost/online_notes_sharing/index.php`

## Project Structure

```
online_notes_sharing/
├── index.php                 # Landing page
├── signup.php                # User registration
├── login.php                 # User login
├── dashboard.php             # User dashboard
├── notes.php                 # Notes listing
├── add_notes.php            # Add notes page
├── edit_notes.php           # Edit notes page
├── profile.php              # User profile
├── change_password.php      # Change password
├── logout.php               # Logout handler
├── config/
│   └── database.php         # Database configuration
├── includes/
│   ├── auth.php             # Authentication helpers
│   ├── header.php           # Common header
│   └── footer.php           # Common footer
├── assets/
│   ├── css/
│   │   └── style.css        # Custom styles
│   ├── js/
│   │   └── main.js          # Custom JavaScript
│   └── uploads/              # Uploaded files directory
├── database/
│   └── schema.sql           # Database schema
└── README.md                # This file
```

## Database Schema

### Tables

1. **users**: User information
   - id, name, mobile, email, password, created_at

2. **notes**: Notes information
   - id, user_id, title, subject, description, created_at, updated_at

3. **note_files**: Uploaded files for notes
   - id, note_id, file_name, file_path, uploaded_at

## Usage

1. **Registration**: 
   - Click "Join" or "Sign Up" to create a new account
   - Fill in name, mobile, email, and password

2. **Login**: 
   - Use registered email and password to login

3. **Dashboard**: 
   - View statistics after login
   - Navigate to different sections using sidebar

4. **Add Notes**: 
   - Click "Add Notes" from notes page
   - Fill in title, subject, description
   - Upload up to 4 files
   - Click "Add" to save

5. **Edit Notes**: 
   - Click "Edit" on any note
   - Modify information
   - Add or delete files
   - Click "Update" to save

6. **Delete Notes**: 
   - Click "Delete" on any note
   - Confirm deletion

7. **Update Profile**: 
   - Navigate to Profile section
   - Update name, mobile, or email
   - Click "Update Profile"

8. **Change Password**: 
   - Navigate to Profile section
   - Click "Change Password" from dropdown
   - Enter current and new password
   - Click "Change Password"

## Security Features

- Password hashing using PHP `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with input sanitization
- File upload validation (type and size)
- Session-based authentication
- CSRF protection ready

## File Upload

- **Allowed Types**: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, GIF
- **Maximum Size**: 5MB per file
- **Maximum Files**: 4 files per note
- **Storage**: Files are stored in `assets/uploads/` directory

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Edge (latest)
- Safari (latest)

## Troubleshooting

1. **Database Connection Error**:
   - Check XAMPP MySQL service is running
   - Verify database credentials in `config/database.php`
   - Ensure database `onss_db` exists

2. **File Upload Error**:
   - Check `assets/uploads/` directory exists and has write permissions
   - Verify PHP `upload_max_filesize` and `post_max_size` settings
   - Check file size is within 5MB limit

3. **Session Issues**:
   - Ensure PHP sessions are enabled
   - Check `session.save_path` in php.ini

4. **404 Errors**:
   - Verify project is in correct XAMPP htdocs folder
   - Check Apache service is running
   - Verify .htaccess file (if used)

## Development

### Customization

- **Styling**: Edit `assets/css/style.css`
- **JavaScript**: Edit `assets/js/main.js`
- **Database**: Modify `database/schema.sql` and re-import

### Adding Features

1. Create new PHP files in root directory
2. Include `includes/header.php` and `includes/footer.php`
3. Use `requireLogin()` for protected pages
4. Use `getDBConnection()` for database operations

## License

This project is open source and available for educational purposes.

## Author

Online Notes Sharing System - Web Development Project

## Version

1.0.0

