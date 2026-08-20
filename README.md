# MorningMug — Coffee Ordering Website

A full-stack coffee ordering web application built with plain HTML, CSS, Vanilla JavaScript, PHP, and MySQL. Developed as part of the DecodeLabs Full Stack Development internship projects.

## Project Overview

MorningMug is a functional coffee ordering website where customers can browse the menu, add items to their cart, place orders, and manage their account. An admin panel allows full product and order management.

This project demonstrates:

=>Project 1 — Responsive Frontend Interface: HTML, CSS, and JavaScript with responsive design, clean UI, and basic user interactions

=>Project 2 — Backend API Development: PHP REST API with GET, POST, PUT, DELETE endpoints, server-side validation, and JSON responses

=>Project 3 — Database Integration: MySQL database with full CRUD operations, relational schema, and prepared statements

=>Project 4 — Frontend & Backend Integration: Connect frontend with PHP backend APIs, send requests, display dynamic data, and handle basic errors and responses

## Features

### Customer
- Browse coffee products by category
- Add products to cart and adjust quantities
- Place orders (guest or logged-in)
- Register and login
- View order history
- Contact form

### Admin
- Dashboard with stats (products, orders, users, messages)
- Full product management (Create, Read, Update, Delete)
- View and update order statuses
- View contact messages


## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | HTML5, CSS3, Vanilla JavaScript |
| Backend | PHP 8 (no frameworks) |
| Database | MySQL via XAMPP |
| Local Server | Apache (XAMPP) |


## Database Schema

users
  id, name, email, password (bcrypt), role, created_at

products
  id, name, description, price, category, image, available, created_at

orders
  id, user_id (FK → users), total_price, status, created_at

order_items
  id, order_id (FK → orders), product_id (FK → products), quantity, unit_price

contact_messages
  id, name, email, message, created_at


## Security Notes

- Passwords are hashed using PHP `password_hash()` (bcrypt)
- All database queries use PDO prepared statements — no raw user input in SQL
- Server-side validation on all form submissions
- Prices are recalculated server-side from the database on every order — client-submitted prices are not trusted


## Author

Mahlet Belete
mahletbelete4@gmail.com
DecodeLabs Full Stack Development Internship  
2026


## License

This project is for educational purposes as part of the DecodeLabs internship program.
=======
# DecodeLabs-Internship
Full-stack web development internship
>>>>>>> 98ae12b3d05dbf595d6115ba6516de4d50dc839e
