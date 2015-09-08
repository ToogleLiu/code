<?php
	
header("Content-Type:text/html; charset=utf-8");

/**
* XML转成数组 (XML to array)
* @param string $xml XML字符串
* @return array
*/
function xml_to_array($xml) {
	if (stripos($xml, '<?xml') !== false) {
		$xml = preg_replace('/<\?xml.*?\?>/is', '', $xml);
	}
	
	$result = array();
	$pattern = '/<\s*(.*?)(\s+.*?)?>(.*?)<\s*\/\s*\1\s*>/is';

	preg_match_all($pattern, $xml, $matches);

	if (!empty($matches[3][0])) {
		foreach ($matches[3] as $key => $value) {
			preg_match_all($pattern, $value, $matches_v);
			if (!empty($matches_v[3][0])) {
				$ret = xml_to_array($value);
			} else {
				$ret = $value;
			}

			if (array_key_exists($matches[1][$key], $result) && !empty($result[$matches[1][$key]])) {
				if (is_array($result[$matches[1][$key]]) && array_key_exists(0, $result[$matches[1][$key]])) {
					$result[$matches[1][$key]][] = $ret;					
				} else {
					$result[$matches[1][$key]] = array($result[$matches[1][$key]], $ret);
				}
			} else {
				$result[$matches[1][$key]] = $ret;
			}
		}
	}

	return $result;
}

/**
* an example
*/

$xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
	<current_observation>
		<credit>NOAA's National Weather Service</credit> 
		<weather name="hello">A Few Clouds</weather> 
		<image id="image1">
		   <url>http://weather.gov/images/xml_logo.gif</url> 
		   <title>NOAA's National Weather Service</title> 
		   <link>http://weather.gov</link> 
		</image>
		<image id="image2">
		   <url>http://weather.gov/images/xml_logo.png</url> 
		   <title>NOAA's National Weather Service Test</title> 
		   <link>http://weather.gov.cn</link> 
		</image>
	</current_observation>
XML;

$array = xml_to_array($xml);
echo "<pre>";
print_r($array);


?>
