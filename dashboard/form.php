<?php
session_name("MySecureAppSession");
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "status" => "fail",
        "message" => "Unauthorized access. Please login as admin."
    ]);
    exit;
}

// Read raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Check if action is set
if (!$data || !isset($data['action'])) {
    echo json_encode(["status" => "fail", "message" => "No action specified"]);
    exit;
}

$action = $data['action'];
$file = __DIR__ . '/form.json';

switch ($action) {
    case 'save_form':
        if (!isset($data['fields']) || !is_array($data['fields'])) {
            echo json_encode(["status" => "fail", "message" => "Invalid fields data"]);
            exit;
        }

        $fields = $data['fields'];

        // Validate unique names
        $names = array_column($fields, 'name');
        if (count($names) !== count(array_unique($names))) {
            echo json_encode(["status" => "fail", "message" => "Duplicate field names found"]);
            exit;
        }

        // Save fields to file
        if (file_put_contents($file, json_encode($fields, JSON_PRETTY_PRINT))) {
            echo json_encode(["status" => "success", "message" => "Form saved successfully."]);
        } else {
            echo json_encode(["status" => "fail", "message" => "Failed to save form data."]);
        }
        break;

    case 'get_form':
        // Return saved form JSON if file exists
        if (file_exists($file)) {
            $json = file_get_contents($file);
            echo json_encode([
                "status" => "success",
                "fields" => json_decode($json, true)
            ]);
        } else {
            echo json_encode(["status" => "fail", "message" => "No saved form found"]);
        }
        break;

    default:
        echo json_encode(["status" => "fail", "message" => "Unknown action"]);
        break;
}
