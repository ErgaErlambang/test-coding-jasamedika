@extends('layouts.master')

@section('title', 'Register')

@push('styles')
<link href="{{ asset("assets/css/pages/login/classic/login-6.css?v=7.0.5") }}" rel="stylesheet" type="text/css" />
<style>
.image-input .image-input-wrapper {
    width: 240px;
    height: 212px;
    border-radius: 0.42rem;
    background-repeat: no-repeat;
    background-size: cover;
}
</style>
@endpush

@section('auth-content')
<div class="login-signup-on">
    <div class="text-center mb-10 mb-lg-20">
        <h3 class="">Sign Up</h3>
        <p class="text-muted font-weight-bold">Enter your details to create your account</p>
        @include('layouts.alert')
    </div>
    <form action="{{ route('auth.signup') }}" method="POST" class="form text-left" id="kt_login_signup_form">
        @csrf
        <div class="form-group py-2 m-0">
            <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="text" placeholder="Fullname" name="fullname" value="{{ old('fullname') }}"/>
        </div>
        <div class="form-group py-2 m-0 border-top">
            <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="text" placeholder="Email" name="email" autocomplete="off" value="{{ old('email') }}"/>
        </div>
        <div class="form-group py-2 m-0 border-top">
            <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="text" placeholder="Phone Number" name="phone" autocomplete="off" value="{{ old('phone') }}"/>
        </div>
        <div class="form-group py-2 m-0 border-top">
            <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="text" placeholder="Driver License Number (SIM)" name="sim" autocomplete="off" value="{{ old('sim') }}"/>
        </div>
        <div class="form-group py-2 m-0 border-top">
            <textarea class="form-control h-auto border-0 px-0 placeholder-dark-75" placeholder="Address" name="address" autocomplete="off">{{ old('address') }}</textarea>
        </div>
        <div class="form-group py-2 m-0 border-top">
            <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="password" placeholder="Password" name="password" />
        </div>
        <div class="form-group py-2 m-0 border-top">
            <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="password" placeholder="Confirm Password" name="cpassword" />
        </div>
        <div class="form-group mt-5">
            <div class="checkbox-inline">
                <label class="checkbox checkbox-outline font-weight-bold">
                <input type="checkbox" name="agree" />
                <span></span>I Agree the
                <a href="#" class="ml-1">terms and conditions</a>.</label>
            </div>
        </div>
        <div class="form-group d-flex flex-wrap flex-center">
            <button id="kt_login_signup_submit" class="btn btn-primary btn-pill font-weight-bold px-9 py-4 my-3 mx-2">Submit</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
    <script>
		var validation;
		var form = document.getElementById("kt_login_signup_form");

		validation = FormValidation.formValidation(
			form,
			{
				fields: {
					fullname: {
						validators: {
							notEmpty: {
								message: 'User name is required'
							}
						}
					},
					email: {
						validators: {
							notEmpty: {
								message: 'Email address is required'
							},
							emailAddress: {
								message: 'The value is not a valid email address'
							}
						}
					},
                    phone: {
                        validators: {
                            notEmpty: {
                                message: 'Phone number is required'
                            },
                            digits: {
                                message: 'Input only numbers'
                            }
                        }
                    },
                    sim: {
                        validators: {
                            notEmpty: {
                                message: 'Driver`s License Number is required'
                            },
                            stringLength: {
                                min: 14,
                                max: 14,
                                message: 'Driver`s License Number has to be 14 characters long',
                            },
                            digits: {
                                message: 'Input only numbers'
                            }
                        }
                    },
                    address: {
                        validators: {
                            notEmpty: {
                                message: 'Address is required'
                            }
                        }
                    },
					password: {
						validators: {
							notEmpty: {
								message: 'The password is required'
							}
						}
					},
					cpassword: {
						validators: {
							notEmpty: {
								message: 'The password confirmation is required'
							},
							identical: {
								compare: function () {
									return form.querySelector('[name="password"]').value;
								},
								message: 'The password and its confirm are not the same'
							}
						}
					},
					agree: {
						validators: {
							notEmpty: {
								message: 'You must accept the terms and conditions'
							}
						}
					},
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
                    submitButton: new FormValidation.plugins.SubmitButton(),
					bootstrap: new FormValidation.plugins.Bootstrap()
				}
			}
		);

        $('#kt_login_signup_submit').on('click', function (e) {

            e.preventDefault();

            validation.validate().then(function (status) {
                if (status == 'Valid') {
                    form.submit();
                }
            });
        });
    </script>
@endpush