<?php
class Api2Controller extends Controller
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
 
	public function actionError()
	{
		echo 'error';
		/*
		if($error=Yii::app()->errorHandler->error)
			$this->render('error', $error);
		*/
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
	
    private function _jsonencode($data) {
		return $this->json_fix_cyr(CJSON::encode($data));
	}

	
	private function _authByToken() {
	    if(!isset($_GET['_token']) || (strlen($_GET['_token']) != 32)) $this->_sendResponse(400, 'Error: Invalid token' );
	//	Yii::app()->user->logout();	
		$token = $_GET['_token'];
		$m = new Users();
		$user = $m->find("token=:x", array(":x"=>$token));
		if (!$user) $this->_sendResponse(400, 'Invalid token');
		$identity = new UserIdentity($user->username, $user->password);
		if(!$identity->authenticate()) $this->_sendResponse(403);
		Yii::app()->user->login($identity);
	}
	
	private function _card2array($card) {
		$res = array(
			'_number' => $card->number,
			'_state' => $card->state,
			'_expire' => $card->expire,
			'_type' => $card->type,
			'_organization_id' => $card->orgId,
			'_owner' => $card->owner,
			'_pin' => $card->pin,
			'_note' => $card->description,
		);
			
		if ($card->type == Cards::TYPE_LIMITED) {
			$limits = array();
			$i = 0;
			foreach ($card->limits as $y) {
				$limits[] = array(
					'_num' => ++$i,
					'_type' => $y->limitType,
					'_fuel' => $y->fuel->code,
					'_volume' => $y->orderVolume,
				);
			}
			$res['_limits'] = $limits;
		}
		else if ($card->type == Cards::TYPE_DEBIT) {
			$balances = array();
			foreach ($card->cardbalances as $y) {
				$balances[] = array(
					'_fuel' => $y->fuel->code,
					'_volume' => $y->volume,
				);
			}
			$res['_balance'] = $balances;
		}	
		return $res;		
	}
	

    // Actions
	
	public function actionLogin() {	
	//	Yii::app()->user->logout();		
	    if(!isset($_GET['_user']) || !isset($_GET['_password']))
			$this->_sendResponse(400, 'Error: Parameters is missing' );
		$identity=new UserIdentity($_GET['_user'],$_GET['_password']);
		if(!$identity->authenticate())
			$this->_sendResponse(403);
		Yii::app()->user->login($identity);
		
		$user= Users::currentUser();
		$user->scenario = 'updatetoken';
		$user->token = md5(uniqid(rand(), true));
		$user->save();

		$res = array(
				'_user'=>$user->username,
				'_token'=>$user->token,
		);
        $this->_sendResponse(200, $this->_jsonencode($res));
    }
	
    public function actionLogout()
    {
		$this->_authByToken();
		$user= Users::currentUser();

		$user->scenario = 'updatetoken';
		$user->token = null;
		$user->save();
		
		Yii::app()->user->logout();		
		$res = array(
		);
        $this->_sendResponse(200, $this->_jsonencode($res));
    }
				
    public function actionTransaction_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->with =  array(  'card', 'fuel', 'cardrefill', 'pumptransaction', 'pumptransaction.terminal', 
							'pumptransaction.terminal.azs', 'pumptransaction.pump', 'pumptransaction.tank', 'autoCard');
		if(isset($_GET['_order'])) {
			if ($_GET['_order'] == 'date') $criteria->order = 't.date';
			else if ($_GET['_order'] == 'id') $criteria->order = 't.id';
			else $this->_sendResponse(400, 'Error: Parameters is missing' );
		}
		else $criteria->order = 't.id';
		
  	    if (isset($_GET['_datefrom'])) $criteria->compare('date',">=".$_GET['_datefrom']);
  	    if (isset($_GET['_dateto'])) $criteria->compare('date',"<=".$_GET['_dateto']);
  	    if (isset($_GET['_idfrom'])) $criteria->compare('t.id',">=".$_GET['_idfrom']);
  	    if (isset($_GET['_idto'])) $criteria->compare('t.id',"<=".$_GET['_idto']);
		if (isset($_GET['_azs_id'])) $criteria->compare('azs.id',$_GET['_azs_id']);
		if (isset($_GET['_terminal_id'])) $criteria->compare('terminal.id',$_GET['_terminal_id']);
		if (isset($_GET['_tank_id'])) $criteria->compare('tank.id',$_GET['_tank_id']);
		if (isset($_GET['_organization_id'])) $criteria->compare('t.orgId',$_GET['_organization_id']);
		if (isset($_GET['_type'])) $criteria->compare('t.operationType',$_GET['_type']);
		if (isset($_GET['_fuel'])) $criteria->compare('fuel.code',$_GET['_fuel']);
		if (isset($_GET['_card'])) $criteria->compare('card.number',$_GET['_card']);
		
	    if(isset($_GET['_offset'])) $criteria->offset = $_GET['_offset'];
		$offset = isset($_GET['_offset'])? $_GET['_offset'] : 0;
		if ($offset < 0) {
			$count = CardOperations::model()->count($criteria);
			$criteria->offset = $count + $offset;			
			$criteria->limit = -$offset;
		}
		else $criteria->offset = $offset;			
		
	    if(isset($_GET['_limit'])) $criteria->limit = $_GET['_limit'];
		
		$res = array();
		foreach (CardOperations::model()->findAll($criteria) as $x) {
			$item = array();
			$item['_id'] = $x->id;
			$item['_date'] = $x->date;
			$item['_azs_id'] = isset($x->pumptransaction) ? $x->pumptransaction->terminal->azs->id : '';
			$item['_terminal_id'] = isset($x->pumptransaction) ? $x->pumptransaction->terminal->id : '';
			$item['_tank_id'] = isset($x->pumptransaction) ? $x->pumptransaction->tankId : '';
			$item['_type'] = $x->operationType;
			$item['_card'] = isset($x->card) ? $x->card->number : '';
			if (isset($x->autoCard)) $item['_card2'] = $x->autoCard->number;				
			$item['_organization_id'] = $x->orgId;
			$item['_fuel'] = $x->fuel->code;
			$item['_volume'] = (($item['_type'] == CardOperations::TYPE_CARD_REFILL ) || ($item['_type'] == CardOperations::TYPE_CLIENT_REFILL )) ? (float)$x->volume : -$x->volume;
			if (isset($x->cardrefill)) $item['_note'] = $x->cardrefill->document;	
			$res[] = $item;
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }
	
    public function actionCard_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->with =  array('limits','limits.fuel',);
		$criteria->order = 't.number';
	    if(isset($_GET['_offset'])) $criteria->offset = $_GET['_offset'];
	    if(isset($_GET['_limit'])) $criteria->limit = $_GET['_limit'];
		$res = array();
		foreach (Cards::model()->findAll($criteria) as $x) {
			$res[] = $this->_card2array($x);
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }
	
    public function actionCard_search()
    {
		$this->_authByToken();
	    if (!isset($_GET['_number'])) $this->_sendResponse(400, 'Error: Parameters is missing' );
		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['_number']));	
		if (!$card) $this->_sendResponse(404, 'Error: card not found' ); 
        $this->_sendResponse(200, $this->_jsonencode($this->_card2array($card)));
	}
	
    public function actionCard_incaccount()
    {
		$this->_authByToken();
		if (!Yii::app()->user->checkAccess('cards.refill')) throw new CHttpException(403, Yii::t('yii','You are not authorized to perform this action.'));			
	    if (!isset($_GET['_number'])) $this->_sendResponse(400, 'Error: Parameters is missing' );
	    if (!isset($_GET['_fuel'])) $this->_sendResponse(400, 'Error: Parameters is missing' );
	    if (!isset($_GET['_volume'])) $this->_sendResponse(400, 'Error: Parameters is missing' );

		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['_number']));		
		if (!$card) $this->_sendResponse(404, 'Card not found');
		$m = new Fuels();
		$fuel = $m->find("code=:x", array(":x"=>$_GET['_fuel']));		
		if (!$fuel) $this->_sendResponse(404, 'Fuel not found');
		$card->incbalance($fuel->id, $_GET['_volume'], (isset($_GET['_note'])) ? $_GET['_note'] : ''); 
		
		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['_number']));		
        $this->_sendResponse(200, $this->_jsonencode($this->_card2array($card)));
	}

	
    public function actionCard_create() {
		$this->_authByToken();
	    if (!isset($_GET['_number']) || !isset($_GET['_state']) || !isset($_GET['_expire']) || !isset($_GET['_owner']))
			$this->_sendResponse(400, 'Error: Parameters is missing' );
		$card = new Cards();
		$card = $card->find("number=:x", array(":x"=>$_GET['_number']));		
		if (isset($card)) $this->_sendResponse(500, 'Card exists');
		$card = new Cards();
		$card->number = $_GET['_number'];
		$card->ownerType = 0;
		$card->state = $_GET['_state'];
		$card->typeExt = $_GET['_type'];
		$card->expireFmt = $_GET['_expire'];
		if (isset($_GET['_organization_id'])) $card->orgId = $_GET['_organization_id'];
		if (isset($_GET['_owner'])) $card->owner = $_GET['_owner'];
		if (isset($_GET['_pin'])) $card->pin = $_GET['_pin'];
		if (isset($_GET['_note'])) $card->description = $_GET['_note'];
		$card->scenario = 'api';
		if (!$card->save()) $this->_sendResponse(500, print_r($card->errors,true));
        $this->_sendResponse(200, $this->_jsonencode($this->_card2array($card)));
	}
	
    public function actionCard_update() {
		$this->_authByToken();
	    if (!isset($_GET['_number'])) $this->_sendResponse(400, 'Error: Parameters is missing' );
		$card = new Cards();
		$card = $card->find("number=:x", array(":x"=>$_GET['_number']));		
		if (!isset($card)) $this->_sendResponse(404, 'Card not found');
		if (isset($_GET['_state'])) $card->state = $_GET['_state'];
		if (isset($_GET['_expire'])) $card->expireFmt = $_GET['_expire'];
		if (isset($_GET['_type'])) $card->type = $_GET['_type'];
		if (isset($_GET['_organization_id'])) $card->orgId = $_GET['_organization_id'];
		if (isset($_GET['_owner'])) $card->owner = $_GET['_owner'];
		if (isset($_GET['_pin'])) $card->pin = $_GET['_pin'];
		if (isset($_GET['_note'])) $card->description = $_GET['_note'];
		$card->scenario = 'api';
		if (!$card->save()) $this->_sendResponse(500, print_r($card->errors,true));
        $this->_sendResponse(200, $this->_jsonencode($this->_card2array($card)));
	}

    public function actionCard_delete() {
		$this->_authByToken();
	    if (!isset($_GET['_number'])) $this->_sendResponse(400, 'Error: Parameters is missing' );
		$card = new Cards();
		$card = $card->find("number=:x", array(":x"=>$_GET['_number']));		
		if (!isset($card)) $this->_sendResponse(404, 'Card not found');
		if (!$card->delete()) $this->_sendResponse(500, print_r($card->errors,true));
		$res = array();
        $this->_sendResponse(200, $this->_jsonencode($res));
	}	
	
    public function actionCard_limit_create() {
		$this->_authByToken();
	    if (!isset($_GET['_card']) || !isset($_GET['_type']) || !isset($_GET['_fuel']) || !isset($_GET['_volume']))
			$this->_sendResponse(400, 'Error: Parameters is missing' );
		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['_card']));		
		if (!$card) $this->_sendResponse(404, 'Card not found');
		$m = new Fuels();
		$fuel = $m->find("code=:x", array(":x"=>$_GET['_fuel']));		
		if (!$fuel) $this->_sendResponse(404, 'Fuel not found');
		
		$lim = new CardLimits();
		$lim->cardId = $card->id;
		$lim->limitType = $_GET['_type'];
		$lim->fuelId = $fuel->id;
		$lim->orderVolume = $_GET['_volume'];
		if (!$lim->save()) $this->_sendResponse(500, print_r($lim->errors, true));

		$res = array();
        $this->_sendResponse(200, $this->_jsonencode($res));
	}
	
    public function actionCard_limit_update() {
		$this->_authByToken();
	    if (!isset($_GET['_card']) || !isset($_GET['_num'])) $this->_sendResponse(400, 'Error: Parameters is missing' );
		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['_card']));	
		if ($_GET['_num'] > count($card->limits)) $this->_sendResponse(400, 'Error: Parameters is missing' );
		$lim = $card->limits[$_GET['_num'] - 1];
		if (isset($_GET['_type'])) $lim->limitType = $_GET['_type'];
		if (isset($_GET['_fuel'])) {
			$m = new Fuels();
			$fuel = $m->find("code=:x", array(":x"=>$_GET['_fuel']));		
			if (!$fuel) $this->_sendResponse(404, 'Fuel not found');
			$lim->fuelId = $fuel->id;			
		} 
		if (isset($_GET['_volume'])) $lim->orderVolume =$_GET['_volume'];
		if (!$lim->save()) $this->_sendResponse(500, print_r($lim->errors, true));

		$res = array();
        $this->_sendResponse(200, $this->_jsonencode($res));
	}
	
    public function actionCard_limit_delete() {
		$this->_authByToken();
	    if (!isset($_GET['_card']) || !isset($_GET['_num']))
			$this->_sendResponse(400, 'Error: Parameters is missing' );
		$m = new Cards();
		$card = $m->find("number=:x", array(":x"=>$_GET['_card']));	
		if ($_GET['_num'] > count($card->limits)) $this->_sendResponse(400, 'Error: Parameters is missing' );
		$lim = $card->limits[$_GET['_num'] - 1];
		if (!$lim->delete()) $this->_sendResponse(500, print_r($lim->errors, true));

		$res = array();
        $this->_sendResponse(200, $this->_jsonencode($res));
	}

    public function actionOrganization_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->order = 't.id';
	    if(isset($_GET['_offset'])) $criteria->offset = $_GET['_offset'];
	    if(isset($_GET['_limit'])) $criteria->limit = $_GET['_limit'];
		$res = array();
		foreach (Organizations::model()->findAll($criteria) as $x) {		
			$res[] = array(
				'_id' => $x->id,
				'_name' => $x->name,
				'_note' => $x->description,
			);
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }
	
    public function actionOrganization_create() {
		$this->_authByToken();
	    if (!isset($_GET['_name']))
			$this->_sendResponse(400, 'Error: Parameters is missing' );

		$org = new Organizations();
		if (isset($_GET['_id'])) $org->id = $_GET['_id'];
		$org->name = $_GET['_name'];
		if (isset($_GET['_note'])) $org->description = $_GET['_note'];
		if (!$org->save()) $this->_sendResponse(500, print_r($org->errors,true));
		if (!$org->id) $org->id = Yii::app()->db->lastInsertID;
	
		$res = array(
			'_id' => $org->id,
			'_name' => $org->name,
			'_note' => $org->description,
		);
        $this->_sendResponse(200, $this->_jsonencode($res));
	}
	
    public function actionOrganization_update() {
		$this->_authByToken();
	    if (!isset($_GET['_id'])) $this->_sendResponse(400, 'Error: Parameters is missing' );
		$org = new Organizations();
		$org = $org->find("id=:x", array(":x"=>$_GET['_id']));		
		if (!isset($org)) $this->_sendResponse(404, 'Organization not found');
		if (isset($_GET['_name'])) $org->name = $_GET['_name'];
		if (isset($_GET['_note'])) $org->description = $_GET['_note'];
		if (!$org->save()) $this->_sendResponse(500, print_r($org->errors,true));
		$res = array(
			'_id' => $org->id,
			'_name' => $org->name,
			'_note' => $org->description,
		);
        $this->_sendResponse(200, $this->_jsonencode($res));
	}

    public function actionOrganization_delete() {
		$this->_authByToken();
	    if (!isset($_GET['_id'])) $this->_sendResponse(400, 'Error: Parameters is missing' );
		$org = new Organizations();
		$org = $org->find("id=:x", array(":x"=>$_GET['_id']));		
		if (!isset($org)) $this->_sendResponse(404, 'Organization not found');
		if (!$org->delete()) $this->_sendResponse(500, print_r($org->errors,true));
		$res = array();
        $this->_sendResponse(200, $this->_jsonencode($res));
	}		

    public function actionFuel_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->order = 't.id';
		$res = array();
		foreach (Fuels::model()->findAll($criteria) as $x) {
			$res[] = array(
				'_id' => $x->id,
				'_code' => $x->code,
				'_name' => $x->name,
			);
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }
	
    public function actionAzs_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->order = 't.id';
		$res = array();
		foreach (Azs::model()->findAll($criteria) as $x) {
			$res[] = array(
				'_id' => $x->id,
				'_name' => $x->name,
				'_note' => $x->description,
			);
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }

    public function actionTerminal_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->order = 't.id';
		$res = array();
		foreach (Terminals::model()->findAll($criteria) as $x) {
			$res[] = array(
				'_id' => $x->id,
				'_azs_id' => $x->azsId,
				'_sn' => $x->sn,
				'_name' => $x->name,
				'_sync_date' => $x->syncDate,
			);
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }

    public function actionTank_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->order = 't.id';
		$res = array();
		foreach (Tanks::model()->findAll($criteria) as $x) {
			$res[] = array(
				'_id' => $x->id,
				'_azs_id' => $x->azsId,
				'_fuel_id' => $x->fuelId,
				'_name' => $x->name,
				'_height' => $x->height,
				'_capacity' => $x->capacity,
			);
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }	
	
    public function actionTank_state_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		if(isset($_GET['_order'])) {
			if ($_GET['_order'] == 'date') $criteria->order = 't.date';
			else $this->_sendResponse(400, 'Error: Parameters is missing' );
		}
		else $criteria->order = 't.id';
		
  	    if (isset($_GET['_datefrom'])) $criteria->compare('date',">=".$_GET['_datefrom']);
  	    if (isset($_GET['_dateto'])) $criteria->compare('date',"<=".$_GET['_dateto']);
		if (isset($_GET['_tank_id'])) $criteria->compare('tankId',$_GET['_tank_id']);
		
		$offset = isset($_GET['_offset'])? $_GET['_offset'] : 0;
		if ($offset < 0) {
			$count = TankRealStates::model()->count($criteria);
			$criteria->offset = $count + $offset;			
		}
		else $criteria->offset = $offset;			
		if(isset($_GET['_limit'])) $criteria->limit = $_GET['_limit'];
		$res = array();
		foreach (TankRealStates::model()->findAll($criteria) as $x) {
			$res[] = array(
				'_id' => $x->id,
				'_date' => $x->date,
				'_tank_id' => $x->tankId,
				'_fuel_level' => $x->fuelLevel,
				'_fuel_volume' => $x->fuelVolume,
				'_fuel_mass' => $x->fuelMass,
				'_temperature' => $x->temperature,
				'_density' => $x->density,
				'_water_level' => $x->waterLevel,
				'_water_volume' => $x->waterVolume,
			);
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }	
	
    public function actionTankincome_list()
    {
		$this->_authByToken();
		$criteria=new CDbCriteria;
		$criteria->with =  array(  'supplier', 'fuel', 'tank', 'tank.azs');
		if(isset($_GET['_order'])) {
			if ($_GET['_order'] == 'date') $criteria->order = 't.date';
			else if ($_GET['_order'] == 'id') $criteria->order = 't.id';
			else $this->_sendResponse(400, 'Error: Parameters is missing' );
		}
		else $criteria->order = 't.id';
		
  	    if (isset($_GET['_datefrom'])) $criteria->compare('date',">=".$_GET['_datefrom']);
  	    if (isset($_GET['_dateto'])) $criteria->compare('date',"<=".$_GET['_dateto']);
  	    if (isset($_GET['_idfrom'])) $criteria->compare('t.id',">=".$_GET['_idfrom']);
  	    if (isset($_GET['_idto'])) $criteria->compare('t.id',"<=".$_GET['_idto']);
		if (isset($_GET['_azs_id'])) $criteria->compare('azs.id',$_GET['_azs_id']);
		if (isset($_GET['_tank_id'])) $criteria->compare('tank.id',$_GET['_tank_id']);
		if (isset($_GET['_fuel'])) $criteria->compare('fuel.code',$_GET['_fuel']);
		
	    if(isset($_GET['_offset'])) $criteria->offset = $_GET['_offset'];
		$offset = isset($_GET['_offset'])? $_GET['_offset'] : 0;
		if ($offset < 0) {
			$count = TankIncome::model()->count($criteria);
			$criteria->offset = $count + $offset;			
			$criteria->limit = -$offset;
		}
		else $criteria->offset = $offset;			
		
	    if(isset($_GET['_limit'])) $criteria->limit = $_GET['_limit'];
		
		$res = array();
		foreach (TankIncome::model()->findAll($criteria) as $x) {
			$item = array();
			$item['_id'] = $x->id;
			$item['_date'] = $x->date;
			$item['_azs_id'] = $x->tank->azs->id;
			$item['_tank_id'] = $x->tank->id;
			$item['_supplier'] = isset($x->supplier) ? $x->supplier->name : '';
			$item['_doc'] = $x->doc;
			$item['_fuel'] = $x->fuel->code;
			$item['_volume'] = $x->volume;
			if (isset($x->cardrefill)) $item['_note'] = $x->cardrefill->document;	
			$res[] = $item;
		}
        $this->_sendResponse(200, $this->_jsonencode($res));
    }
	
}