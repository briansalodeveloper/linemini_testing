<div class="modal fade" id="contentPreview">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('words.Preview') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header p-0 ">
                        <img class="card-img-top rounded-top h-100" role-name="opening-image">
                    </div>
                    <div class="card-body">
                        <div class="clearfix">
                            <h6 role-name="opening-letter" class="preview-content-title"></h6>
                            <span role-name="start-datetime" class="start-datetime clearfix float-right"></span>
                        </div>
                        <hr>
                        <div class="mt-2 content" role-name="contents"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
