<?php

class PortableInfoboxBuilderServiceTest extends WikiaBaseTest {

	protected function setUp() {
		$this->setupFile = dirname( __FILE__ ) . '/../PortableInfoboxBuilder.setup.php';
		parent::setUp();
		$this->builderService = new PortableInfoboxBuilderService();
	}

	/**
	 * @dataProvider dataTranslationsDataProvider
	 */
	public function testTranslationFromData( $data, $expected ) {
		$this->assertEquals( $expected, $this->builderService->translateDataToMarkup( $data ) );
	}

	/**
	 * @dataProvider markupTranslationsDataProvider
	 */
	public function testTranslationFromMarkup( $markup, $expected ) {
		$this->markTestIncomplete('Feature under development');
		$this->assertEquals( $expected, $this->builderService->translateDataToMarkup( $markup ) );
	}


	public function dataTranslationsDataProvider() {
		return [
			[ "", "" ],
			[ "[]", "" ],
			[ '{"data":[{"type":"row", "source":"asdf"}]}', '<infobox><data source="asdf"/></infobox>' ],
			[ '{"data":[{"type":"row", "source":"asdf", "data": {"label": "asdfsda"}}]}', '<infobox><data source="asdf"><label>asdfsda</label></data></infobox>' ],
			[ '{"data":[{"type":"row", "source":"asdf", "data": {"label": ""}}]}', '<infobox><data source="asdf"/></infobox>' ],
			[ '{"data":[{"type":"title", "source":"title", "data": {"defaultValue": "{{PAGENAME}}"}}]}', '<infobox><title source="title"><default>{{PAGENAME}}</default></title></infobox>' ],
			[ '{"data":[{"type":"title", "source":"title", "data": {"defaultValue": ""}}]}', '<infobox><title source="title"/></infobox>' ],
			[ '{"data":[{"type":"title", "source":"title", "data": {"defaultValue": "0"}}]}', '<infobox><title source="title"><default>0</default></title></infobox>' ],
			[ '{"data":[{"type":"group", "data": [{"type": "row", "source": "asdf"}]}]}', '<infobox><group><data source="asdf"/></group></infobox>' ],
			[ '{"theme": "asdf", "data": [{"type": "image", "source": "image", "data": { "alt": {"source": "title", "data": {"default": "asdf"}}}}]}', '<infobox theme="asdf"><image source="image"><alt source="title"><default>asdf</default></alt></image></infobox>' ],
			[ '{"theme": "adsf", "data": [{"type": "group", "data": [{"type":"header", "data":"asdf"}]}]}', '<infobox theme="adsf"><group><header>asdf</header></group></infobox>' ]
		];
	}

	public function markupTranslationsDataProvider() {
		return [
			[ "", "" ],
			[ "", "[]" ],
			[ '<infobox><data source="asdf"/></infobox>', '{"data":[{"type":"row", "source":"asdf"}]}' ],
			[ '<infobox><data source="asdf"><label>asdfsda</label></data></infobox>', '{"data":[{"type":"row", "source":"asdf", "data": {"label": "asdfsda"}}]}' ],
			[ '<infobox><data source="asdf"/></infobox>', '{"data":[{"type":"row", "source":"asdf", "data": {"label": ""}}]}' ],
			[ '<infobox><title source="title"><default>{{PAGENAME}}</default></title></infobox>', '{"data":[{"type":"title", "source":"title", "data": {"defaultValue": "{{PAGENAME}}"}}]}' ],
			[ '<infobox><title source="title"/></infobox>', '{"data":[{"type":"title", "source":"title", "data": {"defaultValue": ""}}]}' ],
			[ '<infobox><title source="title"><default>0</default></title></infobox>', '{"data":[{"type":"title", "source":"title", "data": {"defaultValue": "0"}}]}' ],
			[ '<infobox><group><data source="asdf"/></group></infobox>', '{"data":[{"type":"group", "data": [{"type": "row", "source": "asdf"}]}]}' ],
			[ '<infobox theme="asdf"><image source="image"><alt source="title"><default>asdf</default></alt></image></infobox>', '{"theme": "asdf", "data": [{"type": "image", "source": "image", "data": { "alt": {"source": "title", "data": {"default": "asdf"}}}}]}' ],
			[ '<infobox theme="adsf"><group><header>asdf</header></group></infobox>', '{"theme": "adsf", "data": [{"type": "group", "data": [{"type":"header", "data":"asdf"}]}]}' ]
		];
	}
}
