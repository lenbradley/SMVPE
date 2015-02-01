<?php include 'smvpe.class.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SMVPE : Social Media Video Parse &amp; Embed</title>
</head>
<body>
    <h1>SMVPE : Social Media Video Parse &amp; Embed</h1>
    <p>Below is an example of how you would embed a list of videos using many different URL variants.</p>
    <?php
        $examples = array(
            'www.youtube.com/watch?v=6FWUjJF1ai0&feature=related',
            'youtu.be/6FWUjJF1ai0',
            'http://www.youtube.com/v/6FWUjJF1ai0?version=3&autohide=1',
            'youtube.com/embed/6FWUjJF1ai0',
            'vimeo.com/108498418',
            'player.vimeo.com/video/109485670',
            'vimeo.com/channels/staffpicks/109485670',
            'www.metacafe.com/watch/11402869/ownage_pranks_cheating_pregnant_girlfriend/?test=1&hello=1324345',
            '//metacafe.com/embed/11402869/',
            'http://www.break.com/video/new-terry-crews-old-spice-superbowl-commercial-2810755',
            '//break.com/embed/2810755',
            'http://www.dailymotion.com/video/x2f9s29_things-that-happen-in-star-wars-that-d-be-creepy-if-you-did-them_lifestyle',
            '//dailymotion.com/embed/video/x2f9s29'
        );
        
        // create new empty instance of SMVPE
        $smvpe = new SMVPE();

        // Set source by video ID and embed using chainable methods
        $smvpe->setSourceByID( '6FWUjJF1ai0', 'youtube' )->embed();

        // Pull a random URL from $examples and embed it
        $smvpe->setSource( $examples[ array_rand( $examples ) ] )->embed();

        // Use SMVPE without initiating the class first
        SMVPE::init( $examples[ array_rand( $examples ) ] )->embed();
    ?>
</body>
</html>