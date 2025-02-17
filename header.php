<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-commerce Site</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .top-bar {
            background: #000;
            color: white;
            padding: 5px 0;
            font-size: 0.85rem;
        }

        .navbar {
            padding: 10px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand img {
            width: 100px;
            height: auto;
        }

        .nav-link {
            font-weight: 500;
            color: #333 !important;
            margin: 0 8px;
            font-size: 0.95rem;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: #007bff !important;
        }

        .search-form {
            position: relative;
            margin-right: 10px;
        }

        .search-form input {
            border-radius: 15px;
            padding: 6px 15px;
            border: 1px solid #ddd;
            width: 220px;
            font-size: 0.9rem;
        }

        .search-form button {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: none;
            color: #666;
        }

        .header-icons {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-icon {
            color: #333;
            font-size: 1rem;
            position: relative;
            text-decoration: none;
        }

        .cart-count {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #ff4444;
            color: white;
            border-radius: 50%;
            padding: 2px 5px;
            font-size: 0.7rem;
        }

        @media (max-width: 991px) {
            .search-form {
                margin: 10px 0;
            }
            
            .search-form input {
                width: 100%;
            }

            .header-icons {
                margin-top: 10px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar text-center">
        <div class="container">
            Free shipping on orders over $50 | Shop Now!
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="images/image.png" alt="Brand Name">
            </a>
    
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="new-arrivals.php">New Arrivals</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sale.php">Sale</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                </ul>
                
                <form class="search-form d-flex">
                    <input class="form-control" type="search" placeholder="Search products..." aria-label="Search">
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <div class="header-icons">
                    <a href="login.php" class="header-icon">
                        <i class="fas fa-user"></i>
                    </a>
                    <a href="wishlist.php" class="header-icon">
                        <i class="fas fa-heart"></i>
                    </a>
                    <a href="cart.php" class="header-icon">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-count">0</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
