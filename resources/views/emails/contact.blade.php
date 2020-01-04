

@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
           Josh Admin
        @endcomponent
    @endslot

    {{-- Body --}}
# Hello

We have received a new contact mail.<br />
**Name :** {{ $data->contact_name }}<br />
**Email :** {{ $data->contact_email }}<br />
**Message :** {{ $data->contact_msg }}


Thanks,

    {{-- Footer --}}
    <img src="{{asset('assets/img/logo.png')}}" href="https://megabuy.online" style="width:50px;"/>
    @slot('footer')
        @component('mail::footer')
           &copy; 2017 All Copy right received
        @endcomponent
    @endslot
@endcomponent
