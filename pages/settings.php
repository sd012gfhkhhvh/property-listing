<?php
require_once __DIR__ . '/../crest/crest.php';
require_once __DIR__ . '/../crest/settings.php';

// Retrieve session data
session_start();
if (isset($_SESSION['properties'])) {
    $properties = $_SESSION['properties'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['watermarkImage']) && $_FILES['watermarkImage']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../assets/';
        $uploadFile = $uploadDir . 'watermark.png';

        // Check if the file is a valid image
        $allowedTypes = ['image/png', 'image/jpeg', 'image/gif'];
        $fileType = mime_content_type($_FILES['watermarkImage']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            // Move the uploaded file to the assets directory
            if (move_uploaded_file($_FILES['watermarkImage']['tmp_name'], $uploadFile)) {
                echo "File uploaded successfully.";
            } else {
                echo "Error moving the uploaded file.";
            }
        } else {
            echo "Invalid file type. Please upload a valid image (PNG, JPEG, GIF).";
        }
    }

    header('Location: settings.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing - Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        .dropdown-menu.show { display: block; }
        label {
            cursor: pointer;
            border: 2px solid transparent;
            padding: 10px;
            border-radius: 10px;
        }
        @media (max-width: 767.98px) {
            #sidebar {
                position: fixed;
                left: -250px;
                transition: left 0.3s ease-in-out;
                z-index: 1040;
            }
            #sidebar.active { left: 0; }
            .main-content {
                margin-left: 0 !important;
                transition: margin-left 0.3s ease-in-out;
            }
            #sidebarToggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            #sidebar.active ~ #sidebarToggle { left: 270px; }
            #sidebarClose { display: none; }
            #sidebar.active #sidebarClose { display: block; }
        }
        @media (min-width: 768px) {
            #sidebarToggle, #sidebarClose { display: none; }
        }
        #sidebar .nav-link {
            color: #333;
            transition: background-color 0.3s, color 0.3s;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background-color: #f8f9fa;
            color: #007bff;
        }
        #sidebar .nav-link i {
            width: 20px;
            text-align: center;
        }
        /* New styles */
        body {
            background-color: #f8f9fa;
        }
        #sidebar {
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        #sidebar .nav-link {
            border-radius: 0.25rem;
            margin: 0.25rem 0.5rem;
        }
        #sidebar .nav-link:hover, #sidebar .nav-link.active {
            background-color: #e9ecef;
            color: #0056b3;
        }
        .main-content {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            margin: 1rem;
            padding: 1.5rem;
        }
        .form-control, .form-select {
            border-color: #ced4da;
        }
        .form-control:focus, .form-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav id="sidebar" class="bg-white sidebar sticky-top shadow-sm" style="width: 250px; height: 100vh; overflow-y: auto;">
            <div class="position-sticky">
                <div class="px-4 d-flex justify-content-between align-items-center">
                    <!-- <h2 class="mb-0 fw-bold text-primary">Property Listing</h2> -->
                    <button id="sidebarClose" class="btn btn-link d-md-none text-dark">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
                <ul class="nav flex-column py-3">
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 d-flex align-items-center" href="/">
                            <i class="fa-solid fa-home me-3"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 d-flex align-items-center" href="../pages/properties.php">
                            <i class="fa-solid fa-home me-3"></i>Properties
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 d-flex align-items-center" href="../pages/listing_agents.php">
                            <i class="fa-solid fa-user-group me-3"></i>Listing Agents
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 d-flex align-items-center" href="../pages/locations.php">
                            <i class="fa-regular fa-map me-3"></i>Locations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 d-flex align-items-center" href="../pages/bayut_locations.php">
                            <i class="fa-solid fa-map-pin me-3"></i>Bayut Locations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 d-flex align-items-center" href="../pages/landlords.php">
                            <i class="fa-solid fa-house-user me-3"></i>Landlords
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 d-flex align-items-center" href="../pages/developers.php">
                            <i class="fa-solid fa-helmet-safety me-3"></i>Developers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-3 px-4 d-flex align-items-center active" href="../pages/settings.php">
                            <i class="fa-solid fa-gear me-3"></i>General Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Toggle button for sidebar -->
        <button id="sidebarToggle" class="btn btn-primary d-md-none position-fixed rounded-circle" style="top: 20px; left: 20px; z-index: 1050; width: 50px; height: 50px;">
            <i class="fa-solid fa-bars"></i>
        </button>

        <!-- Main Content -->
        <div class="flex-grow-1" style="height: 100vh; overflow-y: auto;">
            <!-- Fixed Topbar -->
            <?php 
                $availability = 0;
                $listings = 0;			
                foreach ($properties as $property) {
                    $availability++;
                    if (isset($property['status']) && $property['status'] == 'ufCrm83Status') {
                        $listings++;
                    }
                } 
            ?>
            <div class="bg-white shadow-sm" style="position: sticky; top: 0; z-index: 1000;">
                <div class="container-fluid py-2">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 d-flex align-items-center">
                            <div class="me-4">
                                <span class="fs-6 fw-light me-2">Availability:</span>
                                <span class="badge bg-success rounded-pill"><?= $availability ?></span>
                            </div>
                            <div>
                                <span class="fs-6 fw-light me-2">Listings:</span>
                                <span class="badge bg-primary rounded-pill"><?= $listings ?></span>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mt-2 mt-md-0">
                            <form class="d-flex">
                                <input class="form-control me-2" type="search" placeholder="Search properties..." aria-label="Search">
                                <button class="btn btn-outline-primary" type="submit">Search</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Content -->
            <div class="container-fluid px-4 py-4">
                <h2 class="display-6 fw-bold text-primary mb-4">Settings</h2>
                <div class="main-content">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Edit Image</h5>
                                        <img src="../assets/watermark.png?<?php echo time(); ?>" alt="logo" class="img-fluid mb-3 rounded">
                                        <input type="file" name="watermarkImage" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">General Settings</h5>
                                        <div class="mb-3">
                                            <label for="listingReference" class="form-label">Listing Reference</label>
                                            <input type="text" class="form-control" id="listingReference" value="">
                                        </div>
                                        <div class="mb-3">
                                            <label for="website" class="form-label">Website</label>
                                            <input type="text" class="form-control" id="website">
                                        </div>
                                        <div class="mb-3">
                                            <label for="companyName" class="form-label">Company Name</label>
                                            <input type="text" class="form-control" id="companyName">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rent OR Sale in Reference</label>
                                            <select class="form-select">
                                                <option>Select option</option>
                                                <option>Rent</option>
                                                <option>Sale</option>
                                            </select>
                                        </div>
                                        <div class="mb-3 form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="watermark" checked>
                                            <label class="form-check-label" for="watermark">Watermark</label>
                                        </div>
                                        <div class="mb-3 form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="agentIdInReference">
                                            <label class="form-check-label" for="agentIdInReference">Agent ID in Reference</label>
                                        </div>
                                        <div class="mb-3 form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="agentCanEditLiveListing" checked>
                                            <label class="form-check-label" for="agentCanEditLiveListing">Agent Can Edit Live Listing</label>
                                        </div>
                                        <div class="mb-3 form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="agentCanEditLiveListingImagesOnly" checked>
                                            <label class="form-check-label" for="agentCanEditLiveListingImagesOnly">Agent Can Edit Live Listing Images Only</label>
                                        </div>
                                        <div class="mb-3 form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="agentCanEditPendingListings">
                                            <label class="form-check-label" for="agentCanEditPendingListings">Agent Can Edit Pending Listings</label>
                                        </div>
                                        <div class="mb-3 form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="emailNotification">
                                            <label class="form-check-label" for="emailNotification">Email Notification</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-4">Save Changes</button>
                    </form>  
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="./js/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.add('active');
                sidebarToggle.style.left = '270px';
            });

            sidebarClose.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarToggle.style.left = '20px';
            });

            // Close sidebar when clicking outside of it
            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggleButton = sidebarToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggleButton && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    sidebarToggle.style.left = '20px';
                }
            });

            // Add active class to current page link
            const currentPage = window.location.pathname.split("/").pop();
            const navLinks = document.querySelectorAll('#sidebar .nav-link');
            navLinks.forEach(link => {
                if (link.getAttribute('href').includes(currentPage)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>