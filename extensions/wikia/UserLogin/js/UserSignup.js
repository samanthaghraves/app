/* global WikiaForm, UserSignupAjaxValidation, UserSignupMixin */
(function () {
	'use strict';

	/**
	 * JS for signing up with a new account, both on mobile and desktop
	 */
	var UserSignup = {
		inputsToValidate: ['userloginext01', 'email', 'userloginext02', 'birthday'],
		notEmptyFields: ['userloginext01', 'email', 'userloginext02', 'birthday', 'birthmonth', 'birthyear'],
		invalidInputs: {},
		useCaptcha: !window.wgUserLoginDisableCaptcha,

		/**
		 * Enable user signup form with ajax validation
		 */
		init: function () {
			this.wikiaForm = new WikiaForm('#WikiaSignupForm');
			this.submitButton = this.wikiaForm.inputs.submit;
			this.loadCaptcha();

			// imported via UserSignupMixin
			this.setCountryValue(this.wikiaForm);
			this.initOptIn(this.wikiaForm);
		},

		loadCaptcha: function () {
			$.loadReCaptcha()
				.done(function () {
					this.setupValidation();
				}.bind(this))
				.fail(this.handleCaptchaLoadError);

		},

		/**
		 * Captcha is required for signup, so if it fails to load (possibly b/c google is blocked in China)
		 * disable the form fields and inform the user. Note, this is different from when a user
		 * fails the captcha test itself.
		 */
		handleCaptchaLoadError: function () {
			this.wikiaForm.disableAll();

			function createModal(uiModal) {
				var modalConfig = {
					vars: {
						id: 'catchaLoadErrorModal',
						classes: ['captcha-load-error-modal'],
						size: 'medium',
						title: $.msg('usersignup-page-captcha-load-fail-title'),
						content: $.msg('usersignup-page-captcha-load-fail-text')
					}
				};

				uiModal.createComponent(modalConfig, function (captchaErrorModal) {
					captchaErrorModal.show();
				});
			}

			require(['wikia.ui.factory'], function (uiFactory) {
				$.when(uiFactory.init('modal'))
					.then(createModal);
			});

			Wikia.Tracker.track({
				action: Wikia.Tracker.ACTIONS.ERROR,
				category: 'user-sign-up',
				label: 'captcha-load-fail',
				trackingMethod: 'both',
				country: Wikia.geo.getCountryCode()
			});
		},

		/**
		 * Applying ajax validation to the form fields that have been cached via WikiaForm
		 */
		setupValidation: function () {
			var inputs = this.wikiaForm.inputs;

			this.validator = new UserSignupAjaxValidation({
				wikiaForm: this.wikiaForm,
				inputsToValidate: this.inputsToValidate,
				submitButton: this.submitButton,
				notEmptyFields: this.notEmptyFields,
				useCaptcha: this.useCaptcha
			});

			inputs.userloginext01
				.add(inputs.email)
				.add(inputs.userloginext02)
				.on('blur.UserSignup', this.validator.validateInput.bind(this.validator));

			inputs.birthday
				.add(inputs.birthmonth)
				.add(inputs.birthyear)
				.on('change.UserSignup', this.validator.validateBirthdate.bind(this.validator));

			if (
				window.wgUserLoginDisableCaptcha !== true &&
				inputs['g-recaptcha-response']
			) {
				inputs['g-recaptcha-response']
					.on('keyup.UserSignup', this.validator.activateSubmit.bind(this.validator));
			}
		}
	};

	// Add common user signup mixin functions for use in this class
	UserSignupMixin.call(UserSignup);

	// expose global
	window.UserSignup = UserSignup;

	$(function () {
		UserSignup.init();
	});
})();
