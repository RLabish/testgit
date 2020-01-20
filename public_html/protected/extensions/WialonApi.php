<?php


class WialonApi extends CApplicationComponent {
	public $server;
	public $port;
	public $username;
	public $password;

	private $ssid = false;
	
    public function init()   {
        parent::init();
    }
	
	function __destruct() {
		$this->logout();		
	}
	
	private function execute($method, $params = array(), $auth = True) {
		//$this->server = '10.0.0.7/work/iazs';
		//$this->port = 80;	
		if ($auth && !$this->ssid) $this->login();
		$url = sprintf("http://%s:%d/ajax.html?svc=%s&params=%s", $this->server, $this->port, $method, json_encode($params));				
		if ($this->ssid) $url = $url."&ssid=".$this->ssid;
		Yii::log('url:'.$url);
		$conn = curl_init();
		try {

//			curl_setopt($conn, CURLOPT_HTTPPROXYTUNNEL, 0);
	//		curl_setopt($conn, CURLOPT_PROXY, '127.0.0.1:8888');
			
			curl_setopt($conn, CURLOPT_URL, $url);
			curl_setopt($conn, CURLOPT_POST, 1);
			curl_setopt($conn, CURLOPT_RETURNTRANSFER, 1);		
						
			$body = curl_exec($conn);
			if ($body === False) throw new Exception(curl_error($conn));
			$res = json_decode($body, true);
			Yii::log('result:'.print_r($res, true));
		}
		finally {
			curl_close($conn);
		}
		return $res;
	}
	
	public function login() {
		if ($this->ssid) return;
		$res = $this->execute('core/login', array('user' => $this->username, 'password' => $this->password,), false);
		$this->ssid = $res['ssid'];
	}

	public function logout() {
		return;
		if (!$this->ssid) return;
		$res = $this->execute('core/logout');
		$this->ssid = False;
	}		
		
	public function getObjects() {
		$r = $this->execute('core/search_items', 
				array(
					"spec" => array(
						"itemsType" => "avl_unit",
						"propName" => "sys_name",
						"propValueMask" => "*",
						"sortType" => "sys_name",
					),
					"force" => 1,
					"flags" => 0x00000001,
					"from" => 0,
					"to" => 0xffffffff,
				)
		);
		return $r['items'];
    }
	
	public function reportClear() {
		$this->execute('report/cleanup_result');
	}

	public function report($objectId, $datefrom, $dateto) {
		$r = $this->execute('report/exec_report', 
				array(
					"reportResourceId" => 40881,
					"reportTemplateId" => 1,
					"reportObjectId" => $objectId,
					"reportObjectSecId" => 0,
					"interval" => array(
						"from" => $datefrom->getTimestamp(),
						"to" => $dateto->getTimestamp(),
						"flags" => 0
					),
					"tzOffset" => timezone_offset_get(new DateTimeZone(date_default_timezone_get()), new DateTime()),
					"lang" => "ru"
				));		
		return $r;
	/*
	 [reportResult] => Array
        (
            [msgs_rendered] => 0
            [stats] => Array
			
    print 'stats:'
    i = 0
    for x in r['stats']:
        i = i + 1
        print '| %-28s | %8s |' % (x[0], x[1])
)
	*/
    }

	
	
	public function test() {
		/*
		$r = $this->getObjects();
		echo 'Objects: <br>';
		foreach ($r as $x) {
			 printf('| %05d | %-26s|<br>', $x['id'], $x['nm']);
		}
		echo '<br>';
		*/
		$dt1 = new DateTime("2017-02-21T00:00:00Z");
		$dt2 = new DateTime("2017-02-21T23:59:59Z");
		
		$r = $this->report(256981, $dt1, $dt2);

		echo 'Report: <br>';
		
//		['reportResult']['stats']
		foreach ($r as $x) {
			 printf('| %-28s | %8s |<br>', $x['0'], $x['1']);
		}
		echo '<br>';
	}	
	
}
