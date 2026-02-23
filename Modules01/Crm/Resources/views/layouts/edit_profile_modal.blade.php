<div class="jester_ecommerce_profile-modal-header">
    <h3 class="jester_ecommerce_profile-modal-title">@lang('lang_v1.my_profile')</h3>
    <a href="#" class="close-profile-details-modal-btn">
        <i class="fas fa-times"></i>
    </a>
</div>
<div class="jester_ecommerce_profile-modal-body">
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">@lang('user.change_password')</h3>
        </div>
        <div class="box-body">
            {!! Form::open(['url' => action([\Modules\Crm\Http\Controllers\ManageProfileController::class, 'updatePassword']), 'method' => 'post', 'id' => 'update_password']) !!}
                <div class="form-group">
                    {!! Form::label('current_password', __('user.current_password') . ':*') !!}
                    {!! Form::password('current_password', ['class' => 'form-control', 'placeholder' => __('user.current_password'), 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('new_password', __('user.new_password') . ':*') !!}
                    {!! Form::password('new_password', ['class' => 'form-control', 'placeholder' => __('user.new_password'), 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('confirm_password', __('user.confirm_new_password') . ':*') !!}
                    {!! Form::password('confirm_password', ['class' => 'form-control', 'placeholder' => __('user.confirm_new_password'), 'required']) !!}
                </div>
                <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
            {!! Form::close() !!}
        </div>
    </div>
    <div class="box box-solid">
        <div class="box-header">
            <h3 class="box-title">@lang('user.edit_profile')</h3>
        </div>
        <div class="box-body">
            {!! Form::open(['url' => action([\Modules\Crm\Http\Controllers\ManageProfileController::class, 'updateProfile']), 'method' => 'post', 'id' => 'edit_contact_profile', 'files' => true]) !!}
                <div class="form-group">
                    {!! Form::label('surname', __('business.prefix') . ':') !!}
                    {!! Form::text('surname', auth()->user()->surname, ['class' => 'form-control', 'placeholder' => __('business.prefix_placeholder')]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('first_name', __('business.first_name') . ':*') !!}
                    {!! Form::text('first_name', auth()->user()->first_name, ['class' => 'form-control', 'placeholder' => __('business.first_name'), 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('last_name', __('business.last_name') . ':') !!}
                    {!! Form::text('last_name', auth()->user()->last_name, ['class' => 'form-control', 'placeholder' => __('business.last_name')]) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('email', __('business.email') . ':*') !!}
                    {!! Form::email('email', auth()->user()->email, ['class' => 'form-control', 'placeholder' => __('business.email'), 'required']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('language', __('business.language') . ':') !!}
                    @php
                        $languages = [];
                        if (config('constants.langs')) {
                            foreach (config('constants.langs') as $key => $value) {
                                $languages[$key] = $value['full_name'];
                            }
                        }
                    @endphp
                    {!! Form::select('language', $languages, auth()->user()->language, ['class' => 'form-control select2', 'style' => 'width: 100%;']) !!}
                </div>
                <div class="form-group">
                    @if(!empty(auth()->user()->media))
                        <div class="text-center">
                            {!! auth()->user()->media->thumbnail([150, 150], 'img-circle') !!}
                        </div>
                    @endif
                    {!! Form::label('profile_photo', __('lang_v1.upload_image') . ':') !!}
                    {!! Form::file('profile_photo', ['id' => 'profile_photo', 'accept' => 'image/*']) !!}
                    <small><p class="help-block">@lang('purchase.max_file_size', ['size' => (config('constants.document_size_limit') / 1000000)])</p></small>
                </div>
                <button type="submit" class="btn btn-primary pull-right">@lang('messages.update')</button>
            {!! Form::close() !!}
        </div>
    </div>
</div>
