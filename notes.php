<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

function fetchProperties()
{
	$response = CRest::call('crm.item.list', [
		'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
	]);

	return $response['result']['items'] ?? [];
}

// Check if a filter is set in the URL
$filter = $_GET['filter'] ?? null;

// Fetch filtered properties
$properties = fetchProperties($filter);

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

		.nav-pills .nav-link {
			border-radius: 50px;
		}

		.nav-pills .nav-link.completed {
			background-color: #28a745;
			/* Green */
			color: white;
		}

		.nav-pills .nav-link.active {
			background-color: #007bff;
			/* Blue */
			color: white;
		}

		.nav-pills .nav-link:not(.active):not(.completed) {
			background-color: #e9ecef;
			/* Light gray */
		}

		.dropzone {
			cursor: pointer;
			background-color: #f8f9fa;
			/* Light background color */
			transition: background-color 0.3s, border-color 0.3s;
		}

		.dropzone:hover {
			background-color: #e9ecef;
			/* Slightly darker on hover */
			border-color: #007bff;
			/* Blue border on hover */
		}

		.dropzone:after {
			content: '\f093';
			/* Font Awesome upload icon */
			font-family: "Font Awesome 5 Free";
			font-weight: 900;
			color: #007bff;
			font-size: 2rem;
			display: block;
			margin-bottom: 1rem;
		}
	</style>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">

</head>

<body>
	<div class="container mt-4">



		<!-- Top Box -->
		<div class="d-flex justify-content-between align-items-center mb-4 p-3 border rounded bg-light">
			<h2 class="mb-0"><a href="./index.php">Property Listing</a></h2>

			<?php
			$availability = 0;
			$listings = 0;

			foreach ($properties as $property) {
				$availability++;
				if ($property['status'] == 'ufCrm83Status') {
					$listings++;
				}
			}


			?>

			<div class="d-flex align-items-center">
				<button type="button" class="btn btn-outline-success position-relative mx-4">
					Availability <i class="fa-regular fa-clipboard"></i>
					<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
						<?= $availability ?>
					</span>
				</button>
				<button type="button" class="btn btn-outline-primary position-relative">
					Listings <i class="fa-solid fa-list"></i>
					<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
						<?= $listings ?>
					</span>
				</button>
			</div>

			<div class="position-relative">
				<div class="dropdown">
					<button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="fa-solid fa-gear"></i>
					</button>
					<ul class="dropdown-menu">
						<li onclick="location.href='pages/listing_agents.php'"><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-user-group"></i> Listing Agents</button></li>
						<li onclick="location.href='pages/locations.php'"><button class="dropdown-item" type="button"><i class="text-muted fa-regular fa-map"></i> Locations</button></li>
						<li onclick="location.href='pages/bayut_locations.php'"><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-map-pin"></i> Bayut Locations</button></li>
						<li onclick="location.href='pages/landlords.php'"><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-house-user"></i> Landlords</button></li>
						<li onclick="location.href='pages/developers.php'"><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-helmet-safety"></i> Developers</button></li>
						<li onclick="location.href='pages/settings.php'"><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-gear"></i> Settings</button></li>
					</ul>
				</div>
			</div>
		</div>


		<div class="container mt-5">
			<div class="d-flex align-items-center justify-content-between mb-4">
				<ul class="nav nav-pills flex-grow-1">
					<li class="nav-item">
						<a class="nav-link completed" href="create_listing.php">Property Details</a>
					</li>
					<li class="d-flex justify-content-center align-items-center mx-2">
						<i class="fa-solid fa-chevron-right"></i>
					</li>
					<li class="nav-item">
						<a class="nav-link active" href="notes.php">Notes</a>
					</li>
					<li class="d-flex justify-content-center align-items-center mx-2">
						<i class="fa-solid fa-chevron-right"></i>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="documents.php">Documents</a>
					</li>
					<li class="d-flex justify-content-center align-items-center mx-2">
						<i class="fa-solid fa-chevron-right"></i>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="approval.php">Approval</a>
					</li>
					<li class="d-flex justify-content-center align-items-center mx-2">
						<i class="fa-solid fa-chevron-right"></i>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="completed.php">Completed</a>
					</li>
				</ul>
				<button class="btn btn-success mx-3">
					<i class="fas fa-save"></i> Save
				</button>
			</div>
		</div>

		<div class="container mt-5">
			<div class="row p-3 bg-light rounded border">
				<!-- Heading -->
				<div class="col-12 mb-3">
					<h5>Write a New Note</h5>
				</div>

				<!-- Text Editor-like area -->
				<div class="col-12">
					<!-- Formatting toolbar -->
					<div class="d-flex mb-2 align-items-center">
						<select class="form-select form-select-sm w-auto me-2">
							<option selected>Normal</option>
							<option value="1">Heading 1</option>
							<option value="2">Heading 2</option>
						</select>
						<button class="btn btn-light btn-sm me-2"><i class="bi bi-type-bold"></i></button>
						<button class="btn btn-light btn-sm me-2"><i class="bi bi-type-italic"></i></button>
						<button class="btn btn-light btn-sm me-2"><i class="bi bi-type-underline"></i></button>
						<button class="btn btn-light btn-sm me-2"><i class="bi bi-link"></i></button>
						<button class="btn btn-light btn-sm me-2"><i class="bi bi-list-ol"></i></button>
						<button class="btn btn-light btn-sm me-2"><i class="bi bi-list-ul"></i></button>
						<button class="btn btn-light btn-sm"><i class="bi bi-text-paragraph"></i></button>
					</div>

					<!-- Textarea for writing the note -->
					<textarea class="form-control" rows="6" placeholder="Write your note here..."></textarea>
				</div>

				<!-- Add button -->
				<div class="col-12 mt-3">
					<button class="btn btn-primary">Add</button>
				</div>
			</div>
		</div>

		<!-- Include Bootstrap Icons -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css">





		<div class="d-flex justify-content-between align-items-center mt-3 mb-3">
			<a href="create_listing.php" class="btn btn-outline-primary">
				<i class="fa fa-arrow-left"></i> Previous
			</a>
			<div class="d-flex gap-3">
				<button type="button" class="btn btn-success">
					<i class="fa fa-save"></i> Save
				</button>
				<a href="documents.php" class="btn btn-primary">
					<i class="fa fa-arrow-right"></i> Continue
				</a>
			</div>
		</div>

	</div>


	<!-- Bootstrap JS and dependencies -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
	<script src="./js/script.js"></script>
</body>

</html>