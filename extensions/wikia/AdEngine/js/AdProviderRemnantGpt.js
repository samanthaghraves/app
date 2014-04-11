/*exported AdProviderRemnantGpt*/
var AdProviderRemnantGpt = function (log, slotTweaker, wikiaGpt, gptSlotConfig) {
	'use strict';

	var logGroup = 'AdProviderRemnantGpt',
		srcName = 'rh',
		slotMap = gptSlotConfig.getConfig(srcName);

	function canHandleSlot(slotname) {

		if (!slotMap[slotname]) {
			return false;
		}

		return true;
	}

	function fillInSlot(slotname, success) {

		log(['fillInSlot', slotname], 5, logGroup);

		wikiaGpt.pushAd(
			slotname,
			function () { // Success
				slotTweaker.removeDefaultHeight(slotname);
				slotTweaker.removeTopButtonIfNeeded(slotname);
				slotTweaker.adjustLeaderboardSize(slotname);

				success();
			},
			function () { // Hop
				log(slotname + ' was not filled by DART', 'info', logGroup);

				slotTweaker.hide(slotname);
				slotTweaker.hideSelfServeUrl(slotname);

				success();
			},
			srcName
		);

		wikiaGpt.flushAds();
	}

	return {
		name: 'RemnantGpt',
		canHandleSlot: canHandleSlot,
		fillInSlot: fillInSlot
	};
};