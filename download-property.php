<?php
require_once __DIR__ . '/crest/crest.php';
require_once __DIR__ . '/crest/settings.php';
require_once __DIR__ . '/fpdf186/fpdf.php'; // Make sure the path to FPDF is correct

if (!isset($_GET['id'])) {
    echo "No property ID provided!";
    exit;
}

$propertyId = $_GET['id'];

$response = CRest::call('crm.item.get', [
    'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
    'id' => $propertyId
]);

$property = $response['result']['item'] ?? null;

if (!$property) {
    echo "Property not found!";
    exit;
}

// Initialize FPDF and configure settings
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Add Title
$pdf->Cell(0, 10, 'Property Details', 0, 1, 'C');
$pdf->Ln(10);

// Add Property Information
$pdf->SetFont('Arial', '', 12);

// Add details in a structured manner
$pdf->Cell(40, 10, 'Title: ');
$pdf->Cell(0, 10, $property['ufCrm83TitleEn'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Price: ');
$pdf->Cell(0, 10, $property['ufCrm83Price'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Property Type: ');
$pdf->Cell(0, 10, ucfirst($property['ufCrm83PropertyType'] ?? 'N/A'), 0, 1);

$pdf->Cell(40, 10, 'Property Status: ');
$pdf->Cell(0, 10, ucfirst($property['ufCrm83PropertyStatus'] ?? 'N/A'), 0, 1);

$pdf->Cell(40, 10, 'Description: ');
$pdf->MultiCell(0, 10, $property['ufCrm83DescriptionEn'] ?? 'N/A');

$pdf->Cell(40, 10, 'City: ');
$pdf->Cell(0, 10, $property['ufCrm83PfCity'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Community: ');
$pdf->Cell(0, 10, $property['ufCrm83PfCommunity'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Sub Community: ');
$pdf->Cell(0, 10, $property['ufCrm83PfSubCommunity'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Tower/Building: ');
$pdf->Cell(0, 10, $property['ufCrm83PfTower'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Bedrooms: ');
$pdf->Cell(0, 10, $property['ufCrm83Bedroom'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Bathrooms: ');
$pdf->Cell(0, 10, $property['ufCrm83Bathroom'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Size: ');
$pdf->Cell(0, 10, $property['ufCrm83Size'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Financial Status: ');
$pdf->Cell(0, 10, $property['ufCrm83FinancialStatus'] ?? 'N/A', 0, 1);

$pdf->Cell(40, 10, 'Available From: ');
$pdf->Cell(0, 10, $property['ufCrm83AvailableFrom'] ?? 'N/A', 0, 1);

// Add more fields as needed...

// Output the PDF file to the browser for download
$fileName = 'Property_Details_' . $propertyId . '.pdf';
$pdf->Output('D', $fileName); // D forces the download

// Redirect back to index.php after download
// header('Location: index.php');
exit;
