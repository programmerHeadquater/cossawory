<div class="layout">
    
    <div id="editForm">
        <h2>Build form</h2>
    <div class="function">
        <label for="inputType">Select input type:</label>
        <select id="inputType" required>
            <option value="">-- Select Type --</option>
            <option value="text">text</option>
            <option value="textarea">textarea</option>
            <option value="file">file</option>
            <option value="voice">voice (record)</option>
        </select>
        <br>
        <br>
        <br><br>
        <label for="inputLabel">Label (Unique):  </label>
        <input type="text" id="inputLabel" placeholder="Enter label" required />
        <br>
        <br>
        <label for="inputRequired">Does this feild required / optional</label>
        <select id="inputRequired" required>
            <option value="yes" selected="selected" >Yes</option>
            <option value="no">No</option>
        </select>
        <br>
        <br>
        
        <button id="addInputBtn">Add this input</button>
        <br>
        <br>
        <h3>Current Form Preview:</h3>
        <div id="previewForm" class=""></div>
    </div>
    <br>
    <button id="submitFormLayout">Add this to web page</button>
    </div>

</div>
