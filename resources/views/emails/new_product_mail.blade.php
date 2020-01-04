@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => 'https://megabuy.online'])
           MegaBuy
        @endcomponent
    @endslot

    {{-- Body --}}

#{{$data['title']}}: New Matches Today
<br/>
@component('mail::button', ['url' => $data['url'] , 'color'=>'green'])
View All Results
@endcomponent
<br/><br/><br/>
<img src="{{asset($data['logo'])}}" href="https://megabuy.online" style="width:50px;"/><br/>
    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
           &copy; 2019 All CopyRights Reserved
        @endcomponent
    @endslot
@endcomponent
