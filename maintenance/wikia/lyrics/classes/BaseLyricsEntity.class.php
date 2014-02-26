<?php
/**
 * Created by PhpStorm.
 * User: aquilax
 * Date: 2/26/14
 * Time: 12:55 PM
 */

class BaseLyricsEntity {

	protected $db;

	public function __construct( $db ) {
		return $this->db = $db;
	}

	protected function sanitiseData( $data, $dataMap) {
		$result = array();
		foreach ( $data as $key => $value ) {
			if ( isset( $dataMap[$key] ) ) {
				$result[$dataMap[$key]] = $value;
			}
		}
		return $result;
	}

} 