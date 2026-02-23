<!-- <div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('is_location_required', 1, !empty($settings['is_location_required']) ? 1 : 0, ['class' => 'input-icheck'] ); !!} @lang('essentials::lang.is_location_required')
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-xs-12">
            <strong>@lang('essentials::lang.grace_time'):</strong>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_before_checkin',  __('essentials::lang.grace_before_checkin') . ':') !!}
                {!! Form::number('grace_before_checkin', !empty($settings['grace_before_checkin']) ? $settings['grace_before_checkin'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_before_checkin'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkin_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_after_checkin',  __('essentials::lang.grace_after_checkin') . ':') !!}
                {!! Form::number('grace_after_checkin', !empty($settings['grace_after_checkin']) ? $settings['grace_after_checkin'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_after_checkin'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_after_checkin_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_before_checkout',  __('essentials::lang.grace_before_checkout') . ':') !!}
                {!! Form::number('grace_before_checkout', !empty($settings['grace_before_checkout']) ? $settings['grace_before_checkout'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_before_checkout'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkout_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_after_checkout',  __('essentials::lang.grace_after_checkout') . ':') !!}
                {!! Form::number('grace_after_checkout', !empty($settings['grace_after_checkout']) ? $settings['grace_after_checkout'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_after_checkout'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkin_help')</p>
            </div>
        </div>
    </div>
    <p>
        <i class="fas fa-info-circle"></i>
      <span class="text-danger">@lang('essentials::lang.allow_users_for_attendance_moved_to_role')</span>
    </p>
</div> -->



<div class="pos-tab-content">
    <div class="row">
        <div class="col-xs-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('is_location_required', 1, !empty($settings['is_location_required']) ? 1 : 0, ['class' => 'input-icheck'] ); !!} @lang('essentials::lang.is_location_required')
                </label>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('telegram_alert_enabled', 1, !empty($settings['telegram_alert_enabled']) ? 1 : 0, ['class' => 'input-icheck', 'id' => 'telegram_alert_enabled'] ); !!} Enable Telegram Alert
                </label>
            </div>
        </div>
        <div class="clearfix"></div>
        
        <!-- New Allow Time Check In Feature -->
        <div class="col-xs-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('allow_time_checkin', 1, !empty($settings['allow_time_checkin']) ? 1 : 0, ['class' => 'input-icheck', 'id' => 'allow_time_checkin'] ); !!} មិនអាចចុះវត្តមានបានទេចាប់ពីម៉ោង
                </label>
            </div>
        </div>
        
        {{-- Time Check In Settings - Show only when checkbox is checked --}}
        <div id="time_checkin_settings" style="display: {{ !empty($settings['allow_time_checkin']) ? 'block' : 'none' }};">
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('checkin_time', 'Check In Time:') !!}
                    {!! Form::time('checkin_time', !empty($settings['checkin_time']) ? $settings['checkin_time'] : null, ['class' => 'form-control']); !!}
                    <p class="help-block">Set the allowed check-in time</p>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        {{-- Telegram Settings - Show only when checkbox is checked --}}
        <div id="telegram_settings" style="display: {{ !empty($settings['telegram_alert_enabled']) ? 'block' : 'none' }};">
            <div class="col-xs-12">
                <hr>
                <strong>Telegram Settings:</strong>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('telegram_chat_id', 'Telegram Chat ID:') !!}
                    {!! Form::text('telegram_chat_id', !empty($settings['telegram_chat_id']) ? $settings['telegram_chat_id'] : null, ['class' => 'form-control', 'placeholder' => 'Enter Telegram Chat ID']); !!}
                    <p class="help-block">The Chat ID where alerts will be sent</p>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="form-group">
                    {!! Form::label('telegram_bot_token', 'Telegram Bot Token:') !!}
                    {!! Form::text('telegram_bot_token', !empty($settings['telegram_bot_token']) ? $settings['telegram_bot_token'] : null, ['class' => 'form-control', 'placeholder' => 'Enter Bot Token']); !!}
                    <p class="help-block">Your Telegram Bot API Token</p>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
        
        <div class="col-xs-12">
            <strong>@lang('essentials::lang.grace_time'):</strong>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_before_checkin',  __('essentials::lang.grace_before_checkin') . ':') !!}
                {!! Form::number('grace_before_checkin', !empty($settings['grace_before_checkin']) ? $settings['grace_before_checkin'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_before_checkin'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkin_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_after_checkin',  __('essentials::lang.grace_after_checkin') . ':') !!}
                {!! Form::number('grace_after_checkin', !empty($settings['grace_after_checkin']) ? $settings['grace_after_checkin'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_after_checkin'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_after_checkin_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_before_checkout',  __('essentials::lang.grace_before_checkout') . ':') !!}
                {!! Form::number('grace_before_checkout', !empty($settings['grace_before_checkout']) ? $settings['grace_before_checkout'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_before_checkout'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkout_help')</p>
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('grace_after_checkout',  __('essentials::lang.grace_after_checkout') . ':') !!}
                {!! Form::number('grace_after_checkout', !empty($settings['grace_after_checkout']) ? $settings['grace_after_checkout'] : null, ['class' => 'form-control','placeholder' => __('essentials::lang.grace_after_checkout'), 'step' => 1]); !!}
                <p class="help-block">@lang('essentials::lang.grace_before_checkin_help')</p>
            </div>
        </div>
    </div>
    <p>
        <i class="fas fa-info-circle"></i>
      <span class="text-danger">@lang('essentials::lang.allow_users_for_attendance_moved_to_role')</span>
    </p>
</div>

<style type="text/css">
    #telegram_settings, #time_checkin_settings {
        transition: all 0.3s ease;
    }
</style>

<script type="text/javascript">
    (function() {
        var checkInterval = setInterval(function() {
            var telegramCheckbox = $('input[name="telegram_alert_enabled"]');
            var timeCheckinCheckbox = $('input[name="allow_time_checkin"]');
            
            if (telegramCheckbox.length && typeof telegramCheckbox.iCheck !== 'undefined' && 
                timeCheckinCheckbox.length && typeof timeCheckinCheckbox.iCheck !== 'undefined') {
                clearInterval(checkInterval);
                
                // Toggle Telegram Settings
                function toggleTelegramSettings() {
                    if (telegramCheckbox.prop('checked')) {
                        $('#telegram_settings').show();
                    } else {
                        $('#telegram_settings').hide();
                    }
                }
                
                // Toggle Time Checkin Settings
                function toggleTimeCheckinSettings() {
                    if (timeCheckinCheckbox.prop('checked')) {
                        $('#time_checkin_settings').show();
                    } else {
                        $('#time_checkin_settings').hide();
                    }
                }
                
                // Initial state
                toggleTelegramSettings();
                toggleTimeCheckinSettings();
                
                // Telegram checkbox eventsj
                telegramCheckbox.on('ifChecked', function() {
                    $('#telegram_settings').show();
                });
                
                telegramCheckbox.on('ifUnchecked', function() {
                    $('#telegram_settings').hide();
                });
                
                // Time checkin checkbox events
                timeCheckinCheckbox.on('ifChecked', function() {
                    $('#time_checkin_settings').show();
                });
                
                timeCheckinCheckbox.on('ifUnchecked', function() {
                    $('#time_checkin_settings').hide();
                });
            }
        }, 100);
        
        setTimeout(function() {
            clearInterval(checkInterval);
        }, 5000);
    })();
</script>