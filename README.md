                         🏦 Vastrust Backend

A modern, lightweight Banking API built with vanilla PHP using the MVC architecture.
It provides secure endpoints for user registration, authentication, transactions, admin operations, and more.

      Features
1. User Management

- Register and verify user accounts via email OTP

- Login and logout

- Password reset via email verification

- Transaction PIN setup and reset (with verification)

- Secure authentication with hashed passwords

2. Transactions

- Deposit functionality

- Funds transfer between users

- Transaction logging and tracking

- Beneficiary auto-deletion after timeout

3. Admin Operations

- View all users and their accounts

- Activate, deactivate, or update user details

- View all transactions

- Change user passwords or transaction PINs




     Architecture Highlights

- MVC (Model–View–Controller) pattern

- RESTful route handling (regex-based)

- Centralized email verification system

- Clean reusable database layer

- Reusable ResponseHelper and MailerHelper

- PHPMailer (SMTP) integration for all email operations




        Project Structure

Vastrust/
│
├── App/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── TransactionController.php
│   │   ├── AdminController.php
│   │   └── RegController.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── Transaction.php
│   │   └── Verification.php
│   │
│   ├── Services/
│   │   ├── AuthService.php
│   │   ├── TransactionService.php
│   │   ├── VerificationService.php
│   │   └── MailerHelper.php
│   │
│   ├── Core/
│   │   ├── Model.php
│   │   ├── Controller.php
│   │   ├── ResponseHelper.php
│   │   └── Request.php
│   │git
│   └── Validators/
│       ├── RegistrationValidator.php
│       ├── PasswordValidator.php
│       └── PinValidator.php
│
├── config/
│   └── database.php
│
├── public/
    ├──.htaccess
│   └── index.php
│
├── routes/
│   └── api.php
│
└── README.md

    `` Installation & Setup
1️⃣ Clone the Repository
git clone https://github.com/Ollypumping/vastrust.git
cd vastrust

2️⃣ Configure the Environment

Create a database in phpMyAdmin (e.g., vastrust_db), then edit:

config/database.php


3️⃣ Start the Server

If using XAMPP, move the project into:

C:\xampp\htdocs\vastrust


Then use on postman:

http://localhost/vastrust/public


Or start the PHP server manually:

php -S localhost:8000 -t public

             API Overview
    Authentication
Method	Endpoint	Description
POST	/register	Register new user
POST	/login	Login user
POST	/verify	Verify email via OTP
POST	/forgot-password	Request password reset email
POST	/reset-password	Reset password with OTP



     Transactions
Method	Endpoint	Description
POST	/transfer/{id}	Transfer funds
POST	/deposit/{id}	Deposit into account
POST	/withdraw/{id}	Withdraw from account
GET	    /transactions/{id}	Fetch user transactions


     Admin
Method	Endpoint	Description
GET	/admin/{adminId}/users	Fetch all users
GET	/admin/{adminId}/users/{userId}/accounts	Get user accounts
PATCH	/admin/{adminId}/users/{userId}/change-password	Change user password
PATCH	/admin/{adminId}/users/{userId}/change-pin	Change user PIN
PATCH	/admin/{adminId}/users/{userId}/update	Update user details
GET	    /admin/{adminId}/transactions	Get all transactions


     Email Setup
All verification and password reset emails are handled via PHPMailer using Gmail SMTP.



     Utilities

ResponseHelper for Standard JSON response structure
MailerHelper for Handling all email sending logic
VerificationService	Centralized verification for registration, reset, PIN setup
PasswordValidator	Validates password complexity and length


     Common Issues
- “No connection could be made because the target machine actively refused it”

        MySQL isn’t running. Start it in XAMPP Control Panel.

        Check your DB host (127.0.0.1), port (3306), and credentials.

- Emails not sending

        Enable “Less secure app access” or use App Passwords for Gmail.

        Ensure correct SMTP config in MailerHelper.




                                         Author
                                      Olayemi Ojo
