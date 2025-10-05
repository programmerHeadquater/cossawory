
const inputType = document.getElementById('inputType');
const inputLabel = document.getElementById('inputLabel');
const previewForm = document.getElementById('previewForm');
const addInputBtn = document.getElementById('addInputBtn');
const inputRequired = document.getElementById('inputRequired');

const list = [];

function renderPreview() {
  // Clear existing preview
  previewForm.innerHTML = '';

  list.forEach((field, index) => {
    const wrapper = document.createElement('div');
    wrapper.className = 'field-preview';
    wrapper.style.marginBottom = '10px';

    const label = document.createElement('label');
    label.textContent = field.label;
    
    wrapper.appendChild(label);
    wrapper.appendChild(document.createElement('br'));

    if (field.type === 'voice') {
      // Voice recording UI
      const recordBtn = document.createElement('button');
      recordBtn.textContent = 'Start Recording';
      recordBtn.type = 'button';


      const stopBtn = document.createElement('button');
      stopBtn.textContent = 'Stop Recording';
      stopBtn.type = 'button';
      stopBtn.disabled = true;

      const audioPreview = document.createElement('audio');
      audioPreview.controls = true;
      audioPreview.style.display = 'none';

      let mediaRecorder;
      let audioChunks = [];

      recordBtn.addEventListener('click', async () => {
        try {
          const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
          mediaRecorder = new MediaRecorder(stream);
          mediaRecorder.start();
          audioChunks = [];

          mediaRecorder.addEventListener('dataavailable', event => {
            audioChunks.push(event.data);
          });

          mediaRecorder.addEventListener('stop', () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const audioUrl = URL.createObjectURL(audioBlob);
            audioPreview.src = audioUrl;
            audioPreview.style.display = 'block';

            // Optional: Add hidden input to simulate value submission
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = field.label;
            hiddenInput.value = audioUrl;
            wrapper.appendChild(hiddenInput);
          });

          recordBtn.disabled = true;
          stopBtn.disabled = false;
        } catch (err) {
          alert('Microphone access denied or unavailable.');
        }
      });

      stopBtn.addEventListener('click', () => {
        mediaRecorder.stop();
        recordBtn.disabled = false;
        stopBtn.disabled = true;
      });

      wrapper.appendChild(recordBtn);
      wrapper.appendChild(stopBtn);
      wrapper.appendChild(audioPreview);
    }
    else if(field.type === 'image'){
      const input = document.createElement('input');
      input.type = 'file';
      input.name = field.label;
      wrapper.appendChild(input);
    }
     else {
      // Normal input
      const input = document.createElement('input');
      input.type = field.type;
      input.name = field.label;
      wrapper.appendChild(input);
    }

    // Remove button
    const removeBtn = document.createElement('button');
    removeBtn.textContent = 'Remove this input';
    removeBtn.type = 'button';
      removeBtn.classList.add('remove');
    removeBtn.style.marginLeft = '10px';
    removeBtn.addEventListener('click', () => {
      list.splice(index, 1);
      renderPreview();
    });

    wrapper.appendChild(removeBtn);

    // Append wrapper to preview area
    previewForm.appendChild(wrapper);
  });
}

addInputBtn.addEventListener('click', () => {
  const type = inputType.value.trim();
  const label = inputLabel.value.trim();
  const required = inputRequired.value.trim();
  
  if (!type || !label) {
    alert('Please fill out both the input type and label.');
    return;
  }

  if (list.some(field => field.label.toLowerCase() === label.toLowerCase())) {
    alert('Label must be unique.');
    return;
  }
 
 
    list.push({ label, type , required});
  
  
  renderPreview();

  // Clear input fields
  inputType.value = '';
  inputLabel.value = '';
  
});


const submitFormLayout = document.getElementById('submitFormLayout');
submitFormLayout.addEventListener('click', async () => {
  if (list.length === 0) {
    alert('Add some fields before submitting!');
    return;
  }

  const fieldsToSend = list.map(field => ({
    ...field,
    name: field.label.toLowerCase().replace(/\s+/g, '_').replace(/[^\w]/g, '')
  }));

  await fetch('dashboard/form.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      action: 'save_form',
      fields: fieldsToSend
    })
  })
    .then(response => response.json())
    .then(data => {
      console.log('Response:', data);
      if (data.status === 'success') {
        alert('Form saved successfully!');
        renderPreview();
      } else {
        alert('Error: ' + data.message);
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      alert('Failed to submit form layout.');
    });
});

async function loadForm() {
  try {
    const response = await fetch('dashboard/form.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ action: 'get_form' })
    });

    const data = await response.json();

    if (data.status === 'success' && Array.isArray(data.fields)) {
      // console.log(data)
      // Update the list with saved fields
      // Make sure the fields match your expected structure
      list.length = 0; // clear existing list
      data.fields.forEach(field => {
        // We expect 'name' and 'type' from backend, but your UI uses 'label' for display
        // So convert 'name' back to label by replacing underscores with spaces and capitalizing
        
        const label = field.name
          .replace(/_/g, ' ')
          .replace(/\b\w/g, char => char.toUpperCase());
        list.push(field);
        console.log(list)
      });
      
      renderPreview();
    } else {
      console.log('No saved form found or error:', data.message);
      // Optionally, you can start with an empty form or default fields here
    }
  } catch (error) {
    console.error('Failed to load form:', error);
  }
}

// Call loadForm when page loads
window.addEventListener('DOMContentLoaded', loadForm);
