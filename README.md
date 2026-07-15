# MANIMĀRAN STUDIOS 8 | Enterprise Auth System

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)

## 📌 Project Overview
This project is a full-stack, enterprise-grade user authentication and management system developed for **MANIMĀRAN STUDIOS 8**. It provides a complete, secure lifecycle for user identity management, going beyond basic login functionality by implementing role-based access control (RBAC), real-world SMTP email pipelines, Google OAuth 2.0 single sign-on (SSO), and robust activity auditing.

## ✨ UI & Branding (Thriall)
The interface is built around the MANIMĀRAN STUDIOS 8 brand guidelines and the *Thriall* label. The application features a highly polished user experience, including:
* **Modern Auth Layouts:** Split hero-and-form layouts for optimal spatial balance on landing screens.
* **Premium Styling:** Glassmorphism-style cards, refined gradients, and high-contrast navigation (`css/theme.css`).
* **Admin Experience:** A dedicated, branded left-sidebar layout for administrators.

## 🚀 Core Features
* **Secure Authentication:** Standard registration and login using PHP's native `password_hash()` (bcrypt encryption).
* **Google OAuth 2.0 Integration:** Seamless "Sign in with Google" functionality utilizing the official Google API Client for password-less access.
* **SMTP Password Recovery:** Secure password reset pipeline generating time-sensitive, randomized tokens and emailing recovery links via **PHPMailer**.
* **Role-Based Access Control (RBAC):** Distinct routing for standard users and administrators to prevent unauthorized access.
* **Admin Dashboard & Management:** A centralized control panel to view registered users, verify roles, and manage/delete accounts.
* **Activity Auditing:** Automated logging system that tracks the IP address and exact timestamp of every successful authentication event.

## 🗂️ Project Structure
```text
📦 auth-system
 ┣ 📂 assets
 ┃ ┗ 📜 Manimaran-Studios-logo.png
 ┣ 📂 css
 ┃ ┗ 📜 theme.css
 ┣ 📂 includes
 ┃ ┣ 📜 auth_check.php
 ┃ ┗ 📜 db.php
 ┣ 📜 admin.php
 ┣ 📜 config.php (Git Ignored)
 ┣ 📜 forgot-password.php
 ┣ 📜 google-callback.php
 ┣ 📜 google-login.php
 ┣ 📜 login.php
 ┣ 📜 profile.php
 ┣ 📜 register.php
 ┗ 📜 user-logs.php
```

## 🛠️ Technology Stack
| Component | Technology Used |
| :--- | :--- |
| **Frontend** | HTML5, CSS3, Bootstrap 5 (Zephyr Theme), Bootstrap Icons |
| **Backend** | PHP 8 (Vanilla) |
| **Database** | MySQL |
| **Libraries** | PHPMailer, Google API Client, SweetAlert2 |
| **Dependency Manager**| Composer |

## 🗄️ Database Schema
The system utilizes two primary relational tables:

**1. `users` Table** (Stores identity and credentials)
* `id` (Primary Key, Auto-Increment)
* `name`, `email` (Unique)
* `password` (Nullable for Google Sign-In users)
* `google_id` (Unique identifier from OAuth)
* `role` ('user' or 'admin')
* `reset_token`, `reset_token_expires` (For password recovery)

**2. `login_logs` Table** (Tracks authentication events)
* `id` (Primary Key, Auto-Increment)
* `user_id` (Foreign Key linked to `users`)
* `login_time` (Timestamp)
* `ip_address` (User's network IP)

## ⚙️ Installation & Setup

**1. Clone the Repository**
Clone this repository into your local server environment (e.g., `C:\xampp\htdocs\auth-system`).

**2. Install Dependencies**
Navigate to the project folder in your terminal and install the required PHP libraries using Composer:
> `composer install`

**3. Database Configuration**
* Open PHPMyAdmin and create a new database named `auth_system`.
* Import the provided `.sql` file to generate the `users` and `login_logs` tables.
* Update `includes/db.php` with your local database credentials if they differ from XAMPP defaults.

**4. Environment Variables & API Keys**
For security, sensitive keys are isolated. Update the following files with your credentials:
* **`config.php`:** Add your Google Cloud Console OAuth 2.0 `Client ID` and `Client Secret` here (this file is excluded from version control via `.gitignore`).
* **`forgot-password.php`:** Add your Gmail address and 16-character Google App Password for SMTP functionality.

## 🔒 Security Note
This repository utilizes a strictly configured `.gitignore` file to ensure that the `vendor/` directory and `config.php` (containing OAuth client secrets) are never exposed to public version control.

# Enterprise PHP Authentication System

## 📌 Project Overview
This project is a full-stack, enterprise-grade user authentication and management system built for MANIMĀRAN STUDIOS 8. It goes beyond basic login functionality by implementing secure password hashing, role-based access control (RBAC), activity logging, real-world email integration, and third-party OAuth 2.0 authentication.

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
* **`google-login.php` & `google-callback.php`:** Add your Google Cloud Console OAuth 2.0 `Client ID` and `Client Secret`