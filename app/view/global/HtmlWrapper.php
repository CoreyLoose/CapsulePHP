<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $charset ?>"/>

        <title><?php echo $title ?></title>

        <?php
        foreach( $cssIncludes as $css) {
            echo '<link rel="stylesheet" type="text/css" href="',$css,'"/>';
        }
        ?>
    </head>
    <body>
        <?php
        echo $body;

        foreach( $jsIncludes as $js) {
            echo '<script src="',$js,'" type="text/javascript"></script>'."\n";
        }
        ?>
    </body>
</html>