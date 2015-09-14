<?php

/**
* 
* 
*/
class HDZZ
{
	
	const HTTP_URL  = 'http://xxx.com';

	const HTTP_POST = 'POST';
	const HTTP_GET  = 'GET';

	const AGENT_NAME = 'kehuceshi2';		//代理名称
	const AGENT_PASSWD = 'kehuceshi2';		//代理密码

	private $error_code = 0;
	private $error_message = '';

	
	/**
	* curl
	*
	*/
	private function authenticate($method, $opt = NULL)
	{
		if (empty($opt['url_opts']['action'])) {
			return false;
		}

		if (is_array($opt['req_opts']['body']) && !empty($opt['req_opts']['body'])) {
			$curlFields = $this->format_url($opt['req_opts']['body']);
		} else {
			return false;
		}

		$url = self::HTTP_URL . '/' . $opt['url_opts']['action'];
		if ($method == 'GET') {
			$url .= '?' . $curlFields;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlFields);
		}
		if ($method == 'GET') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		}
		
		$ret = curl_exec($ch);
		curl_close($ch);

		if (stripos($ret, '<?xml') !== false) {
			//xml转成数组
			// return json_decode(json_encode(simplexml_load_string($ret)),1);
			return $this->xml_to_array($ret);
		} else {
			return $ret;
		}
	}

	/**
	* 格式化参数
	*
	*/
	private function format_url(array $param)
	{
		if (empty($param) || !is_array($param)) {
			return '';
		}

		$curlFields = '';
		$first = false;
		foreach ($param as $key => $value) {
			if ($first == false) {
				$first = true;
				$curlFields .= $key . '=' . rawurlencode($value);
			} else {
				$curlFields .= '&' . $key . '=' . rawurlencode($value);
			}
		}

		return $curlFields;
	}

	/**
	*	将xml 数据转换为数组
	*	@param  $xml   
	*	@return array
	*/

	private function xml_to_array($xml){
        if (stripos($xml, '<?xml') !== false) {
            $xml = preg_replace('/<\?xml.*\?>/is', '', $xml);
        }
        $reg = "/<(\\w+)[^>]*?>([\\x00-\\xFF]*?)<\\/\\1>/";
        if(preg_match_all($reg, $xml, $matches))
        {
            $count = count($matches[0]);
            $arr = array();
            for($i = 0; $i < $count; $i++)
            {
                $key= $matches[1][$i];
                $val = $this -> xml_to_array( $matches[2][$i] );  // 递归
                if(array_key_exists($key, $arr))
                {
                    if(is_array($arr[$key]))
                    {
                        if(!array_key_exists(0,$arr[$key]))
                        {
                            $arr[$key] = array($arr[$key]);
                        }   
                    }else{
                        $arr[$key] = array($arr[$key]);
                    }   
                    $arr[$key][] = $val;
                }else{
                    $arr[$key] = $val;
                }
            }
        
            return $arr;
            
        }else{
            return $xml;
        }
    }	

	/**
	*
	* @param array $param 
	* @param string $func 
	* @param bool
	*/
	private function execute(array $param = array(), $func, $noAgent = FALSE)
	{
		$this->clear_error();
        $method = self::HTTP_POST;
  
  		if ($noAgent == FALSE) {
	  		$param['agentname'] = self::AGENT_NAME;
	  		$param['agentpasswd'] = self::AGENT_PASSWD;
	  	}

        $opt['url_opts']['action'] = $func;
        $opt['req_opts']['body'] = $param;
	
        try{
            $ret = $this->authenticate($method, $opt);
            return $ret;
        }catch (Exception $ex) {
            $this->error_code = -2;
            $this->error_message = $ex->getMessage();
            return false;
        }
	}

	/**
	* 处理接口返回的数据
	* @param  $result 接口返回的数据
	* @param  $retFields array 用来拼数据的字段名，当返回用逗号分隔的字符串时要用到
	* @return $result
	*/
	private function handleResult($result, array $retFields)
	{
		if (!empty($result)) {
			if (is_string($result)) {
				$result = array($result);
			}
			if (is_array($result)) {
				foreach ($result as $key => $value) {
					$value = explode(',', $value);
					if (is_array($retFields) && count($retFields) == count($value)) {
						$result[$key] = array_combine($retFields, $value);
					}
				}
				if (count($result) == 1) {
					$result = $result[0];
				}
			}
		}
		return $result;
	}

	private function clear_error(){
		$this->error_code = 0;
		$this->error_message = '';
	}

    /**
	* 添加黑名单
	* @param $param['userphone'] string  需要拉黑的电话号码
	* @return 
	*
	*/
	public function addBlack(array $param) {
		$ret = $this->execute($param, 'AddBlack');
		return $ret;
    }

    /**
	* @param $param['username']
	* @return array	
	*
	*/
	public function checkUserStatus(array $param) {

		$ret = $this->execute($param, 'CheckUserStatus');
		
		if (isset($ret['ArrayOfAnyType']['anyType']) && !empty($ret['ArrayOfAnyType']['anyType'])) {
			$retFields = array(
						'userid', 
						'usernumber', 	
						'userlogintime',
						'groupid', 	
						'userstatus', 	
						'SetbusyIs', 	
						'Callalltime',	
					);

			$ret['ArrayOfAnyType']['anyType'] = $this->handleResult($ret['ArrayOfAnyType']['anyType'], $retFields);
		}
		return $ret;
		
    }

}

?>