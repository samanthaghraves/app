<?php

class ArticleVideoController extends WikiaController {
	public function index() {
		$wg = F::app()->wg;

		$title = RequestContext::getMain()->getTitle()->getPartialURL();

		// TODO: replace it with DS icon when it's ready (XW-2824)
		$this->setVal( 'closeIconUrl',
			$wg->extensionsPath . '/wikia/ArticleVideo/images/close.svg' );
		$this->setVal( 'videoPlayButtonUrl',
			$wg->extensionsPath . '/wikia/ArticleVideo/images/play-button-solid.svg' );
		$this->setVal( 'videoDetails', $wg->articleVideoFeaturedVideos[$title] );
	}
}
