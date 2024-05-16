@extends('layouts.master')

@section('title', 'Login')

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
    <div class="login-signin">
        <div class="text-center mb-10 mb-lg-20">
            <h2 class="font-weight-bold">Sign In</h2>
            <p class="text-muted font-weight-bold">Enter your email and password</p>
            @include('layouts.alert')
        </div>
        <form action="{{ route('auth.signin') }}" method="POST" class="form text-left" id="kt_login_signin_form">
            @csrf
            <div class="form-group py-2 m-0">
                <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="email" placeholder="email" value="{{ old('email') }}" name="email" autocomplete="off"/>
            </div>
            <div class="form-group py-2 border-top m-0">
                <input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="Password" placeholder="Password" name="password" />
            </div>
            <div class="form-group d-flex flex-wrap justify-content-between align-items-center mt-5">
                <div class="checkbox-inline">
                    <label class="checkbox m-0 text-muted font-weight-bold">
                    <input type="checkbox" name="remember" />
                    <span></span>Remember me</label>
                </div>
            </div>
            <div class="text-center mt-15">
                <button id="kt_login_signin_submit" class="btn btn-primary btn-pill shadow-sm py-4 px-9 font-weight-bold">Sign In</button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        var validation;
        var form = document.getElementById("kt_login_signin_form");
        validation = FormValidation.formValidation(
            form,
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
                    form.submit();
                }
            });
        });
    </script>
@endpush
