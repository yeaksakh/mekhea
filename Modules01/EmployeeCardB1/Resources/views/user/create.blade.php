@extends('layouts.app')

@section('title', __( 'user.add_user' ))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang( 'user.add_user' )</h1>
</section>

<!-- Main content -->
<section class="content">
  {!! Form::open(['url' => action([\App\Http\Controllers\ManageUserController::class, 'store']), 'method' => 'post', 'id' => 'user_add_form', 'files'=>true ]) !!}
  <div class="row">
    <div class="col-md-12">
    @component('components.widget')
      <div class="col-md-6">
        {!! Form::label('signature', __( 'business.profile_photo' ) . ':*') !!}
        <div class="form-group">
          {!! Form::file('profile_photo', ['id' => 'profile_photo', 'accept' => 'image/*']); !!}
          <img id="profile_photo_preview" style="max-width: 100px; margin-top: 10px; display: none;">
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('signature', __( 'business.signature' ) . ':*') !!}
          <input type="file" name="sign_image" id="sign_image" accept="image/*">
          <img id="sign_image_preview" style="max-width: 100px; margin-top: 10px; display: none;">
        </div>
      </div>

      <script>
        // Function to handle image preview
        function previewImage(input, previewId) {
          const file = input.files[0];
          const preview = document.getElementById(previewId);

          if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
              preview.src = e.target.result;
              preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
          } else {
            preview.style.display = 'none';
          }
        }

        // Add event listeners to both file inputs
        document.getElementById('profile_photo').addEventListener('change', function() {
          previewImage(this, 'profile_photo_preview');
        });

        document.getElementById('sign_image').addEventListener('change', function() {
          previewImage(this, 'sign_image_preview');
        });
      </script>
      @endcomponent
      @component('components.widget')
      <div class="col-md-2">
        <div class="form-group">
          {!! Form::label('surname', __( 'business.prefix' ) . ':') !!}
          {!! Form::text('surname', null, ['class' => 'form-control', 'placeholder' => __( 'business.prefix_placeholder' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('first_name', __( 'business.first_name' ) . ':*') !!}
          {!! Form::text('first_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.first_name' ) ]); !!}
        </div>
      </div>
      <div class="col-md-5">
        <div class="form-group">
          {!! Form::label('last_name', __( 'business.last_name' ) . ':') !!}
          {!! Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => __( 'business.last_name' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('email', __( 'business.email' ) . ':*') !!}
          {!! Form::text('email', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'business.email' ) ]); !!}
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <div class="checkbox">
            <br />
            <label>
              {!! Form::checkbox('is_active', 'active', true, ['class' => 'input-icheck status']); !!} {{ __('lang_v1.status_for_user') }}
            </label>
            @show_tooltip(__('lang_v1.tooltip_enable_user_active'))
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="form-group">
          <div class="checkbox">
            <br />
            <label>
              {!! Form::checkbox('is_enable_service_staff_pin', 1, false, ['class' => 'input-icheck status', 'id' => 'is_enable_service_staff_pin']); !!} {{ __('lang_v1.enable_service_staff_pin') }}
            </label>
            @show_tooltip(__('lang_v1.tooltip_is_enable_service_staff_pin'))
          </div>
        </div>
      </div>
      <div class="col-md-2 hide service_staff_pin_div">
        <div class="form-group">
          {!! Form::label('service_staff_pin', __( 'lang_v1.staff_pin' ) . ':') !!}
          {!! Form::password('service_staff_pin', ['class' => 'form-control', 'required' => true, 'placeholder' => __( 'lang_v1.staff_pin' ) ]); !!}
        </div>
      </div>
      @endcomponent
    </div>
    <div class="col-md-12">
      @component('components.widget', ['title' => __('lang_v1.roles_and_permissions')])
      <div class="col-md-4">
        <div class="form-group">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('allow_login', 1, true,
              [ 'class' => 'input-icheck', 'id' => 'allow_login']); !!} {{ __( 'lang_v1.allow_login' ) }}
            </label>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="user_auth_fields">
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('username', __( 'business.username' ) . ':') !!}
            @if(!empty($username_ext))
            <div class="input-group">
              {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
              <span class="input-group-addon">{{$username_ext}}</span>
            </div>
            <p class="help-block" id="show_username"></p>
            @else
            {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => __( 'business.username' ) ]); !!}
            @endif
            <p class="help-block">@lang('lang_v1.username_help')</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('password', __( 'business.password' ) . ':*') !!}
            {!! Form::password('password', ['class' => 'form-control', 'required', 'placeholder' => __( 'business.password' ) ]); !!}
          </div>
        </div>
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('confirm_password', __( 'business.confirm_password' ) . ':*') !!}
            {!! Form::password('confirm_password', ['class' => 'form-control', 'required', 'placeholder' => __( 'business.confirm_password' ) ]); !!}
          </div>
        </div>
      </div>
      @endcomponent
      @component('components.widget')
      <div class="clearfix"></div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('role', __( 'user.role' ) . ':*') !!} @show_tooltip(__('lang_v1.admin_role_location_permission_help'))
          {!! Form::select('role', $roles, null, ['class' => 'form-control select2']); !!}
          <span class="input-group-addon" onclick="window.location.href='{{ action([\App\Http\Controllers\RoleController::class, 'index']) . '?type=hrm_designation' }}'" style="cursor: pointer; background-color: #28a745; color: white; padding: 6px 12px; border-radius: 4px;">
            <i class="fas fa-plus"></i>​
          </span>
        </div>
      </div>
      <div class="clearfix"></div>
      <div class="col-md-3">
        <h4>@lang( 'role.access_locations' ) @show_tooltip(__('tooltip.access_locations_permission'))</h4>
      </div>
      <div class="col-md-9">
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('access_all_locations', 'access_all_locations', true,
              ['class' => 'input-icheck']); !!} {{ __( 'role.all_locations' ) }}
            </label>
            @show_tooltip(__('tooltip.all_location_permission'))
          </div>
        </div>
        @foreach($locations as $location)
        <div class="col-md-12">
          <div class="checkbox">
            <label>
              {!! Form::checkbox('location_permissions[]', 'location.' . $location->id, false,
              [ 'class' => 'input-icheck']); !!} {{ $location->name }} @if(!empty($location->location_id))({{ $location->location_id}}) @endif
            </label>
          </div>
        </div>
        @endforeach
      </div>
      @endcomponent
    </div>

    <div class="col-md-12">
      @component('components.widget', ['title' => __('sale.sells')])
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('cmmsn_percent', __( 'lang_v1.cmmsn_percent' ) . ':') !!} @show_tooltip(__('lang_v1.commsn_percent_help'))
          {!! Form::text('cmmsn_percent', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.cmmsn_percent' ) ]); !!}
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          {!! Form::label('max_sales_discount_percent', __( 'lang_v1.max_sales_discount_percent' ) . ':') !!} @show_tooltip(__('lang_v1.max_sales_discount_percent_help'))
          {!! Form::text('max_sales_discount_percent', null, ['class' => 'form-control input_number', 'placeholder' => __( 'lang_v1.max_sales_discount_percent' ) ]); !!}
        </div>
      </div>
      <div class="clearfix"></div>

      <div class="col-md-4">
        <div class="form-group">
          <div class="checkbox">
            <br />
            <label>
              {!! Form::checkbox('selected_contacts', 1, false,
              [ 'class' => 'input-icheck', 'id' => 'selected_contacts']); !!} {{ __( 'lang_v1.allow_selected_contacts' ) }}
            </label>
            @show_tooltip(__('lang_v1.allow_selected_contacts_tooltip'))
          </div>
        </div>
      </div>
      <div class="col-sm-4 hide selected_contacts_div">
        <div class="form-group">
          {!! Form::label('user_allowed_contacts', __('lang_v1.selected_contacts') . ':') !!}
          <div class="form-group">
            {!! Form::select('selected_contact_ids[]', [], null, ['class' => 'form-control select2', 'multiple', 'style' => 'width: 100%;', 'id' => 'user_allowed_contacts' ]); !!}
          </div>
        </div>
      </div>

      @endcomponent
      
        @component('components.widget')
    <div class="clearfix"></div>
    <h4>{{ __('Additional Information') }}</h4>
    <hr>

    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('name_in_khmer', __( 'Name In Khmer' ) . ':') !!}
            {!! Form::text('name_in_khmer', null, ['class' => 'form-control', 'placeholder' => __( 'Name In Khmer' ) ]); !!}
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('uniform_size', __( 'Uniform Size' ) . ':') !!}
            {!! Form::text('uniform_size', null, ['class' => 'form-control', 'placeholder' => __( 'Uniform Size' ) ]); !!}
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('member_date', __( 'Member Date' ) . ':') !!}
            {!! Form::date('member_date', null, ['class' => 'form-control']); !!}
        </div>
    </div>
    

 <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('hieght', __( 'Hieght (cm)' ) . ':') !!}
            {!! Form::number('hieght', null, ['class' => 'form-control', 'placeholder' => __( 'Hieght' ), 'step' => '0.01']); !!}
        </div>
    </div>
    </div>
    
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::label('weight', __( 'Weight (kg)' ) . ':') !!}
            {!! Form::number('weight', null, ['class' => 'form-control', 'placeholder' => __( 'Weight' ), 'step' => '0.01']); !!}
        </div>
    </div>
    

    
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('guardien_type', __( 'Guardian Type' ) . ':') !!}
            {!! Form::select('guardien_type', [
                'parent' => __('Parent'),
                'spouse' => __('Spouse'),
                'sibling' => __('Sibling'),
                'other' => __('Other')
            ], null, ['class' => 'form-control', 'placeholder' => __( 'Select Guardien Type' )]); !!}
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('insurance_number', __( 'Insurance Number' ) . ':') !!}
            {!! Form::text('insurance_number', null, ['class' => 'form-control', 'placeholder' => __( 'Insurance Number' ) ]); !!}
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('ss_number', __( 'Social Security Number' ) . ':') !!}
            {!! Form::text('ss_number', null, ['class' => 'form-control', 'placeholder' => __( 'SS Number' ) ]); !!}
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('date_left_job', __( 'Date Left Job' ) . ':') !!}
            {!! Form::date('date_left_job', null, ['class' => 'form-control']); !!}
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('reason', __( 'Reason for Leaving' ) . ':') !!}
            {!! Form::text('reason', null, ['class' => 'form-control', 'placeholder' => __( 'Reason' ) ]); !!}
        </div>
    </div>
    
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('job_history', __( 'Job History' ) . ':') !!}
            {!! Form::textarea('job_history', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __( 'Detailed job history' ) ]); !!}
        </div>
    </div>
    
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('job_description', __( 'Job Description' ) . ':') !!}
            {!! Form::textarea('job_description', null, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __( 'Detailed job description' ) ]); !!}
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('hobby', __( 'Hobby' ) . ':') !!}
            {!! Form::textarea('hobby', null, ['class' => 'form-control',  'rows' => 3, 'placeholder' => __( 'Hobby' ) ]); !!}
        </div>
    </div>
    
    <div class="clearfix"></div>
    
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label('education', __( 'Education' ) . ':') !!}
            {!! Form::textarea('education', null, ['class' => 'form-control',  'rows' => 3, 'placeholder' => __( 'Education' ) ]); !!}
        </div>
    </div>
