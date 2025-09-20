<div class="submission">
    
    <br>
    <h2>Submission Form</h2>

    <form class="submission-form" action="index.php?page=submissionSubmit" method="post">
        <label for="title">Title <span class="required">Required</span></label>
        <input type="text" id="title" name="title" required value="" placeholder="Type here">

        <br>
        
        <label for="questions"> Questions / Concern / comment <span class="required">Required</span> </label>
        <textarea id="questions" name="questions" rows="3" value="" placeholder="Type here" required></textarea>

        <br>

        <label for="why_this_app">Why this app? <span class="ok">Optional</span></label>
        <textarea id="why" name="why_this_app" rows="3" placeholder="Anonymity"></textarea>


        <button class="submit" type="submit">Submit</button>
    </form>
    <br>
</div>