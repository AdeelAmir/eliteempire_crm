<div class="modal fade" id="editFaqModal" tabindex="200" role="dialog" aria-labelledby="editFaqModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <input type="hidden" name="id" id="editFaqId" value="0" />
            <div class="modal-header">
                <h5 class="modal-title" id="editFaqModalLabel">Knowledge Zone</h5>
            </div>
            <div class="modal-body" style="max-height: 400px; overflow-y:auto;">
                <div class="form-group">
                    <label for="question">Question</label>
                    <input type="text" name="question" id="question1" class="form-control" required />
                </div>
                <div class="form-group">
                    <label for="faqAnswer1">Answer</label>
                    <textarea name="answer" id="faqAnswer1" rows="5"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
