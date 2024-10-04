<div class="d-flex justify-content-between align-items-center mb-4 p-3 border rounded bg-light">
    <a href="<?php echo __DIR__ . '/../index.php' ?>    " class="">
        <h2 class="mb-0">Property Listing</h2>
    </a>

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