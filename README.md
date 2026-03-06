# 📱 CESAE Parking Management App

![Laravel](https://img.shields.io/badge/Laravel-10-red)
![PHP](https://img.shields.io/badge/PHP-8.x-blue)
![MySQL](https://img.shields.io/badge/MySQL-Database-orange)
![Tailwind](https://img.shields.io/badge/TailwindCSS-UI-38bdf8)
![Status](https://img.shields.io/badge/status-development-yellow)

## 📌 Project Overview

The **CESAE Parking Management App** is a web application developed to manage the **CESAE Digital parking lot** in a **fair, automated, and efficient way**.

The system allows employees to **reserve parking spaces**, while administrators maintain full control over:

* parking spots
* reservations
* users
* system rules

The application was designed using a **Mobile First approach**, with future support for **Progressive Web App (PWA)** functionality, enabling installation on mobile devices and quick access to reservations.

---

# 🎯 Objectives

The main goals of this project are:

* Ensure **fair distribution of parking spaces**
* Automate the **reservation and parking management process**
* Allow **security staff to validate reservations on-site**
* Provide a **simple and mobile-friendly interface**
* Offer a **complete administrative management system**

---

# 🚗 Parking Structure

The parking lot consists of:

| Type              | Quantity | Description                         |
| ----------------- | -------- | ----------------------------------- |
| Total spaces      | 10       | Total available parking spots       |
| Reserved spaces   | 3        | Fixed spaces for CESAE vehicles     |
| Rotational spaces | 7        | Available for employee reservations |

### Reserved spaces include

* 1 space for the **electric van**
* 2 spaces for **CESAE vehicles**

### Admin configuration options

Administrators can:

* add or remove parking spots
* define **fixed or rotational spaces**
* temporarily **block spaces** (events, maintenance, etc.)

---

# 👥 User Roles

## 👑 Administrator

Administrators have full system control and can:

* manage parking spaces
* manage reservations
* manage users
* configure system rules
* view reservation history and statistics

---

## 👨‍💼 Employee

Employees can:

* reserve a parking space
* cancel reservations
* view upcoming reservations
* access personal reservation history

---

## 🛡 Security

Security staff are responsible for:

* validating reservations on-site
* verifying parking occupancy

---

# ⚙️ Main Features

* 🚗 Parking reservation system
* ⏳ Automatic **waiting list** when parking is full
* ⚠️ **Penalty system** for late cancellations
* 📊 Reservation management dashboard
* 👥 User role management
* 📱 **Mobile-optimized interface**

---

# 🧠 Technologies Used

### Backend

* **Laravel**
* **PHP**

### Frontend

* **Blade Templates**
* **Tailwind CSS**
* **JavaScript**

### Database

* **MySQL**

---

# 📱 Future Improvements

The project is prepared to evolve into a **Progressive Web App (PWA)** with:

* 📲 installation on mobile devices
* 🔔 push notifications
* ⚡ faster loading and offline capabilities
* 📱 native-app-like experience

---

# 🛠 Installation

To run the project locally:

### 1️⃣ Clone the repository

```bash
git clone https://github.com/your-username/cesae-parking-app.git
cd cesae-parking-app
```

### 2️⃣ Install dependencies

```bash
composer install
npm install
```

### 3️⃣ Configure environment

Copy the example environment file:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

Configure your database in `.env`.

---

### 4️⃣ Run migrations

```bash
php artisan migrate
```

---

### 5️⃣ Start the development server

```bash
php artisan serve
```

The application will be available at:

```
http://localhost:8000
```

---

# 📂 Project Structure

```
app/
resources/
 ├── views
 ├── css
 └── js
routes/
database/
public/
```

Main components:

* **Controllers** → application logic
* **Models** → database interaction
* **Views** → Blade templates for the interface
* **Routes** → application routing

---

# 📚 Academic Context

This project was developed as part of the **Final Project of the Web Development Course at CESAE Digital**.

The goal was to apply knowledge in:

* full-stack web development
* database design
* application architecture
* user experience design

---

# 👨‍💻 Author

Developed by:

**Catarina Rato**
**Graça Neves**
**Jandira Pedrosa**
**Sara Carvalho**


CESAE Digital
Web Development Program
