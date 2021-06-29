<?php
function f_rand($min = 0, $max = 1, $mul = 1000000)
{
    if ($min > $max) return false;
    return mt_rand($min * $mul, $max * $mul) / $mul;
}
