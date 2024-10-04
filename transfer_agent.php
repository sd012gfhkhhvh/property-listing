<?php
require_once(__DIR__ . '/crest/crest.php');
require_once(__DIR__ . '/crest/settings.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve submitted form data
    $propertyIdsString = isset($_POST['transferAgentPropertyIds']) ? $_POST['transferAgentPropertyIds'] : '';
    $agent_id = isset($_POST['agent_id']) ? $_POST['agent_id'] : '';

    // Convert the property IDs string to an array
    $propertyIds = explode(',', $propertyIdsString);

    // echo '<pre>';
    // print_r($propertyIds);  // Debug: Print the property IDs
    // print_r($agent_id);     // Debug: Print the selected agent ID
    // echo '</pre>';

    if (!empty($propertyIds)) {
        $agent_res = CRest::call('crm.item.get', [
            'entityTypeId' => LISTING_AGENTS_ENTITY_TYPE_ID,
            'id' => $agent_id
        ]);
        $agent = $agent_res['result']['item'] ?? null;

        // echo '<pre>';
        // print_r($agent);  // Debug: Print the selected agent
        // echo '</pre>';

        if (!$agent) {
            header('Location: index.php');
            exit;
        }

        // Transfer properties to the selected agent
        foreach ($propertyIds as $propertyId) {
            $res = CRest::call('crm.item.update', [
                'entityTypeId' => PROPERTY_LISTING_ENTITY_TYPE_ID,
                'id' => $propertyId,
                'fields' => [
                    'ufCrm83AgentId' => $agent_id,
                    'ufCrm83AgentName' => $agent['ufCrm95AgentName'],
                    'ufCrm83AgentEmail' => $agent['ufCrm95AgentEmail'],
                    'ufCrm95AgentMobile' => $agent['ufCrm95AgentPhone'],
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
