<div class="modal fade" id="email_modal" tabindex="-1" role="dialog" aria-labelledby="addDlg1" aria-hidden="true" >
    <div class="modal-dialog modal-lg " role="document" style = "width:700px;">
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Email Form</h4>
            </div>
            <form action="{{url('/sendemail')}}" method="POST" enctype="multipart/form-data">
                <div class="modal-body" style = "padding-top:20px; padding-bottom:20px;">
                    <div class="form-group">
                        <label  class="bold font-15" for="">To: Admin(info@megabuy.online)</label>
                    </div>
                    <div class="form-group">
                        <label for="Title" class="bold font-16">Title</label>
                            <input type="text" name="title" placeholder="Title" class="form-control" style="width:99%;">
                    </div>
                    <div class="form-group">
                        <input type = "hidden" name = "_token" value = "{{csrf_token()}}"/>
                        <label for="emailText" class="bold font-16">Message</label>
                        <textarea name="emailText" placeholder="Input Text Here." id="emailMsg" style="margin-bottom:10px;" id="" cols="100" rows="10"></textarea><br/>
                    </div>
                    {{-- <div class="form-group">
                        <input type="file" name="attachment">
                    </div> --}}
                </div>
                <div class="modal-footer">
                    <div class="form-group">
                        <button type="submit" class="btn btn-success mb-0" style="margin-bottom:0px;">Send</button>
                        <button class="btn btn-primary" style="margin-right:8px;" data-dismiss="modal">Cancel</button>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</div>