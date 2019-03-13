<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Glauber Portella
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

if (!defined('_PS_VERSION_'))
	exit;

class BlockManufacturerSlider extends Module
{
	/**
	 * Cache filepath
	 * The cache file is a JSON formatted as
	 * [
	 *     { "id": "Manufacturer ID", name": "Manufacturer name", "link_rewrite": "Manufacturer PS link_rewrite", "image": "Manufacturer brand image" },
	 *     { "id": "Manufacturer ID", name": "Manufacturer name", "link_rewrite": "Manufacturer PS link_rewrite", "image": "Manufacturer brand image" },
	 *     { "id": "Manufacturer ID", name": "Manufacturer name", "link_rewrite": "Manufacturer PS link_rewrite", "image": "Manufacturer brand image" },
	 *     .
	 *     .
	 *     .
	 *     { "id": "Manufacturer ID", "name": "Manufacturer name", "link_rewrite": "Manufacturer PS link_rewrite", "image": "Manufacturer brand image" }
	 * ]
	 * @var string
	 */
	private $_cache_filepath;

	public function __construct()
    {
        $this->name = 'blockmanufacturerslider';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
		$this->author = 'Glauber Portella';
		$this->need_instance = 0;

        $this->bootstrap = true;
		parent::__construct();

		$this->displayName = $this->l('Manufacturers Brands Slider block');
        $this->description = $this->l('Displays a block listing manufacturers brands in a slider.');
		$this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

		$this->_cache_filepath = _PS_MODULE_DIR_.$this->name.'/cache/data.json';
    }

	public function install()
	{
		$success = (parent::install() &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayHomeManufacturerCarousel') &&
			$this->registerHook('actionObjectManufacturerDeleteAfter') &&
			$this->registerHook('actionObjectManufacturerAddAfter') &&
			$this->registerHook('actionObjectManufacturerUpdateAfter') &&
			$this->updateCache()
		);
		return $success;
    }

    public function hookDisplayHeader($params)
    {
    	$this->context->controller->addCSS($this->_path.'views/stylesheets/jquery.bxslider.css');
    	$this->context->controller->addJqueryPlugin(array('bxslider'));

    	$this->context->controller->addCSS($this->_path.'views/stylesheets/blockmanufacturerslider.css');
    	$this->context->controller->addJS($this->_path.'views/javascripts/blockmanufacturerslider.js');
    }

    public function hookDisplayHomeManufacturerCarousel($params)
    {
    	if (!file_exists($this->_cache_filepath))
    		return;

    	$manufacturers = json_decode(file_get_contents($this->_cache_filepath), true);
    	
    	$this->smarty->assign(array(
    		'manufacturers' => $manufacturers
		));
    	
    	return $this->display(__FILE__, 'display_home_manufacturer_carousel.tpl');
    }

    public function hookActionObjectManufacturerAddAfter($params)
    {
    	$this->updateCache();
    }

    public function hookActionObjectManufacturerUpdateAfter($params)
    {
    	$this->updateCache();
    }

    public function hookActionObjectManufacturerDeleteAfter($params)
    {
    	$this->updateCache();
    }

    /**
     * Creates a cache file with manufacturer data
     * @return boolean
     */
    protected function updateCache()
    {
    	// Load all manufacturers from Prestashop DB
    	// select only manufacturers that have image (brand)
    	// save the selected manufactures in $this->_cache_filepath json
    	$manufacturers = Manufacturer::getManufacturers();
    	$jsonData = array();

    	foreach ($manufacturers as $manufacturer) {
			// @todo Add module configuration to select the image size to use
			// @todo Add new images sizes for module
			// medium size image
    		//$image = $manufacturer['id_manufacturer'].'-'.ImageType::getFormatedName('medium').'.jpg';
			//$imageFile = _PS_MANU_IMG_DIR_.$manufacturer['id_manufacturer'].'-'.ImageType::getFormatedName('medium').'.jpg';

			// original size image
			$image = $manufacturer['id_manufacturer'].'.jpg';
			$imageFile = _PS_MANU_IMG_DIR_.$manufacturer['id_manufacturer'].'.jpg';
			if (file_exists($imageFile)) {
	    		$jsonData[] = array(
	    			'id_manufacturer' => $manufacturer['id_manufacturer'],
	    			'name' => $manufacturer['name'],
	    			'link_rewrite' => $manufacturer['link_rewrite'],
	    			'image' => '/img/m/'.$image
				);
			}
    	}

    	// save json (rewrite file if exists)
    	file_put_contents($this->_cache_filepath, json_encode($jsonData));

    	return true;
    }
}