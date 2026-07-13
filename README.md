# Enterprise PHP Authentication System

## 📌 Project Overview
This project is a full-stack, enterprise-grade user authentication and management system built for Manimaran Ventures. It goes beyond basic login functionality by implementing secure password hashing, role-based access control (RBAC), activity logging, real-world email integration, and third-party OAuth 2.0 authentication.

## 🚀 Key Features
* **Secure User Authentication:** Registration and login functionality using PHP's native `password_hash()` and `password_verify()` for secure bcrypt encryption.
* **Google OAuth 2.0 Integration:** "Sign in with Google" functionality utilizing the official Google API Client to handle seamless, password-less authentication.
* **Email Password Recovery:** Secure password reset pipeline that generates time-sensitive, randomized tokens and emails users a recovery link via **PHPMailer** and SMTP.
* **Role-Based Access Control (RBAC):** Distinct user roles (Admin vs. Standard User) with protected routing to prevent unauthorized access.
* **Admin Dashboard & Management:** A dedicated control panel where administrators can view all registered users and permanently delete accounts.
* **User Activity Auditing:** An automated logging system that tracks the IP address and exact timestamp of every successful user login.

## 🛠️ Technology Stack
| Component | Technology Used |
| :--- | :--- |
| **Frontend** | HTML5, CSS3, Bootstrap 5 (Zephyr Theme) |
| **Backend** | PHP 8 (Vanilla) |
| **Database** | MySQL (via XAMPP) |
| **Libraries** | PHPMailer, Google API Client, SweetAlert2 |
| **Package Manager** | Composer |

## 🗄️ Database Schema
The system utilizes two primary tables to manage users and activity:

**1. `users` Table**
Stores core identity, credentials, and role data.
* `id` (Primary Key, Auto-Increment)
* `name`, `email` (Unique)
* `password` (Nullable for Google Sign-In users)
* `google_id` (Unique identifier from OAuth)
* `role` ('user' or 'admin')
* `reset_token`, `reset_token_expires` (For password recovery)

**2. `login_logs` Table**
Tracks successful authentication events.
* `id` (Primary Key, Auto-Increment)
* `user_id` (Foreign Key linked to `users`)
* `login_time` (Timestamp)
* `ip_address` (User's network IP)

## ⚙️ Installation & Setup Instructions

**1. Clone the Repository**
Download or clone this repository into your local server environment (e.g., `C:\xampp\htdocs\auth-system`).

**2. Install Dependencies**
Navigate to the project folder in your terminal and install the required PHP libraries using Composer:
> `composer install`

**3. Database Configuration**
* Open PHPMyAdmin and create a new database named `auth_system`.
* Import the provided SQL structure to generate the `users` and `login_logs` tables.
* Update `includes/db.php` with your local database credentials if they differ from the XAMPP defaults.

**4. Environment Variables (API Keys)**
To enable the bonus features, you must update the following files with your own credentials:
* **`forgot-password.php`:** Add your Gmail address and 16-character Google App Password.
* **`google-login.php` & `google-callback.php`:** Add your Google Cloud Console OAuth 2.0 `Client ID` and `Client Secret`.