<?php

function Combinations ($arrays, $N=-1, $count=FALSE, $weight=FALSE)
{   
    if ($N == -1) {
        
        $arrays = array_values($arrays);
        $count = count($arrays);
        $weight = array_fill(-1, $count+1, 1);
        $Q = 1;
        
        foreach ($arrays as $i=>$array) {
            $size = count($array);
            $Q = $Q * $size;
            $weight[$i] = $weight[$i-1] * $size;
        }
        
        $result = array();
        for ($n=0; $n<$Q; $n++)
            $result[] = Combinations($arrays, $n, $count, $weight);
        
        return $result;
    }
    else {

        $SostArr = array_fill(0, $count, 0);
        
        $oldN = $N;
        
        // Идём по радрядам начиная с наибольшего
        for ($i=$count-1; $i>=0; $i--)
        {
            $SostArr[$i] = floor( $N/$weight[$i-1] );
            $N = $N - $SostArr[$i] * $weight[$i-1];
        }
        
        $result = array();
        for ($i=0; $i<$count; $i++)
            $result[$i] = $arrays[$i][ $SostArr[$i] ];
        
        return $result;
    }   
}

?>