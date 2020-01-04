<table class="table table-bordered table-hover table-last-bottom">
    <thead>
    <tr>
        <th>#</th>
        <th>Image </th>
        <th>Text</th>
        <th width="200px;"></th>
    </tr>
    </thead>
    <tbody >
    @foreach($childList as $key=>$item)
        <tr>
            <td>{{$key+1}}</td>
            <td>
                <img src = "{{correctImgPath($item['img'])}}" style = "width:80px; border-radius: 50%; height:80px;" onerror = "noExitImg(this)"/>
            </td>
            <td>{{$item['category_text']}}</td>
            <td>
                <a href="javascript:void(0);" onclick = "editItem('{{$item['id']}}')" class="btn default btn-xs purple">
                    <i class="livicon" data-name="pen" data-loop="true" data-color="#000" data-hovercolor="black" data-size="14"></i>
                    Edit
                </a>
                <a href="javascript:void(0);" onclick = "deleteItem('{{$item['id']}}')" class="btn default btn-xs black">
                    <i class="livicon" data-name="trash" data-loop="true" data-color="#000" data-hovercolor="black" data-size="14"></i>
                    Delete
                </a>
            </td>
            </td>
        </tr>
    @endforeach
    @if(count($childList) == 0)
        <tr>
            <td colspan = "9">There is not data</td>
        </tr>
    @endif

    </tbody>
</table>