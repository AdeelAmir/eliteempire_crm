<div class="modal fade" id="leadSentModal" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="card">
                <div class="card-body text-center">
                    <img id="leadSentModalImg" src="{{asset('public/storage/logo/checked.png')}}" alt="" style="width: 100px;" />
                    <h2 class="text-center mt-2">Awesome!</h2>
                    <p class="mt-2 mb-0">
                        Your lead has been sent.
                    </p>
                    <p class="text-center mt-2 mb-5">
                        @if(session()->has('leadStore'))
                            #<b>{{session()->get('leadStore')}}</b>
                        @endif
                    </p>
                    @if($Role == 1)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('admin/leads')}}';">OK</button>
                    @elseif($Role == 2)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('global_manager/leads')}}';">OK</button>
                    @elseif($Role == 3)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('acquisition_manager/leads')}}';">OK</button>
                    @elseif($Role == 4)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('disposition_manager/leads')}}';">OK</button>
                    @elseif($Role == 5)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('acquisition_representative/leads')}}';">OK</button>
                    @elseif($Role == 6)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('disposition_representative/leads')}}';">OK</button>
                    @elseif($Role == 7)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('cold_caller/leads')}}';">OK</button>
                    @elseif($Role == 8)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('affiliate/leads')}}';">OK</button>
                    @elseif($Role == 9)
                      <button class="btn btn-primary w-100" type="button" onclick="window.location.href='{{url('realtor/leads')}}';">OK</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
