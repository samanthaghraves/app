<?php
namespace Wikia\ExactTarget\Builders;

use Wikia\Util\Assert;
use Wikia\ExactTarget\ResourceEnum as Enum;

class UpdateRequestBuilder extends BaseRequestBuilder {
	const SAVE_OPTION_TYPE = 'SaveOption';

	private $userData;
	private $edits;

	private static $supportedTypes = [ self::PROPERTIES_TYPE, self::EDITS_TYPE, self::USER_TYPE ];

	public function withUserData( array $userData ) {
		$this->userData = $userData;
		return $this;
	}

	public function withEdits( $edits ) {
		$this->edits = $edits;
		return $this;
	}

	public function build() {
		$oRequest = new \ExactTarget_UpdateRequest();
		$oRequest->Options = $this->prepareUpdateCreateOptions();

		// prepare exact target structure
		if ( $this->type === self::PROPERTIES_TYPE ) {
			$objects = $this->prepareUserPreferencesParams( $this->userId, $this->properties );
		} elseif ( $this->type === self::USER_TYPE ) {
			$objects = $this->prepareUsersUpdateParams( $this->userData );
		} elseif ( $this->type === self::EDITS_TYPE ) {
			$objects = $this->prepareUserEditsParams( $this->edits );
		} else {
			$objects = [ ];
		}
		// make it soap vars
		$oRequest->Objects = $this->prepareSoapVars( $objects, self::DATA_EXTENSION_OBJECT_TYPE );

		return $oRequest;
	}

	protected function getSupportedTypes() {
		return self::$supportedTypes;
	}

	private function prepareUserPreferencesParams( $id, $properties ) {
		Assert::true( isset( $this->userId ) );
		$objects = [ ];
		foreach ( $properties as $sProperty => $sValue ) {
			$objects[] = $this->prepareDataObject( Enum::CUSTOMER_KEY_USER_PROPERTIES,
				[ Enum::USER_PROPERTIES_USER => $id, Enum::USER_PROPERTIES_PROPERTY => $sProperty ],
				[ Enum::USER_PROPERTIES_VALUE => $sValue ]
			);
		}
		return $objects;
	}

	private function prepareUsersUpdateParams( array $usersData ) {
		$aDataExtension = [ ];
		foreach ( $usersData as $aUserData ) {
			$userId = $aUserData[ Enum::USER_ID ];
			// remove userId as its handled differently
			unset( $aUserData[ Enum::USER_ID ] );
			Assert::true( !empty( $userId ) );

			$aDataExtension[] = $this->prepareDataObject( Enum::CUSTOMER_KEY_USER,
				[ Enum::USER_ID => $userId ], $aUserData );
		}
		return $aDataExtension;
	}

	private function prepareUpdateCreateOptions() {
		$updateOptions = new \ExactTarget_UpdateOptions();

		$saveOption = new \ExactTarget_SaveOption();
		$saveOption->PropertyName = self::DATA_EXTENSION_OBJECT_TYPE;
		$saveOption->SaveAction = \ExactTarget_SaveAction::UpdateAdd;

		$updateOptions->SaveOptions = [ $this->wrapToSoapVar( $saveOption, self::SAVE_OPTION_TYPE ) ];
		return $updateOptions;
	}

	private function prepareUserEditsParams( $edits ) {
		$result = [ ];
		foreach ( $edits as $userId => $contributions ) {
			foreach ( $contributions as $wikiId => $number ) {
				$result[] = $this->prepareDataObject( Enum::CUSTOMER_KEY_USER_ID_WIKI_ID,
					[ Enum::USER_ID => $userId, Enum::USER_WIKI_ID => $wikiId ],
					[ Enum::USER_WIKI_FIELD_CONTRIBUTIONS => $number ]
				);
			}
		}

		return $result;
	}

}
