<?php

header('Content-Type:text/html;charset=utf-8');

$arr = array(66,2,11,8,9,23,2,390,233,0,30,230,23);


//冒泡排序
function bubble_sort($arr) {
    $len = count($arr);
    for ($i=0; $i < $len-1; $i++) { 
        for ($j=0; $j < $len-$i-1; $j++) { 
            if ($arr[$j] > $arr[$j+1]) {
                $temp = $arr[$j];
                $arr[$j] = $arr[$j+1];
                $arr[$j+1] = $temp;
            }
        }

    }
    return $arr;
}


//快速排序
function quick_sort($arr) {

    if (count($arr) < 2) {
        return $arr;
    }

    $key = $arr[0];
    $right_arr = $left_arr = array();

    for ($i=1; $i < count($arr); $i++) { 
        if ($arr[$i] < $key) {
            $right_arr[] = $arr[$i];
        } else {
            $left_arr[] = $arr[$i];
        }
    }

    $right_arr = quick_sort($right_arr);
    $left_arr = quick_sort($left_arr);

    return array_merge($right_arr, array($key), $left_arr);
}

// $arr = quick_sort($arr);


//选择排序
function select_sort($arr) {
    $len = count($arr);
    for ($i=0; $i < $len; $i++) { 
        $temp = $arr[$i];
        for ($j=$i+1; $j < $len; $j++) { 
            if ($arr[$j] < $temp) {
                $temp = $arr[$j];
                $k = $j;
            }
        }
        $arr[$k] = $arr[$i];
        $arr[$i] = $temp;
    } 
    return $arr;
}


//插入排序
function insert_sort($arr) {
    for ($i=1; $i < count($arr); $i++) { 
        for ($j=$i-1; $j >= 0; $j--) { 
            if ($arr[$j+1] < $arr[$j]) {
                $temp = $arr[$j+1];
                $arr[$j+1] = $arr[$j];
                $arr[$j] = $temp;
            }
        }
    }

    return $arr;
}


?>