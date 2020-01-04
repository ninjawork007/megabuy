@if(isset($info))
<div class="form-group">
    <label class="col-md-3 control-label" for="name">Text</label>
    <div class="col-md-9">
        <input name="show_text"  placeholder="Show Text" class="form-control" value = "{{$info['category_text']}}"></div>
</div>
<div class = "form-group">
    <label class="col-md-3 control-label" for="name">Image</label>
    <div class="col-md-9">
        <button type="button" class="btn btn-responsive btn-sm" onclick = "onClickFilgDlg('#logImg');">File</button>
    </div>
</div>
<div class = "form-group">
    <div class = "col-md-9 col-md-offset-3">
        <img id = "logImg_img" class = "logImg" style = "width:80px; height:80px;" @if($info['img'] != '') src = "{{correctImgPath1($info['img'])}}" @endif/>
        <input type = "hidden" id = "logImg_val"  @if($info['img'] != '')  value = "{{correctImgPath1($info['img'])}}" @endif/>
        <div class = "@if($info['img'] == '') hidden @endif delBtnWrapper" style = "position:absolute; width:30px; left:100px;height: 30px; bottom:0" onclick = "delImage(this)">
            <i class = "fa fa-close cursor" ></i>
        </div>
    </div>
</div>
@endif