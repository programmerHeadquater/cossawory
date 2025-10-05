<?php
// Path to your JSON file
$jsonFile = __DIR__ . '/../dashboard/form.json';


// Default fallback fields if JSON file not found or empty


// Read JSON file content
$fields = [];
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $decoded = json_decode($jsonContent, true);

    if (is_array($decoded) && count($decoded) > 0) {
        $fields = $decoded;  // keep the array as it is, no changes
    }
}
echo '<pre>';
var_dump($fields);
echo '</pre>';
?>

<div class="submission">
    <br>
    <h2>Submission Form</h2>

    <form class="submission-form" enctype="multipart/form-data" action="index.php?page=submissionSubmit" method="post">

        <?php foreach ($fields as $field): ?>
            <?php
            // Make a "name" attribute for form input from label
            $name = strtolower(str_replace(' ', '_', $field['label']));
            ?>

            <label for="<?php echo htmlspecialchars($name); ?>">
                <?php echo htmlspecialchars($field['label']); ?>
                <?php if ($field['required'] === 'yes'): ?>
                    <span class="required">Required</span>
                <?php else: ?>
                    <span class="ok">Optional</span>
                <?php endif; ?>
            </label>

            <?php if ($field['type'] === 'textarea'): ?>
                <textarea id="<?php echo htmlspecialchars($name); ?>" name="<?php echo htmlspecialchars($name); ?>" rows="3"
                    placeholder="Type here" <?=$field['required'] =='yes' ?"required":""?>></textarea>
            <?php else: ?>
                <input type="<?php echo htmlspecialchars($field['type']) ?>" id="<?php echo htmlspecialchars($name); ?>"
                    name="<?php echo htmlspecialchars($name); ?>" <?=$field['required'] =='yes' ?"required":""?> placeholder="Type here" />
            <?php endif; ?>
            <br><br>
        <?php endforeach; ?>

        <button class="submit" type="submit">Submit</button>
    </form>
    <br>
</div>