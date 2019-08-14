<?php
//ini_set("memory_limit", "1G");
require_once('lib/LivrariOnline/lo.php');
class Xtreme_Livrarionline_Model_Carrier_Livrarionline extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface {
	protected $_code = 'livrarionline';

	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
	{
		if (!Mage::getStoreConfig('carriers/'.$this->_code.'/active')) {
			return false;
		}

		$quote = Mage::getSingleton('checkout/session')->getQuote();

		$handling = Mage::getStoreConfig('carriers/'.$this->_code.'/handling');
		$result = Mage::getModel('shipping/rate_result');
		$show = true;
		$carriers = Mage::getModel('livrarionline/carriers')->getCollection()->addFieldToFilter('`status`', 1);
		foreach($carriers as $carrier)
		{
			$flat_fee = $carrier->getFlatFee(); //preiau valoarea flat fee
			$shippingStateConfig = explode(',',  $carrier->getApplicableStates()); // preiau judetele pe care se aplica serviciul
			$destState = $request->getDestRegionId(); // preiau judetul destinatie

			if (in_array($destState, $shippingStateConfig)) {
				if ($flat_fee && $flat_fee>=0) {
					$price_res = $flat_fee; //daca avem flat fee setat
				} else {
					$price_res = Mage::helper('livrarionline')->getEstimate($carrier, $quote);
				}

				if($price_res){
					$method = Mage::getModel('shipping/rate_result_method');
					$method->setCarrier($this->_code);
					$method->setMethod($this->_code."_".$carrier->getCarrierId());
					$method->setCarrierTitle(Mage::getStoreConfig('carriers/'.$this->_code.'/title'));
					$method->setMethodTitle($carrier->getName());
					$method->setPrice($price_res);
					$method->setCost($price_res);
					$result->append($method);
				}
			}
			/*else
			{
				$error = Mage::getModel('shipping/rate_result_error');
				$error->setCarrier($this->_code);
				$error->setCarrierTitle($this->getConfigData('name'));
				$error->setErrorMessage($this->getConfigData('specificerrmsg'));
				$result->append($error);
			}*/
		}

		return $result;

		/*
		 //Case1: Price Depends on Country,State and Pin Code
		 echo $destCountry = $request->getDestCountryId().': Dest Country<br/>';
		 echo $destRegion = $request->getDestRegionId().': Dest Region<br/>';
		 echo $destRegionCode = $request->getDestRegionCode().': Dest Region Code<br/>';
		 print_r($destStreet = $request->getDestStreet()); echo ': Dest Street<br/>';
		 echo $destCity = $request->getDestCity().': Dest City<br/>';
		 echo $destPostcode = $request->getDestPostcode().': Dest Postcode<br/>';
		 echo $country_id = $request->getCountryId().': Package Source Country ID<br/>';
		 echo $region_id = $request->getRegionId().': Package Source Region ID<br/>';
		 echo $city = $request->getCity().': Package Source City<br/>';
		 echo $postcode = $request->getPostcode().': Package Source Post Code<br/>';

		 //Case2: Price Depends on Total Order Value or Weight
		 echo $packageValue = $request->getPackageValue().': Dest Package Value<br/>';
		 echo $packageValueDiscout = $request->getPackageValueWithDiscount().': Dest Package Value After Discount<br/>';
		 echo $packageWeight = $request->getPackageWeight().': Package Weight<br/>';
		 echo $packageQty = $request->getPackageQty().': Package Quantity <br/>';
		 echo $packageCurrency = $request->getPackageCurrency().': Package Currency <br/>';

		 //Case3: Price Depends on order dimensions
		 echo $packageheight = $request->getPackageHeight() .': Package height <br/>';
		 echo $request->getPackageWeight().': Package Width <br/>';
		 echo $request->getPackageDepth().': Package Depth <br/>';
		 


		//Case4: Price based on product attribute
		if ($request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
					continue;
				}

				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
							$product_id = $child->getProductId();
							$productObj = Mage::getModel('catalog/product')->load($product_id);
							$ship_price = $productObj->getData('shipping_price'); //our shipping attribute code
							$price += (float)$ship_price;
						}
					}
				} else {
					$product_id = $item->getProductId();
					$productObj = Mage::getModel('catalog/product')->load($product_id);
					$ship_price = $productObj->getData('shipping_price'); //our shipping attribute code
					$price += (float)$ship_price;
				}
			}
		}
      
		//Case5: Shipping option based configurable product option
		if ($request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
					continue;
				}
				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
							$product_id = $child->getProductId();
							$value = $item->getOptionByCode('info_buyRequest')->getValue();
							$params = unserialize($value);
							$attributeObj = Mage::getModel('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY,'shirt_size'); // our configurable attribute
							$attribute_id = $attributeObj->getAttributeId();
							$attribute_selected = $params['super_attribute'][$attribute_id];

							$label = '';
							foreach($attributeObj->getSource()->getAllOptions(false) as $option){
								if($option['value'] == $attribute_selected){
									$label =  $option['label'];
								}
							}
							if($label = 'Small'){
								$price += 15;
							} else if($label = 'Medium'){
								$price += 20;
							} else if($label = 'Large'){
								$price += 22;
							}
						}
					}
				} else {
					$product_id = $item->getProductId();
					$value = $item->getOptionByCode('info_buyRequest')->getValue();
					$params = unserialize($value);
					$attributeObj = Mage::getModel('eav/config')->getAttribute(Mage_Catalog_Model_Product::ENTITY,'shirt_size'); // our configurable attribute
					$attribute_id = $attributeObj->getAttributeId();
					$attribute_selected = $params['super_attribute'][$attribute_id];

					$label = '';
					foreach($attributeObj->getSource()->getAllOptions(false) as $option){
						if($option['value'] == $attribute_selected){
							$label =  $option['label'];
						}
					}
					if($label = 'Small'){
						$price += 15;
					} else if($label = 'Medium'){
						$price += 20;
					} else if($label = 'Large'){
						$price += 22;
					}
				}
			}
		}
		
		
		//Case6: Price based on custom options
		if ($request->getAllItems()) {
			foreach ($request->getAllItems() as $item) {
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
					continue;
				}
				if ($item->getHasChildren() && $item->isShipSeparately()) {
					foreach ($item->getChildren() as $child) {
						if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
							$product_id = $child->getProductId();
							$value = $item->getOptionByCode('info_buyRequest')->getValue();
							$params = unserialize($value);
							$options_select = $params['options'];

							$product = Mage::getModel('catalog/product')->load($product_id);
							$options = $product->getOptions();
							foreach ($options as $option) {
								if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
									$option_id =  $option->getId();
									foreach ($option->getValues() as $value) {
										if($value->getId() == $options_select[$option_id]){
											if($value->getTitle() == 'Express'){
												$price += 50;
											}else if($value->getTitle() == 'Normal'){
												$price += 10;
											}
										}

									}
								}
							}
						}
					}
				} else {
					$product_id = $item->getProductId();
					$value = $item->getOptionByCode('info_buyRequest')->getValue();
					$params = unserialize($value);
					$options_select = $params['options'];

					$product = Mage::getModel('catalog/product')->load($product_id);
					$options = $product->getOptions();
					foreach ($options as $option) {
						if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
							$option_id =  $option->getId();
							foreach ($option->getValues() as $value) {
								if($value->getId() == $options_select[$option_id]){
									if($value->getTitle() == 'Express'){
										$price += 50;
									}else if($value->getTitle() == 'Normal'){
										$price += 10;
									}
								}

							}
						}
					}
				}
			}
		}
		*/
	}
	public function getAllowedMethods()
	{
		return array('livrarionline'=>$this->getConfigData('name'));
	}
	
	public function isTrackingAvailable()
	{
		return true;
	}
	
	public function getTrackingInfo($tracking)
	{
		$awb_res = Mage::helper('livrarionline')->trackAWB($tracking);
		$track = Mage::getModel('shipping/tracking_result_status');
		$track->setTrackSummary( sprintf("%s : %s", $awb_res->f_stare_curenta->stamp, $awb_res->f_stare_curenta->stare) )
			->setTracking($tracking)
			->setCarrierTitle($this->getConfigData('name'));
		return $track;
	}	
}