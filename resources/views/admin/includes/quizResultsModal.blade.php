<div class="modal fade" id="quizResultsModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="card">
                {{--<div class="card-header">
                    <div class="text-right cross"> <i data-dismiss="modal" class="fa fa-times cursor-pointer"></i> </div>
                </div>--}}
                <input type="hidden" name="quizAssignmentId" id="quizAssignmentId" value="0" />
                <div class="card-body text-center">
                    <img id="quizResultsModalImg" src="{{asset('public/assets/images/trophy.png')}}" alt="" style="width: 200px;" />
                    <h4 id="resultStatusMessage"></h4>
                    <p>You have scored <span id="resultPercentage"></span></p>
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-outline-primary" type="button" id="continueBtn" data-dismiss="modal" onclick="ResultContinue();" style="display: none;">Continue</button>
                    <button class="btn btn-outline-primary" type="button" id="againBtn" data-dismiss="modal" onclick="ResultTryAgain();" style="display: none;">Try Again</button>
                </div>
            </div>
        </div>
    </div>
</div>