@endcomponent
    </div>

  </div>
  @include('user.edit_profile_form_part')

  @if(!empty($form_partials))
  @foreach($form_partials as $partial)
  {!! $partial !!}
  @endforeach
  @endif
  <div class="row">
    <div class="col-md-12 text-center">
      <button type="submit" class="tw-dw-btn tw-dw-btn-primary tw-dw-btn-lg tw-text-white" id="submit_user_button">@lang( 'messages.save' )</button>
    </div>
  </div>
  {!! Form::close() !!}
  @stop
  @section('javascript')
  <script type="text/javascript">
    __page_leave_confirmation('#user_add_form');
    $(document).ready(function() {
      $('#selected_contacts').on('ifChecked', function(event) {
        $('div.selected_contacts_div').removeClass('hide');
      });
      $('#selected_contacts').on('ifUnchecked', function(event) {
        $('div.selected_contacts_div').addClass('hide');
      });

      $('#is_enable_service_staff_pin').on('ifChecked', function(event) {
        $('div.service_staff_pin_div').removeClass('hide');
      });

      $('#is_enable_service_staff_pin').on('ifUnchecked', function(event) {
        $('div.service_staff_pin_div').addClass('hide');
        $('#service_staff_pin').val('');
      });

      $('#allow_login').on('ifChecked', function(event) {
        $('div.user_auth_fields').removeClass('hide');
      });
      $('#allow_login').on('ifUnchecked', function(event) {
        $('div.user_auth_fields').addClass('hide');
      });

      $('#user_allowed_contacts').select2({
        ajax: {
          url: '/contacts/customers',
          dataType: 'json',
          delay: 250,
          data: function(params) {
            return {
              q: params.term, // search term
              page: params.page,
              all_contact: true
            };
          },
          processResults: function(data) {
            return {
              results: data,
            };
          },
        },
        templateResult: function(data) {
          var template = '';
          if (data.supplier_business_name) {
            template += data.supplier_business_name + "<br>";
          }
          template += data.text + "<br>" + LANG.mobile + ": " + data.mobile;

          return template;
        },
        minimumInputLength: 1,
        escapeMarkup: function(markup) {
          return markup;
        },
      });
    });

    $('form#user_add_form').validate({
      rules: {
        first_name: {
          required: true,
        },
        email: {
          email: true,
          remote: {
            url: "/business/register/check-email",
            type: "post",
            data: {
              email: function() {
                return $("#email").val();
              }
            }
          }
        },
        password: {
          required: true,
          minlength: 5
        },
        confirm_password: {
          equalTo: "#password"
        },
        username: {
          minlength: 5,
          remote: {
            url: "/business/register/check-username",
            type: "post",
            data: {
              username: function() {
                return $("#username").val();
              },
              @if(!empty($username_ext))
              username_ext: "{{$username_ext}}"
              @endif
            }
          }
        }
      },
      messages: {
        password: {
          minlength: 'Password should be minimum 5 characters',
        },
        confirm_password: {
          equalTo: 'Should be same as password'
        },
        username: {
          remote: 'Invalid username or User already exist'
        },
        email: {
          remote: '{{ __("validation.unique", ["attribute" => __("business.email")]) }}'
        }
      }
    });
    $('#username').change(function() {
      if ($('#show_username').length > 0) {
        if ($(this).val().trim() != '') {
          $('#show_username').html("{{__('lang_v1.your_username_will_be')}}: <b>" + $(this).val() + "{{$username_ext}}</b>");
        } else {
          $('#show_username').html('');
        }
      }
    });
  </script>
  @endsection