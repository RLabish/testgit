<?php
class ApiController extends Controller
{
    // Members
    /**
     * Key which has to be in HTTP USERNAME and PASSWORD headers 
     */
    Const APPLICATION_ID = 'ASCCPE';
 
    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'json';
    /**
     * @return array action filters
     */
    public function filters()
    {
            return array();
    }
 
	private function _sendResponse($status = 200, $body = '', $content_type = 'text/html')
	{
		// set the status
		$status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
		header($status_header);
		// and the content type
		header('Content-type: ' . $content_type);
 
		// pages with body are easy
		if($body != '')
		{
			// send the body
			echo $body;
		}
		// we need to create the body if none is passed
		else
		{
			// create some body messages
			$message = '';
 
			// this is purely optional, but makes the pages a little nicer to read
			// for your users.  Since you won't likely send a lot of different status codes,
			// this also shouldn't be too ponderous to maintain
			switch($status)
			{
				case 401:
					$message = 'You must be authorized to view this page.';
					break;
				case 404:
					$message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
					break;
				case 500:
					$message = 'The server encountered an error processing your request.';
					break;
				case 501:
					$message = 'The requested method is not implemented.';
					break;
			}
 
			// servers don't always have a signature turned on 
			// (this is an apache directive "ServerSignature On")
			$signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];
 
			// this should be templated in a real-world solution
			$body = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
</head>
<body>
    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
    <p>' . $message . '</p>
    <hr />
    <address>' . $signature . '</address>
</body>
</html>';
 
			echo $body;
		}
		Yii::app()->end();
	}
	
	private function _getStatusCodeMessage($status)
	{
		// these could be stored in a .ini file and loaded
		// via parse_ini_file()... however, this will suffice
		// for an example
		$codes = Array(
			200 => 'OK',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
		);
		return (isset($codes[$status])) ? $codes[$status] : '';
	}	
	
