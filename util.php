<?php

// Removes unwanted characters from input
//
// These characters are allowed:
//    \w  All letters and numbers
//     @  At sign
//     +  Plus sign
//     .  Period
//     -  Dash
//     _  Underscore
//    " " Optionally, whitespace can be allowed

function purify($input,$allow_whitespace=1)
{
    if ($input == "") {
        return "";
    }
    $output = strip_tags($input);
    if ($allow_whitespace) {
        $output = trim(preg_replace("/[^\w@+.\-\s\/_]/", "", $output));
    } else {
        $output = trim(preg_replace("/[^\w@+.\-\/_]/", "", $output));
    }
    $output = substr($output, 0, 512);
    return $output;
}

?>

