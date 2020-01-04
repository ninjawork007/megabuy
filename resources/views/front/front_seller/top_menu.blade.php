<ul class="nav nav-tabs">
    <li class="@if($top_menu == 'activity') active @endif">
        <a href="{{url("front/my/seller/activity")}}">Activty</a>
    </li>
    <li class="@if($top_menu == 'messages') active @endif">
        <a href="{{url("front/my/messages")}}">
            Messages
            @if (isset($new_message))
                ({{$new_message}})
            @endif
        </a>
    </li>
    <li class="@if($top_menu == 'account') active @endif">
        <a href="{{url("front/my/account")}}" >Account</a>
    </li>
    <span class="font-10 pull-right mt-20 color-blue hidden">mala6263 (0)</span>
</ul>