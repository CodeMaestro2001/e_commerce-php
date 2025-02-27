<!DOCTYPE html>
<html>
<head>
<style>
.footer {
    background-color: #fff;
    padding: 4rem 0;
    font-family: 'Inter', sans-serif;
    border-top: 1px solid #eee;
}

.footer-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1.5fr;
    gap: 4rem;
}

.footer-brand {
    margin-bottom: 1.5rem;
}

.footer-brand img {
    height: 40px;
    margin-bottom: 1.5rem;
}

.brand-description {
    color: #666;
    line-height: 1.6;
    margin-bottom: 2rem;
}

.footer-heading {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1.5rem;
}

.footer-links {
    list-style: none;
    padding: 0;
}

.footer-links li {
    margin-bottom: 1rem;
}

.footer-links a {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: #000;
}

.contact-info {
    color: #666;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.contact-item i {
    margin-right: 1rem;
    color: #333;
}

.social-icons {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.social-icons a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f5f5f5;
    color: #333;
    transition: all 0.3s ease;
}

.social-icons a:hover {
    background-color: #333;
    color: #fff;
}

@media (max-width: 768px) {
    .footer-container {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
}
</style>
</head>
<body>

<footer class="footer">
    <div class="footer-container">
        <!-- Brand Section -->
        <div class="footer-brand">
            <img src="images/image.png" alt="Dione Logo">
            <p class="brand-description">Welcome to Dione, where fashion meets love. Elevate your style with our premium clothing brand, crafted to celebrate the timeless bond of Dione.</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-pinterest"></i></a>
                <a href="#"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>

        <!-- My Account Section -->
        <div>
            <h3 class="footer-heading">MY ACCOUNT</h3>
            <ul class="footer-links">
                <li><a href="#">Account</a></li>
                <li><a href="#">Orders</a></li>
                <li><a href="#">Wishlist</a></li>
                <li><a href="#">Shopping Cart</a></li>
            </ul>
        </div>

        <!-- Pages Section -->
        <div>
            <h3 class="footer-heading">PAGES</h3>
            <ul class="footer-links">
                <li><a href="#">Size Charts</a></li>
                <li><a href="#">Order Tracking</a></li>
                <li><a href="#">Return Policy</a></li>
                <li><a href="#">About Us</a></li>
            </ul>
        </div>

        <!-- Contact Info Section -->
        <div>
            <h3 class="footer-heading">CONTACT INFO</h3>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Dione Clothing, Sri Lanka</p>
                </div>
                <div class="contact-item">
                    <i class="fab fa-whatsapp"></i>
                    <p>Whatsapp: 0710 99 888 7</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p>Email: Hello@Dione.Lk</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Don't forget to include Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</body>
</html>