<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';

// Function to add watermark to an image
function addWatermark($sourceImagePath, $destinationImagePath)
{
    // Ensure the source image exists
    if (!file_exists($sourceImagePath)) {
        echo "Source image file does not exist: $sourceImagePath<br>";
        return false;
    }

    // Determine image type and load accordingly
    $imageType = exif_imagetype($sourceImagePath);
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($sourceImagePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($sourceImagePath);
            break;
        default:
            echo "Unsupported image type: $imageType<br>";
            return false;
    }

    if (!$image) {
        echo "Failed to load source image. Check if the file is a valid image.<br>";
        return false;
    }

    // Load the watermark
    $watermark = imagecreatefrompng('./assets/watermark.png');
    if (!$watermark) {
        echo "Failed to load watermark image. Ensure the watermark file exists and is a valid PNG image.<br>";
        imagedestroy($image);
        return false;
    }

    // Get dimensions of the source image and watermark
    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);
    $watermarkWidth = imagesx($watermark);
    $watermarkHeight = imagesy($watermark);

    // Calculate position for the watermark to be centered
    $x = ($imageWidth - $watermarkWidth) / 2;
    $y = ($imageHeight - $watermarkHeight) / 2;

    // Merge the watermark onto the image
    if (!imagecopy($image, $watermark, $x, $y, 0, 0, $watermarkWidth, $watermarkHeight)) {
        echo "Failed to merge watermark onto the image.<br>";
        imagedestroy($image);
        imagedestroy($watermark);
        return false;
    }

    // Save the image with watermark
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $saved = imagejpeg($image, $destinationImagePath);
            break;
        case IMAGETYPE_PNG:
            $saved = imagepng($image, $destinationImagePath);
            break;
    }

    if (!$saved) {
        echo "Failed to save the image with watermark.<br>";
        imagedestroy($image);
        imagedestroy($watermark);
        return false;
    }

    // Free up memory
    imagedestroy($image);
    imagedestroy($watermark);

    return true;
}


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property = $_POST;
    $photo = $_FILES['photo'];
    $floorPlan = $_FILES['floorPlan'];

    echo '<pre>';
    print_r($property);
    print_r($photo);

    // Define paths to save temporary images
    $parentDir = __DIR__ . '/tmp';

    // Ensure the temporary directory exists
    if (!is_dir($parentDir)) {
        if (!mkdir($parentDir, 0777, true)) {
            echo "Failed to create temporary directory: $parentDir<br>";
            exit();
        }
    }

    // Define paths for uploaded files
    $photoTmpPath = $parentDir . '/' . basename($photo['name']);
    $floorPlanTmpPath = $parentDir . '/' . basename($floorPlan['name']);

    // Check file upload errors
    if ($photo['error'] !== UPLOAD_ERR_OK) {
        echo "Photo upload error: " . $photo['error'] . "<br>";
    }

    if ($floorPlan['error'] !== UPLOAD_ERR_OK) {
        echo "Floor plan upload error: " . $floorPlan['error'] . "<br>";
    }

    // Move uploaded files to temporary directory
    if (!move_uploaded_file($photo['tmp_name'], $photoTmpPath)) {
        echo "Failed to move photo to tmp directory.<br>";
    }

    if (!move_uploaded_file($floorPlan['tmp_name'], $floorPlanTmpPath)) {
        echo "Failed to move floor plan to tmp directory.<br>";
    }

    // Verify file existence
    if (!file_exists($photoTmpPath)) {
        echo "Source image file does not exist: $photoTmpPath<br>";
    }

    if (!file_exists($floorPlanTmpPath)) {
        echo "Source image file does not exist: $floorPlanTmpPath<br>";
    }

    // Add watermark to images
    if (
        file_exists($photoTmpPath) && file_exists($floorPlanTmpPath) &&
        addWatermark($photoTmpPath, $photoTmpPath) &&
        addWatermark($floorPlanTmpPath, $floorPlanTmpPath)
    ) {

        // Upload to Bitrix24
        $response = CRest::call('crm.item.add', [
            'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
            'fields' => [
                'TITLE' => $property['titleDeed'],
                'ufCrm83PropertyType' => $property['property_type'],
                'ufCrm83Size' => $property['size'],
                'ufCrm83UnitNo' => $property['unitNo'],
                'ufCrm83Furnished' => $property['furnished'],
                'ufCrm83Bedroom' => $property['bedrooms'],
                'ufCrm83Bathroom' => $property['bathrooms'],
                'ufCrm83Parking' => $property['parkingSpaces'],
                'ufCrm83TotalPlotSize' => $property['totalPlotSize'],
                'ufCrm83LotSize' => $property['lotSize'],
                'ufCrm83BuildupArea' => $property['buildUpArea'],
                'ufCrm83LayoutType' => $property['layoutType'],
                'ufCrm83ProjectName' => $property['projectName'],
                'ufCrm83ProjectStatus' => $property['projectStatus'],
                'ufCrm83Ownership' => $property['ownership'],
                'ufCrm83Developers' => $property['developers'],
                'ufCrm83BuildYear' => $property['buildYear'],
                'ufCrm83Amenities' => $property['amenities'],

                'ufCrm83AgentName' => $property['listingAgent'],
                'ufCrm83ListingOwner' => $property['listingOwner'],
                'ufCrm83LandlordName' => $property['landlordName'],
                'ufCrm83LandlordEmail' => $property['landlordEmail'],
                'ufCrm83LandlordContact' => $property['landlordContact'],
                'ufCrm83Availability' => $property['availability'],
                'ufCrm83AvailableFrom' => $property['availableFrom'],

                'ufCrm83ReraPermitNumber' => $property['reraPermitNumber'],
                'ufCrm83ReraPermitIssueDate' => $property['reraPermitIssueDate'],
                'ufCrm83ReraPermitExpirationDate' => $property['reraPermitExpirationDate'],
                'ufCrm83DtcmPermitNumber' => $property['dtcmPermitNumber'],

                'ufCrm83TitleEn' => $property['title_english'],
                'ufCrm83DescriptionEn' => $property['description_english'],
                'ufCrm83TitleAr' => $property['title_arabic'],
                'ufCrm83DescriptionAr' => $property['description_arabic'],

                'ufCrm83Price' => $property['price'],
                'ufCrm83HidePrice' => $property['hidePrice'],
                'ufCrm83PaymentMethod' => $property['paymentMethod'],
                'ufCrm83DownPaymentPrice' => $property['downPayment'],
                'ufCrm83NoOfCheques' => $property['numCheques'],
                'ufCrm83ServiceCharge' => $property['serviceCharge'],
                'ufCrm83FinancialStatus' => $property['financialStatus'],
                'ufCrm83YearlyPrice' => $property['yearlyPrice'],
                'ufCrm83MonthlyPrice' => $property['monthlyPrice'],
                'ufCrm83WeeklyPrice' => $property['weeklyPrice'],
                'ufCrm83DailyPrice' => $property['dailyPrice'],

                'ufCrm83_1726230114498' => [
                    [
                        'NAME' => $photo['name'],
                        'CONTENT' => base64_encode(file_get_contents($photoTmpPath))
                    ]
                ],
                'ufCrm83VideoTourUrl' => $property['videoUrl'],
                'ufCrm_83_360_VIEW_URL' => $property['viewUrl'],
                'ufCrm83QrCodePropertyBooster' => $property['qrCode'],

                'ufCrm83PfLocation' => $property['propertyLocation'],
                'ufCrm83PfCity' => $property['propertyCity'],
                'ufCrm83PfCommunity' => $property['propertyCommunity'],
                'ufCrm83PfSubCommunity' => $property['propertySubCommunity'],
                'ufCrm83PfTower' => $property['propertyTower'],
                'ufCrm83BayutLocation' => $property['bayutLocation'],
                'ufCrm83BayutCity' => $property['bayutCity'],
                'ufCrm83BayutCommunity' => $property['bayutCommunity'],
                'ufCrm83BayutSubCommunity' => $property['bayutSubCommunity'],
                'ufCrm83BayutTower' => $property['bayutTower'],
                'ufCrm83Latitude' => $property['latitude'],
                'ufCrm83Longitude' => $property['longitude'],
                'ufCrm83FloorPlan' => [
                    0 => [
                        'NAME' => $floorPlan['name'],
                        'CONTENT' => base64_encode(file_get_contents($floorPlanTmpPath))
                    ]
                ],

                'ufCrm83PfEnable' => $property['pfEnable'] == 'on' ? 'Y' : 'N',
                'ufCrm83BayutEnable' => $property['bayutEnable'] == 'on' ? 'Y' : 'N',
                'ufCrm83DubizleEnable' => $property['dubizleEnable'] == 'on' ? 'Y' : 'N',
                'ufCrm83WebsiteEnable' => $property['websiteEnable'] == 'on' ? 'Y' : 'N',
            ]
        ]);

        header("Location: index.php");
    } else {
        echo "Failed to add watermark to images.<br>";
    }
}
