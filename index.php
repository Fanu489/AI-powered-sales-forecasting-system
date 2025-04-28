<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI-Powered Forecasting System</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #0d1b33;
            color: #ffffff;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 0;
        }
        
        /* Hero section */
        .hero {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            min-height: 80vh;
        }
        
        .hero-content {
            flex: 1;
            min-width: 300px;
            padding-right: 20px;
        }
        
        .hero-image {
            flex: 1;
            min-width: 300px;
            text-align: right;
        }
        
        .hero-image img {
            max-width: 100%;
            height: 500px;
            width:600px;
            border-radius: 10px;
        }
        
        h1 {
            font-size: 4rem;
            font-weight: bold;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .subtitle {
            font-size: 1.2rem;
            margin-bottom: 40px;
            max-width: 600px;
        }
        
        .btn {
            display: inline-block;
            background-color: #1de9b6;
            color: #0d1b33;
            padding: 15px 40px;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background-color: #00bfa5;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        /* Features section */
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-top: 80px;
            gap: 20px;
        }
        
        .feature {
            flex: 1;
            min-width: 300px;
            margin-bottom: 40px;
        }
        
        .feature-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            margin-right: 15px;
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .feature-description {
            color: #b0bec5;
            font-size: 1rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-content, .hero-image {
                flex: 100%;
                text-align: center;
                padding: 20px 0;
            }
            
            .subtitle {
                margin: 0 auto 40px auto;
            }
            
            .feature {
                flex: 100%;
                text-align: center;
            }
            
            .feature-header {
                justify-content: center;
                flex-direction: column;
            }
            
            .feature-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?php
    // You can set these variables dynamically from a database or CMS
    $pageTitle = "AI-Powered Sales Forecasting System";
    $pageSubtitle = "Experience unparalleled accuracy and efficiency in your forecasting needs.";
    $ctaButtonText = "Get Started";
    
    // Features array
    $features = [
        [
            "icon" => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#1de9b6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"></path><path d="M18.4 9l-6-6-7 7"></path></svg>',
            "title" => "Real-Time Data Analysis",
            "description" => "Our system processes data in real time, ensuring you have the most up-to-date information at your finger."
        ],
        [
            "icon" => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#1de9b6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>',
            "title" => "Customizable Dashboards",
            "description" => "Tailor your dashboard to display the metrics that matter most to you and your business."
        ],
        [
            "icon" => '<svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#1de9b6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16.7a4 4 0 0 1-4 4H4a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4h8.5c1.2 0 2.3.5 3.2 1.4l3.9 3.9c.9.9 1.4 2 1.4 3.2v4.2z"></path></svg>',
            "title" => "Seamless Integration",
            "description" => "Easily integrate our forecasting system with your existing tools and platforms."
        ]
    ];
    ?>

    <div class="container">
        <section class="hero">
            <div class="hero-content">
                <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
                <p class="subtitle"><?php echo htmlspecialchars($pageSubtitle); ?></p>
                <a href="views/users/register.php" class="btn"><?php echo htmlspecialchars($ctaButtonText); ?></a>
            </div>
            <div class="hero-image">
                <img src="index image.jpg" alt="AI Forecasting Illustration">
            </div>
        </section>

        <section class="features">
            <?php foreach ($features as $feature): ?>
                <div class="feature">
                    <div class="feature-header">
                        <div class="feature-icon">
                            <?php echo $feature["icon"]; ?>
                        </div>
                        <h3 class="feature-title"><?php echo htmlspecialchars($feature["title"]); ?></h3>
                    </div>
                    <p class="feature-description"><?php echo htmlspecialchars($feature["description"]); ?></p>
                </div>
            <?php endforeach; ?>
        </section>
    </div>

    <?php
    // Simple form processing example
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"] ?? "";
        $email = $_POST["email"] ?? "";
        $message = $_POST["message"] ?? "";
        
        // Here you would typically:
        // 1. Validate the input
        // 2. Sanitize the data
        // 3. Send an email or store in database
        // 4. Redirect or show success message
        
        // For demonstration purposes:
        $formSuccess = true;
    }
    ?>

    <!-- You can add a contact form section here if needed -->
    <!-- 
    <div class="container">
        <section id="contact" class="contact">
            <h2>Contact Us</h2>
            <?php if (isset($formSuccess)): ?>
                <div class="success-message">Thank you for your message! We'll get back to you soon.</div>
            <?php else: ?>
                <form method="post" action="#contact">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>
                    <button type="submit" class="btn">Send Message</button>
                </form>
            <?php endif; ?>
        </section>
    </div>
    -->

    <footer>
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> AI-Powered Forecasting System. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>