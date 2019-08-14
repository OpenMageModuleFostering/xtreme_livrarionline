<?php
ini_set("memory_limit", "1G");
require_once('lib/LivrariOnline/lo.php');
class Xtreme_Livrarionline_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_code = 'livrarionline';

	public function createAwbFromOrder($awb_id)
	{
		$awb = Mage::getModel('livrarionline/awb')->load($awb_id);
		$parcels = Mage::getModel('livrarionline/awb_parcels')->getCollection();
		$parcels->addFieldToFilter('awb_id', $awb_id);
		$order = Mage::getModel('sales/order')->load($awb->getOrderId());
		$carrier = Mage::getModel('livrarionline/carriers')->load($awb->getServiceId());

		$billing = $order->getBillingAddress();
		$shipping = $order->getShippingAddress();
		
		$login_id = Mage::getStoreConfig('carriers/'.$this->_code.'/login_id');
		$security_key = Mage::getStoreConfig('carriers/'.$this->_code.'/security_key');
		$carrier_id = Mage::getStoreConfig('carriers/'.$this->_code.'/carrier_id');
		$store = Mage::app()->getStore();
		$name = $store->getName();
		
		$price = 0;
		$lo = new LO ();

		$f_request_awb = array();
		$f_request_awb['f_shipping_company_id'] = (int) $carrier->getShippingCompanyId(); // int 	obligatoriu
		$f_request_awb['request_data_ridicare'] = '2013-11-28';  // date Y-m-d	optional
		$f_request_awb['request_ora_ridicare'] = '14:00:00';  // time without time zone H:i:s 	optional
		$f_request_awb['request_ora_ridicare_end'] = '14:00:00';  // time without time zone 	optional
		$f_request_awb['request_ora_livrare_sambata'] = '14:00:00';  // time without time zone optional
		$f_request_awb['request_ora_livrare_end_sambata'] = '14:00:00'; // time without time zone optional
		$f_request_awb['request_ora_livrare'] = '14:00:00'; // time without time zone optional
		$f_request_awb['request_ora_livrare_end'] = '14:00:00';  // time without time zone optional
		$f_request_awb['descriere_livrare'] = $awb->getDescription();
		$f_request_awb['referinta_expeditor'] = $awb->getReference(); // varchar(255) Obligatoriu
		$f_request_awb['valoare_declarata'] = $awb->getDeclaredValue();	// decimal(10,2) Obligatoriu
		$f_request_awb['ramburs'] = $awb->getCod(); // decimal(10,2) Obligatoriu
		$f_request_awb['asigurare_la_valoarea_declarata'] = $lo->checkboxSelected($awb->getInsurance()); // Boolean Obligatoriu
		$f_request_awb['retur_documente'] = $lo->checkboxSelected($awb->getReturndocs()); // boolean 	optional
		$f_request_awb['retur_documente_bancare'] = $lo->checkboxSelected($awb->getReturndocsbank()); // boolean	 optional
		$f_request_awb['confirmare_livrare'] = $lo->checkboxSelected($awb->getDeliveryconf()); // boolean	optional
		$f_request_awb['livrare_sambata'] = $lo->checkboxSelected($awb->getDeliverysat()); // Boolean optional
		$f_request_awb['currency'] = ($awb->getDeclaredValue() > 0) ? $awb->getCurrency() : '';  // char(3) Obligatoriu cand "valoare_declarata" > 0
		$f_request_awb['currency_ramburs'] = ($awb->getCod() > 0) ? $awb->getCurrency() : ''; // char(3) Obligatoriu cand "ramburs" > 0
		$f_request_awb['notificare_email']= $lo->checkboxSelected($awb->getEmailnotify()); // Boolean optional
		$f_request_awb['notificare_sms'] = $lo->checkboxSelected($awb->getSmsnotify()); // Boolean optional
		$f_request_awb['cine_plateste'] = (int) $awb->getPayee(); // 0 - merchant,2 - destinatar,1 - expeditor    Obligatoriu
		$f_request_awb['serviciuid']= (int) $carrier->getServiceId();	 // int Obligatoriu
		$f_request_awb['request_mpod'] = $lo->checkboxSelected($awb->getDeliveryconfmerchant()); // Boolean optional

		$colete = array();
		foreach($parcels as $item)
		{
			$colete[] = array(
				'greutate'=> (float) (($item->getWeight() > 0) ? $item->getWeight() : 1), // decimal 10,2 kg
				'lungime'=> (float)(($item->getLength() > 0) ? $item->getLength() : 1), // integer      cm
				'latime'=> (float)(($item->getWidth() > 0) ? $item->getWidth() : 1), // integer      cm
				'inaltime'=> (float)(($item->getHeight() > 0) ? $item->getHeight() : 1), // integer      cm
				'continut'=> (int)$item->getContent(), // int      1;"Acte" 2;"Tipizate" 3;"Fragile" 4;"Generale"
				'tipcolet'=> (int)$item->getParcelType() // int 1;"Plic"2;"Colet"3;"Palet"11
			);
		}

		$f_request_awb['colete'] = $colete;

		$f_request_awb['destinatar'] = array(
			'first_name' => ($shipping->getFirstname()) ? $shipping->getFirstname() : $billing->getFirstname(), //Obligatoriu
			'last_name'=> ($shipping->getLastname()) ? $shipping->getLastname() : $billing->getLastname(), //Obligatoriu
			'email' => ($shipping->getEmail()) ? $shipping->getEmail() : $billing->getEmail(),	//Obligatoriu
			'phone' => ($shipping->getTelephone()) ? $shipping->getTelephone() : $billing->getTelephone(), //phone sau mobile Obligatoriu
			'mobile' => '',
			'lang' => 'ro', //Obligatoriu ro/en
			'company_name' => ($shipping->getCompany()) ? $shipping->getCompany() : $billing->getCompany(), //optional
			'j' => '', //optional
			'bank_account' => ($shipping->getIban()) ? $shipping->getIban() : $billing->getIban(), //optional
			'bank_name' => ($shipping->getBanca()) ? $shipping->getBanca() : $billing->getBanca(), //optional
			'cui' => ($shipping->getCif()) ? $shipping->getCif() : $billing->getCif()//optional
		);
		
		$region = Mage::getModel('directory/region')->load($shipping->getRegionId());
		$country = Mage::getModel('directory/country')->loadByCode($shipping->getCountryId());
		//$street = explode("\n", $shipping->getStreet());
		$street = $shipping->getStreet();
		$f_request_awb['shipTOaddress'] = array(
			//Obligatoriu
			'address1' => $street[0],
			'address2' => (count($street) == 2) ? $street[1] : '',
			'city' => $shipping->getCity(),
			//'state'	=> iconv("UTF-8", "ISO-8859-1//TRANSLIT", $region->getName()),
			'state' => $region->getName(),
			'zip' => $shipping->getPostcode(),
			'country' => $country->getName(),
			'phone' => $shipping->getTelephone(),
			'observatii'=> ''
		);

		//$region = Mage::getModel('directory/region')->load($request->getRegionId());
		$lo_store = Mage::getModel('livrarionline/stores')->load($awb->getPickFrom());
		$region = $country = '';
		if($lo_store->getCountry())
		{
			$region = Mage::getModel('directory/region')->loadByCode($lo_store->getState(), $lo_store->getCountry())->getName();
			$country = Mage::getModel('directory/country')->loadByCode($lo_store->getCountry());
			$country = $country->getName();
		}
		$f_request_awb['shipFROMaddress'] = array(
			'email'			=> $lo_store->getEmail(),
			'first_name'	=> $lo_store->getFirstname(),
			'last_name' 	=> $lo_store->getLastname(),		
			'mobile'	 	=> '',
			'main_address' 	=> $lo_store->getAddress1(),
			'city' 			=> $lo_store->getCity(),
			'state' 		=> $region,
			'zip' 			=> $lo_store->getZipcode(),
			'country' 		=> $country,
			'phone'			=> $lo_store->getPhone(),
			'instructiuni'	=> ''	
		);
		//echo '<pre>';
		//var_dump($f_request_awb);exit;

		//Mage::log(var_export($f_request_awb, true));
		
		$lo->f_login = $login_id;
		$lo->setRSAKey($security_key);
				
		$response_awb = $lo->GenerateAwb($f_request_awb);
		//Mage::log($response_awb);
		
		//raspuns generare AWB
		if ((isset($response_awb->status) && $response_awb->status == 'error') || empty($response_awb))
		{
		    Mage::log("Error livrarionline:");
		    throw new Exception("Error livrarionline (response + f_request_awb dump):<br/>"."<pre>".print_r($response_awb, true).print_r($f_request_awb, true)."</pre>");
		    return false;
		}
		else
		    return $response_awb->f_awb_collection[0]; //array de awb-uri (awb pe fiecare colet)
	}

	public function getEstimate($carrier, $quote)
	{
		$shipping = $quote->getShippingAddress();
		if(!$shipping->getRegionId() || 
			!$shipping->getCity() || 
			!$shipping->getPostcode() ||
			!$shipping->getFirstname()) 
			return 0;
		
		if($quote->getCustomerId())
		{
		    $cust = Mage::getModel('customer/customer')->load($quote->getCustomerId());
		}

		$default_store = Mage::getModel('livrarionline/stores')->getCollection()
						->addFieldToFilter('`default`', 1)
						->addFieldToFilter('`status`', 1)
						->getFirstItem();
						
		$billing = $quote->getBillingAddress();
		//$shipping = $quote->getShippingAddress();
		
		$cartItems = $quote->getAllVisibleItems();
		
		$login_id = Mage::getStoreConfig('carriers/'.$this->_code.'/login_id');
		$security_key = Mage::getStoreConfig('carriers/'.$this->_code.'/security_key');
		$store = Mage::app()->getStore();
		$name = $store->getName();
		
		$price = 0;
		
		$f_request_awb = array();
		$f_request_awb['f_shipping_company_id'] 			= (int) $carrier->getShippingCompanyId(); // int 	obligatoriu
		$f_request_awb['request_data_ridicare'] 			= date('Y-m-d');  // date Y-m-d	optional
		$f_request_awb['request_ora_ridicare'] 				= date('H:i:s');  // time without time zone H:i:s 	optional
		$f_request_awb['request_ora_ridicare_end'] 			= date('H:i:s');  // time without time zone 	optional
		$f_request_awb['request_ora_livrare_sambata'] 		= date('H:i:s');  // time without time zone optional
		$f_request_awb['request_ora_livrare_end_sambata'] 	= date('H:i:s'); // time without time zone optional
		$f_request_awb['request_ora_livrare'] 				= date('H:i:s'); // time without time zone optional
		$f_request_awb['request_ora_livrare_end'] 			= date('H:i:s');  // time without time zone optional
		$f_request_awb['descriere_livrare'] 				= 'Estimare pret '.$name;
		$f_request_awb['referinta_expeditor'] 				= ''; // varchar(255) Obligatoriu
		$f_request_awb['valoare_declarata'] 				= $quote->getGrandTotal();	// decimal(10,2) Obligatoriu
		$f_request_awb['ramburs'] 							= $quote->getGrandTotal(); // decimal(10,2) Obligatoriu
		$f_request_awb['asigurare_la_valoarea_declarata'] 	= false; // Boolean Obligatoriu
		$f_request_awb['retur_documente'] 					= false; // boolean 	optional
		$f_request_awb['retur_documente_bancare'] 			= false; // boolean	 optional
		$f_request_awb['confirmare_livrare'] 				= false; // boolean	optional
		$f_request_awb['livrare_sambata'] 					= false; // Boolean optional
		$f_request_awb['currency'] 							= Mage::app()->getStore()->getCurrentCurrencyCode();  // char(3) Obligatoriu cand "valoare_declarata" > 0
		$f_request_awb['currency_ramburs'] 					= Mage::app()->getStore()->getCurrentCurrencyCode(); // char(3) Obligatoriu cand "ramburs" > 0
		$f_request_awb['notificare_email']					= false; // Boolean optional
		$f_request_awb['notificare_sms'] 					= false; // Boolean optional
		$f_request_awb['cine_plateste'] 					= 0; // 0 - merchant,2 - destinatar,1 - expeditor    Obligatoriu
		$f_request_awb['serviciuid']						= (int) $carrier->getServiceId();	 // int Obligatoriu
		$f_request_awb['request_mpod']						= false; // Boolean optional

		$colete = array();
		foreach ($cartItems as $item)
		{
			$colete[] = array(
				'greutate'	=> 1, // decimal 10,2 kg
				'lungime'	=> ($item->getLength() > 0) ? $item->getLength() : 1, // integer      cm
				'latime'	=> ($item->getWidth() > 0) ? $item->getWidth() : 1, // integer      cm
				'inaltime'	=> ($item->getHeight() > 0) ? $item->getHeight() : 1, // integer      cm
				'continut'	=> 1,
				'tipcolet'	=> 1,
			);
		}

		$f_request_awb['colete'] = $colete;
		
		$shipping = $quote->getShippingAddress();

		$f_request_awb['destinatar'] = array(
			'first_name' => ($shipping->getFirstname()) ? $shipping->getFirstname() : $billing->getFirstname(), //Obligatoriu
			'last_name'=> ($shipping->getLastname()) ? $shipping->getLastname() : $billing->getLastname(), //Obligatoriu
			'email' => ($shipping->getEmail()) ? $shipping->getEmail() : $billing->getEmail(),	//Obligatoriu
			'phone' => ($shipping->getTelephone()) ? $shipping->getTelephone() : $billing->getTelephone(), //phone sau mobile Obligatoriu
			'mobile' => '',
			'lang' => 'ro', //Obligatoriu ro/en
			'company_name' => '', //optional
			'j' => '', //optional
			'bank_account' => '', //optional
			'bank_name' => '', //optional
			'cui' => ''//optional
		);
		
		$region = Mage::getModel('directory/region')->load($shipping->getRegionId());
		$country = Mage::getModel('directory/country')->loadByCode($shipping->getCountryId());
		//$street = explode("\n", $shipping->getStreet());
		$street = $shipping->getStreet();
		$f_request_awb['shipTOaddress'] = array(
			//Obligatoriu
			'address1' => $street[0],
			'address2' => (count($street) == 2) ? $street[1] : '',
			'city' => $shipping->getCity(),
			'state'	=> $region->getName(),
			//'state'	=> self::toLatin1($region->getName()),
			'zip' => $shipping->getPostcode(),
			'country' => $country->getName(),
			'phone' => $shipping->getTelephone(),
			'observatii'=> ''
		);

		$region = $country = '';
		if($default_store->getCountry())
		{
			$region = Mage::getModel('directory/region')->loadByCode($default_store->getState(), $default_store->getCountry())->getName();
			$country = Mage::getModel('directory/country')->loadByCode($default_store->getCountry());
			$country = $country->getName();
		}
		$f_request_awb['shipFROMaddress'] = array(
			'email'			=> $default_store->getEmail(),
			'first_name'	=> $default_store->getFirstname(),
			'last_name' 	=> $default_store->getLastname(),				
			'mobile'	 	=> '',
			'main_address' 	=> $default_store->getAddress1(),
			'city' 			=> $default_store->getCity(),
			'state' 		=> $region,
			'zip' 			=> $default_store->getZipcode(),
			'country' 		=> $country,
			'phone'			=> $default_store->getPhone(),
			'instructiuni'	=> ''	
		);
		
		//print_r($f_request_awb);exit;

		Mage::log(var_export($f_request_awb, true));
		
		$lo = new LO ();
		
		$lo->f_login = $login_id;
		$lo->setRSAKey($security_key);
				
		$response_awb = $lo->EstimeazaPret($f_request_awb);
		//Mage::log($response_awb);
		
		//raspuns generare AWB
		if ((isset($response_awb->status) && $response_awb->status == 'error') || empty($response_awb))
		{
			if($response_awb->message == 'Pretul nu se poate calcula.') return false;
			
		    Mage::log("Error livrarionline:");
		    throw new Exception("Error livrarionline (response + f_request_awb dump):<br/>"."<pre>".print_r($response_awb, true).print_r($f_request_awb, true)."</pre>");
		    return false;
		}
		else
		    return $response_awb->f_pret; //array de awb-uri (awb pe fiecare colet)
	}
	
	public function printAWB($awb_id)
	{
		$login_id = Mage::getStoreConfig('carriers/'.$this->_code.'/login_id');
		$security_key = Mage::getStoreConfig('carriers/'.$this->_code.'/security_key');

		$awb = Mage::getModel('livrarionline/awb')->load($awb_id);
		$url = sprintf("http://api.livrarionline.ro/Lobackend_print/PrintAwb.aspx?f_login=%s&awb=%s", $login_id, $awb->getAwbNo());

		$client = new Zend_Http_Client($url);
		$response = $client->request();

		return $response->getBody();
	}
	
	public function cancelAWB($awb_id)
	{
		$login_id = Mage::getStoreConfig('carriers/'.$this->_code.'/login_id');
		$security_key = Mage::getStoreConfig('carriers/'.$this->_code.'/security_key');

		$awb = Mage::getModel('livrarionline/awb')->load($awb_id);
		$f_request_cancel = array('awb'=>$awb->getAwbNo());
		$lo = new LO ();
		
		$lo->f_login = $login_id;
		$lo->setRSAKey($security_key);
				
		$response_cancel = $lo->CancelLivrare($f_request_cancel);
		//Mage::log($response_cancel);
		
		//raspuns generare AWB
		if ((isset($response_cancel->status) && $response_cancel->status == 'error') || empty($response_cancel))
		{
			//if($response_cancel->message == 'Pretul nu se poate calcula.') return false;
			
		    Mage::log("Error livrarionline:");
		    throw new Exception("Error livrarionline (response + f_request_cancel dump):<br/>"."<pre>".print_r($response_cancel, true).print_r($f_request_cancel, true)."</pre>");
		    return false;
		}
		else
		    return $response_cancel->status;
	}

	public function trackAWB($awb_no)
	{
		$login_id = Mage::getStoreConfig('carriers/'.$this->_code.'/login_id');
		$security_key = Mage::getStoreConfig('carriers/'.$this->_code.'/security_key');

		//$awb = Mage::getModel('livrarionline/awb')->load($awb_id);
		$f_request_cancel = array('awb'=>$awb_no);
		$lo = new LO ();
		
		$lo->f_login = $login_id;
		$lo->setRSAKey($security_key);
				
		$response = $lo->Tracking($f_request_cancel);
		//Mage::log($response);
				
		//raspuns generare AWB
		if ((isset($response->status) && $response->status == 'error') || empty($response))
		{
			//if($response_cancel->message == 'Pretul nu se poate calcula.') return false;
			
		    Mage::log("Error livrarionline:");
		    throw new Exception("Error livrarionline (response + f_request_cancel dump):<br/>"."<pre>".print_r($response, true).print_r($f_request_cancel, true)."</pre>");
		    return false;
		}
		else
		    return $response;
	}
	
	private function toLatin1($str) {
		//Mage::log($str);

        $from = array('ş', 'ş', 'Ş', 'Ș', 'ț', 'ţ', 'Ţ', 'Ț', 'Ă', 'ă', 'Â', 'â', 'Î', 'î');
        $to = array('s', 's', 'S', 'S', 't', 't', 'T', 'T', 'A', 'a', 'A', 'a', 'I', 'i');
        $str = str_replace($from, $to, $str);

		//Mage::log($str);
        $str = preg_replace('@[^a-z0-9 \-\.\(\)\,]+@iUs', '', $str);
		//Mage::log($str);

        return $str;
    }
}