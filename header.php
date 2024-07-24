<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Project</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo base_url(); ?>asset/css/style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="header.css">

    <style>
        #layoutSidenav_content {
            position: absolute;
            right: 0;
            left: 195px;
            top: 70px;
        }
        /* General styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .nav {
            display: flex;
            flex-direction: column;
            grid-gap: 10px; /* Optional: Adds spacing between items */
        }
        .nav a {
            text-decoration: none;
            position: relative;
            margin: 0.5rem 0;
        }
        .top-border {
            background-color: #343a40; /* Dark background */
            color: #ffffff;
            display: flex;
            flex-shrink: 0;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            left: 0;
            right: 0;
            height: 67px;
            z-index: 3;
        }
        .topborder li a{
            color: #f4f2ee;
    text-decoration: none;
    font-size: 20px;
    left: 10px;
    position: relative;
        }

        #layoutSidenav {
            display: flex;
            height: 100vh;
        }

        #layoutSidenav_nav {
            width: 12%; /* Sidebar width */
            background-color: #343a40; /* Dark background */
            padding: 1rem;
            border-right: 1px solid #000; /* Black border */
            position: fixed;
            top: 60px;
            bottom: 0;
            z-index: 5;
        }

        .sidebar-link {
            color: #ffffff;
            text-decoration: none;
            padding: 0.5rem 1rem;
            display: block;
        }
        .sidebar-link:hover {
            background-color: #495057; /* Hover effect */
        }

        .ellipsis {
            position: relative;
        }
        #navbarDropdown{
            right: 23px;
    position: absolute;
        }
        .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            background-color: #343a40; /* Dark background for dropdown */
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
            z-index: 1000;
            border-radius: 0.25rem;
            padding: 0.5rem 0;
        }

        .dropdown-item {
            color: #ffffff;
            padding: 0.25rem 1.5rem;
            display: flex;
            align-items: center;
        }
        .dropdown-item a{
            color: Black;
            text-decoration: none;
        }
        .dropdown-item:hover {
            background-color: #495057;
        }

        .dropdown-item img {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }

        .show {
            display: block;
            /* display: block; */
    background-color: white;
        }

    </style>
</head>
<body class="sb-nav-fixed">
    <?php if (is_admin_login()): ?>
    <div class="top-border">
        <li style="list-style: none; padding: 10px;" class="breadcrumb-item fs-4 name"><a style="color: white; text-decoration: none; position:relative; font-size:20px; left : 20px;" href="index.php">Dashboard</a></li>
        <div class="ellipsis">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" style="color: white;
    text-decoration: none;">
                &#8230; <!-- Ellipsis character -->
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                <li class="dropdown-item">
                    <!-- <img src="path/to/logo.png" alt="Logo">  -->
                    <a href="profile.php"><b>Profile</b></a>
                </li>
                <li class="dropdown-item">
                    <!-- <img src="path/to/logo.png" alt="Logo"> -->
                    <a href="setting.php"><b>Setting</b></a>
                </li>
                <li class="dropdown-item">
                    <!-- <img src="path/to/logo.png" alt="Logo"> -->
                    <a href="logout.php"><b>Logout</b></a>
                </li>
            </ul>
        </div>
    </div>
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <a class="sidebar-link" href="category.php">Category</a>
                        <a class="sidebar-link" href="author.php">Author</a>
                        <a class="sidebar-link" href="location_rack.php">Location Rack</a>
                        <a class="sidebar-link" href="book.php">Book</a>
                        <a class="sidebar-link" href="user.php">User</a>
                        <a class="sidebar-link" href="issue_book.php">Issue Book</a>
                        <a class="sidebar-link" href="logout.php">Logout</a>
                    </div>
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <?php endif; ?>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const dropdownToggle = document.getElementById('navbarDropdown');
                    const dropdownMenu = document.querySelector('.dropdown-menu');

                    dropdownToggle.addEventListener('click', function(event) {
                        event.preventDefault();
                        dropdownMenu.classList.toggle('show');
                    });

                    document.addEventListener('click', function(event) {
                        if (!dropdownToggle.contains(event.target) && !dropdownMenu.contains(event.target)) {
                            dropdownMenu.classList.remove('show');
                        }
                    });
                });
            </script>
</body>
</html>
