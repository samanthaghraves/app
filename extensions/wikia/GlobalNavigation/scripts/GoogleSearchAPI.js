(function () {
	if ( true ) {
		var searchForm = document.getElementById('searchForm'),
			searchContainer = document.getElementsByClassName('search-form-wrapper'),
            cx = '005745855109319432328:coaj7jf_wgs',//TODO: Hide it
			gcse = document.createElement('script'),
			s = document.getElementsByTagName('script')[0];

		gcse.type = 'text/javascript';
		gcse.async = true;
		gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
				   '//cse.google.com/cse.js?cx=' + cx;
		s.parentNode.insertBefore(gcse, s);

		searchForm.addEventListener('submit', function (evt) {
			evt.preventDefault();
			this.searchQuery = $('#searchInput').val();

			//get info of local or global and modify query if it is local search
			$selectElement = $('#searchSelect');
			$selectedOption = $selectElement.find('option:selected');
			if ($selectedOption.val() === 'local') {
				this.searchQuery += ' site:' + window.location.hostname;
			}

			//Invoke google search
			this.googleInput = $('.gsc-input');
			this.googleInput.val(this.searchQuery);
			this.googleButton = $('.gsc-search-button');
			this.googleButton.trigger('click');
		});
	}
})();
