<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Retrieve session data
session_start();
if (isset($_SESSION['properties'])) {
    $properties = $_SESSION['properties'];
}

$response = CRest::call('crm.item.get', [
    'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
    'id' => $_GET['id']
]);

$property = $response['result']['item'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Listing</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .settings-menu,
        .bulk-options-menu {
            display: none;
        }

        .dropdown-menu.show {
            display: block;
        }

        .property-tag {
            font-size: 0.9rem;
            padding: 0.25rem 0.5rem;
            margin-right: 0.5rem;
        }

        .listing-images .col-md-4 {
            margin-bottom: 15px;
        }

        .listing-images img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .main-image img {
            height: 415px;
        }

        .amenity-badge {
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin: 0.25rem;
            display: inline-block;
            font-size: 0.9rem;
            color: #495057;
            background-color: #fff;
        }

        .section-bg {
            /* background-color: #f8f9fa; */
            padding: 2rem 0;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>

    <div class="d-flex">
        		<!-- Sticky Left Sidebar -->
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

		<!-- Toggle button for sidebar -->
		<button id="sidebarToggle" class="btn btn-primary d-md-none position-fixed rounded-circle" style="top: 20px; left: 20px; z-index: 1050; width: 50px; height: 50px;">
			<i class="fa-solid fa-bars"></i>
		</button>

		<style>
			@media (max-width: 767.98px) {
				#sidebar {
					position: fixed;
					left: -250px;
					transition: left 0.3s ease-in-out;
					z-index: 1040;
				}
				#sidebar.active {
					left: 0;
				}
				.main-content {
					margin-left: 0 !important;
					transition: margin-left 0.3s ease-in-out;
				}
				#sidebarToggle {
					display: flex;
					align-items: center;
					justify-content: center;
				}
				#sidebar.active ~ #sidebarToggle {
					left: 270px;
				}
				#sidebarClose {
					display: none;
				}
				#sidebar.active #sidebarClose {
					display: block;
				}
			}
			@media (min-width: 768px) {
				#sidebarToggle, #sidebarClose {
					display: none;
				}
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

        <!-- Main Content Area -->
        <div class="flex-grow-1" style="height: 100vh; overflow-y: auto;">
            			<!-- Fixed Topbar -->
			 <?php 
			 	$availability = 0;
				 $listings = 0;			
				 foreach ($properties as $property) {
					 $availability++;
					 //fixed a bug
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
								<span class="badge bg-success rounded-pill">
									<?= $availability ?>
								</span>
							</div>
							<div>
								<span class="fs-6 fw-light me-2">Listings:</span>
								<span class="badge bg-primary rounded-pill">
									<?= $listings ?>
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
                <h2 class="display-10 fw-bold text-primary">Property Details</h2>
            <div class="row mb-3">
                <div class="col-md-8">
                    <h1 class="h3"><?php echo $property['title'] ?></h1>
                </div>
                <div class="col-md-4 text-md-end">
                    <h2 class="h3">AED <?php echo $property['ufCrm83Price'] ?></h2>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <p class="text-muted">
                        <i class="fas fa-map-marker-alt"></i> <?php echo $property['ufCrm83PfCity'] . ' - ' . $property['ufCrm83PfCommunity'] . ' - ' . $property['ufCrm83PfSubCommunity'] . ' - ' . $property['ufCrm83PfTower'] ?>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <span class="badge bg-primary property-tag"><?php echo $property['ufCrm83PropertyType'] == 'AP' ? 'Apartment' : $property['ufCrm83PropertyType'] ?></span>
                        <span class="badge bg-secondary property-tag">Beds: <?php echo $property['ufCrm83Bedroom'] ?></span>
                        <span class="badge bg-secondary property-tag">Baths: <?php echo $property['ufCrm83Bathroom'] ?></span>
                        <span class="badge bg-secondary property-tag">Sq Ft: <?php echo $property['ufCrm83Size'] ?></span>
                    </div>

                    <h2>Description</h2>
                    <p><?php echo $property['ufCrm83DescriptionEn'] ?></p>

                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Listed By</h5>
                            <span><?php echo $property['ufCrm83AgentName'] ?></span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">Property Details</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Property ID:</td>
                                    <td><?php echo isset($property['ufCrm83ReferenceId']) ? $property['ufCrm83ReferenceId'] : 'Unavailable'; ?></td>
                                </tr>
                                <tr>
                                    <td>Bedrooms:</td>
                                    <td><?php echo $property['ufCrm83Bedroom'] ?></td>
                                </tr>
                                <tr>
                                    <td>Price:</td>
                                    <td>AED <?php echo $property['ufCrm83Price'] ?></td>
                                </tr>
                                <tr>
                                    <td>Bathrooms:</td>
                                    <td><?php echo $property['ufCrm83Bathroom'] ?></td>
                                </tr>
                                <tr>
                                    <td>Property Size:</td>
                                    <td><?php echo $property['ufCrm83Size'] ?></td>
                                </tr>
                                <tr>
                                    <td>Property Type:</td>
                                    <td><?php echo $property['ufCrm83PropertyType'] ?></td>
                                </tr>
                                <tr>
                                    <td>Property Status:</td>
                                    <td><?php echo $property['ufCrm83Status'] ?></td>
                                </tr>
                            </table>

                            <h5 class="card-title mt-4">Additional details</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td>Financial Status:</td>
                                    <td><?php echo $property['ufCrm83FinancialStatus'] ?></td>
                                </tr>
                                <tr>
                                    <td>Available From:</td>
                                    <td><?php echo $property['ufCrm83AvailableFrom'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <div class="row listing-images">
                <div class="col-md-8 main-image">
                    <img src="<?= htmlspecialchars($property['ufCrm83Photos'][0] ?? $property['ufCrm83_1726230114498'][0]['urlMachine']) ?>" alt="image" class="img-fluid rounded">
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-6 col-md-12">
                            <img src="<?= htmlspecialchars($property['ufCrm83Photos'][1]) ?>" alt="image" class="img-fluid rounded">
                        </div>
                        <div class="col-6 col-md-12">
                            <img src="<?= htmlspecialchars($property['ufCrm83Photos'][2]) ?>" alt="Interior view 2" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($property['ufCrm83Photos'][3]) ?>" alt="Balcony view" class="img-fluid rounded">
                </div>
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($property['ufCrm83Photos'][4]) ?>" alt="Kitchen view" class="img-fluid rounded">
                </div>
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($property['ufCrm83Photos'][5]) ?>" alt="Staircase view 1" class="img-fluid rounded">
                </div>
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($property['ufCrm83Photos'][6]) ?>" alt="Staircase view 2" class="img-fluid rounded">
                </div>
            </div>
            <section class="section-bg">
                <div class="row">
                    <div class="col-12">
                        <h2 class="mb-4">Private Amenities</h2>
                        <?php if (!empty($property['ufCrm83Amenities']) && is_array($property['ufCrm83Amenities'])): ?>
                            <div>
                                <?php foreach (explode(',', $property['ufCrm83Amenities'][0]) as $amenity): ?>
                                    <span class="amenity-badge"><?php echo htmlspecialchars($amenity); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No amenities available.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </section>

            <section class="section-bg">
                <div class="row">
                    <div class="col-12">
                        <h2 class="mb-4">Floor plans</h2>
                        <!-- Placeholder for floor plans -->
                        <div class="bg-light p-5 text-center">
                            <a href="<?= htmlspecialchars($property['ufCrm83FloorPlan'][0]['urlMachine']) ?>">Download floor plan</a>
                        </div>
                    </div>
                </div>
            </section>
                        </div>
        </div>

    </div>



    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="./js/script.js"></script>

    </form>
</body>

</html>