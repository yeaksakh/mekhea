<table class="table table-condensed">
	@forelse($medias as $media)
		<tr>
			<td>
				  
					    @if(!empty($business->logo))
					        <img src="{{ asset('/uploads/business_logos/' . e($business->logo)) }}" class="img img-thumbnail img-logo tw-flex-shrink-0" alt="Company Logo">
					    @endif
					    <a href="{{ route('home') }}" class="tw-flex-1 tw-min-w-0">
					        <p style="font-size: 26px;" class="tw-font-medium tw-text-black tw-font-bold tw-truncate">
					            {{ Session::get('business.name') }}
					        </p>
					    </a>
					
			</td>
			<td>
				<small>
					@lang('lang_v1.uploaded_by'):
					{{$media->uploaded_by_user->user_full_name}}
				</small>
			</td>
			<td>
				<a href="{{$media->display_url}}" download="{{$media->display_name}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-accent no-print"><i class="fas fa-download"></i> @lang('lang_v1.download')</a>
				@if(!empty($delete))
					<button type="button" data-href="{{action([\App\Http\Controllers\ProductController::class, 'deleteMedia'], [$media->id])}}" class="tw-dw-btn tw-dw-btn-xs tw-dw-btn-outline  tw-dw-btn-error delete-media no-print"><i class="fas fa-trash"></i> @lang('messages.delete')</a>
				@endif
			</td>
		</tr>
	@empty
		<tr>
			<td colspan="3" class="text-center">@lang('lang_v1.no_attachment_found')</td>
		</tr>
	@endforelse
</table>