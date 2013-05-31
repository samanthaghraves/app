<?php
/**
 * User: artur
 * Date: 29.05.13
 * Time: 15:20
 */

class TableVisitor extends DOMNodeVisitorBase {

	/**
	 * @param DOMNode $currentNode
	 * @return bool
	 */
	public function canVisit(DOMNode $currentNode) {
		return $this->isElement($currentNode, 'table');
	}

	/**
	 * @param DOMNode $currentNode
	 */
	public function visit(DOMNode $currentNode) {
		$table = new JsonFormatTableNode( );
		$this->getJsonFormatTraversingState()->pushNode( $table );
		for( $i = 0; $i < $currentNode->childNodes->length; $i++ ) {
			$childNode = $currentNode->childNodes->item($i);
			$this->tryVisitTBody( $childNode );
			$this->tryVisitRow( $childNode );
		}
		$this->getJsonFormatTraversingState()->popNode( $table );
	}

	protected function tryVisitTBody( $currentNode ) {
		if ( $this->isElement( $currentNode, 'tbody' ) ) {
			for( $i = 0; $i < $currentNode->childNodes->length; $i++ ) {
				$childNode = $currentNode->childNodes->item($i);
				$this->tryVisitRow( $childNode );
			}
		}
	}

	protected function tryVisitRow( $currentNode ) {
		if ( $this->isElement( $currentNode, 'tr' ) ) {
			$row = new JsonFormatTableRowNode( );
			$this->getJsonFormatTraversingState()->pushNode( $row );

			for( $i = 0; $i < $currentNode->childNodes->length; $i++ ) {
				$childNode = $currentNode->childNodes->item($i);
				$this->tryVisitCell( $childNode );
			}

			$this->getJsonFormatTraversingState()->popNode( $row );
		}
	}

	protected function tryVisitCell( DOMNode $currentNode ) {
		if ( $this->isElement( $currentNode, 'td' )
			|| $this->isElement( $currentNode, 'th' )) {
			$cell = new JsonFormatTableCellNode( );
			$this->getJsonFormatTraversingState()->pushNode( $cell );

			if ( $this->hasSingleDiv( $currentNode ) ) {
				// special case: if single div, ignore it but get all the contents.
				// tables cells often contain div wrapper
				$this->iterate( $currentNode->childNodes->item(0)->childNodes );
			} else {
				$this->iterate( $currentNode->childNodes );
			}

			$this->getJsonFormatTraversingState()->popNode( $cell );
		}
	}

	protected function hasSingleDiv( DOMNode $currentNode ) {
		return DomHelper::isElement( $currentNode, 'td' )
			&& $currentNode->childNodes->length == 1
			&& DomHelper::isElement( $currentNode->childNodes->item(0), 'div' );
	}
}
