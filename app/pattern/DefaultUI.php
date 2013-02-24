<?php
class DefaultUI
{
    public function __construct() {
        capsule()->resource->load('/css/global.css');
    }

    public function draw($content)
    {
        if( capsule()->tree->currentDepth() == 1 ) {
            $current_title = capsule()->output->getTitle();

            $single_col_params = array('content' => $content);

            capsule()->template->draw('layouts/single_col', $single_col_params);
        }
        else {
            echo $content;
        }
    }
}
