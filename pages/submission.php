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

        <label for="disability">What's your disability? <span class="ok">Optional</span></label>
        <input type="text" id="disability" name="disability" placeholder="Select below Or type">
        <div class="disablityTags">
            <br>
            <ul>
                <li><a href="">Colour Blind</a></li>
                <li><a href="">Dyslexia</a></li>
                <li><a href="">Wheel chair</a></li>
                <li><a href="">Deaf</a></li>
                <li><a href="">Hear loss</a></li>
                <li><a href="">Mobility</a></li>
                <li><a href="">Old</a></li>
            </ul>
        </div>
        <br>
        <div class="tags">
            <span></span>
        </div>

        <button class="submit" type="submit">Submit</button>
    </form>
    <br>
</div>