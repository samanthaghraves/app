<div id="CreatePageDialog" title="<?= wfMsg( 'createpage-dialog-title' ) ?>" >
	<form name="CreatePageForm" id="CreatePageForm" onsubmit="CreatePage.submitDialog(); return false;">
		<div id="CreatePageContainer">
			<div id="CreatePageDialogSub">
				<?= wfMsg( 'createpage-dialog-message2' ) ?>
			</div>
			<input id="wpCreatePageDialogTitle" name="wpCreatepageDialogTitle" type="text" />
			<?php // TODO: XW-2380 | remove the condition
			if ( !wfMessage( 'createpage-dialog-message3' )->isBlank() ): ?>
				<div id="CreatePageDialogInputSub">
					<?= wfMessage( 'createpage-dialog-message3' )->text() ?>
				</div>
			<?php endif; ?>
			<div id="CreatePageDialogTitleErrorMsg" class="CreatePageError hiddenStructure"></div>
				<?php if( !$useFormatOnly ): ?>
				<div id="CreatePageDialogChoose">
					<?= wfMsg( 'createpage-dialog-choose' ) ?>
				</div>
				<ul id="CreatePageDialogChoices">
					<? foreach( $options as $name => $params ) :?>
						<?php if($type == "short"): ?>
							<li id="CreatePageDialog<?= ucfirst( $name ) ;?>Container" class="chooser" <?= ( $params[ 'width' ] ) ?  "style=\"width: {$params[ 'width' ]}\"" : null ;?>">
								<div>
									<input type="radio" name="wpCreatePageChoices" id="CreatePageDialog<?= ucfirst( $name ) ;?>" value="<?= $name ;?>" />
									<label for="CreatePageDialog<?= ucfirst( $name ) ;?>">
										<?= $params[ 'label' ];?>
										<img src="<?= $params[ 'icon' ] ;?>" />
									</label>
								</div>
							</li>
						<?php elseif($type == "long"): ?>
							<li class="long chooser accent" id="CreatePageDialog<?= ucfirst( $name ) ;?>Container" >
								<input type="radio" name="wpCreatePageChoices" id="CreatePageDialog<?= ucfirst( $name ) ;?>" value="<?= $name ;?>">
								<img src="<?= $params[ 'icon' ] ;?>">
								<div>
									<span><?= $params[ 'label' ];?></span>
										<br><?php echo !empty($params[ 'desc' ]) ? $params[ 'desc' ]:""; ?>
								</div>
							</li>
						<?php endif; ?>
						<script type="text/javascript">CreatePage.options['<?= $name ;?>'] = <?= json_encode( $params ) ;?>;</script>
					<? endforeach ;?>
				</ul>
		</div>
		<?php else: ?>
			<br />
			<input type="hidden" name="wpCreatePageChoices" value="format" />
		<?php endif; ?>
		<input id="hiddenCreatePageDialogButton" type="submit" style="display: none;" name="hiddenCreatePageDialogButton" value="<?= wfMsg("createpage-dialog-title") ?>" />
	</form>
</div>
