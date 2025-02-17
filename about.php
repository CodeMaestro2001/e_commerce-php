<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Dione - Modern Fashion Brand</title>
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
        }

        /* Header styles */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: #fff;
            z-index: 1000;
            height: 60px;
            line-height: 60px;
        }

        /* Navbar specific styles */
        nav.navbar {
            margin: 0 !important;
            padding: 0 !important;
            height: 100%;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Hero section adjustment */
        .hero {
            background-color: #f8f8f8;
            padding: 2rem 0;
            margin-top: 60px; /* Match header height */
            text-align: center;
        }

        .hero h1 {
            font-size: 2.2rem;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .hero p {
            font-size: 1rem;
            color: #666;
            max-width: 800px;
            margin: 5px auto 0 auto;
            line-height: 1.5;
        }

        /* Container adjustments */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Rest of your existing styles */
        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
            margin: 2rem 0;
            align-items: center;
        }

        .about-image {
            width: 100%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .about-content h2 {
            font-size: 1.8rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .about-content p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 0.8rem;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin: 3rem 0;
            text-align: center;
        }

        .feature-card {
            padding: 1.2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .social-links {
            position: fixed;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #333;
            color: white;
            border-radius: 50%;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .social-links a:hover {
            background: #555;
        }

        @media (max-width: 768px) {
            .about-grid {
                grid-template-columns: 1fr;
            }

            .features {
                grid-template-columns: 1fr;
            }

            .social-links {
                position: static;
                flex-direction: row;
                justify-content: center;
                margin: 2rem 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="hero">
        <div class="container">
            <h1>OUR BRAND</h1>
            <p>Welcome to Dione! Where style meets sophistication in every stitch. We craft fashion that empowers the modern woman to express her unique identity through timeless yet trending pieces.</p>
        </div>
    </section>

    <div class="container">
        <!-- Rest of your content remains the same -->
        <div class="about-grid">
            <img src="images/about_image.jpg" alt="Fashion Model" class="about-image">
            <div class="about-content">
                <h2>THE CONCEPT</h2>
                <p>Dione stands apart through our commitment to limited edition pieces that ensure our customers wear truly unique fashion. We don't mass-produce - instead, we carefully curate each collection to maintain exclusivity and quality.</p>
                <p>Our talented team and proven infrastructure allow us to be dynamic, releasing new collections weekly. This approach keeps our styles fresh and our customers ahead of trends. We believe fashion should be both accessible and exceptional.</p>
                <p>Every piece in our collection is thoughtfully designed and crafted with premium fabrics, ensuring both style and comfort for the modern woman's lifestyle.</p>
            </div>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">💳</div>
                <h3>SAFE PAYMENT</h3>
                <p>Secure transactions with all major payment methods accepted. Cash on delivery available.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">💬</div>
                <h3>ONLINE SUPPORT</h3>
                <p>Our dedicated team is always here to help via email, phone, or social media.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3>FREE SHIPPING</h3>
                <p>Complimentary shipping on all orders over Rs. 5500/- nationwide.</p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>