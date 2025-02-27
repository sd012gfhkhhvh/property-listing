<?php

$previousData = $_POST;

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
</head>

<body>
	<div class="container mt-4">



		<!-- Top Box -->
		<div class="d-flex justify-content-between align-items-center mb-4 p-3 border rounded bg-light">
			<h2 class="mb-0">Property Listing</h2>

			<div class="d-flex align-items-center">

				<button type="button" class="btn btn-outline-success position-relative mx-4">
					Availability <i class="fa-regular fa-clipboard"></i>
					<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success">
						322
					</span>
				</button>
				<button type="button" class="btn btn-outline-primary position-relative">
					Listings <i class="fa-solid fa-list"></i>
					<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">
						1079
					</span>
				</button>

			</div>

			<div class="position-relative">
				<div class="dropdown">
					<button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="fa-solid fa-gear"></i>
					</button>
					<ul class="dropdown-menu">
						<li><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-user-group"></i> Listing Agents</button></li>
						<li><button class="dropdown-item" type="button"><i class="text-muted fa-regular fa-map"></i> Locations</button></li>
						<li><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-map-pin"></i> Bayut Locations</button></li>
						<li><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-house-user"></i> Landlords</button></li>
						<li><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-helmet-safety"></i> Developers</button></li>
						<li><button class="dropdown-item" type="button"><i class="text-muted fa-solid fa-gear"></i> Settings</button></li>
					</ul>
				</div>
			</div>

		</div>


		<div class="container mt-5">
			<div class="d-flex align-items-center justify-content-between mb-4">
				<ul class="nav nav-pills flex-grow-1">
					<li class="nav-item">
						<a class="nav-link completed" href="property_details.php">Property Details</a>
					</li>
					<li class="d-flex justify-content-center align-items-center mx-2">
						<i class="fa-solid fa-chevron-right"></i>
					</li>
					<li class="nav-item">
						<a class="nav-link active" href="media.php">Media</a>
					</li>
					<li class="d-flex justify-content-center align-items-center mx-2">
						<i class="fa-solid fa-chevron-right"></i>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="location.php">Location</a>
					</li>
					<li class="d-flex justify-content-center align-items-center mx-2">
						<i class="fa-solid fa-chevron-right"></i>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="publishing.php">Publishing</a>
					</li>
					<li class="d-flex justify-content-center align-items-center mx-2">
						<i class="fa-solid fa-chevron-right"></i>
					</li>
					<li class="nav-item">
						<a class="nav-link " href="notes.php">Notes</a>
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

		<form action="location.php" method="POST" enctype="multipart/form-data">

			
			<input type="hidden" name="property_type" value="<?php echo $previousData['property_type'] ?? ''; ?>">
			<input type="hidden" name="offer_type" value="<?php echo $previousData['offer_type'] ?? ''; ?>">
			<input type="hidden" name="titleDeed" value="<?php echo $previousData['titleDeed'] ?? ''; ?>">
			<input type="hidden" name="propertyType" value="<?php echo $previousData['propertyType'] ?? ''; ?>">
			<input type="hidden" name="size" value="<?php echo $previousData['size'] ?? ''; ?>">
			<input type="hidden" name="unitNo" value="<?php echo $previousData['unitNo'] ?? ''; ?>">
			<input type="hidden" name="bedrooms" value="<?php echo $previousData['bedrooms'] ?? ''; ?>">
			<input type="hidden" name="bathrooms" value="<?php echo $previousData['bathrooms'] ?? ''; ?>">
			<input type="hidden" name="parkingSpaces" value="<?php echo $previousData['parkingSpaces'] ?? ''; ?>">
			<input type="hidden" name="furnished" value="<?php echo $previousData['furnished'] ?? ''; ?>">
			<input type="hidden" name="totalPlotSize" value="<?php echo $previousData['totalPlotSize'] ?? ''; ?>">
			<input type="hidden" name="lotSize" value="<?php echo $previousData['lotSize'] ?? ''; ?>">
			<input type="hidden" name="buildUpArea" value="<?php echo $previousData['buildUpArea'] ?? ''; ?>">
			<input type="hidden" name="layoutType" value="<?php echo $previousData['layoutType'] ?? ''; ?>">
			<input type="hidden" name="projectName" value="<?php echo $previousData['projectName'] ?? ''; ?>">
			<input type="hidden" name="projectStatus" value="<?php echo $previousData['projectStatus'] ?? ''; ?>">
			<input type="hidden" name="ownership" value="<?php echo $previousData['ownership'] ?? ''; ?>">
			<input type="hidden" name="developers" value="<?php echo $previousData['developers'] ?? ''; ?>">
			<input type="hidden" name="buildYear" value="<?php echo $previousData['buildYear'] ?? ''; ?>">
			<input type="hidden" name="listingAgent" value="<?php echo $previousData['listingAgent'] ?? ''; ?>">
			<input type="hidden" name="listingOwner" value="<?php echo $previousData['listingOwner'] ?? ''; ?>">
			<input type="hidden" name="landlordName" value="<?php echo $previousData['landlordName'] ?? ''; ?>">
			<input type="hidden" name="landlordEmail" value="<?php echo $previousData['landlordEmail'] ?? ''; ?>">
			<input type="hidden" name="landlordContact" value="<?php echo $previousData['landlordContact'] ?? ''; ?>">
			<input type="hidden" name="availableFrom" value="<?php echo $previousData['availableFrom'] ?? ''; ?>">
			<input type="hidden" name="price" value="<?php echo $previousData['price'] ?? ''; ?>">
			<input type="hidden" name="hidePrice" value="<?php echo $previousData['hidePrice'] ?? ''; ?>">
			<input type="hidden" name="paymentMethod" value="<?php echo $previousData['paymentMethod'] ?? ''; ?>">
			<input type="hidden" name="downPayment" value="<?php echo $previousData['downPayment'] ?? ''; ?>">
			<input type="hidden" name="numCheques" value="<?php echo $previousData['numCheques'] ?? ''; ?>">
			<input type="hidden" name="serviceCharge" value="<?php echo $previousData['serviceCharge'] ?? ''; ?>">
			<input type="hidden" name="financialStatus" value="<?php echo $previousData['financialStatus'] ?? ''; ?>">
			<input type="hidden" name="reraPermitNumber" value="<?php echo $previousData['reraPermitNumber'] ?? ''; ?>">
			<input type="hidden" name="reraPermitIssueDate" value="<?php echo $previousData['reraPermitIssueDate'] ?? ''; ?>">
			<input type="hidden" name="reraPermitExpirationDate" value="<?php echo $previousData['reraPermitExpirationDate'] ?? ''; ?>">
			<input type="hidden" name="dtcmPermitNumber" value="<?php echo $previousData['dtcmPermitNumber'] ?? ''; ?>">


			<div class="container mt-5">
				<div class="bg-light p-4 rounded border">
					<h4 class="mb-4">Add Photos</h4>
					<div class="text-center mb-3">
						<label for="photos" class="dropzone p-4 border border-dashed rounded d-block">
							<input type="file" class="d-none" id="photos" name="photos">
							<p class="mb-0">Drop files here or click to upload</p>

						</label>
					</div>

					<p class="text-muted">At least one image is required</p>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="videoUrl" class="form-label">Video Tour URL</label>
							<input type="text" id="videoUrl" name="videoUrl" class="form-control" placeholder="Enter video tour URL">
						</div>
						<div class="col-md-6 mb-3">
							<label for="viewUrl" class="form-label">360 View URL</label>
							<input type="text" id="viewUrl" name="viewUrl" class="form-control" placeholder="Enter 360 view URL">
						</div>
					</div>
					<div class="row">
						<div class="col-md-6 mb-3">
							<label for="qrCode" class="form-label">QR Code (Property Booster)</label>
							<input type="text" id="qrCode" name="qrCode" class="form-control" placeholder="Enter QR code URL">
						</div>
					</div>
				</div>
			</div>



			<div class="d-flex justify-content-between align-items-center mt-3 mb-3">
				<a href="property_details.php" class="btn btn-outline-primary">
					<i class="fa fa-arrow-left"></i> Previous
				</a>
				<div class="d-flex gap-3">
					<button type="button" class="btn btn-success">
						<i class="fa fa-save"></i> Save
					</button>
					<button type="submit" onclick="location.href='location.php'" class="btn btn-primary">
						<i class="fa fa-arrow-right"></i> Continue
					</button>
				</div>
			</div>

		</form>

	</div>


	<!-- Bootstrap JS and dependencies -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
	<script src="./js/script.js"></script>
</body>

</html>