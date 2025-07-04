# 🌐 E-commerce Web Application – Food, Shopping, Travel

Welcome to the **E-commerce Web Application** built by **JAYASURYA-KK**!  
This all-in-one platform allows users to:

- 🍔 **Order food online**
- 🛍️ **Shop for products**
- ✈️ **Book travel services**

All under one seamless, user-friendly system.

---

## 📦 Modules Included

### 🍔 Food Ordering
- Browse restaurant menus
- Add food items to cart
- Place and track food orders

### 🛍️ Online Shopping
- Search and view product listings
- Add to cart and checkout
- Admin panel to manage inventory and orders

### ✈️ Travel Booking
- Explore and book travel packages
- View deals, pricing, and destinations
- Submit booking and contact inquiries

---

## 🧰 Tech Stack

| Layer     | Technology                      |
|-----------|----------------------------------|
| Frontend  | HTML, CSS, JavaScript, Bootstrap |
| Backend   | PHP                              |
| Database  | MySQL                            |
| Server    | Apache (XAMPP/WAMP)              |

---

## 📬 Contact Form with EmailJS (Setup Guide)

Your `contact.php` file uses **EmailJS** to send form submissions directly to your email **without needing a backend**.

### 🔧 1. Create an EmailJS Account
- Visit 👉 [https://www.emailjs.com/](https://www.emailjs.com/)
- Click **Get Started for Free**
- Sign up using your email or Google

### 🛠️ 2. Add an Email Service
- Go to the **Email Services** tab
- Click **"Add New Service"**
- Choose your email provider (Gmail, Outlook, etc.)
- Authorize and copy your **Service ID**  
  👉 Example: `service_xxxxxxx`

### 🧾 3. Create an Email Template
- Go to the **Email Templates** tab
- Click **"Create New Template"**
- Add the following fields (must match your form):
user_name
user_email
user_phone
user_subject
message
current_date
- Save and copy your **Template ID**  
👉 Example: `template_xxxxxxx`

### 🔑 4. Get Your Public Key
- Go to **Account > API Keys**
- Copy your **Public Key**  
👉 Example: `QLxxxxxxxxxxxxxxxx`

### 💡 5. Update IDs in `contact.php`

Inside the `<script>` section of your form:

```js
emailjs.init('YOUR_PUBLIC_KEY'); // Example: QLxxxxxxxxxxxxxxxx

emailjs.send('YOUR_SERVICE_ID', 'YOUR_TEMPLATE_ID', templateParams);
```
🔁 Replace the placeholders with your actual values from EmailJS.

🔐 Admin Panel Credentials
Use the following credentials to log in to the admin panels:

🛠️ Module	👤 Username	🔑 Password
🌐 Main Admin	admin	admin
🛍️ Shopping Admin	surya@gmail.com	surya@2007
🍔 Food Admin	surya	surya@2007
✈️ Travel Admin	surya	surya@2007
📂 Project Setup (Local)
Clone the Repository

bash
Copy
Edit
git clone https://github.com/JAYASURYA-KK/E-commerce.git
Move Project to Server Directory

XAMPP: C:/xampp/htdocs/E-commerce

WAMP: C:/wamp/www/E-commerce

Start Apache and MySQL via XAMPP/WAMP

Import the MySQL Database

Visit: http://localhost/phpmyadmin

Create a new database (e.g., ecommerce)

Import the .sql file (if available)
