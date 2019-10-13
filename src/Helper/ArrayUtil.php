<?php

namespace App\Helper;

class ArrayUtil
{
    public function sortByFirstColumn($a, $b)
    {
        return $a[0] > $b[0];
    }
}