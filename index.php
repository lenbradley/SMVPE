<?php require 'smvpe.class.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SMVPE : Social Media Video Parse &amp; Embed</title>
</head>
<body>
    <h1>SMVPE : Social Media Video Parse &amp; Embed</h1>
    <?php
        $test = array(
            'http://www.youtube.com/watch?v=6FWUjJF1ai0&feature=related',
            'http://youtu.be/6FWUjJF1ai0',
            'http://www.youtube.com/v/6FWUjJF1ai0?version=3&autohide=1',
            'www.youtube.com/embed/6FWUjJF1ai0',
            'http://vimeo.com/108498418',
            'player.vimeo.com/video/109485670',
            'http://www.metacafe.com/watch/11402869/ownage_pranks_cheating_pregnant_girlfriend/',
            '//www.metacafe.com/embed/11402869/',
            'http://www.break.com/video/new-terry-crews-old-spice-superbowl-commercial-2810755',
            '//www.break.com/embed/2810755',
            'http://www.dailymotion.com/video/x2f9s29_things-that-happen-in-star-wars-that-d-be-creepy-if-you-did-them_lifestyle',
            '//www.dailymotion.com/embed/video/x2f9s29'
        );

        $smvpe = new SMVPE();

        foreach ( $test as $url ) {
            echo '<pre>';
            $smvpe->setSource( $url );
            echo htmlentities( print_r( $smvpe->source, true ) ) . PHP_EOL;
            echo htmlentities( print_r( $smvpe->site, true ) ) . PHP_EOL;
            echo htmlentities( print_r( '---', true ) ) . PHP_EOL;
            echo '</pre>';
        }        
    ?>
</body>
</html>