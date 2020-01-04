

@component('mail::layout')
{{-- Header --}}
@slot('header')
    @component('mail::header', ['url' => "https://megabuy.online"])
       MegaBuy
    @endcomponent
@endslot

{{-- Body --}}
# Hello

A MegaBuy User sent an email.<br />
**Name :** {{ $data['user']['first_name'].' '.$data['user']['last_name'] }}<br />
**Email :** {{ $data['user']['email'] }}<br />
**Title :** {{ $data['title']}} <br/>
**Message :** {{ $data['msg'] }}

Thanks,

{{-- Footer --}}

<img src="{{asset('assets/img/logo.png')}}" href="https://megabuy.online" style="width:50px;"/>
@slot('footer')
    @component('mail::footer')
       &copy; 2019 All CopyRights Reserved
    @endcomponent
@endslot
@endcomponent
