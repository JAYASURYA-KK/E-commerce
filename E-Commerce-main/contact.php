<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - JS Weby</title>

    <!-- EmailJS Script -->
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Arial", sans-serif;
        line-height: 1.6;
        color: #333;
        overflow-x: hidden;
        padding-top: 80px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    /* Header Styles */
    .header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 0;
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 2rem;
    }

    .logo {
        font-size: 2rem;
        font-weight: bold;
        background: linear-gradient(45deg, #fff, #f0f0f0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .nav-menu {
        display: flex;
        list-style: none;
        gap: 2rem;
    }

    .nav-menu a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
        border-radius: 25px;
    }

    .nav-menu a:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .auth-buttons {
        display: flex;
        gap: 1rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 25px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .btn-login {
        background: transparent;
        color: white;
        border: 2px solid white;
    }

    .btn-login:hover {
        background: white;
        color: #667eea;
        transform: translateY(-2px);
    }

    .btn-signup {
        background: linear-gradient(45deg, #ff6b6b, #ee5a24);
        color: white;
    }

    .btn-signup:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(238, 90, 36, 0.4);
    }

    /* Main Content Styles */
    .product-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 2rem;
    }

    .container {
        width: 100%;
    }

    .product-box {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .product-main {
        padding: 3rem;
    }

    /* Contact Cards */
    .contact-card-container {
        display: flex;
        align-items: center;
        padding: 2.5rem;
        margin-bottom: 2rem;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
        border-left: 6px solid;
        position: relative;
        overflow: hidden;
    }

    .contact-card-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.1) 100%);
        pointer-events: none;
    }

    .contact-card-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .contact-card-container.mail {
        border-left-color: #4285f4;
    }

    .contact-card-container.whatsapp {
        border-left-color: #25d366;
    }

    .contact-card-container.location {
        border-left-color: #ea4335;
    }

    .contact-icon {
        margin-right: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        position: relative;
        overflow: hidden;
    }

    .contact-icon::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, transparent 0%, rgba(255, 255, 255, 0.2) 100%);
    }

    .contact-icon a {
        color: white;
        text-decoration: none;
        z-index: 1;
        position: relative;
    }

    .contact-icons {
        font-size: 2.8rem;
        color: white;
    }

    .contact-details {
        flex: 1;
    }

    .contact-details h2 {
        font-size: 2rem;
        margin-bottom: 0.8rem;
        color: #333;
        font-weight: 700;
    }

    .contact-details p {
        font-size: 1.1rem;
        color: #666;
        line-height: 1.6;
    }

    .contact-details a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .contact-details a:hover {
        color: #764ba2;
        text-decoration: underline;
    }

    /* Form Section */
    .complaint-form-section {
        margin: 3rem 0;
        padding: 3rem;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        border-left: 6px solid #667eea;
        position: relative;
        overflow: hidden;
    }

    .complaint-form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, transparent 0%, rgba(102, 126, 234, 0.05) 100%);
        pointer-events: none;
    }

    .complaint-form-section h2 {
        font-size: 2.2rem;
        margin-bottom: 2rem;
        color: #333;
        font-weight: 700;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .form-group {
        margin-bottom: 1.8rem;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.8rem;
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e1e5e9;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-family: inherit;
        background: white;
    }

    .form-group input:focus,
    .form-group textarea:focus,
    .form-group select:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        transform: translateY(-2px);
    }

    .form-group textarea {
        min-height: 140px;
        resize: vertical;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .btn-submit {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        padding: 1.2rem 2.5rem;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-submit:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .status-message {
        padding: 1.2rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        font-weight: 600;
        text-align: center;
        font-size: 1.1rem;
        display: none;
    }

    .status-success {
        background: linear-gradient(135deg, #d4edda, #c3e6cb);
        color: #155724;
        border: 2px solid #c3e6cb;
        display: block;
    }

    .status-error {
        background: linear-gradient(135deg, #f8d7da, #f5c6cb);
        color: #721c24;
        border: 2px solid #f5c6cb;
        display: block;
    }

    .config-info {
        background: #e8f5e8;
        border: 2px solid #28a745;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        text-align: center;
        color: #155724;
    }

    /* Map Section */
    .map-section {
        margin-top: 3rem;
        padding: 3rem;
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .map-container {
        width: 100%;
        height: 450px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: 0;
    }

    .map-link {
        margin-top: 1.5rem;
        text-align: center;
    }

    .map-link a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        padding: 0.8rem 2rem;
        border-radius: 25px;
        background: rgba(102, 126, 234, 0.1);
        transition: all 0.3s ease;
        display: inline-block;
    }

    .map-link a:hover {
        background: rgba(102, 126, 234, 0.2);
        transform: translateY(-2px);
    }

    /* Error styling */
    .form-group input.error,
    .form-group textarea.error,
    .form-group select.error {
        border-color: #dc3545;
        box-shadow: 0 0 0 4px rgba(220, 53, 69, 0.1);
    }

    /* Loading state */
    .loading {
        position: relative;
        pointer-events: none;
    }

    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .header-container {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
        }

        .nav-menu {
            flex-wrap: wrap;
            justify-content: center;
        }

        .auth-buttons {
            flex-wrap: wrap;
            justify-content: center;
        }

        .contact-card-container {
            flex-direction: column;
            text-align: center;
            padding: 2rem;
        }

        .contact-icon {
            margin-right: 0;
            margin-bottom: 1.5rem;
        }

        .product-main {
            padding: 2rem 1rem;
        }

        .complaint-form-section {
            padding: 2rem;
        }

        .form-row {
            grid-template-columns: 1fr;
        }

        body {
            padding-top: 140px;
        }

        .map-section {
            margin-top: 2rem;
            padding: 2rem;
        }

        .map-container {
            height: 300px;
        }
    }

    @media (max-width: 480px) {
        .logo {
            font-size: 1.5rem;
        }

        .nav-menu {
            gap: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .contact-details h2 {
            font-size: 1.6rem;
        }

        .contact-details p {
            font-size: 1rem;
        }

        .complaint-form-section h2 {
            font-size: 1.8rem;
        }
    }
    </style>
</head>

<body>
    <!-- HEADER -->
    <header class="header">
        <script src="../google-translate-widget.js"></script>
        <div class="header-container">
            <div class="logo">JS Weby</div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="../home.php">Home</a></li>
                    <li><a href="contact.php" class="menu-title">Contact</a></li>
                    <li><a href="../about.html" class="menu-title">About</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <a href="../Online-Food-Ordering-System-in-PHP-main/login.php" class="btn btn-login">Login</a>
                <a href="../Online-Food-Ordering-System-in-PHP-main/registration.php" class="btn btn-signup">Sign Up</a>
            </div>
        </div>
    </header>

    <!-- MAIN -->
    <main>
        <div class="product-container">
            <div class="container">
                <div class="product-box">
                    <div class="product-main">
                        <!-- Contact Cards -->

                        <!-- MAIL -->
                        <div class="contact-card-container mail">
                            <div class="contact-icon">
                                <a href="mailto:jayasuryak.24cse@kongu.edu">
                                    <ion-icon class="contact-icons mail-icon" name="mail-outline"></ion-icon>
                                </a>
                            </div>
                            <div class="contact-details">
                                <h2>Email</h2>
                                <p>
                                    <a href="mailto:jayasuryak.24cse@kongu.edu">jayasuryak.24cse@kongu.edu</a>
                                </p>
                            </div>
                        </div>

                        <!-- WhatsApp -->
                        <div class="contact-card-container whatsapp">
                            <div class="contact-icon">
                                <a href="https://wa.me/919080418085">
                                    <ion-icon class="contact-icons whatsapp-icon" name="logo-whatsapp"></ion-icon>
                                </a>
                            </div>
                            <div class="contact-details">
                                <h2>WhatsApp</h2>
                                <p>
                                    <a href="https://wa.me/919080418085">+91 9080418085</a>
                                </p>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="contact-card-container location">
                            <div class="contact-icon">
                                <a href="https://goo.gl/maps/kongu-engineering-college">
                                    <ion-icon class="contact-icons location-icon" name="location-outline"></ion-icon>
                                </a>
                            </div>
                            <div class="contact-details">
                                <h2>Location</h2>
                                <p>
                                    Kongu Engineering College<br>
                                    Perundurai, Erode - 638060<br>
                                    Tamil Nadu, India
                                </p>
                            </div>
                        </div>

                        <!-- Contact Form Section -->
                        <div class="complaint-form-section">
                            <h2>Send us a Message</h2>


                            <!-- Status Message -->
                            <div id="statusMessage" class="status-message"></div>

                            <form id="contactForm">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="user_name">Full Name *</label>
                                        <input type="text" id="user_name" name="user_name" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="user_email">Email Address *</label>
                                        <input type="email" id="user_email" name="user_email" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="user_phone">Phone Number</label>
                                        <input type="tel" id="user_phone" name="user_phone">
                                    </div>
                                    <div class="form-group">
                                        <label for="user_subject">Subject *</label>
                                        <select id="user_subject" name="user_subject" required>
                                            <option value="">Select Subject</option>
                                            <option value="General Inquiry">General Inquiry</option>
                                            <option value="Technical Support">Technical Support</option>
                                            <option value="Complaint">Complaint</option>
                                            <option value="Feedback">Feedback</option>
                                            <option value="Business Partnership">Business Partnership</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="message">Message *</label>
                                    <textarea id="message" name="message"
                                        placeholder="Please describe your inquiry or complaint in detail..."
                                        required></textarea>
                                </div>
                                <button type="submit" class="btn-submit" id="submitBtn">
                                    Send Message
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Map Section -->
                    <div class="map-section">
                        <div class="map-container">
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3910.675642841223!2d77.60705317458897!3d11.275873049384325!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ba96f6fda67d5d9%3A0x506a8f3543ed79f7!2sKongu%20Engineering%20College!5e0!3m2!1sen!2sin!4v1717137662205!5m2!1sen!2sin"
                                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                        <div class="map-link">
                            <a href="https://www.google.com/maps/place/Kongu+Engineering+College/@11.275873,77.609628,17z"
                                target="_blank">
                                View Larger Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

    <script>
    // Initialize EmailJS with your public key
    (function() {
        emailjs.init('Qxxxxxxx');
        console.log('✅ EmailJS initialized successfully');
    })();

    // Form handling and validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contactForm');
        const submitBtn = document.getElementById('submitBtn');
        const statusMessage = document.getElementById('statusMessage');
        const inputs = form.querySelectorAll('input, textarea, select');

        console.log('🔧 EmailJS Configuration:');
       

        // Add real-time validation
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });

        function validateField(field) {
            const value = field.value.trim();

            // Remove existing error styling
            field.classList.remove('error');

            // Check if required field is empty
            if (field.hasAttribute('required') && !value) {
                field.classList.add('error');
                return false;
            }

            // Email validation
            if (field.type === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    field.classList.add('error');
                    return false;
                }
            }

            return true;
        }

        function showMessage(message, type) {
            statusMessage.textContent = message;
            statusMessage.className = `status-message status-${type}`;
            statusMessage.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Auto hide success message after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    statusMessage.style.display = 'none';
                }, 5000);
            }
        }

        function resetForm() {
            form.reset();
            inputs.forEach(input => input.classList.remove('error'));
        }

        // Form submission with EmailJS
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            console.log('🚀 Form submission started...');

            // Validate all fields
            let isValid = true;
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                console.log('❌ Validation failed');
                showMessage('Please fill in all required fields correctly.', 'error');
                return;
            }

            console.log('✅ All fields validated successfully');

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Sending...';

            // Prepare template parameters
            const templateParams = {
                user_name: document.getElementById('user_name').value.trim(),
                user_email: document.getElementById('user_email').value.trim(),
                user_phone: document.getElementById('user_phone').value.trim() || 'Not provided',
                user_subject: document.getElementById('user_subject').value,
                message: document.getElementById('message').value.trim(),
                current_date: new Date().toLocaleString('en-IN', {
                    timeZone: 'Asia/Kolkata',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                })
            };

            console.log('📝 Template parameters prepared:', templateParams);

            // Send email using EmailJS with correct template ID
            emailjs.send('sexxxxxx', 'templxxxxxx', templateParams)
                .then(function(response) {
                    console.log('✅ SUCCESS!', response.status, response.text);
                    showMessage('✅ Message sent successfully! We\'ll get back to you soon.',
                        'success');
                    resetForm();
                }, function(error) {
                    console.error('❌ FAILED...', error);

                    // Detailed error handling
                    let errorMessage = '❌ Failed to send message. ';

                    if (error.status === 400) {
                        errorMessage += 'Please check all required fields and try again.';
                    } else if (error.status === 401) {
                        errorMessage += 'Authentication failed. Please contact support.';
                    } else if (error.status === 404) {
                        errorMessage += 'Service not found. Please contact support.';
                    } else if (error.status === 429) {
                        errorMessage += 'Too many requests. Please try again later.';
                    } else {
                        errorMessage +=
                            'Please try again or contact us directly at jayasuryak.24cse@kongu.edu';
                    }

                    showMessage(errorMessage, 'error');
                })
                .finally(function() {
                    // Reset button state
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('loading');
                    submitBtn.textContent = 'Send Message';
                    console.log('🔄 Form submission completed');
                });
        });
    });
    </script>
</body>

</html>
