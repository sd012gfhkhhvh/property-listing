<?php
require_once(__DIR__ . '/crest/crest.php');
require_once(__DIR__ . '/crest/settings.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve submitted form data
    $propertyIdsString = isset($_POST['transferOwnerPropertyIds']) ? $_POST['transferOwnerPropertyIds'] : '';
    $ownerName = isset($_POST['ownerName']) ? $_POST['ownerName'] : '';

    // Convert the property IDs string to an array
    $propertyIds = explode(',', $propertyIdsString);

    // echo '<pre>';
    // print_r($propertyIds);  // Debug: Print the property IDs
    // print_r($ownerName);     // Debug: Print the entered owner name
    // echo '</pre>';

    if (!empty($propertyIds)) {

        // Transfer properties to the selected agent
        foreach ($propertyIds as $propertyId) {
            $res = CRest::call('crm.item.update', [
                'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
                'id' => $propertyId,
                'fields' => [
                    'ufCrm83ListingOwner' => $ownerName,
                ]
            ]);
        }

        header('Location: index.php');
    } else {
        header('Location: index.php');
    }
} else {
    echo 'Invalid request method.';
}