function json_fix_cyr($json_str) {
    $cyr_chars = array (
        '\u0404' => 'Є',
        '\u0456' => 'і',  '\u0406' => 'I',
	

        '\u0430' => 'а', '\u0410' => 'А',
        '\u0431' => 'б', '\u0411' => 'Б',
        '\u0432' => 'в', '\u0412' => 'В',
        '\u0433' => 'г', '\u0413' => 'Г',
        '\u0434' => 'д', '\u0414' => 'Д',
        '\u0435' => 'е', '\u0415' => 'Е',
        '\u0451' => 'ё', '\u0401' => 'Ё',
        '\u0436' => 'ж', '\u0416' => 'Ж',
        '\u0437' => 'з', '\u0417' => 'З',
        '\u0438' => 'и', '\u0418' => 'И',
        '\u0439' => 'й', '\u0419' => 'Й',
        '\u043a' => 'к', '\u041a' => 'К',
        '\u043b' => 'л', '\u041b' => 'Л',
        '\u043c' => 'м', '\u041c' => 'М',
        '\u043d' => 'н', '\u041d' => 'Н',
        '\u043e' => 'о', '\u041e' => 'О',
        '\u043f' => 'п', '\u041f' => 'П',
        '\u0440' => 'р', '\u0420' => 'Р',
        '\u0441' => 'с', '\u0421' => 'С',
        '\u0442' => 'т', '\u0422' => 'Т',
        '\u0443' => 'у', '\u0423' => 'У',
        '\u0444' => 'ф', '\u0424' => 'Ф',
        '\u0445' => 'х', '\u0425' => 'Х',
        '\u0446' => 'ц', '\u0426' => 'Ц',
        '\u0447' => 'ч', '\u0427' => 'Ч',
        '\u0448' => 'ш', '\u0428' => 'Ш',
        '\u0449' => 'щ', '\u0429' => 'Щ',
        '\u044a' => 'ъ', '\u042a' => 'Ъ',
        '\u044b' => 'ы', '\u042b' => 'Ы',
        '\u044c' => 'ь', '\u042c' => 'Ь',
        '\u044d' => 'э', '\u042d' => 'Э',
        '\u044e' => 'ю', '\u042e' => 'Ю',
        '\u044f' => 'я', '\u042f' => 'Я',
 
        '\r' => '',
        '\n' => '<br />',
        '\t' => ''
    );
 
    foreach ($cyr_chars as $cyr_char_key => $cyr_char) {
        $json_str = str_replace($cyr_char_key, $cyr_char, $json_str);
    }
    return $json_str;
}	
	
	private function _authByToken() {
		return;
	    if(!isset($_GET['token']) || (strlen($_GET['token']) != 32)) $this->_sendResponse(500, 'Error: Invalid token' );
		Yii::app()->user->logout();	
		$token = $_GET['token'];
		$m = new Users();
		$user = $m->find("token=:x", array(":x"=>$token));
		if (!$user) $this->_sendResponse(500, 'Invalid token');
		$identity = new UserIdentity($user->username, $user->password);
		if(!$identity->authenticate()) $this->_sendResponse(403);
		Yii::app()->user->login($identity);
	}

    // Actions
    public function actionMethod()
    {
	#	echo 'method: '.$_GET['method'].'<br>';
		
		switch ($_GET['method']) {
			case ('login'): $this->methodLogin(); break;
			case ('logout'): $this->methodLogout(); break;
			case ('transaction_list'): $this->methodTransactionList(); break;
			case ('card_list'): $this->methodCardList(); break;
			case ('card_limit_create'): $this->methodCardLimitCreate(); break;
			case ('card_limit_update'): $this->methodCardLimitUpdate(); break;
			case ('card_limit_delete'): $this->methodCardLimitDelete(); break;
			default:
				$this->_sendResponse(501, sprintf('method <b>%s</b> is not implemented', $_GET['method']) );
				Yii::app()->end();			
		}
	}
	
	public function actionLogin() {	
		Yii::app()->user->logout();		
	    if(!isset($_GET['user']) || !isset($_GET['password']))
			$this->_sendResponse(500, 'Error: Parameters is missing' );
		$identity=new UserIdentity($_GET['user'],$_GET['password']);
		if(!$identity->authenticate())
			$this->_sendResponse(403);
		Yii::app()->user->login($identity);
		
		$user= Users::currentUser();
		$user->scenario = 'updatetoken';
		$user->token = md5(uniqid(rand(), true));
		$user->save();
		
        $this->_sendResponse(200, CJSON::encode(array('user'=>$user->username,'token'=>$user->token)));
    }
	
    public function actionLogout()
    {
		$this->_authByToken();
		$user= Users::currentUser();

		$user->scenario = 'updatetoken';
		$user->token = null;
		$user->save();
		
		Yii::app()->user->logout();		
        $this->_sendResponse(200, CJSON::encode(array()));
    }
				
    public function actionTransaction_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->with =  array(  'card', 'fuel', 'pumptransaction', 'pumptransaction.terminal', 
							'pumptransaction.terminal.azs', 'pumptransaction.pump', 'pumptransaction.tank', 'autoCard');
		$criteria->order = 't.id';
	    if(isset($_GET['offset'])) $criteria->offset = $_GET['offset'];
	    if(isset($_GET['limit'])) $criteria->limit = $_GET['limit'];
		$res = array();
		foreach (CardOperations::model()->findAll($criteria) as $x) {
			$res[] = array(
				'id' => $x->id,
				'date' => $x->date,
				'type' => $x->operationType,
				'card' => $x->card->number,
				'fuel' => $x->fuel->code,
				'volume' => -$x->volume,
			);
		}
        $this->_sendResponse(200, CJSON::encode($res));
    }
	
    public function actionCard_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->with =  array('limits','limits.fuel',);
		$criteria->order = 't.number';
	    if(isset($_GET['offset'])) $criteria->offset = $_GET['offset'];
	    if(isset($_GET['limit'])) $criteria->limit = $_GET['limit'];
		$res = array();
		foreach (Cards::model()->findAll($criteria) as $x) {
			$limits = array();
			$i = 0;
			foreach ($x->limits as $y) {
				$limits[] = array(
					'num' => ++$i,
					'type' => $y->limitType,
					'fuel' => $y->fuel->code,
					'volume' => $y->orderVolume,
				);
			}
			/*
			$balances = array();
			foreach ($x->cardbalances as $y) {
				$balances[] = array(
					'fuel' => $y->fuel->code,
					'volume' => $y->volume,
				);
			}
			*/
			
			$res[] = array(
				'number' => $x->number,
				'state' => $x->state,
				'expire' => $x->expire,
				'owner' => $x->owner,
				'note' => $x->description,
				'limits'  => $limits,
//				'balance'  => $balances,
			);
		}
        $this->_sendResponse(200, $this->json_fix_cyr(CJSON::encode($res)));							
    }
	
    public function actionCard_limit_create() {
		$this->_authByToken();
	    if (!isset($_GET['card']) || !isset($_GET['type']) || !isset($_GET['fuel']) || !isset($_GET['volume']))
			$this->_sendResponse(500, 'Error: Parameters is missing' );
		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['card']));		
		if (!$card) $this->_sendResponse(500, 'Card not found');
		$m = new Fuels();
		$fuel = $m->find("code=:x", array(":x"=>$_GET['fuel']));		
		if (!$fuel) $this->_sendResponse(500, 'Fuel not found');
		
		$lim = new CardLimits();
		$lim->cardId = $card->id;
		$lim->limitType = $_GET['type'];
		$lim->fuelId = $fuel->id;
		$lim->orderVolume = $_GET['volume'];
		if (!$lim->save()) $this->_sendResponse(500, print_r($lim->errors, true));

		$res = array();
        $this->_sendResponse(200, $this->json_fix_cyr(CJSON::encode($res)));									
	}
	
    public function actionCard_limit_update() {
		$this->_authByToken();
	    if (!isset($_GET['card']) || !isset($_GET['num'])) $this->_sendResponse(500, 'Error: Parameters is missing' );
		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['card']));	
		if ($_GET['num'] > count($card->limits)) $this->_sendResponse(500, 'Error: Parameters is missing' );
		$lim = $card->limits[$_GET['num'] - 1];
		if (isset($_GET['type'])) $lim->limitType = $_GET['type'];
		if (isset($_GET['fuel'])) {
			$m = new Fuels();
			$fuel = $m->find("code=:x", array(":x"=>$_GET['fuel']));		
			if (!$fuel) $this->_sendResponse(500, 'Fuel not found');
			$lim->fuelId = $fuel->id;			
		} 
		if (isset($_GET['volume'])) $lim->orderVolume =$_GET['volume'];
		if (!$lim->save()) $this->_sendResponse(500, print_r($lim->errors, true));

		$res = array();
        $this->_sendResponse(200, $this->json_fix_cyr(CJSON::encode($res)));	
	}
	
    public function actionCard_limit_delete() {
		$this->_authByToken();
	    if (!isset($_GET['card']) || !isset($_GET['num']))
			$this->_sendResponse(500, 'Error: Parameters is missing' );
		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['card']));	
		if ($_GET['num'] > count($card->limits)) $this->_sendResponse(500, 'Error: Parameters is missing' );
		$lim = $card->limits[$_GET['num'] - 1];
		if (!$lim->delete()) $this->_sendResponse(500, print_r($lim->errors, true));

		$res = array();
        $this->_sendResponse(200, $this->json_fix_cyr(CJSON::encode($res)));					
	}

}