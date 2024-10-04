<?php
require_once(__DIR__ . '/crest/crest.php');
require_once(__DIR__ . '/crest/settings.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $propertyIds = isset($_POST['property_ids']) ? json_decode($_POST['property_ids'], true) : [];

    if (!empty($propertyIds)) {

        foreach ($propertyIds as $propertyId) {
            // TODO: PUBLISH PROPERTY
        }

        echo 'Properties published successfully.';
    } else {
        echo 'No properties selected for publish.';
    }
} else {
    echo 'Invalid request method.';
}
