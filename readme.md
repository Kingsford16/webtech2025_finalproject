# CRMS – Campus Resource Management System

## Overview
The **Campus Resource Management System (CRMS)** is a web-based application designed to manage **institutional resource booking workflows** within a university or campus environment. The system provides a centralized platform for **students** to request shared resources and for **resource managers** to review, approve, or reject those requests efficiently.

CRMS focuses specifically on **resource allocation, approval workflows**, eliminating the need for paper forms, or informal communication channels.

---

## Problem Statement
In many campus environments, shared resources (such as rooms, equipment, or facilities) are managed manually through paper forms, online forms, or verbal approvals. This often results in:

- Lost or untracked booking requests
- Delayed approval decisions
- Lack of transparency for students
- No centralized record of approved or denied bookings

CRMS solves these issues by introducing a **structured, role-based digital system** for managing campus resources.

---

## Key Features

### 1. Role-Based User Access
CRMS implements strict role-based access control to ensure that users only interact with features relevant to their responsibilities.

- **Students**
  - Submit resource booking requests
  - View booking status (Pending, Approved, Denied)

- **Resource Managers**
  - View all incoming booking requests
  - Approve or deny requests

- **Administrators** (optional extension)
  - Manage system users, resources and resource managers
  - Configure system settings

---

### 2. Resource Booking Workflow
- Students submit booking requests with required details
- Requests are stored in the database with a default `Pending` and `Uncompleted` status
- Resource managers review requests from a dedicated dashboard
- Each request can be approved or denied with a single action

---

### 3. Approval & Rejection Handling
- Approved or denied requests update instantly in the system


---

## System Architecture

CRMS follows a **modular PHP-based architecture**:

- **Frontend**
  - HTML
  - CSS / Tailwind CSS
  - JavaScript

- **Backend**
  - PHP
  - Modular function-based structure

- **Database**
  - MySQL
  - Relational schema for users, resources, resource managers, and bookings

- **Link to live server**
  - [crms-for-students.wuaze.com ↗](http://crms-for-students.wuaze.com)
---

## Project Structure

```
crms/
│
├── student/              # Student booking views and logic
├── staff/                # Resource manager approval dashboards and actions
├── admin/                # Admin dashboards and  add, edit & delete actions
│
├── functions/            # Contains a function file (automate.php) that automates the system to determine when an event (approved requests) starts (by event date, event time) and completes
├── login/                # Register, login and logout files
├── logs/                 # PHP and system error logs
├── css/                  # Contains css files for web pages look and feel
├── db/                   # Contains the MySQL database file (crms.sql) of the web application
│
├── images/               # Contains images in the web application
├── settings/             # Shared backend logic (DB connection, helper functions)
├── logs/                 # Server-level configuration
├── index.php             # Application entry point
└── readme.md             # Project documentation
```

---

## Database Design

The database is centered around the following core tables:

- users
- resources
- resmanagers
- bookings 

Each booking request maintains a lifecycle from submission to final decision.

---

## Security Considerations

- Session-based authentication
- Role-based authorization
- Server-side validation of user input
- Restricted access to approval endpoints
- Centralized error logging

---

## Installation & Setup

1. Clone or download the project repository
2. Place the project folder inside your web server root (e.g., `htdocs`)
3. Create a MySQL database and import the provided SQL schema
4. Configure database credentials in the config directory
5. Ensure the `/logs` directory is writable
6. Start Apache and MySQL services
7. Access the system through your browser (e.g., `localhost/crms`)

---

## Future Enhancements

- Resource availability calendar
- File uploads for booking justification
- Multi-level approval workflows
- REST API for mobile integration
- Automated email upon booking request approval, cancellation and completion
- Advanced reporting and analytics

---

## Academic Context

CRMS was developed as a **computer science academic project**, with emphasis on:

- Backend system design
- Secure role-based workflows
- Real-world campus resource management
- Maintainable and scalable code structure

---

## Author

**CRMS Project Developer**  
Kingsford Ammisah

---

## License

This project is intended strictly for academic and educational purposes.

