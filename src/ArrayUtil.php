<?php

namespace App;

class ArrayUtil
{
    public function sortByFirstColumn($a, $b)
    {
        return $a[0] > $b[0];
    }
}