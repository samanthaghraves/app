/*!
 * VisualEditor tracking for Wikia
 */

/* global mw, require */

require( ['wikia.tracker'], function ( tracker ) {
	var actions = tracker.ACTIONS,
		// These are topics used by MediaWiki, consider them reserved. Each topic should be
		// assigned to a function which will map the data associated with a topic to a format
		// understood by Wikia.Tracker.
		mwTopics = {
			'behavior.lastTransactionTillSaveDialogOpen': function ( data ) {
				return {
					'action': actions.OPEN,
					'label': 'dialog-save',
					'value': data.duration
				};
			},
			'behavior.saveDialogClose': function ( data ) {
				return {
					'action': actions.CLOSE,
					'label': 'dialog-save',
					'value': data.duration
				};
			},
			'command.execute': function ( data ) {
				return {
					'action': actions.KEYPRESS,
					'label': 'tool-' + nameToLabel( data.name )
				};
			},
			'Edit': function ( data ) {
				var params = {
					'action': actions.CLICK,
					'category': 'article'
				};

				switch( data.action ) {
					case 'edit-link-click':
						params.label = 've-edit';
						break;
					case 'section-edit-link-click':
						params.label = 've-section-edit';
						break;
				}

				return params;
			},
			'performance.system.activation': function ( data ) {
				return {
					'action': actions.IMPRESSION,
					'label': 'edit-page-ready',
					'value': Math.round( data.duration )
				};
			},
			'performance.user.reviewComplete': function ( data ) {
				return {
					'action': actions.SUCCESS,
					'label': 'dialog-save-review-changes',
					'value': data.duration
				};
			},
			'performance.user.reviewError': function ( data ) {
				return {
					'action': actions.ERROR,
					'label': 'dialog-save-review-changes',
					'value': data.duration
				};
			},
			'performance.user.saveComplete': function ( data ) {
				return {
					'action': actions.SUCCESS,
					'label': 'publish',
					'value': data.duration
				};
			},
			'performance.user.saveError': function ( data, topics ) {
				if ( !data.type ) {
					data.type = topics[ topics.length - 1 ];
				}
				return {
					'action': actions.ERROR,
					'label': 'publish-' + data.type,
					'retries': data.retries,
					'value': data.duration
				};
			},
			'tool': function ( data ) {
				return {
					'action': actions.CLICK,
					'label': 'tool-' + nameToLabel( data.name ),
					'value': data.toolbar === 'surface' ? 1 : 0
				};
			}
		},
		// @see {@link nameToLabel} for more information
		nameToLabelMap = {
			'meta': 'page-settings',
			'mwSave': 'save',
			'transclusion': 'template',
			'wikiaMediaInsert': 'media-insert',
			'wikiaSourceMode': 'source'
		},
		// A lot of the events sent in the 'wikia' topic are tracked generically (for example,
		// all dialog 'open' and 'close' events). Sometimes this isn't desired because we want
		// to track them manually and provide custom data. Adding those events to this array
		// will allow this. Events should be listed alphabetically in the format: "label/action"
		wikiaTopicBlacklist = [
			'dialog-save/close',
			'dialog-save/open'
		],
		rUppercase = /[A-Z]/g;

	/**
	 * Convert symbolic names to tracking labels, falling back to the symbolic name if there is
	 * nothing else to map it to.
	 *
	 * @example
	 * "meta" -> "page-settings"
	 *
	 * @method
	 * @param {string} name The symbolic name
	 * @returns {string} The converted label
	 */
	function nameToLabel( name ) {
		return nameToLabelMap[name] || name;
	}

	/**
	 * Editor tracking function.
	 *
	 * @method
	 * @param {string} topic Event sub-category (like "tool", "button", etc.); this will be joined
	 * with data.label to form the whole label for the event.
	 * @param {Object} data The data to send to our internal tracking function.
	 */
	function track( topic, data ) {
		var i, mwEvent, topics,
			params = {
				category: 'editor-ve',
				trackingMethod: 'both'
			};

		// MW events
		if ( topic !== 'wikia' ) {
			// MW events are categorized in dot notation (like "performance.system.activation")
			// This allows us to follow an entire topic ("performance") without having to track
			// all the sub-topics separately.
			topics = topic.split( '.' );
			for ( i = 0; i < topics.length; i++ ) {
				topic = ( i === 0 ? '' : topic + '.' ) + topics[i];
				mwEvent = mwTopics[topic];
				if ( mwEvent ) {
					// We have found a match; exit the loop
					break;
				}
			}
			// Only track things we care about
			if ( !mwEvent ) {
				return;
			}
			data = $.isFunction( mwEvent ) ? mwEvent( data, topics ) : mwEvent;
		}

		// Normalize tracking labels
		data.label = data.label.replace( rUppercase, upperToHyphenLower );

		// Don't track blacklisted Wikia tracking calls.
		if (
			topic === 'wikia' &&
			$.inArray( [ data.label, data.action ].join( '/' ), wikiaTopicBlacklist ) > -1
		) {
			return;
		}

		// Send off to Wikia.Tracker
		tracker.track( ve.extendObject( params, data ) );
	}

	/**
	 * Convert a string from lazyCamelCase to lowercase-hyphen style.
	 *
	 * @method
	 * @param {string} match The matched string to convert.
	 * @returns {string} The converted string.
	 */
	function upperToHyphenLower( match ) {
		return '-' + match.toLowerCase();
	}

	// Track time to init when accessing directly from the URI via ?veaction=edit
	if ( mw.libs.ve.activateOnPageLoad ) {
		mw.hook( 've.activationComplete' ).add( function () {
			ve.track( 'wikia' , {
				'action': actions.IMPRESSION,
				'label': 'edit-page-ready-from-page-load',
				'value': Math.round( ve.now() - window.wgNow )
			} );
		} );
	}

	// Exports
	ve.track.actions = actions;
	ve.track.nameToLabel = nameToLabel;
	ve.trackSubscribeAll( track );
} );
