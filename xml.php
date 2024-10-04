<?php
require_once(__DIR__ . '/crest/crest.php');
require_once(__DIR__ . '/crest/settings.php');

function fetchProperties($propertyIds = null)
{
    if (!$propertyIds) {
        $response = CRest::call('crm.item.list', [
            'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
        ]);
    } else {
        $response = CRest::call('crm.item.list', [
            'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
            'filter' => ['ID' => $propertyIds]
        ]);
    }


    return $response['result']['items'] ?? [];
}

$propertyIds = isset($_GET['property_ids']) ? json_decode($_GET['property_ids'], true) : [];
$propertyId = $_GET['propertyId'] ?? null;
$platform = $_GET['platform'] ?? 'pf';

if ($propertyId) {
    $propertyIds = [$propertyId];
}

$properties = fetchProperties($propertyIds);

header('Content-Type: application/xml');

if ($platform === 'pf') {
    echo generatePfXml($properties);
} elseif ($platform === 'dubizzle') {
    echo generateDubizzleXml($properties);
} elseif ($platform === 'bayut') {
    // echo generateBayutXml($properties);
    echo generatePfXml($properties); // Bayut XML format is unavailable
} else {
    echo generatePfXml($properties);
}

function generatePfXml($properties)
{
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><list/>');
    $xml->addAttribute('last_update', date('y-m-d H:i:s')); // Current date-time
    $xml->addAttribute('listing_count', count($properties));

    foreach ($properties as $property) {
        $propertyNode = $xml->addChild('property');
        $propertyNode->addAttribute('last_update', date('y-m-d H:i:s', strtotime($property['updatedTime'] ?? '')));
        $propertyNode->addAttribute('id', $property['id'] ?? '');

        addCDataElement($propertyNode, 'reference_number', $property['ufCrm83ReferenceNumber'] ?? '');
        addCDataElement($propertyNode, 'permit_number', $property['ufCrm83PermitNumber'] ?? '');

        if (isset($property['ufCrm83RentalPeriod']) && $property['ufCrm83RentalPeriod'] === 'M') {
            addCDataElement($propertyNode->addChild('price'), 'monthly', $property['ufCrm83Price'] ?? '');
        }

        addCDataElement($propertyNode, 'offering_type', $property['ufCrm83OfferingType'] ?? '');
        addCDataElement($propertyNode, 'property_type', $property['ufCrm83PropertyType'] ?? '');

        addCDataElement($propertyNode, 'geopoints', $property['ufCrm83Geopoints'] ?? '');
        addCDataElement($propertyNode, 'city', $property['ufCrm83PfCity'] ?? '');
        addCDataElement($propertyNode, 'community', $property['ufCrm83PfCommunity'] ?? '');
        addCDataElement($propertyNode, 'sub_community', $property['ufCrm83PfSubCommunity'] ?? '');
        addCDataElement($propertyNode, 'title_en', $property['ufCrm83TitleEn'] ?? '');
        addCDataElement($propertyNode, 'description_en', $property['ufCrm83DescriptionEn'] ?? '');
        addCDataElement($propertyNode, 'size', $property['ufCrm83Size'] ?? '');
        addCDataElement($propertyNode, 'bedroom', $property['ufCrm83Bedroom'] ?? '');
        addCDataElement($propertyNode, 'bathroom', $property['ufCrm83Bathroom'] ?? '');

        $agentNode = $propertyNode->addChild('agent');
        addCDataElement($agentNode, 'id', $property['ufCrm83AgentId'] ?? '');
        addCDataElement($agentNode, 'name', $property['ufCrm83AgentName'] ?? '');
        addCDataElement($agentNode, 'email', $property['ufCrm83AgentEmail'] ?? '');
        addCDataElement($agentNode, 'phone', $property['ufCrm83AgentPhone'] ?? '');
        addCDataElement($agentNode, 'photo', $property['ufCrm83AgentPhoto'] ?? '');

        $photoNode = $propertyNode->addChild('photo');

        foreach ($property['ufCrm83Photos'] as $photo) {
            $urlNode = addCDataElement($photoNode, 'url', $photo);
            $urlNode->addAttribute('last_update', date('Y-m-d H:i:s'));
            $urlNode->addAttribute('watermark', 'Yes');
        }

        addCDataElement($propertyNode, 'parking', $property['ufCrm83Parking'] ?? '');
        addCDataElement($propertyNode, 'furnished', $property['ufCrm83Furnished'] ?? '');
        addCDataElement($propertyNode, 'price_on_application', $property['ufCrm83PriceOnApplication'] ?? '');
    }

    return $xml->asXML();
}

