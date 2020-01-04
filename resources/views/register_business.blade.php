@extends('layouts/default_login')
@section('title')
    Login
    @parent
@stop

@section('content')
    <div id="fb-root"></div>
    <script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v5.0&appId=452536115398962&autoLogAppEvents=1"></script>
    <div class = "login-wrapper">
        <div class = "text-center">
            <h2 class = "title">Create An Account</h2>
        </div>
        {{-- <div class = "text-center">
            <span class = "font-16">go to the account?<a class = "color-blue" href = "{{url("/register")}}">Create an Account</a></span>
        </div> --}}
        <div id="notific">
            @include('notifications')
        </div>
        <form action="{{ url('register_business') }}" class="omb_loginForm"  autocomplete="off" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class = "row mt-10">
            <div class = "col-lg-6 col-xs-12" style = "border-right: 1px solid #e2e2e2;">
                <div class = "row">
                    <div class = "col-lg-6">
                        <input name = "first_name"  class="form-control input-border-bottom" placeholder="First name"/>
                        {!! $errors->first('first_name', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class = "col-lg-6">
                        <input name = "last_name" class="form-control input-border-bottom" placeholder="Last name"/>
                        {!! $errors->first('last_name', '<span class="help-block">:message</span>') !!}
                    </div>
                </div>
                <div class="form-group mt-10">
                    <input name = "email" class="form-control input-border-bottom" placeholder="Email">
                    {!! $errors->first('email', '<span class="help-block">:message</span>') !!}
                </div>
                <div class = "row">
                    <div class = "col-lg-6">
                        <input name = "verify_code" class="form-control input-border-bottom" placeholder="Verify code"/>
                        {!! $errors->first('verify_code', '<span class="help-block">:message</span>') !!}
                    </div>
                    <div class = "col-lg-6">
                        <button type="button" onclick = "sendVerifyCode()" class="btn btn-primary btn-block btn-flat">send</button>
                    </div>
                </div>
                <div class="form-group mt-10" style = "position:relative;">
                    <input type = "password" name = "password"  class="form-control input-border-bottom" placeholder="Password">
                    {!! $errors->first('password', '<span class="help-block">:message</span>') !!}
                    <div class = "hidden" style = "position:absolute; right:0px; display:inline-block; top:8px;">
                        <input type = "checkbox"/>show
                    </div>
                </div>
                <div class="form-group mt-10" style = "position:relative;">
                    <input name = "bio"  class="form-control hidden input-border-bottom" value="111">
                </div>
                <div class="form-group mt-10" style = "position:relative;">
                    <input type = "password" name = "password_confirm"  class="form-control input-border-bottom" placeholder="Confirm Password">
                    {!! $errors->first('password_confirm', '<span class="help-block">:message</span>') !!}
                </div>
                <div class="form-group text-center">
                    <span>
                        By creating an account, you agree to our <a href = "#" class = "color-blue"> User Agreement </a> and acknowledge reading our <a href = "#" class = "color-blue"> User Privacy Notice </a>
                    </span>
                </div>
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Create account</button>
                </div>
            </div>
            <div class = "col-lg-6 col-xs-12 " style = "margin-top:100px;">
                <div class="form-group">
                <div class="fb-login-button" data-onlogin="checkLoginState()" data-width="250" data-size="large" data-button-type="continue_with" data-auto-logout-link="false" data-use-continue-as="false"></div>
                </div>
                <div class = "form-group">
                    <div id="gSignInWrapper">
                        <div id="customBtn" class="customGPlusSignIn">
                            <img src="{{url("/assets/img/btn-google.png")}}" style="width:35px;height:35px;"/>
                            <span class="buttonText">Continue with Google</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class = "row text-center">
            <span>Already a member ? <a href = "{{url("/login")}}">Sign in</a></span>
        </div>
        </form>
        <form action="{{ route('register_business') }}" method="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="form-group">
                <input name = "first_name" id="first_name" class="form-control input-border-bottom hidden">
            </div>
            <div class="form-group">
                <input name = "last_name" id="last_name" class="form-control input-border-bottom hidden">
            </div>
            <div class="form-group">
                <input type = "email" name = "email" id="email" class="form-control input-border-bottom hidden">
            </div>
            <div class="form-group">
                <input type = "password" name = "password" id="password" class="form-control input-border-bottom hidden">
            </div>
            <div class="form-group">
                <input type = "password"  name = "password_confirm" id="password_confirm" class="form-control input-border-bottom hidden">
            </div>
            <div class="form-group">
                <input name = "verify_code" id="verify_code" class="form-control input-border-bottom hidden" placeholder="Code"/>
            </div>
            <div class="form-group">
                <input name = "bio" id="bio" class="form-control input-border-bottom hidden" placeholder="Code"/>
            </div>
            <div class="form-group text-center">
                <button type="submit" id="google_submit" class="btn btn-primary btn-block btn-flat hidden">Create Account</button>
            </div>
        </form>
    </div>
    <script src="https://apis.google.com/js/api:client.js"></script>
    <script>
    //Google Signup Script
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
        function init() {
            gapi.load('auth2', function() {
                auth2 = gapi.auth2.init({
                    client_id: '83223052972-rpifd6rp89612k9k7i6vsrhaq95f6qv5.apps.googleusercontent.com',
                    cookiepolicy: 'single_host_origin',
                    scope: 'profile email'
                });
                element = document.getElementById('glogin');
                auth2.attachClickHandler(element, {}, onSignIn, onFailure);
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
                document.getElementById('first_name').value = profile.getGivenName();
                document.getElementById('last_name').value = profile.getFamilyName();
                document.getElementById('email').value = profile.getEmail();
                document.getElementById('password').value = '121';
                document.getElementById('password_confirm').value = '121';
                document.getElementById('verify_code').value = "1google";
                document.getElementById('bio').value = "111";
                document.getElementById('google_submit').click();
            }
            // The ID token you need to pass to your backend:
            var id_token = googleUser.getAuthResponse().id_token;
            console.log("ID Token: " + id_token);
        }
        function onFailure(error) {
            console.log(error);
        }

        //FaceBook SignUp Script
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
                        document.getElementById('first_name').value = response.name.split(' ')[0];
                        document.getElementById('last_name').value = response.name.split(' ')[1];
                        document.getElementById('password').value = '121';
                        document.getElementById('password_confirm').value = '121';
                        document.getElementById('bio').value = "111";
                        document.getElementById('verify_code').value = '1google';
                        document.getElementById('google_submit').click();
                    });
                } else {
                    console.log('User cancelled login or did not fully authorize.');
                }
            });
            });
        }

        
        function sendVerifyCode(){
            var param = new Object();
            param._token = _token;
            var email = $("input[name='email']").val();
            if(email == ""){
                errorMsg("please input email");
                return;
            }
            if(!checkEmail(email)){
                errorMsg("incorrect email format");
                return;
            }
            param.email = email;
            var url = "{{url("/send_email")}}";
            $.post(url, param, function(data){
                if(data.status == "1"){
                    successMsg("Verification Code Sent! Please Check out Junk mail, too.");
                    return;
                }
            }, "json");
        }
    </script>
@stop