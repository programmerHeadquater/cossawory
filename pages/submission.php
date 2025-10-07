<title>Submision</title>
<?php
// Path to your JSON file
$jsonFile = __DIR__ . '/../dashboard/form.json';

// Read JSON file content
$fields = [];
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $decoded = json_decode($jsonContent, true);
    if (is_array($decoded) && count($decoded) > 0) {
        $fields = $decoded;
    }
}
?>

<div class="submission">
    <br>
    <h2>Submission Form</h2>

    <form class="submission-form" enctype="multipart/form-data" action="index.php?page=submissionSubmit" method="post">
        <?php $firstFocusableField = true; ?>
        <?php foreach ($fields as $field): ?>
            <?php
            $name = strtolower(str_replace(' ', '_', $field['label']));
            $inputId = 'input_' . $name;
            // Set autofocus only once
            $autofocus = $firstFocusableField ? 'autofocus' : '';
            ?>

            <label for="<?= htmlspecialchars($inputId); ?>">
                <?= htmlspecialchars($field['label']); ?>
                <?php if ($field['required'] === 'yes'): ?>
                    <span class="required">Required</span>
                <?php else: ?>
                    <span class="ok">Optional</span>
                <?php endif; ?>
            </label>

            <?php if ($field['type'] === 'textarea' || $field['type'] === 'text'): ?>
                <textarea id="<?= htmlspecialchars($inputId); ?>" name="<?= htmlspecialchars($name); ?>" rows="3"
                    placeholder="Type here" <?= $field['required'] === 'yes' ? 'required' : '' ?> <?= $autofocus ?>></textarea>
                    <?php $firstFocusableField = false; ?>

            <?php elseif ($field['type'] === 'file'): ?>
                <input type="file" id="<?= htmlspecialchars($inputId); ?>" name="<?= htmlspecialchars($name); ?>"
                    placeholder="Type here" <?= $field['required'] === 'yes' ? 'required' : '' ?> <?= $autofocus ?> />
                    <?php $firstFocusableField = false;  ?>

            <?php elseif ($field['type'] === 'audio'): ?>
                <div class="audio-recorder">
                    <button class="greenBtn" type="button" onclick="startRecording(this)" data-name="<?= $name ?>">🎙️ Start</button>

                    <button type="redBtn button" onclick="stopRecording(this)" data-name="<?= $name ?>" disabled>🛑
                        Stop</button><br><br>

                    <audio id="audioPlayback_<?= $name ?>" controls style="display: none;"></audio>

                    <input type="file" id="audioBlob_<?= $name ?>" name="<?= htmlspecialchars($name); ?>" accept="audio/*"
                        style="display: none;" <?= $field['required'] === 'yes' ? 'required' : '' ?> <?= $autofocus ?> />
                        <?php $firstFocusableField = false;  ?>
                </div>
            <?php endif; ?>

            <br><br>
        <?php endforeach; ?>

        <button class="submit" type="submit">Submit</button>
    </form>
    <br>
</div>

<!-- ✅ JavaScript to handle per-field audio recording -->
<script>
    const recorders = {};

    function startRecording(button) {
        const name = button.dataset.name;
        const stopBtn = document.querySelector(`button[data-name="${name}"][onclick="stopRecording(this)"]`);
        const playback = document.getElementById('audioPlayback_' + name);
        const input = document.getElementById('audioBlob_' + name);

        navigator.mediaDevices.getUserMedia({ audio: true })
            .then(stream => {
                const mediaRecorder = new MediaRecorder(stream);
                const chunks = [];

                // Store everything per field name
                recorders[name] = {
                    mediaRecorder,
                    stream,
                    chunks,
                    playback,
                    input
                };

                mediaRecorder.ondataavailable = event => {
                    if (event.data.size > 0) {
                        chunks.push(event.data);
                    }
                };

                mediaRecorder.onstop = () => {
                    const blob = new Blob(chunks, { type: 'audio/webm' });
                    const url = URL.createObjectURL(blob);

                    playback.src = url;
                    playback.style.display = 'block';

                    const file = new File([blob], 'recorded_audio.webm', { type: 'audio/webm' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    input.files = dataTransfer.files;

                    // Stop the mic stream to release resources
                    stream.getTracks().forEach(track => track.stop());
                };

                mediaRecorder.start();

                button.disabled = true;
                stopBtn.disabled = false;
            })
            .catch(error => {
                alert('Microphone access denied or error: ' + error.message);
            });
    }

    function stopRecording(button) {
        const name = button.dataset.name;
        const startBtn = document.querySelector(`button[data-name="${name}"][onclick="startRecording(this)"]`);

        if (recorders[name] && recorders[name].mediaRecorder.state !== 'inactive') {
            recorders[name].mediaRecorder.stop();
            button.disabled = true;
            startBtn.disabled = false;
        }
    }
</script>