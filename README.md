# 🌐 E-commerce Web Application – Food, Shopping, Travel

Welcome to the **E-commerce Web Application** built by **JAYASURYA-KK**!  
This all-in-one platform offers users the ability to **order food**, **shop for products**, and **book travel services** — all from one unified system.

---

## 📦 Modules Included

### 🍔 Food Ordering
- Browse restaurant menus
- Add food items to cart
- Place and track food orders

### 🛍️ Online Shopping
- Search and view product listings
- Add to cart and checkout
- Admin panel to manage inventory

### ✈️ Travel Booking
- Book travel packages and destinations
- View offers, details, and pricing
- Contact support for bookings

---

## 🧰 Tech Stack

| Layer       | Technology                  |
|-------------|------------------------------|
| Frontend    | HTML, CSS, JavaScript, Bootstrap |
| Backend     | PHP                          |
| Database    | MySQL                        |
| Server      | Apache (via XAMPP/WAMP)      |

---

## 📬 Contact Form with EmailJS (Setup Guide)
Your contact.php file uses EmailJS to send contact form messages directly to your email — without needing any backend.

Here’s how to set it up:

### 🔧 1. Create an EmailJS Account
Visit 👉 https://www.emailjs.com/

Click Get Started for Free

Sign up using your email or Google account

### 🛠️ 2. Add an Email Service
Go to the Email Services tab

Click "Add New Service"

Choose your email provider (e.g., Gmail, Outlook)

Authorize and connect your email

Copy your Service ID
👉 Example: sexxxxxxxx

🧾 3. Create an Email Template
Go to the Email Templates tab

Click "Create New Template"

Add the following fields (must match your form):

sql
Copy
Edit
user_name  
user_email  
user_phone  
user_subject  
message  
current_date
Save and copy the Template ID
👉 Example: templxxxxxx

### 🔑 4. Get Your Public Key
Go to the Account tab → API Keys

Copy your Public Key
👉 Example: QLxxxxxxxx

### 💡 5. Update These IDs in Your Code
In your contact.php file’s <script> section, update the code like this:

javascript
Copy
Edit

🔁 Replace the IDs with your actual EmailJS values.

