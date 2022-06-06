<div class="edit-menu-container btn-group-vertical">
    @if(!empty($data->getAttr('id', false)))
        <button class="btn btn-02 mb-2" id="duplicateBtn">{{ __('words.DuplicateThisProject') }}</button>
        @if(!in_array($page, ['message']) || ($page == 'message' && $data->getAttr('isStatusNotSend', true)))
            <button class="btn btn-02 mb-2" id="deleteBtn">{{ __('words.DeleteThisProject') }}</button>
            <button class="btn btn-02 mb-2" id="undoEdit">{{ __('words.UndoEditing') }}</button>
        @endif
    @else
        <button class="btn btn-02" id="clearBtn">{{ __('words.ClearInputItems') }}</button>
    @endif
</div>