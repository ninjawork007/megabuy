@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://megabuy.online'])
            MegaBuy
        @endcomponent
    @endslot

    {{-- Body --}}
# Hello  {!! $user->user_name !!},<br>

Please click on the following link to updated your password
@component('mail::button', ['url' =>  $user->forgotPasswordUrl ])
    Reset Password
@endcomponent


    Thanks,

    {{-- Footer --}}
    <img src="{{asset($data['logo'])}}" href="https://megabuy.online" style="width:50px;"/>
    @slot('footer')
    @component('mail::footer')
    &copy; 2019 All CopyRights Reserved
@endcomponent
@endslot
@endcomponent