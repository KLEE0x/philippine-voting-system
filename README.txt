PROJECT NAME
VOTEHUB - Philippine Voting System 

DESCRIPTION
VH is a PHP and MySQL voting management system created for school project demonstration. It is inspired by the voting process in the Philippines, but it is not an official COMELEC platform. The system includes role-based access, voter records, candidate and position management, ballot submission, vote counting, public results, and audit logs.

MERGED LOVABLE UI DESIGN
This updated version uses the Lovable-generated VoteWise/PVMS frontend as visual reference. The React/Tailwind design was converted into plain PHP, HTML, CSS, and basic JavaScript so it can run directly in XAMPP without npm, Vite, React, TypeScript, or Tailwind build commands.

TECHNOLOGIES USED
- PHP
- HTML
- CSS
- Basic JavaScript
- MySQL
- phpMyAdmin
- XAMPP

HOW TO INSTALL IN XAMPP
1. Extract the ZIP file.
2. Make sure the final folder name is:
   philippine-voting-system
3. Copy the folder to:
   C:\xampp\htdocs\
4. Start Apache and MySQL in XAMPP.
5. Open phpMyAdmin:
   http://localhost/phpmyadmin
6. Import the SQL file:
   philippine-voting-system/database/philippine_voting_system.sql
7. Open the system in your browser:
   http://localhost/philippine-voting-system

IMPORTANT FOLDER STRUCTURE
Correct:
C:\xampp\htdocs\philippine-voting-system\index.php
C:\xampp\htdocs\philippine-voting-system\config
C:\xampp\htdocs\philippine-voting-system\database
C:\xampp\htdocs\philippine-voting-system\assets

Wrong:
C:\xampp\htdocs\philippine-voting-system\philippine-voting-system\index.php

DEFAULT LOGIN ACCOUNTS
Admin:
username: admin
password: admin123

Election Officer:
username: officer
password: officer123

Auditor:
username: auditor
password: auditor123

Voter:
username: voter1
password: voter123

DATABASE
Database name:
philippine_voting_system

SQL file:
database/philippine_voting_system.sql

NOTES
- Do not re-import the SQL file if you already added important records and you only want to update the design.
- Re-importing the SQL file can reset your database.
- If you see "Index of /philippine-voting-system", your folder is probably nested inside another folder. Move index.php and the project folders directly inside C:\xampp\htdocs\philippine-voting-system.

FUTURE INNOVATION FEATURES
The system includes an Innovation Features page for future enhancements such as QR code voter verification, biometric verification, SMS/email confirmation, blockchain-based audit trail, real-time results dashboard, precinct map, COMELEC-style transmission simulation, election machine simulation, voter education, and fraud detection dashboard.
