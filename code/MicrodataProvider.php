<?php
/**
 * Milkyway Multimedia
 * MicrodataProvider.php
 *
 * @package mwm
 * @author Mellisa Hankins <mell@milkywaymultimedia.com.au>
 */

class MicrodataProvider extends ViewableData implements TemplateGlobalProvider {
	private static $schema_url = 'http://schema.org';
	private static $inst;

	public static function inst() {
		if(!self::$inst)
			self::$inst = MicrodataProvider::create();

		return self::$inst;
	}

	public function __get($property) {
		if($this->hasMethod($property))
			return $this->$property();
		else
			return $this->itemProp($property);
	}

	public static function get_template_global_variables() {
		return array(
			'microData' => 'inst',
		);
	}

	public function Address($prop = '', $ref = '', $array = false) {
		return $this->getAttributes($prop, 'Address', true, $ref, $array);
	}

	public function PostalAddress($prop = '', $ref = '', $array = false) {
		return $this->getAttributes($prop, 'PostalAddress', true, $ref, $array);
	}

	public function Offers($prop = '', $ref = '', $array = false) {
		return $this->getAttributes($prop, 'Offer', true, $ref, $array);
	}

	public function Person($prop = '', $ref = '', $array = false) {
		return $this->getAttributes($prop, 'Person', true, $ref, $array);
	}

	public function Organization($prop = '', $ref = '', $array = false) {
		return $this->getAttributes($prop, 'Organization', true, $ref, $array);
	}

	public function Product($prop = '', $ref = '', $array = false) {
		return $this->getAttributes($prop, 'Product', true, $ref, $array);
	}

	public function InStock($ref = '', $array = false) {
		return $this->getAttributes('availability', 'InStock', false, $ref, $array);
	}

	public function NoStock($ref = '', $array = false) {
		return $this->getAttributes('availability', 'NoStock', false, $ref, $array);
	}

	public function Website($prop = '', $ref = '', $array = false) {
		return $this->getAttributes($prop, 'WebSite', true, $ref, $array);
	}

	public function itemProp($value, $array = false) {
		return $this->getAttribute($this->fixValueForProp($value), 'itemprop', $array);
	}

	public function itemType($value, $array = false) {
		$value = strtoupper($value) == $value ? $value : preg_replace('/(?<!^)([A-Z])/', '-\\1', $value);
		return $this->getAttribute($this->itemTypeURL($value), 'itemtype', $array);
	}

	public function itemScope($array = false) {
		return $this->getAttribute('itemscope', 'itemscope', $array);
	}

	public function itemRef($value, $array = false) {
		$value = strtoupper($value) == $value ? $value : preg_replace('/(?<!^)([A-Z])/', '-\\1', $value);
		return $this->getAttribute($value, 'itemref', $array);
	}

	public function itemTypeURL($type) {
		return Controller::join_links($this->config()->schema_url, ucfirst($type));
	}

	public function linkTag($prop = '', $type = '', $scope = false, $ref = '') {
		$tags = $this->getAttributes($prop, $type, $scope, $ref, true);

		if($type)
			$tags['href'] = $this->itemTypeURL($type);

		if(count($tags))
			return DBField::create_field('HTMLText', '<link ' . $this->convertArray($tags) . ' />');
		else
			return '';
	}

	public function metaTag($prop = '', $content = '', $type = '', $scope = false, $ref = '') {
		$tags = $this->getAttributes($prop, $type, $scope, $ref, true);

		if($content)
			$tags['content'] = $content;

		if($type)
			$tags['href'] = $this->itemTypeURL($type);

		if(count($tags))
			return DBField::create_field('HTMLText', '<meta ' . $this->convertArray($tags) . ' />');
		else
			return '';
	}

	public function getAttributes($prop = '', $type = '', $scope = false, $ref = '', $array = false) {
		$tags = array();

		if($prop)
			$tags['itemprop'] = $this->fixValueForProp($prop);

		if($type)
			$tags['itemtype'] = $this->itemTypeURL($type);

		if($scope)
			$tags['itemscope'] = 'itemscope';

		if($ref)
			$tags['itemref'] = $ref;

		return $array ? $tags : $this->convertArray($tags);
	}

	public function getAttribute($value = '', $key = 'itemprop', $array = false) {
		if(!$value) return '';

		if($array)
			return array($key => $value);
		else
			return "$key=\"$value\"";
	}

	protected function fixValueForProp($value) {
		return strtoupper($value) == $value ? $value : singleton('s')->camelize($value);
	}

	protected function convertArray($tags) {
		$parts = array();

		foreach($tags as $name => $value) {
			$parts[] = ($value === true) ? "{$name}=\"{$name}\"" : "{$name}=\"" . Convert::raw2att($value) . "\"";
		}

		return DBField::create_field('HTMLText', trim(implode(' ', $parts)));
	}
} 