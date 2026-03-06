🔎 Lost & Found Web Application

A simple Lost and Found Web Application developed for the Mid-Term Web Programming Exam (UTS Web Programming).

This web application allows users to report lost items, report found items, and browse through the listings to help reconnect items with their rightful owners.

🔗 Repository:
https://github.com/ClayMicholaz/utswebpro

📌 Project Overview

The Lost & Found App is designed to assist people in reporting and locating lost belongings. Users can post information about items they have lost or found and search through reports submitted by others.

This project demonstrates fundamental full-stack web development skills using front-end and back-end technologies.

🛠 Technologies Used
Technology	Description
HTML5	Page structure
CSS3	Styling and layout
Bootstrap	Responsive UI framework
JavaScript	Client-side interactivity and form validation
PHP	Server-side scripting
MySQL	Database for storing item reports
✨ Features

📄 Report lost items

📄 Report found items

🔍 Browse lost and found item listings

📑 View item details

📱 Responsive UI using Bootstrap

✔ Basic form validation using JavaScript

💾 Store and retrieve item data using PHP and MySQL

📂 Project Structure
utswebpro
│
├── index.php                # Home page
├── login.php                # Login page
├── register.php             # User registration
│
├── pages/
│   ├── lost_items.php
│   ├── found_items.php
│   ├── report_lost.php
│   ├── report_found.php
│   └── item_detail.php
│
├── config/
│   └── database.php         # Database connection
│
├── controllers/
│   ├── reportLostController.php
│   ├── reportFoundController.php
│   └── authController.php
│
├── includes/
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   └── functions.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   ├── js/
│   │   └── script.js
│   │
│   ├── images/
│   │
│   └── uploads/
│
├── database/
│   └── lost_found.sql
│
└── README.md
🚀 Installation & Setup

Follow these steps to run the project locally:

1️⃣ Clone the repository
git clone https://github.com/ClayMicholaz/utswebpro.git
2️⃣ Move the project to your server directory

Example for XAMPP:

htdocs/

Example path:

C:\xampp\htdocs\utswebpro
3️⃣ Start Apache and MySQL

Open XAMPP Control Panel and start:

Apache

MySQL

4️⃣ Import the database

Open phpMyAdmin

Create a new database

lost_found_db

Import the SQL file from:

database/lost_found.sql
5️⃣ Run the project

Open your browser:

http://localhost/utswebpro
📸 Screenshots

You can add screenshots of the application here.

Example:

screenshots/homepage.png
screenshots/report-item.png
screenshots/items-list.png
🎓 Academic Purpose

This project was developed as part of the Mid-Term Examination for the Web Programming course.

The objective of this project is to demonstrate understanding of:

Front-end web development

Responsive UI design

Client-side scripting

Server-side development using PHP

Database interaction with MySQL

👨‍💻 Author

Name: Clay Micholaz, Bun Jantolio, Egner Constatin
Course: Web Programming
Project: Mid-Term Exam (UTS Web Programming)
