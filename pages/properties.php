<?php
require_once(__DIR__ . '/../crest/crest.php');
require_once(__DIR__ . '/../crest/settings.php');

function fetchProperties($filter = null)
{

	$filterConditions = [];

	if ($filter === 'draft') {
		$filterConditions['ufCrm83Status'] = 'DRAFT';
	} elseif ($filter === 'live') {
		$filterConditions['ufCrm83Status'] = 'LIVE';
	} elseif ($filter === 'pending') {
		$filterConditions['ufCrm83Status'] = 'PENDING';
	} elseif ($filter === 'archived') {
		$filterConditions['ufCrm83Status'] = 'ARCHIVED';
	}

	// if (!empty($filter['refId'])) {
	// 	$filterConditions['ufCrm83RefId'] = $filter['refId'];
	// }
	if (!empty($filter['community'])) {
		$filterConditions['ufCrm83PfCommunity'] = $filter['community'];
	}
	if (!empty($filter['subCommunity'])) {
		$filterConditions['ufCrm83PfSubCommunity'] = $filter['subCommunity'];
	}
	if (!empty($filter['building'])) {
		$filterConditions['ufCrm83PfBuilding'] = $filter['building'];
	}
	if (!empty($filter['unitNo'])) {
		$filterConditions['ufCrm83UnitNo'] = $filter['unitNo'];
	}
	if (!empty($filter['permit'])) {
		$filterConditions['ufCrm83Permit'] = $filter['permit'];
	}
	if (!empty($filter['listingOwner'])) {
		$filterConditions['ufCrm83ListingOwner'] = $filter['listingOwner'];
	}
	if (!empty($filter['listingTitle'])) {
		$filterConditions['ufCrm83ListingTitle'] = $filter['listingTitle'];
	}
	if (!empty($filter['category'])) {
		$filterConditions['ufCrm83Category'] = $filter['category'];
	}
	if (!empty($filter['propertyType'])) {
		$filterConditions['ufCrm83PropertyType'] = $filter['propertyType'];
	}
	if (!empty($filter['saleRent'])) {
		$filterConditions['ufCrm83SaleRent'] = $filter['saleRent'];
	}
	if (!empty($filter['listingAgents'])) {
		$filterConditions['ufCrm83ListingAgents'] = $filter['listingAgents'];
	}
	if (!empty($filter['landlord'])) {
		$filterConditions['ufCrm83Landlord'] = $filter['landlord'];
	}
	if (!empty($filter['landlordEmail'])) {
		$filterConditions['ufCrm83LandlordEmail'] = $filter['landlordEmail'];
	}
	if (!empty($filter['landlordPhone'])) {
		$filterConditions['ufCrm83LandlordPhone'] = $filter['landlordPhone'];
	}
	if (!empty($filter['bedrooms'])) {
		$filterConditions['ufCrm83Bedroom'] = $filter['bedrooms'];
	}
	if (!empty($filter['developers'])) {
		$filterConditions['ufCrm83Developers'] = $filter['developers'];
	}
	if (!empty($filter['price'])) {
		$filterConditions['ufCrm83Price'] = $filter['price'];
	}
	if (!empty($filter['portals'])) {
		$filterConditions['ufCrm83Portals'] = $filter['portals'];
	}

	// Call Bitrix24 API to fetch properties with filter conditions
	$response = CRest::call('crm.item.list', [
		'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
		'filter' => $filterConditions
	]);

	return $response['result']['items'] ?? [];
}

// Check if a filter is set in the URL
$filter = $_GET['filter'] ?? null;

// Fetch filtered properties
$properties = fetchProperties($filter);

// echo '<pre>';
// print_r($properties);
// echo '</pre>';

// Store properties in session
session_start();
$_SESSION['properties'] = $properties;

