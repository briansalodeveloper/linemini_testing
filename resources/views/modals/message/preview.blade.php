@push('modals')
    <div class="modal fade" id="contentPreview">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('words.SendingConfirmation') }}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span class="sub-title">{{ __('messages.custom.weWillSendWithFollowingContents') }}</span>
                    <span class="content-title">{{ __('words.PreviewScreen') }}</span>
                    <div class="messages">
                        <div class="message">
                            <div class="botIcon">
                                <img class="" src="{{ _vers('images/message/botIcon.png') }}">
                            </div>
                            <div class="img">
                                <img class="" role-name="thumbnail">
                            </div>
                            <div class="time-box"><div class="time" role-name="time">10:37 AM</div></div>
                        </div>
                        <div class="message">
                            <div class="botIcon">
                                <img class="" src="{{ _vers('images/message/botIcon.png') }}">
                            </div>
                            <div class="content">
                                <div class="text" role-name="contents"></div>
                            </div>
                            <div class="time-box"><div class="time" role-name="time">10:37 AM</div></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-02 col-md-3 col-sm-4 px-1" data-dismiss="modal" aria-label="Close">{{ __('words.Cancel') }}</button>
                    @if($data->getAttr('isStatusNotSend', true))
                        <button type="button" id="saveAndSendMessage" class="btn btn-success col-md-3 col-sm-4 ml-5">{{ __('words.Send') }}</button>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endpush