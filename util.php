<?php

// Removes bad characters from input
function purify($input,$allow_whitespace=1)
{
    $output = strip_tags($input);
    if ($allow_whitespace) {
        $output = trim(preg_replace("/[^\w@+.\-\s\/]/", "", $output));
    } else {
        $output = trim(preg_replace("/[^\w@+.\-\/]/", "", $output));
    }
    $output = substr($output, 0, 120);
    return $output;
}

?>

