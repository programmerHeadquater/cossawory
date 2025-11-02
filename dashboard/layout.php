<div class="layout">
    
    <div id="editForm">
        <h2>Build form</h2>
        <br>
    <div class="function">
        <label for="inputType">Select input type:</label>
        <select id="inputType" required>
            <option value="">-- Select Type --</option>
            <option value="text">text</option>
            <option value="textarea">textarea</option>
            <option value="file">file</option>
            <option value="audio">audio (record)</option>
        </select>
        
        <br><br>
        <label for="inputLabel">Label:  </label>
        <input type="text" id="inputLabel" placeholder="Enter Title" required />
        <br>
        <br>
        <label for="inputRequired">Is this feild required or optional?</label>
        <select id="inputRequired" required>
            <option value="yes" selected="selected" >Required</option>
            <option value="no">Optional</option>
        </select>
        <br>
        <br>
        
        <button class="greenBtn" id="addInputBtn">Add to the form</button>
        <br>
        <br>
        <h3>Current Form Preview:</h3>
        <div id="previewForm" class=""></div>
    </div>
    <br>
    <button class="greenBtn" id="submitFormLayout">Add to the web page</button>
    </div>

</div>
