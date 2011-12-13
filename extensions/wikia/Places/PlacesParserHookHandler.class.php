<?php

/**
 * PlacesParserHookHandler class
 *
 * @author Jakub Kurcek <jakub at wikia-inc.com>
 * @author Maciej Brencz <macbre at wikia-inc.com>
 * @date 2010-10-11
 * @copyright Copyright (C) 2010 Jakub Kurcek, Wikia Inc.
 * @copyright Copyright (C) 2010 Maciej Brencz, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 */

class PlacesParserHookHandler {

	// stores wikitextId to be used when rendering placeholder for RTE
	static public $lastWikitextId;

	/**
	 * Render <place> tag
	 *
	 * @param string $content tag content (will be ignored)
	 * @param array $attributes tag attributes
	 * @param Parser $parser MW parser instance
	 * @param PPFrame $frame parent frame with the context
	 * @return string HTML output of the tag
	 */
	static public function renderPlaceTag($content, array $attributes, Parser $parser, PPFrame $frame) {
		wfProfileIn(__METHOD__);

		// wrap data in a model object
		$placeModel = F::build('PlaceModel', array($attributes), 'newFromAttributes');

		// are we rendering for RTE?
		$inRTE = !empty(F::app()->wg->RTEParserEnabled);

		if ($inRTE) {
			$wikitext = F::build('RTEData', array('wikitext', self::$lastWikitextId), 'get');

			$data = array(
				'wikitext' => $wikitext,
				'placeholder' => 1,
			);

			$rteData = F::build('RTEData', array($data), 'convertDataToAttributes');
		}
		else {
			$rteData = false;
		}

		// render parser hook
		$html = F::app()->sendRequest(
			'Places',
			'placeFromModel',
			array(
				'model'	=> $placeModel,
				'rteData' => $rteData,
			)
		)->toString();

		// add JS snippets code
		if (!$inRTE) {
			$html .= self::getJSSnippet();
		}

		// add model to be stored in database
		F::build( 'PlacesHooks' )->setModelToSave($placeModel);

		$html = self::cleanHTML($html);

		wfProfileOut(__METHOD__);
		return $html;
	}

	/**
	 * Render <places> tag
	 *
	 * @param string $content tag content (will be ignored)
	 * @param array $attributes tag attributes
	 * @param Parser $parser MW parser instance
	 * @param PPFrame $frame parent frame with the context
	 * @return string HTML output of the tag
	 */
	static public function renderPlacesTag($content, array $attributes, Parser $parser, PPFrame $frame) {
		wfProfileIn(__METHOD__);

		// parse attributes
		$height = !empty($attributes['height']) && is_numeric($attributes['height']) ? $attributes['height'] : 400;
		$categories = !empty($attributes['category']) ? explode('|', $attributes['category']) : false;
		$animate = !empty($attributes['animate'])
			?
			is_numeric($attributes['animate']) ? intval($attributes['animate']) : 5 /* default animation delay in sec */
			:
			false;

		// get all places on this wiki
		$placesModel = F::build('PlacesModel');
		$markers = empty($categories) ? $placesModel->getAll() : $placesModel->getFromCategories($categories);

		// render parser hook
		$html = F::app()->sendRequest(
			'Places',
			'renderMarkers',
			array(
				'markers' => $markers,
				'height' => $height,
				'options' => array(
					'animate' => $animate
				)
			)
		)->toString();

		wfProfileOut(__METHOD__);
		return $html;
	}

	/**
	 * Remove whitespaces confusing MW parser
	 *
	 * @param string $html parser tag output to be cleaned up
	 * @return string cleaned up HTML
	 */
	static private function cleanHTML($html) {
		return strtr($html, array(
			"\n" => '',
			"\t" => '',
		));
	}

	/**
	 * Get JavaScript code snippet to be loaded
	 */
	static public function getJSSnippet(Array $options = array()) {
		$html = F::build('JSSnippets')->addToStack(
			array(
				'/extensions/wikia/Places/css/Places.css',
				'/extensions/wikia/Places/js/Places.js',
			),
			array(),
			'Places.init',
			$options,
			array(JSSnippets::FILTER_NONE, 'wikiamobile')
		);

		return $html;
	}
}
