<?php
header('Content-Type:text/xml;charset=utf-8');

/**
* 数组转XML
*/
function array_to_xml($arr) {
    $xml = '';
    if (!empty($arr)) {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                if (array_key_exists(0, $value)) {
                    
                    foreach ($value as $kk => $vv) {
                        $xml .= '<'.$key.' id="'.$key.'_'.$kk.'">'."\n"; 
                        $xml .= array_to_xml($vv);
                        $xml .= '</'.$key.'>'."\n"; 
                    }

                } else {

                    if (!is_numeric($key)) {
                       $xml .= '<'.$key.'>'."\n"; 
                    }
                    
                    $xml .= array_to_xml($value);
                    
                    if (!is_numeric($key)) {
                       $xml .= '</'.$key.'>'."\n"; 
                    }

                }   
            } else {
                $xml .= '<'.$key.'>'.$value.'</'.$key.'>'."\n";
            }
        }
    } 
    return $xml;
}

$arr = array(
        'a' => 123,
        'b' => 456,
        'c' => array(
                'aa' => 343,
                'bb' => 2389,
                'cc' => 233334,
            ),
        'd' => array(
                array('aaa' => 888,'bbb' => 999),
                array('aaa' => 111,'bbb' => 6788),
            ),
    );

$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
$xml .= '<body>'."\n";
$xml .= array_to_xml($arr);
$xml .= '</body>';


echo $xml;

?>