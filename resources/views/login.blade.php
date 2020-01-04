@extends('layouts/default_login')
@section('title')
    Login
    @parent
@stop
<style>
.log-img{
        width: 112px;
        position: absolute;
        top: 2px;
        left: -8px;
    }
</style>
@section('content')
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v5.0&appId=452536115398962&autoLogAppEvents=1"></script>
    <div class = "login-wrapper" style = "position:relative;">
        <img class = "log-img" src  = "{{url("assets/img/logo.png")}}" />
        <div class = "text-center">
            <h2 class = "title">Login</h2>
        </div>
        <div class = "text-center">
            <span class = "font-16">Sign in to<a class = "color-blue" href = "{{url("/")}}"> MegaBuy</a> or <a class = "color-blue" href = "{{url("/register_business")}}">Create an Account</a></span>
        </div>
        <div id="notific">
            @include('notifications')
        </div>
        <form action="{{ route('login') }}" class="omb_loginForm"  autocomplete="off" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class = "row mt-10">
                <div class = "col-lg-6 col-xs-12" style = "border-right: 1px solid #e2e2e2;">
                    <div class="form-group">
                        <label for="defaultconfig" class="control-label">
                            Email or username
                        </label>
                        <input type = "email" name = "email" class="form-control input-border-bottom" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <label for="defaultconfig" class="control-label">
                            password
                        </label>
                        <input type = "password"  name = "password" class="form-control input-border-bottom" placeholder="Password">

                    </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                        <a href="{{url('forgotpwd')}}" class="color-blue">Forgot Password?</a>
                    </div>
                    <div class="form-group text-center hidden">
                        <a href = "#" class = "color-blue">Text a temporary password</a>
                    </div>
                    <div class="form-group text-center hidden">
                        <a href = "#" class = "color-blue">reset password</a>
                    </div>
                </div>
                <div class = "col-lg-6 col-xs-12 " style = "margin-top:100px;">
                    <div class="form-group">
                        <!-- <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in using
                            Facebook</a> -->
                        <div class="fb-login-button" data-onlogin="checkLoginState()" data-width="250" data-size="large" data-button-type="login_with" data-auto-logout-link="false" data-use-continue-as="false"></div>
                        <!-- <a id="facebook_login" href="">
                            <img src="{{asset('assets/img/facebook-login.png')}}" style="width:220px;height:50px;"/>
                        </a> -->
                    </div>
                    <div class = "form-group">
                        <!-- <a href="#" class="btn btn-block btn-social btn-google btn-flat"><i class="fa fa-google-plus"></i> Sign in using
                            Google+</a> -->
                        <div id="gSignInWrapper">
                            <div id="customBtn" class="customGPlusSignIn">
                                <img src="{{url("/assets/img/btn-google.png")}}" style="width:35px;height:35px;"/>
                                <span class="buttonText">Sign In with Google</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <form action="{{ route('login') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <input type = "email" name = "email" id="email" class="form-control input-border-bottom hidden">
            </div>
            <div class="form-group">
                <input type = "password" name = "password" id="password" class="form-control input-border-bottom hidden">
            </div>
            <div class="form-group text-center">
                <button type="submit" id="google_submit" class="btn btn-primary btn-block btn-flat hidden">Create account</button>
            </div>
        </form>
        <div class = "row text-center hidden">
            <input type = "checkbox" style = "width:20px;"/> Stay signed if
        </div>
        <div class = "row text-center hidden">
            <span>Using a public or shared device?</span>
        </div>
        <div class = "row text-center hidden">
            <span>Uncheck to protect your account. <a href = "#">Learn more</a></span>
        </div>
    </div>
    <script src="https://apis.google.com/js/api:client.js"></script>
    <script>
    // Google Login Script
        var googleUser = {};
        var startApp = function() {
            gapi.load('auth2', function(){
            // Retrieve the singleton for the GoogleAuth library and set up the client.
            auth2 = gapi.auth2.init({
                client_id: '83223052972-rpifd6rp89612k9k7i6vsrhaq95f6qv5.apps.googleusercontent.com',
                cookiepolicy: 'single_host_origin',
                // Request scopes in addition to 'profile' and 'email'
                scope: 'profile email'
            });
            attachSignin(document.getElementById('customBtn'));
            });
        };
        startApp();
        function attachSignin(element) {
            console.log(element.id);
            auth2.attachClickHandler(element, {},
                onSignIn, function(error) {
                alert(JSON.stringify(error, undefined, 2));
                });
        }

        function onSignIn(googleUser) {
            // Useful data for your client-side scripts:
            var profile = googleUser.getBasicProfile();
            console.log("ID: " + profile.getId()); // Don't send this directly to your server!
            console.log('Full Name: ' + profile.getName());
            console.log('Given Name: ' + profile.getGivenName());
            console.log('Family Name: ' + profile.getFamilyName());
            console.log("Image URL: " + profile.getImageUrl());
            console.log("Email: " + profile.getEmail());
            if(profile.getId()){
                console.log('logged in');
                document.getElementById('email').value = profile.getEmail();
                document.getElementById('password').value = '121';
                document.getElementById('google_submit').click();
            }
            // The ID token you need to pass to your backend:
            var id_token = googleUser.getAuthResponse().id_token;
            console.log("ID Token: " + id_token);
        }
        function onFailure(error) {
            console.log(error);
        }

        //Facebook Login Script
        function checkLoginState() {
            FB.getLoginStatus(function(response) {
                console.log(response);
                FB.login(function(response) {
                if (response.authResponse) {
                    console.log('Welcome!  Fetching your information.... ');
                    FB.api('/me', function(response) {
                        console.log(response);
                        console.log('Good to see you, ' + response.name + '.');
                        document.getElementById('email').value = response.id + "@facebook.com";
                        document.getElementById('password').value = '121';
                        document.getElementById('google_submit').click();
                    });
                } else {
                    console.log('User cancelled login or did not fully authorize.');
                }
            });
            });
        }
        
        
    </script>
@stop