// Function to generate XML for 'dubizzle' platform
function generateDubizzleXml($properties)
{
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Properties/>');
    $xml->addAttribute('last_update', date('y-m-d H:i:s')); // Current date-time
    $xml->addAttribute('listing_count', count($properties));

    foreach ($properties as $property) {
        $propertyNode = $xml->addChild('Property');
        $propertyNode->addAttribute('id', $property['id'] ?? '');

        addCDataElement($propertyNode, 'Last_Updated', $property['updatedTime'] ?? '');
        addCDataElement($propertyNode, 'Property_Ref_No', $property['ufCrm83ReferenceNumber'] ?? '');
        addCDataElement($propertyNode, 'Permit_Number', $property['ufCrm83PermitNumber'] ?? '');
        addCDataElement($propertyNode, 'Price', $property['ufCrm83Price'] ?? '');
        addCDataElement($propertyNode, 'Property_purpose', $property['ufCrm83OfferingType'] ?? '');
        addCDataElement($propertyNode, 'Property_Type', $property['ufCrm83PropertyType'] ?? '');
        addCDataElement($propertyNode, 'City', $property['ufCrm83PfCity'] ?? '');
        addCDataElement($propertyNode, 'Locality', $property['ufCrm83PfCommunity'] ?? '');
        addCDataElement($propertyNode, 'Sub_Locality', $property['ufCrm83PfSubCommunity'] ?? '');
        addCDataElement($propertyNode, 'Property_Title', $property['ufCrm83TitleEn'] ?? '');
        addCDataElement($propertyNode, 'Property_Description', $property['ufCrm83DescriptionEn'] ?? '');
        addCDataElement($propertyNode, 'Property_Size', $property['ufCrm83Size'] ?? '');
        addCDataElement($propertyNode, 'Bedrooms', $property['ufCrm83Bedroom'] ?? '');
        addCDataElement($propertyNode, 'Bathrooms', $property['ufCrm83Bathroom'] ?? '');
        addCDataElement($propertyNode, 'Furnished', $property['ufCrm83Furnished'] ?? '');
        addCDataElement($propertyNode, 'Parking', $property['ufCrm83Parking'] ?? '');

        // Listing Agent Details
        $agentNode = $propertyNode->addChild('Listing_Agent');
        addCDataElement($agentNode, 'Name', $property['ufCrm83AgentName'] ?? '');
        addCDataElement($agentNode, 'Email', $property['ufCrm83AgentEmail'] ?? '');
        addCDataElement($agentNode, 'Phone', $property['ufCrm83AgentPhone'] ?? '');

        // Portals
        $portalsNode = $propertyNode->addChild('Portals');
        foreach ($property['portals'] ?? [] as $portal) {
            addCDataElement($portalsNode, 'Portal', $portal);
        }

        // Images
        $imagesNode = $propertyNode->addChild('Images');
        foreach ($property['ufCrm83Photos'] ?? [] as $image) {
            $imageNode = $imagesNode->addChild('Image', $image);
            $imageNode->addAttribute('last_update', date('Y-m-d H:i:s'));
        }

        // Features
        $featuresNode = $propertyNode->addChild('Features');
        foreach ($property['ufCrm83Amenities'] as $amenity) {
            $amenityList = explode(',', $amenity); // Assuming amenities are comma-separated
            foreach ($amenityList as $code) {
                $fullName = getFullAmenityName(trim($code)); // Use the function to get the full name
                addCDataElement($featuresNode, 'Feature', $fullName);
            }
        }
    }

    return $xml->asXML();
}


