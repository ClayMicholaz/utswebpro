# Lost & Found Web Application

A PHP + MySQL web application for reporting lost and found items, browsing reports, and verifying item return claims.

Repository: https://github.com/ClayMicholaz/utswebpro

## Overview

This project helps users:

- Report lost items
- Report found items
- Browse and search item reports
- View item details

It also includes an admin workflow:

- Admin-only dashboard
- Claim return approval flow
- Approved returns recorded in `item_claims`

## Tech Stack

- PHP (server-side)
- MySQL (database)
- HTML/CSS/JavaScript (frontend)

## Features

- User authentication:
  - Register
  - Login
  - Change password
  - Logout
- Role-based redirect after login:
  - `user` -> `pages/home.php`
  - `admin` -> `pages/admin_dashboard.php`
- Lost/found reporting with optional image upload
- Lost/found item listing pages with:
  - Search by item name or report ID
  - Date filter
- Item detail page
- Admin dashboard with:
  - Role protection
  - Lost and found report overview
  - Search/filter/date controls
  - Return approval form
  - Auto-update of `found_reports.status` to `returned`
  - Insert approved claim into `item_claims`
- Shared site layout components:
  - Reusable navbar (`includes/navbar.php`)
  - Reusable footer (`includes/footer.php`)
  - Shared layout styles (`assets/layout.css`)

## Current Project Structure

```text
utswebpro/
|- index.php
|- lafdb.sql
|- README.md
|- assets/
|  |- admin_dashboard.css
|  |- change_password.css
|  |- home.css
|  |- home.js
|  |- item_detail.css
|  |- items.css
|  |- layout.css
|  |- login.css
|  |- register.css
|  |- report.css
|- auth/
|  |- change_password.php
|  |- change_password_process.php
|  |- login.php
|  |- login_process.php
|  |- logout.php
|  |- register.php
|  |- register_process.php
|- config/
|  |- database.php
|- controllers/
|  |- authController.php
|  |- reportFoundController.php
|  |- reportLostController.php
|- core/
|  |- auth.php
|- includes/
|  |- footer.php
|  |- functions.php
|  |- header.php
|  |- navbar.php
|- pages/
|  |- admin_dashboard.php
|  |- found_items.php
|  |- home.php
|  |- item_detail.php
|  |- lost_items.php
|  |- report_found.php
|  |- report_lost.php
|- screenshots/
|- uploads/
	 |- found/
	 |- lost/
```

## Database

Database schema file: `lafdb.sql`

Main tables:

- `users`
- `lost_items`
- `found_items`
- `lost_reports`
- `found_reports`
- `item_claims`

## Installation (XAMPP)

1. Clone the repository:

   ```bash
   git clone https://github.com/ClayMicholaz/utswebpro.git
   ```

2. Move project folder to XAMPP htdocs:

   ```text
   C:\xampp\htdocs\utswebpro
   ```

3. Start Apache and MySQL from XAMPP Control Panel.

4. Create database and import schema:
   - Open phpMyAdmin
   - Create database: `lafdb`
   - Import: `lafdb.sql`

5. Run in browser:

   ```text
   http://localhost/utswebpro
   ```

## Notes

- `index.php` redirects to `auth/login.php`.
- Admin dashboard is intentionally separate from the shared navbar/footer layout.

## Authors

- Clay Micholaz
- Bun Jantolio
- Egner Constatin

Project: Mid-Term Web Programming Exam (UTS Web Programming)
