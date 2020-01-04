@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
           MegaBuy
        @endcomponent
    @endslot

    {{-- Body --}}
# Hello

This is the verification code for you.<br />
**Code :** {{ $code }}<br />
 


Thanks For Your Registration!

{{-- <img src="{{asset($data['logo'])}}" href="https://megabuy.online" style="width:50px;"/> --}}
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
           &copy; 2019 All CopyRights Reserved
        @endcomponent
    @endslot
@endcomponent