# Smart Attendance System (Edu-Tech)
### ITM Gorakhpur — CSE Department | Session 2025-26
**Author:** Shiva Giri
---

## 📁 Project Structure

```
edu-tech/
├── frontend/
│   ├── login.html              ← Login page (entry point)
│   ├── student_dashboard.html  ← Student portal
│   ├── teacher_dashboard.html  ← Teacher portal
│   └── admin_dashboard.html    ← Admin/HOD portal
├── api/
│   ├── login.php               ← Authentication API
│   ├── markAttendance.php      ← Mark attendance API
│   ├── getAttendance.php       ← Fetch attendance API
│   ├── getNotices.php          ← Notices API
│   └── logout.php              ← Logout
├── config/
│   └── db_connection.php       ← Database configuration
├── database.sql                ← MySQL schema + sample data
└── README.md                   ← This file
```

---


---

## 🛠️ Technology Stack
| Layer      | Technology                    |
|------------|-------------------------------|
| Frontend   | HTML5, CSS3, JavaScript (ES6) |
| Backend    | PHP 8.x                       |
| Database   | MySQL 8.0                     |
| Server     | Apache (XAMPP/WAMP)           |
| Fonts      | Google Fonts (Sora)           |

---

## 👥 User Roles
- **Student** — View attendance, notices, fee status
- **Teacher** — Mark attendance, upload notices, view reports
- **Admin/HOD** — Full management, analytics, reports

---

## 🔐 Security Features
- Password hashing with `password_hash()`
- Session-based authentication
- Role-based access control (RBAC)
- SQL injection prevention via prepared statements
- XSS protection

---

## 📊 Features
- Real-time attendance tracking
- Subject-wise attendance with percentage
- Circular progress indicator
- Color-coded status (Green ≥75%, Yellow 65-75%, Red <75%)
- CSV report export
- Notice board system
- Admin analytics dashboard with charts
- Responsive design (mobile/tablet/desktop)

---

*Project developed as Mini Project for B.Tech CSE Semester III, 2025-26*