// Function to generate XML for 'bayut' platform
function generateBayutXml($properties)
{
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><list/>');
    $xml->addAttribute('last_update', date('Y-m-d H:i:s'));
    $xml->addAttribute('listing_count', count($properties));

    foreach ($properties as $property) {
        $propertyNode = $xml->addChild('property');
        // Fill in the bayut-specific XML structure
        // For example:
        addCDataElement($propertyNode, 'bayut_id', $property['id'] ?? '');
        // Add other elements as needed...
    }

    return $xml->asXML();
}

function addCDataElement(SimpleXMLElement $node, $name, $value)
{
    $child = $node->addChild($name);
    $dom = dom_import_simplexml($child);
    $dom->appendChild($dom->ownerDocument->createCDATASection($value));

    return $child;
}

function getFullAmenityName($shortCode)
{
    $amenityMap = [
        'BA' => 'Balcony',
        'BP' => 'Basement parking',
        'BB' => 'BBQ area',
        'AN' => 'Cable-ready',
        'BW' => 'Built in wardrobes',
        'CA' => 'Carpets',
        'AC' => 'Central air conditioning',
        'CP' => 'Covered parking',
        'DR' => 'Drivers room',
        'FF' => 'Fully fitted kitchen',
        'GZ' => 'Gazebo',
        'PY' => 'Private Gym',
        'PJ' => 'Jacuzzi',
        'BK' => 'Kitchen Appliances',
        'MR' => 'Maids Room',
        'MB' => 'Marble floors',
        'HF' => 'On high floor',
        'LF' => 'On low floor',
        'MF' => 'On mid floor',
        'PA' => 'Pets allowed',
        'GA' => 'Private garage',
        'PG' => 'Garden',
        'PP' => 'Swimming pool',
        'SA' => 'Sauna',
        'SP' => 'Shared swimming pool',
        'WF' => 'Wood flooring',
        'SR' => 'Steam room',
        'ST' => 'Study',
        'UI' => 'Upgraded interior',
        'GR' => 'Garden view',
        'VW' => 'Sea/Water view',
        'SE' => 'Security',
        'MT' => 'Maintenance',
        'IC' => 'Within a Compound',
        'IS' => 'Indoor swimming pool',
        'SF' => 'Separate entrance for females',
        'BT' => 'Basement',
        'SG' => 'Storage room',
        'CV' => 'Community view',
        'GV' => 'Golf view',
        'CW' => 'City view',
        'NO' => 'North orientation',
        'SO' => 'South orientation',
        'EO' => 'East orientation',
        'WO' => 'West orientation',
        'NS' => 'Near school',
        'HO' => 'Near hospital',
        'TR' => 'Terrace',
        'NM' => 'Near mosque',
        'SM' => 'Near supermarket',
        'ML' => 'Near mall',
        'PT' => 'Near public transportation',
        'MO' => 'Near metro',
        'VT' => 'Near veterinary',
        'BC' => 'Beach access',
        'PK' => 'Public parks',
        'RT' => 'Near restaurants',
        'NG' => 'Near Golf',
        'AP' => 'Near airport',
        'CS' => 'Concierge Service',
        'SS' => 'Spa',
        'SY' => 'Shared Gym',
        'MS' => 'Maid Service',
        'WC' => 'Walk-in Closet',
        'HT' => 'Heating',
        'GF' => 'Ground floor',
        'SV' => 'Server room',
        'DN' => 'Pantry',
        'RA' => 'Reception area',
        'VP' => 'Visitors parking',
        'OP' => 'Office partitions',
        'SH' => 'Core and Shell',
        'CD' => 'Children daycare',
        'CL' => 'Cleaning services',
        'NH' => 'Near Hotel',
        'CR' => 'Conference room',
        'BL' => 'View of Landmark',
        'PR' => 'Children Play Area',
        'BH' => 'Beach Access'
    ];

    return $amenityMap[$shortCode] ?? $shortCode; // Return full name or short code if not found
}
