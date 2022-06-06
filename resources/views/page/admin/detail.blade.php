@extends('layouts.app')

@section('bodyClass', 'pg-top')

@section('content')
    <div class="d-flex justify-content-between m-4">
        <h3><i class="fas fa-user"></i> {{__('words.UserManagement') }}</h3>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card card-seemless">
                            <div class="card-body table-responsive">
                                <form method="POST" action="{{ route('admin.update', $admin->id) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label>{{ __('words.Name') }}:</label>
                                        <span>{{ $admin->name }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('words.Email') }}:</label>
                                        <span>{{ $admin->email }}</span>
                                    </div>
                                    <div class="form-group">
                                        <label>{{ __('words.RegisteredDate') }}:</label>
                                        <span>{{ $admin->created_at->format('Y年m月d日') }}</span>
                                    </div>
                                    <div class="row justify-content-center mb-3">
                                        <button id="submit" type="submit" class="btn btn-secondary col-4 w-100">{{ __('words.Edit') }}</button>
                                    </div>
                                    <ul class="nav nav-tabs" id="custom-content-below-tab" role="tablist">
                                        <li class="nav-item">
                                        <a class="nav-link" id="custom-content-below-home-tab" data-toggle="pill" href="#custom-content-below-home" role="tab" aria-controls="custom-content-below-home" aria-selected="false">Home</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="custom-content-below-profile-tab" data-toggle="pill" href="#custom-content-below-profile" role="tab" aria-controls="custom-content-below-profile" aria-selected="false">Profile</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link active" id="custom-content-below-messages-tab" data-toggle="pill" href="#custom-content-below-messages" role="tab" aria-controls="custom-content-below-messages" aria-selected="true">Messages</a>
                                        </li>
                                        <li class="nav-item">
                                        <a class="nav-link" id="custom-content-below-settings-tab" data-toggle="pill" href="#custom-content-below-settings" role="tab" aria-controls="custom-content-below-settings" aria-selected="false">Settings</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="custom-content-below-tabContent">
                                        <div class="tab-pane fade" id="custom-content-below-home" role="tabpanel" aria-labelledby="custom-content-below-home-tab">
                                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin malesuada lacus ullamcorper dui molestie, sit amet congue quam finibus. Etiam ultricies nunc non magna feugiat commodo. Etiam odio magna, mollis auctor felis vitae, ullamcorper ornare ligula. Proin pellentesque tincidunt nisi, vitae ullamcorper felis aliquam id. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Proin id orci eu lectus blandit suscipit. Phasellus porta, ante et varius ornare, sem enim sollicitudin eros, at commodo leo est vitae lacus. Etiam ut porta sem. Proin porttitor porta nisl, id tempor risus rhoncus quis. In in quam a nibh cursus pulvinar non consequat neque. Mauris lacus elit, condimentum ac condimentum at, semper vitae lectus. Cras lacinia erat eget sapien porta consectetur.
                                        </div>
                                        <div class="tab-pane fade" id="custom-content-below-profile" role="tabpanel" aria-labelledby="custom-content-below-profile-tab">
                                        Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
                                        </div>
                                        <div class="tab-pane fade active show" id="custom-content-below-messages" role="tabpanel" aria-labelledby="custom-content-below-messages-tab">
                                        Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia, ex vitae gravida fermentum, lectus ipsum gravida arcu, id fermentum metus arcu vel metus. Curabitur eget sem eu risus tincidunt eleifend ac ornare magna.
                                        </div>
                                        <div class="tab-pane fade" id="custom-content-below-settings" role="tabpanel" aria-labelledby="custom-content-below-settings-tab">
                                        Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis.
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success swalDefaultSuccess">
                                        Launch Success Toast
                                    </button>
                                    <button type="button" class="btn btn-success toastrDefaultSuccess">
                                        Launch Success Toast
                                    </button>
                                    <div class="row justify-content-center mb-3">
                                        <button id="submit" type="submit" class="btn btn-warning col-4 w-100">{{ __('words.Save') }}</button>
                                    </div>
                                </form>
                                <input type="text" class="form-control" name="name">
                                <input type="text" class="form-control" name="email">
                                <button type="button" class="btn btn-primary">Change Password</button>
                                <input type="password" class="form-control" name="password">
                                <input type="password" class="form-control" name="passwordConfirm">
                            </div>
                            <div class="card-footer clearfix">
                                <button type="button" class="btn btn-primary">Save</button>
                                <button type="button" class="btn btn-primary">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
