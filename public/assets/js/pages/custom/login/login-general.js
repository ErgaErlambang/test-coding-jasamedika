"use strict";

// Class Definition
var KTLogin = function () {
	var _login;

	var _handleSignInForm = function () {
		var validation;

		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validation = FormValidation.formValidation(
			KTUtil.getById('kt_login_signin_form'),
			{
				fields: {
					email: {
						validators: {
							notEmpty: {
								message: 'email is required'
							},
							emailAddress: {
								message: 'please input the valid email'
							}
						}
					},
					password: {
						validators: {
							notEmpty: {
								message: 'Password is required'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					submitButton: new FormValidation.plugins.SubmitButton(),
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		);

		$('#kt_login_signin_submit').on('click', function (e) {
			e.preventDefault();

			validation.validate().then(function (status) {
				if (status == 'Valid') {
					KTUtil.getById('kt_login_signin_form').submit();
				}
			});
		});
	}

	var _handleForgotForm = function (e) {
		var validation;

		// Init form validation rules. For more info check the FormValidation plugin's official documentation:https://formvalidation.io/
		validation = FormValidation.formValidation(
			KTUtil.getById('kt_login_forgot_form'),
			{
				fields: {
					email: {
						validators: {
							notEmpty: {
								message: 'Email address is required'
							},
							emailAddress: {
								message: 'The value is not a valid email address'
							}
						}
					}
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		);
	}

	// Public Functions
	return {
		// public functions
		init: function () {
			_login = $('#kt_login');

			_handleSignInForm();
			_handleForgotForm();
		}
	};
}();

// Class Initialization
jQuery(document).ready(function () {
	KTLogin.init();
	_handleSignUpForm();
});
