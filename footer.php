<footer class="bg-light pt-5 pb-4">
    <div class="container">
        <div class="row">
            <!-- Newsletter Subscription -->
            <div class="col-md-4">
                <h5 class="fw-bold">BE THE FIRST TO KNOW</h5>
                <form action="subscribe.php" method="POST" class="d-flex">
                    <input type="email" name="email" class="form-control" placeholder="Enter your email address..." required>
                    <button type="submit" class="btn btn-dark ms-2">Sign Up</button>
                </form>
            </div>

            <!-- Customer Service Links -->
            <div class="col-md-3 mt-4 mt-md-0">
                <h5 class="fw-bold">CUSTOMER SERVICE</h5>
                <ul class="list-unstyled">
                    <li><a href="contact.php" class="text-dark text-decoration-none">Contact Us</a></li>
                    <li><a href="delivery.php" class="text-dark text-decoration-none">Delivery</a></li>
                    <li><a href="returns.php" class="text-dark text-decoration-none">Returns and Exchanges</a></li>
                    <li><a href="size-guide.php" class="text-dark text-decoration-none">Size Guide</a></li>
                    <li><a href="privacy.php" class="text-dark text-decoration-none">Privacy Policy</a></li>
                    <li><a href="shipping.php" class="text-dark text-decoration-none">International Shipping</a></li>
                    <li><a href="feedback.php" class="text-dark text-decoration-none">Fashionable Feedback</a></li>
                </ul>
            </div>

            <!-- Discover Section -->
            <div class="col-md-2 mt-4 mt-md-0">
                <h5 class="fw-bold">DISCOVER</h5>
                <ul class="list-unstyled">
                    <li><a href="about.php" class="text-dark text-decoration-none">The Company</a></li>
                </ul>
            </div>

            <!-- Follow Us on Social Media -->
            <div class="col-md-3 mt-4 mt-md-0 text-md-end">
                <h5 class="fw-bold">FOLLOW US ON</h5>
                <a href="#" class="btn btn-dark btn-sm rounded-circle"><i class="bi bi-facebook"></i></a>
                <a href="#" class="btn btn-dark btn-sm rounded-circle"><i class="bi bi-pinterest"></i></a>
                <a href="#" class="btn btn-dark btn-sm rounded-circle"><i class="bi bi-instagram"></i></a>
                <a href="#" class="btn btn-dark btn-sm rounded-circle"><i class="bi bi-envelope"></i></a>
            </div>
        </div>
    </div>

    <!-- Floating Social Media & Rewards Section -->
    <div class="position-fixed bottom-0 start-0 m-3">
        <div class="d-flex flex-column align-items-start">
        <img width="48" height="48" src="https://img.icons8.com/color/48/facebook-new.png" alt="facebook-new"/>            <a href="#" class="mb-2"><img width="48" height="48" src="https://img.icons8.com/fluency/48/instagram-new.png" alt="instagram-new"/></a>
            <a href="#"><img width="48" height="48" src="https://img.icons8.com/color/48/tiktok--v1.png" alt="tiktok--v1"/></a>
        </div>
    </div>

    <!-- Floating Chat Button -->
    <div class="position-fixed bottom-0 end-0 m-3">
        <a href="#" class="btn btn-dark rounded-pill p-3">
            <i class="bi bi-chat"></i> Chat
            <span class="badge bg-danger ms-2">1</span>
        </a>
    </div>

    <!-- Footer Bottom Section -->
    <div class="text-center p-3 mt-3 bg-dark text-white">
        © <?php echo date('Y'); ?> E-commerce Site. All rights reserved.
        <a href="terms.php" class="text-white text-decoration-none ms-3">Terms & Conditions</a> |
        <a href="privacy.php" class="text-white text-decoration-none">Privacy Policy</a>
    </div>
</footer>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
