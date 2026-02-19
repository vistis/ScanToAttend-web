# ScanToAttend-web

Web components for ScanToAttend, an IoT-based attendance recording system using fingerprint scanner. This is in partial submission for the final project of CS 397: Internet of Everything, Paragon International University, AY 2025-2026.

The project aims to build a fingerprint-based attendance recording system that can integrate into existings ERP systems, specifically that of Paragon International University. The structure of the web backend tries to mimic the environment of the university structure, with multiple class sections for each course and multiple sessions for each class. The attendance is recorded on a session by session basis.

The backend automatically checks for the session the student is attending when they scan to check-in, prevent overlapping sessions for students and instructors, and aggregate attendance record for many view types.

The frontend provides a human-friendly way to view attendance records and manage the system.

## Technical Information

### Backend

- Framework: `Laravel 12`
- System dependencies:
    - Relational Database (PostgreSQL or MySQL/MariaDB)
    - PHP (check Laravel 12's requirements of PHP modules)
    - Composer
    - Web server (optional)
 
- Username and email address are generated automatically; change email domain in `.env`
- `artisan` Console commands:
    - `admin:add {first-name} {last-name} {password}`: add an admin account
    - `admin:list`: list all admin accounts
    - `admin:change-password {admin-id} {new-password}`: change the password of an admin account
    - `admin:remove`: remove an admin account
    - `mail:update-domain`: update the domain of existing email to a new one set in `.env`
 
- How to run:
    1. Make `.env` and configure file: `cp .env.example .env`
    2. Install package dependencies: `composer install`
    3. Generate app key: `php artisan key:generate`
    4. Run dev server: `php artisan serve`
