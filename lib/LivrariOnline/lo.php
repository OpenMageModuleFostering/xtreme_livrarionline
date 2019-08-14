<?php 
require_once('AES_Encryption.php');
require_once('padCrypt.php');
require_once('RSA.php');
require_once('curl.php');

class LO
{
	//private
	private $f_request 	= NULL;
	private $f_secure 	= NULL;
	private $aes_key 	= NULL;
	private $iv 		= NULL;
	private $rsa_key	= NULL;

	//definesc erorile standard: nu am putut comunica cu serverul, raspunsul de la server nu este de tip JSON. Restul de erori vin de la server
	private $error 		= array('server' => 'Nu am putut comunica cu serverul', 'notJSON' => 'Raspunsul primit de la server nu este formatat corect');

	//public
	public $f_login 	= NULL;
	public $version		= NULL;
	

	//////////////////////////////////////////////////////////////
	// 						METODE PUBLICE						//
	//////////////////////////////////////////////////////////////

	//setez versiunea de kit
	public function LO(){
		$this->version = "0.1b";
	}

	//setez cheia RSA
	public function setRSAKey($rsa_key)
	{
		$this->rsa_key = $rsa_key;
	}

		//////////////////////////////////////////////////////////////
		// 				METODE COMUNICARE CU SERVER					//
		//////////////////////////////////////////////////////////////

		public function CancelLivrare($f_request)
		{
			return $this->LOCommunicate($f_request, 'http://api.livrarionline.ro/Lobackend.asmx/CancelLivrare');
		}

		public function GenerateAwb($f_request)
		{
			return $this->LOCommunicate($f_request, 'http://api.livrarionline.ro/Lobackend.asmx/GenerateAwb');
		}

		public function PrintAwb($f_request,$class,$style)
		{
			return '<a style="'.$style.'" class="'.$class.'" id="print-awb" href="http://api.livrarionline.ro/Lobackend_print/PrintAwb.aspx?f_login='.$this->f_login.'&awb='.$f_request['awb'].'" target="_blank">Click pentru print AWB</a>';
		}

		public function Tracking($f_request)
		{
			return $this->LOCommunicate($f_request, 'http://api.livrarionline.ro/Lobackend.asmx/Tracking');
		}

		public function EstimeazaPret($f_request)
		{
			return $this->LOCommunicate($f_request, 'http://estimare.livrarionline.ro/EstimarePret.asmx/EstimeazaPret');
		}

		
		//////////////////////////////////////////////////////////////
		// 				END METODE COMUNICARE CU SERVER				//
		//////////////////////////////////////////////////////////////

		//helper pentru validarea bifarii unui checkbox si trimiterea de valori boolean catre server
		public function checkboxSelected($value)
		{
			if ($value) return true;
			return false;
		}

	//////////////////////////////////////////////////////////////
	// 					END METODE PUBLICE						//
	//////////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////////
	// 						METODE PRIVATE						//
	//////////////////////////////////////////////////////////////

	//criptez f_request cu AES
	private function AESEnc()
	{

		$this->aes_key 		= md5(uniqid());
		$this->iv 			= '285c02831e028bff';
		$aes 				= new AES_Encryption($this->aes_key, $this->iv, "PKCS7", "cbc");
		$this->f_request 	= bin2hex(base64_encode($aes->encrypt($this->f_request)));
	}

	//criptez cheia AES cu RSA
	private function RSAEnc()
	{
		$rsa = new Crypt_RSA();
		$rsa->loadKey($this->rsa_key);
		$rsa->setPublicKey();
		$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
		$this->f_secure = base64_encode($rsa->encrypt($this->aes_key));
	}

	//setez f_request, criptez f_request cu AES si cheia AES cu RSA
	private function setFRequest($f_request)
	{
		$this->f_request = json_encode($f_request);
		$this->AESEnc();
		$this->RSAEnc();
	}

	//construiesc JSON ce va fi trimis catre server
	private function createJSON()
	{
		$request 				= array();

		$request['f_login'] 	= $this->f_login;
		$request['f_request']	= $this->f_request;
		$request['f_secure']	= $this->f_secure;

		return json_encode(array('loapi' => $request));
	}

	//metoda pentru verificarea daca un string este JSON - folosit la primirea raspunsului de la server
	private function isJSON($string) {
		if (is_object(json_decode($string)))
			return true;
		return false;
	}

	//metoda pentru verificarea raspunsului obtinut de la server. O voi apela cand primesc raspunsul de la server
	private function processResponse($response)
	{
		//daca nu primesc raspuns de la server
		if ($response == FALSE)
			return (object)array('status' => 'error','message' => $this->error['server']);
		else
		{
			//verific daca raspunsul este de tip JSON
			if ($this->isJSON($response))
			{
				$response = json_decode($response);
				return $response->loapi;
			}
			else
				return (object)array('status' => 'error','message' => $response);
		}
	}

	//metoda comunicare cu server LO
	private function LOCommunicate($f_request, $urltopost)
	{
		$cc = new cURL();
		$this->setFRequest($f_request);
		error_log(urlencode($this->createJSON()));
		$response = $cc->post($urltopost,'loapijson='.urlencode($this->createJSON()));
		return $this->processResponse($response);
	}

	//////////////////////////////////////////////////////////////
	// 						END METODE PRIVATE					//
	//////////////////////////////////////////////////////////////
}
?>