// Function to fetch property details by ID
function fetchPropertyDetails($id)
{
	$response = CRest::call('crm.item.get', [
		'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
		'id' => $id
	]);

	return $response['result']['item'] ?? [];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Property Listing</title>
	<!-- Bootstrap CSS -->
	<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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

		.main-content {
            background-color: #ffffff;
            border-radius: 0.5rem;
            box-shadow: 0 0 15px rgba(0,0,0,0.05);
            /* margin: 1rem; */
            padding: 1.5rem;
        }

		.text-truncate-two-lines {
			display: -webkit-box;
			-webkit-line-clamp: 2;
			-webkit-box-orient: vertical;
			overflow: hidden;
			text-overflow: ellipsis;
		}
	</style>
	<script>
		function toggleCheckboxes(source) {
			const checkboxes = document.querySelectorAll('input[name="property_ids[]"]');
			checkboxes.forEach(checkbox => {
				checkbox.checked = source.checked;
			});
		}
	</script>
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
                <h2 class="display-10 fw-bold text-primary container">Properties</h2>
				<div class="main-content container mt-4">
					<!-- Filter Modal -->
					<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-xl">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="filterModalLabel">Filters</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<form id="filterForm" method="GET" action="index.php">
										<div class="row g-3">
											<div class="col-md-3">
												<label for="refId" class="form-label">Ref. ID</label>
												<input type="text" id="refId" name="refId" class="form-control">
											</div>
											<div class="col-md-3">
												<label for="community" class="form-label">Community</label>
												<select id="community" name="community" class="form-select">
													<option value="">Please select</option>
													<option value="Community 1">Community 1</option>
													<option value="Community 2">Community 2</option>
													<option value="Community 3">Community 3</option>
												</select>
											</div>
											<div class="col-md-3">
												<label for="subCommunity" class="form-label">Sub Community</label>
												<select id="subCommunity" name="subCommunity" class="form-select">
													<option value="">Please select</option>
													<option value="Sub Community 1">Sub Community 1</option>
													<option value="Sub Community 2">Sub Community 2</option>
													<option value="Sub Community 3">Sub Community 3</option>
												</select>
											</div>
											<div class="col-md-3">
												<label for="building" class="form-label">Building</label>
												<select id="building" name="building" class="form-select">
													<option value="">Please select</option>
													<option value="Building 1">Building 1</option>
													<option value="Building 2">Building 2</option>
													<option value="Building 3">Building 3</option>
												</select>
											</div>
										</div>
										<div class="row g-3 mt-3">
											<div class="col-md-3">
												<label for="unitNo" class="form-label">Unit No.</label>
												<input type="text" id="unitNo" name="unitNo" class="form-control">
											</div>
											<div class="col-md-3">
												<label for="permit" class="form-label">Permit # or DMTC #</label>
												<input type="text" id="permit" name="permit" class="form-control">
											</div>
											<div class="col-md-3">
												<label for="listingOwner" class="form-label">Listing Owner</label>
												<select id="listingOwner" name="listingOwner" class="form-select">
													<option value="">Please select</option>
													<option value="Listing Owner 1">Listing Owner 1</option>
													<option value="Listing Owner 2">Listing Owner 2</option>
													<option value="Listing Owner 3">Listing Owner 3</option>
												</select>
											</div>
											<div class="col-md-3">
												<label for="listingTitle" class="form-label">Listing Title</label>
												<input type="text" id="listingTitle" name="listingTitle" class="form-control">
											</div>
										</div>
										<div class="row g-3 mt-3">
											<div class="col-md-3">
												<label for="category" class="form-label">Category</label>
												<input type="text" id="category" name="category" class="form-control">
											</div>
											<div class="col-md-3">
												<label for="propertyType" class="form-label">Property Type</label>
												<select id="propertyType" name="propertyType" class="form-select">
													<option value="">Please select</option>
													<option value="Property Type 1">Property Type 1</option>
													<option value="Property Type 2">Property Type 2</option>
													<option value="Property Type 3">Property Type 3</option>
												</select>
											</div>
											<div class="col-md-3">
												<label for="saleRent" class="form-label">Sale/ Rent</label>
												<input type="text" id="saleRent" name="saleRent" class="form-control">
											</div>
											<div class="col-md-3">
												<label for="listingAgents" class="form-label">Listing Agents</label>
												<select id="listingAgents" name="listingAgents" class="form-select">
													<option value="">Please select</option>
													<option value="Listing Agent 1">Listing Agent 1</option>
													<option value="Listing Agent 2">Listing Agent 2</option>
													<option value="Listing Agent 3">Listing Agent 3</option>
												</select>
											</div>
										</div>
										<div class="row g-3 mt-3">
											<div class="col-md-3">
												<label for="landlord" class="form-label">Landlord</label>
												<select id="landlord" name="landlord" class="form-select">
													<option value="">Please select</option>
													<option value="Landlord 1">Landlord 1</option>
													<option value="Landlord 2">Landlord 2</option>
													<option value="Landlord 3">Landlord 3</option>
												</select>
											</div>
											<div class="col-md-3">
												<label for="landlordEmail" class="form-label">Landlord Email</label>
												<input type="email" id="landlordEmail" name="landlordEmail" class="form-control">
											</div>
											<div class="col-md-3">
												<label for="landlordPhone" class="form-label">Landlord Phone</label>
												<input type="text" id="landlordPhone" name="landlordPhone" class="form-control">
											</div>
											<div class="col-md-3">
												<label for="bedrooms" class="form-label">Bedrooms</label>
												<div class="d-flex align-items-center">
													<span class="me-2">1</span>
													<input type="range" id="bedrooms" name="bedrooms" class="form-range flex-grow-1" min="1" max="7">
													<span class="ms-2">7</span>
												</div>
											</div>
										</div>
										<div class="row g-3 mt-3">
											<div class="col-md-3">
												<label for="developers" class="form-label">Developers</label>
												<select id="developers" name="developers" class="form-select">
													<option value="">Please select</option>
													<option value="Developer 1">Developer 1</option>
													<option value="Developer 2">Developer 2</option>
													<option value="Developer 3">Developer 3</option>
												</select>
											</div>
											<div class="col-md-3">
												<label for="price" class="form-label">Price</label>
												<div class="d-flex align-items-center">
													<span class="me-2">0</span>
													<input type="range" id="price" name="price" class="form-range flex-grow-1" min="0" max="479999000">
													<span class="ms-2">479999000</span>
												</div>
											</div>
											<div class="col-md-3">
												<label for="portals" class="form-label">Portals</label>
												<input type="text" id="portals" name="portals" class="form-control">
											</div>
										</div>
									</form>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
									<button type="reset" form="filterForm" class="btn btn-outline-secondary">Reset</button>
									<button type="submit" form="filterForm" class="btn btn-primary">Apply</button>
								</div>
							</div>
						</div>
					</div>

					<!-- Buttons Section -->
					<!-- changed it to dropdown -->
					<div class="row mb-4">
						<div class="col-12 col-lg-8 mb-3 mb-lg-0">
							<div class="d-flex align-items-center">
								<!-- dropdown -->
								<div class="dropdown me-2">
									<button class="btn btn-outline-primary dropdown-toggle" type="button" id="listingFiltersDropdown" data-bs-toggle="dropdown" aria-expanded="false">
										<?php
										$filterLabels = [
											'all' => 'All Listings',
											'draft' => 'Draft',
											'live' => 'Live',
											'pending' => 'Pending',
											'archived' => 'Archived',
											'duplicate' => 'Duplicate'
										];
										echo $filterLabels[$filter] ?? 'Select Filter';
										?>
									</button>
									<ul class="dropdown-menu" aria-labelledby="listingFiltersDropdown">
										<li><a class="dropdown-item <?php echo $filter == 'all' ? 'active' : '' ?>" href="index.php?filter=all">All Listings</a></li>
										<li><a class="dropdown-item <?php echo $filter == 'draft' ? 'active' : '' ?>" href="index.php?filter=draft">Draft</a></li>
										<li><a class="dropdown-item <?php echo $filter == 'live' ? 'active' : '' ?>" href="index.php?filter=live">Live</a></li>
										<li><a class="dropdown-item <?php echo $filter == 'pending' ? 'active' : '' ?>" href="index.php?filter=pending">Pending</a></li>
										<li><a class="dropdown-item <?php echo $filter == 'archived' ? 'active' : '' ?>" href="index.php?filter=archived">Archived</a></li>
										<li><a class="dropdown-item <?php echo $filter == 'duplicate' ? 'active' : '' ?>" href="index.php?filter=duplicate">Duplicate</a></li>
									</ul>
								</div>
								<!-- filter -->
								<button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#filterModal">
									<i class="fas fa-filter me-2"></i>Filters
								</button>
							</div>
						</div>
						<div class="col-12 col-lg-4 d-flex flex-wrap justify-content-lg-end align-items-center">
							<button onclick="location.href='../create_listing.php'" class="btn btn-primary me-2 mb-2 mb-lg-0">
								<i class="fas fa-plus me-2"></i>Create Listing
							</button>
							
							<div class="dropdown">
								<button class="btn btn-secondary dropdown-toggle" type="button" id="bulkActionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="fas fa-cog me-2"></i>Bulk Actions
								</button>
								<ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="bulkActionsDropdown">
									<li><h6 class="dropdown-header">Transfer</h6></li>
									<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#transferAgentModal" onclick="selectAndAddPropertiesToAgentTransfer()"><i class="fas fa-user-tie me-2"></i>Transfer to Agent</button></li>
									<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#transferOwnerModal" onclick="selectAndAddPropertiesToOwnerTransfer()"><i class="fas fa-user me-2"></i>Transfer to Owner</button></li>
									<li><hr class="dropdown-divider"></li>
									<li><h6 class="dropdown-header">Publish</h6></li>
									<li><button class="dropdown-item" type="button" onclick="publishSelectedProperties()"><i class="fas fa-globe me-2"></i>Publish All</button></li>
									<li><button class="dropdown-item" type="button" onclick="publishSelectedPropertiesToBayut()"><i class="fas fa-building me-2"></i>Publish To Bayut</button></li>
									<li><button class="dropdown-item" type="button" onclick="publishSelectedPropertiesToDubizzle()"><i class="fas fa-home me-2"></i>Publish To Dubizzle</button></li>
									<li><button class="dropdown-item" type="button" onclick="publishSelectedPropertiesToPF()"><i class="fas fa-search me-2"></i>Publish To PF</button></li>
									<li><button class="dropdown-item" type="button" onclick="unPublishSelectedProperties()"><i class="fas fa-eye-slash me-2"></i>Unpublish</button></li>
									<li><hr class="dropdown-divider"></li>
									<li><button class="dropdown-item text-danger" type="button" onclick="deleteSelectedProperties()"><i class="fas fa-trash-alt me-2"></i>Delete</button></li>
								</ul>
							</div>
						</div>
					</div>

					<!-- Modal (Transfer to Agent) -->
					<div class="modal fade" id="transferAgentModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="transferModalLabel">Transfer Property to Agent</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<!-- Form inside modal -->
									<form id="transferAgentForm" method="POST" action="transfer_agent.php">
										<input type="hidden" id="transferAgentPropertyIds" name="transferAgentPropertyIds">
										<div class="form-group">
											<label for="agentSelect">Select Listing Agent</label>
											<select class="form-control" id="agentSelect" name="agent_id" required>
												<?php
												// Fetch and display listing agents
												$agents_result = CRest::call('crm.item.list', ['entityTypeId' => LISTING_AGENTS_ENTITY_TYPE_ID]);
												$listing_agents = $agents_result['result']['items'] ?? [];

												foreach ($listing_agents as $agent) {
													echo '<option value="' . htmlspecialchars($agent['id']) . '">' . htmlspecialchars($agent['ufCrm95AgentName']) . '</option>';
												}
												?>
											</select>
										</div>
										<button type="submit" class="btn btn-primary">Transfer</button>
									</form>
								</div>
							</div>
						</div>
					</div>

					<!-- Modal (Transfer to Owner) -->
					<div class="modal fade" id="transferOwnerModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title" id="transferModalLabel">Transfer Property to Owner</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<!-- Form inside modal -->
									<form id="transferOwnerForm" method="POST" action="transfer_owner.php">
										<input type="hidden" id="transferOwnerPropertyIds" name="transferOwnerPropertyIds">
										<div class="form-group">
											<label for="ownerName">Enter Owner Name</label>
											<input type="text" class="form-control" id="ownerName" name="ownerName" required />
											
										</div>
										<button type="submit" class="btn btn-primary">Transfer</button>
									</form>
								</div>
							</div>
						</div>
					</div>


					<!-- Table Section -->
					<div class="table">
						<div style="overflow-x: auto;">
							<table class="table table-hover table-striped align-middle shadow-sm">
								<thead class="table-dark">
									<tr>
										<th>
											<div class="form-check">
												<input class="form-check-input" type="checkbox" id="select-all" onclick="toggleCheckboxes(this)">
												<label class="form-check-label" for="select-all"></label>
											</div>
										</th>
										<th scope="col">Actions</th>
										<th scope="col">Reference</th>
										<th scope="col">Property</th>
										<th scope="col">Details</th>
										<th scope="col">Type</th>
										<th scope="col">Price</th>
										<th scope="col">Location</th>
										<th scope="col">Agent</th>
										<th scope="col">Owner</th>
									</tr>
								</thead>

								<tbody>
									<form action="export.php" method="POST" id="exportForm">
										<?php foreach ($properties as $property): ?>
											<tr>
												<td>
													<div class="form-check">
														<input class="form-check-input" type="checkbox" name="property_ids[]" value="<?php echo htmlspecialchars($property['id']); ?>">
														<label class="form-check-label"></label>
													</div>
												</td>
												<td>
													<div class="dropdown">
														<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
															<i class="fa-solid fa-ellipsis-vertical"></i>
														</button>
														<ul class="dropdown-menu shadow" style="">
															<li><a class="dropdown-item" href="../edit-property.php?id=<?php echo $property['id']; ?>"><i class="fa-solid fa-edit me-2"></i>Edit</a></li>
															<li><a class="dropdown-item" href="../view-property.php?id=<?php echo $property['id']; ?>"><i class="fa-solid fa-eye me-2"></i>View Details</a></li>
															<li><a class="dropdown-item" href="#" onclick="copyLink('<?php echo $property['id']; ?>')"><i class="fa-solid fa-link me-2"></i>Copy Link</a></li>
															<li><a class="dropdown-item" href="../download-property.php?id=<?php echo $property['id']; ?>"><i class="fa-solid fa-download me-2"></i>Download PDF</a></li>
															<li><a class="dropdown-item" href="../xml.php?propertyId=<?php echo $property['id']; ?>"><i class="fa-solid fa-upload me-2"></i>Publish</a></li>
															<li><hr class="dropdown-divider"></li>
															<li><a class="dropdown-item text-danger" href="../delete-property.php?id=<?php echo $property['id']; ?>"><i class="fa-solid fa-trash me-2"></i>Delete</a></li>
														</ul>
													</div>
												</td>
												<td class="">
													<?php
													if (isset($property['ufCrm83ReferenceNumber']) && $property['ufCrm83ReferenceNumber'] !== null && $property['ufCrm83ReferenceNumber'] !== '') {
														echo htmlspecialchars($property['ufCrm83ReferenceNumber']);
													} else {
														echo 'N/A';
													}
													?>
												</td>
												<td>
													<div class="d-flex align-items-center">
														<img src="<?= htmlspecialchars($property['ufCrm83Photos'][0] ?? $property['ufCrm83_1726230114498'][0]['urlMachine']) ?>"
															class="img-thumbnail me-3"
															style="width: 60px; height: 60px; object-fit: cover;"
															alt="Property Image">
														<div>
															<h6 class="mb-0"><?= htmlspecialchars($property['ufCrm83TitleEn']) ?></h6>
															<small class="text-muted d-inline-block text-truncate" style="max-width: 150px;">
																<?= htmlspecialchars($property['ufCrm83DescriptionEn']) ?>
															</small>
														</div>
													</div>
												</td>
												<td>
													<div class="d-flex flex-column">
														<span class="badge bg-light text-dark mb-1"><?= htmlspecialchars($property['ufCrm83Size']) ?> sq ft</span>
														<div>
															<span class="badge bg-info text-dark me-1"><i class="fa-solid fa-bath me-1"></i><?= htmlspecialchars($property['ufCrm83Bathroom']) ?></span>
															<span class="badge bg-info text-dark"><i class="fa-solid fa-bed me-1"></i><?= htmlspecialchars($property['ufCrm83Bedroom']) ?></span>
														</div>
													</div>
												</td>
												<td>
													<span class="badge bg-primary"><?= htmlspecialchars($property['ufCrm83PropertyType']) == 'AP' ? 'Apartment' : htmlspecialchars($property['ufCrm83PropertyType']) ?></span>
													<?php if (isset($property['status']) && $property['status']): ?>
														<span class="badge bg-success"><?= htmlspecialchars($property['status']) ?></span>
													<?php endif; ?>
												</td>
												<td>
													<h6 class="mb-0">AED <?= number_format(htmlspecialchars($property['ufCrm83Price'])) ?></h6>
													<small class="text-muted">
														<?= htmlspecialchars(in_array($property['ufCrm83OfferingType'], ['RR', 'CR']) ? 'Rent' : 'Sale') ?>
													</small>
												</td>
												<td>
													<span class="badge bg-light text-dark"><?= htmlspecialchars($property['ufCrm83PfCommunity']) ?></span>
												</td>
												<td>
													<span class="badge bg-secondary"><?= htmlspecialchars($property['ufCrm83AgentName']) ?></span>
												</td>
												<td>
													<span class="badge bg-light text-dark"><?= htmlspecialchars($property['ufCrm83ListingOwner']) ?></span>
												</td>
											</tr>
										<?php endforeach; ?>
									</form>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		function deleteSelectedProperties() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			if (propertyIds.length === 0) {
				alert('No properties selected');
				return;
			}

			// Send selected IDs to delete.php
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'delete.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onload = function() {
				if (xhr.status === 200) {
					// Handle the response if needed
					console.log('Response:', xhr.responseText);
					// alert('Properties deleted successfully');
					location.reload();
				} else {
					console.error('Error:', xhr.statusText);
				}
			};
			xhr.send('property_ids=' + encodeURIComponent(JSON.stringify(propertyIds)));
		}

		function publishSelectedProperties() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			if (propertyIds.length === 0) {
				alert('No properties selected');
				return;
			}

			// Create the URL for publishing
			var url = `../xml.php?property_ids=${encodeURIComponent(JSON.stringify(propertyIds))}`;

			// Redirect to the constructed URL
			window.location.href = url; // This will navigate to the xml.php with the query parameters
		}

		function exportProperties() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			if (propertyIds.length === 0) {
				alert('No properties selected');
				return;
			}

			document.getElementById('exportForm').submit()
		}

		function publishSelectedPropertiesToBayut() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			if (propertyIds.length === 0) {
				alert('No properties selected');
				return;
			}

			// Create the URL for publishing
			var url = `../xml.php?platform=bayut&property_ids=${encodeURIComponent(JSON.stringify(propertyIds))}`;

			// Redirect to the constructed URL
			window.location.href = url; // This will navigate to the xml.php with the query parameters
		}

		function publishSelectedPropertiesToDubizzle() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			if (propertyIds.length === 0) {
				alert('No properties selected');
				return;
			}

			// Create the URL for publishing
			var url = `../xml.php?platform=dubizzle&property_ids=${encodeURIComponent(JSON.stringify(propertyIds))}`;

			// Redirect to the constructed URL
			window.location.href = url; // This will navigate to the xml.php with the query parameters
		}

		function publishSelectedPropertiesToPF() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			if (propertyIds.length === 0) {
				alert('No properties selected');
				return;
			}

			// Create the URL for publishing
			var url = `../xml.php?property_ids=${encodeURIComponent(JSON.stringify(propertyIds))}`;

			// Redirect to the constructed URL
			window.location.href = url; // This will navigate to the xml.php with the query parameters
		}

		function unPublishSelectedProperties() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			if (propertyIds.length === 0) {
				alert('No properties selected');
				return;
			}

			// Send selected IDs to delete.php
			var xhr = new XMLHttpRequest();
			xhr.open('POST', 'unpublish.php', true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.onload = function() {
				if (xhr.status === 200) {

					console.log('Response:', xhr.responseText);
					alert('Success: Properties unpublished successfully');

				} else {
					console.error('Error:', xhr.statusText);
				}
			};
			xhr.send('property_ids=' + encodeURIComponent(JSON.stringify(propertyIds)));
		}

		function transferSelectedPropertiesToAgent() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			// Ensure the modal is open and agent_id exists
			var agent_id = document.querySelector('#agentModal #agent_id'); // Select agent_id inside the modal

			// if (!agent_id || !agent_id.value) {
			// 	alert('Please select an agent.');
			// 	return;
			// }

			if (propertyIds.length === 0) {
				alert('No properties selected');
				return;
			}

			// Build the query string
			var queryParams = new URLSearchParams({
				// agent_id: agent_id.value, // Get the value of the agent_id input
				property_ids: propertyIds.join(',') // Join property IDs with commas
			});

			// Redirect to transfer_agent.php with the query parameters
			window.location.href = '../transfer_agent.php?' + queryParams.toString();
		}

		function selectAndAddPropertiesToAgentTransfer() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			// Assign property IDs to the hidden input field
			document.getElementById('transferAgentPropertyIds').value = propertyIds.join(',');

			// // Check if the agentSelect dropdown has a value
			// var agentSelect = document.getElementById('agentSelect');
			// console.log("Selected Agent ID: " + agentSelect.value);

			// if (!agentSelect.value) {
			// 	alert("Please select an agent before proceeding.");
			// }
		}

		function selectAndAddPropertiesToOwnerTransfer() {
			// Gather all checked checkboxes
			var checkboxes = document.querySelectorAll('input[name="property_ids[]"]:checked');
			var propertyIds = Array.from(checkboxes).map(checkbox => checkbox.value);

			// Assign property IDs to the hidden input field
			document.getElementById('transferOwnerPropertyIds').value = propertyIds.join(',');

			// // Check if the agentSelect dropdown has a value
			// var ownerName = document.getElementById('ownerName');
			// console.log("Owner Name: " + ownerName);

			// if (!ownerName.value) {
			// 	alert("Please enter the owner name before proceeding.");
			// }
		}



		function copyLink(propertyId) {
			var url = `${window.location.origin}/projects/property-listing/view-property.php?id=${propertyId}`;

			navigator.clipboard.writeText(url).then(function() {
				alert('Link copied to clipboard');
			}).catch(function(err) {
				console.error('Failed to copy the link: ', err);
			});
		}
	</script>

	<!-- Bootstrap JS and dependencies -->
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
	<script src="./js/script.js">
	</script>
</body>

</html>