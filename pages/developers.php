<?php
require_once __DIR__ . '/../crest/crest.php';
require_once __DIR__ . '/../crest/settings.php';

// Retrieve session data
session_start();
if (isset($_SESSION['properties'])) {
    $properties = $_SESSION['properties'];
}

function deleteDeveloper($developerId)
{
    CRest::call('crm.item.delete', [
        'entityTypeId' => DEVELOPERS_ENTITY_TYPE_ID,
        'id' => $developerId
    ]);

    header('Location: developers.php');
}

$result = CRest::call('crm.item.list', [
    'entityTypeId' => DEVELOPERS_ENTITY_TYPE_ID
]);

$developers = $result['result']['items'] ?? [];

$data = $_POST;

if ($data) {
    $response = CRest::call('crm.item.add', [
        'entityTypeId' => DEVELOPERS_ENTITY_TYPE_ID,
        'fields' => [
            'ufCrm93DeveloperName' => $data['developer'],
        ]
    ]);

    header('Location: developers.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing</title>
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
    </style>
</head>
<body>
    <div class="d-flex">
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
                        <a class="nav-link active py-3 px-4 d-flex align-items-center" href="/">
                            <i class="fa-solid fa-home me-3"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active py-3 px-4 d-flex align-items-center" href="../pages/properties.php">
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
                        <a class="nav-link py-3 px-4 d-flex align-items-center" href="../pages/settings.php">
                            <i class="fa-solid fa-gear me-3"></i>General Settings
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <button id="sidebarToggle" class="btn btn-primary d-md-none position-fixed rounded-circle" style="top: 20px; left: 20px; z-index: 1050; width: 50px; height: 50px;">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="flex-grow-1" style="height: 100vh; overflow-y: auto;">
            <div class="bg-white shadow-sm" style="position: sticky; top: 0; z-index: 1000;">
                <div class="container-fluid py-2">
                    <div class="row align-items-center">
                        <div class="col-12 col-md-6 d-flex align-items-center">
                            <div class="me-4">
                                <span class="fs-6 fw-light me-2">Availability:</span>
                                <span class="badge bg-success rounded-pill">
                                    <?= $availability ?? 0 ?>
                                </span>
                            </div>
                            <div>
                                <span class="fs-6 fw-light me-2">Listings:</span>
                                <span class="badge bg-primary rounded-pill">
                                    <?= $listings ?? 0 ?>
                                </span>
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

            <div class="container-fluid px-5 py-4">
                <h2 class="display-10 fw-bold text-primary container">Developers</h2>
                <div class="container mt-4">
                    <button type="button" class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addDeveloperModal">
                        Add Developer
                    </button>

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Developer Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="locationTableBody">
                            <?php foreach ($developers as $developer) : ?>
                                <tr>
                                    <td><?= $developer['id'] ?></td>
                                    <td><?= $developer['ufCrm93DeveloperName'] ?></td>
                                    <td>
                                        <form action="./delete_developer.php" method="POST">
                                            <input type="hidden" name="developerId" value="<?= $developer['id'] ?>">
                                            <input type="hidden" name="entityTypeId" value="<?= DEVELOPERS_ENTITY_TYPE_ID ?>">
                                            <button class="btn btn-danger" type="submit">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="modal fade" id="addDeveloperModal" tabindex="-1" aria-labelledby="addDeveloperModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addDeveloperModalLabel">Add Developer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="addDeveloperForm" method="post" action="./developers.php">
                                    <div class="mb-3">
                                        <label for="developer" class="form-label">Developer Name</label>
                                        <input type="text" class="form-control" id="developer" name="developer" required>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" form="addDeveloperForm">Add Developer</button>
                            </div>
                        </div>
                    </div>
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
            const mainContent = document.querySelector('.flex-grow-1');

            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.add('active');
                sidebarToggle.style.left = '270px';
            });

            sidebarClose.addEventListener('click', function() {
                sidebar.classList.remove('active');
                sidebarToggle.style.left = '20px';
            });

            document.addEventListener('click', function(event) {
                const isClickInsideSidebar = sidebar.contains(event.target);
                const isClickOnToggleButton = sidebarToggle.contains(event.target);

                if (!isClickInsideSidebar && !isClickOnToggleButton && sidebar.classList.contains('active')) {
                    sidebar.classList.remove('active');
                    sidebarToggle.style.left = '20px';
                }
            });

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