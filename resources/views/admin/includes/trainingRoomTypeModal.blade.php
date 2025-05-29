<div class="modal fade" id="trainingRoomTypeModal" tabindex="200" role="dialog" aria-labelledby="trainingRoomTypeModalLabel"
     aria-hidden="true" style="margin-top: 10%;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="trainingRoomTypeModalLabel">Training Type</h5>
            </div>
            <div class="modal-body">
                <div class="form-group text-center">
                    <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_video_btn" id="add_video_btn"
                    onclick="window.location.href='{{url('admin/training-room/video/add/' . $FolderId . '/' . $RoleId)}}';" style="width: 70%;">Video</button>
                    <br>
                    <br>
                    <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_article_btn" id="add_article_btn"
                    onclick="window.location.href='{{url('admin/training-room/article/add/' . $FolderId . '/' . $RoleId)}}';" style="width: 70%;">Article</button>
                    <br>
                    <br>
                    <button type="button" class="btn btn-light btn-lg pt-3 pb-2" name="add_quiz_btn" id="add_quiz_btn"
                    onclick="window.location.href='{{url('admin/training-room/quiz/add/' . $FolderId . '/' . $RoleId)}}';" style="width: 70%;">Quiz</button>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
