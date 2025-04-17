<?php
namespace CravenDunnill\ProductSummary\Block\Product;

class Summary extends \Magento\Catalog\Block\Product\View
{
	/**
	 * Get current product
	 *
	 * @return \Magento\Catalog\Model\Product
	 */
	public function getProduct()
	{
		return $this->_coreRegistry->registry('current_product');
	}

	/**
	 * Get product attribute value
	 *
	 * @param string $attributeCode
	 * @return mixed
	 */
	public function getAttributeValue($attributeCode)
	{
		$product = $this->getProduct();
		$attribute = $product->getResource()->getAttribute($attributeCode);
		
		if (!$attribute) {
			return null;
		}
		
		$value = $product->getData($attributeCode);
		
		if ($attribute->usesSource()) {
			$value = $attribute->getSource()->getOptionText($value);
		}
		
		return $value;
	}

	/**
	 * Get suitability attributes
	 *
	 * @return array
	 */
	public function getSuitabilityAttributes()
	{
		$suitabilityAttributes = [
			'tile_suitability_floors' => 'Floors',
			'tile_suitability_walls' => 'Walls',
			'tile_suitability_kitchen' => 'Kitchen',
			'tile_suitability_bathroom' => 'Bathroom',
			'tile_suitability_living' => 'Living',
			'tile_suitability_indoor' => 'Indoor',
			'tile_suitability_outdoor' => 'Outdoor'
		];
		
		$result = [];
		
		foreach ($suitabilityAttributes as $code => $label) {
			$value = $this->getAttributeValue($code);
			if ($value) {
				// Convert Magento\Framework\Phrase to string if needed
				$stringValue = (string)$value;
				if ($stringValue == 'Yes' || $stringValue == '1') {
					$result[] = $label;
				}
			}
		}
		
		return $result;
	}

	/**
	 * Get properties attributes
	 *
	 * @return array
	 */
	public function getPropertiesAttributes()
	{
		$properties = [];
		
		// Traffic
		$traffic = $this->getAttributeValue('tile_traffic');
		if ($traffic) {
			$properties[] = (string)$traffic;
		}
		
		// Rectified
		$rectified = $this->getAttributeValue('tile_rectified');
		if ($rectified) {
			$stringRectified = (string)$rectified;
			if ($stringRectified == 'Yes' || $stringRectified == '1') {
				$properties[] = 'Rectified';
			}
		}
		
		return $properties;
	}

	/**
	 * Check if summary has data to display
	 *
	 * @return bool
	 */
	public function hasSummaryData()
	{
		return 
			!empty($this->getSuitabilityAttributes()) || 
			$this->getAttributeValue('tile_ptv') || 
			!empty($this->getPropertiesAttributes()) || 
			$this->getAttributeValue('tile_recycled_content');
	}
}