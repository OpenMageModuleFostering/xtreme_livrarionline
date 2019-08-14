<?php
class Xtreme_Livrarionline_Block_Livrarionline extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getLivrarionline()     
     { 
        if (!$this->hasData('livrarionline')) {
            $this->setData('livrarionline', Mage::registry('livrarionline'));
        }
        return $this->getData('livrarionline');
        
    }
}