<?php
require_once 'conn/submission.php';
use function submission\insertSubmissionFromJson;

// Path to your form structure JSON file
$jsonFile = __DIR__ . '/../dashboard/form.json'; // adjust path if needed

$formFields = [];
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $formFields = json_decode($jsonContent, true);
} else {
    exit("❌ Form structure file not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resultData = [];

    foreach ($formFields as $field) {
        $fieldData = $field;

        if (!isset($field['name'])) {
            continue;
        }

        $name = $field['name'];

        // Handle file-based inputs (file, image, video, audio)
        if (isset($field['type']) && in_array($field['type'], ['file', 'image', 'video', 'audio'])) {
            if (isset($_FILES[$name]) && $_FILES[$name]['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $originalName = basename($_FILES[$name]['name']);
                $safeName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', $originalName);

                $destination = $uploadDir . $safeName;

                // Optional: filter by MIME type (you can customize this)
                $allowedMimeTypes = [
                    'image/jpeg', 'image/png', 'application/pdf',
                    'video/mp4', 'video/mpeg',
                    'audio/mpeg', 'audio/mp3', 'audio/wav', 'audio/ogg' ,'audio/webm'
                ];

                if (!in_array($_FILES[$name]['type'], $allowedMimeTypes)) {
                    echo "<p>❌ Invalid file type for '{$name}': {$_FILES[$name]['type']}</p>";

                    break;
                }

                // Optional: size limit (10MB)
                $maxSize = 10 * 1024 * 1024;
                if ($_FILES[$name]['size'] > $maxSize) {
                    echo "<p>❌ File too large for '{$name}' (max 10MB)</p>";
                    continue;
                }

                // Move file
                if (move_uploaded_file($_FILES[$name]['tmp_name'], $destination)) {
                    $fieldData['value'] = [
                        'name' => $_FILES[$name]['name'],
                        'type' => $_FILES[$name]['type'],
                        'path' => 'uploads/' . $safeName
                    ];
                } else {
                    echo "<p>❌ Failed to save uploaded file for '{$name}'</p>";
                }
            }
        } else {
            // Handle text-based inputs
            if (isset($_POST[$name]) && $_POST[$name] !== '') {
                $fieldData['value'] = $_POST[$name];
            }
        }

        // Save the field only if it has a value
        if (isset($fieldData['value'])) {
            $resultData[] = $fieldData;
            
        }
    }

    // ✅ Display submitted data (for debugging)
    echo "<h2>✅ Final Submitted Data</h2>";
    echo "<pre>" . htmlspecialchars(json_encode($resultData, JSON_PRETTY_PRINT)) . "</pre>";

    // ✅ Submit the data to your backend function
    $response = insertSubmissionFromJson($resultData);

    echo "<h3>📥 Submission Response</h3>";
    echo "<pre>";
    print_r($response);
    echo "</pre>";
} else {
    echo "<p>📭 Please submit the form.</p>";
}
