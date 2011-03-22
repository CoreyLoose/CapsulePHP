<p>Sorry we couldn't find the page you were looking for.</p>

<?php
if( $debug ) {
	echo '<p>Attempted url: "',$attemptedUrl,'"</p>';
	
	if( $debugMessage ) {
		echo '<p>'.$debugMessage.'</p>';
	}
	
	if( isset(capsule()->tree) ) {
		capsule()->tree->draw();
	}
}