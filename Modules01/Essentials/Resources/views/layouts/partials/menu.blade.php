    <div class="recommended-item" data-id="sop">
        <a href="{{ action([\Modules\SOP\Http\Controllers\SOPController::class, 'index']) }}"  title="{{__('sop::lang.sop')}}"
        class="recommended-link {{ request()->segment(2) == 'sop' ? 'active' : '' }}">
            <img src="{{ asset('public/uploads/SOP/1752721052_sop.svg') }}"
                class="recommended-icon" alt="">
            <div>
                <p class="text-base font-medium text-gray-800">{{__('sop::lang.sop')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('sop::lang.sop')}}</p>
            </div>
        </a>
    </div>



    

             {{-- Essentials Additional Tiles --}}
<div class="recommended-item" data-key="todo">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\ToDoController::class, 'index']) }}" title="{{ __('essentials::lang.todo') }}"  class="recommended-link">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/todo.svg') }}" 
             class="recommended-icon" 
             alt="{{ __('essentials::lang.todo') }}">
         

         <div>
                <p class="text-base font-medium text-gray-800">{{__('essentials::lang.todo')}}</p>
                <p class="recommended-text text-sm text-gray-600">Manage {{__('essentials::lang.todo')}}</p>
            </div>
    </a>
</div>



<div class="recommended-item" data-key="document">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\DocumentController::class, 'index']) }}" 
       title="{{ __('essentials::lang.document') }}"  class="recommended-link">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/document.svg') }}" 
             class="recommended-icon" 
             alt="{{ __('essentials::lang.document') }}">
        <p class="text-base font-medium text-gray-800">{{ __('essentials::lang.document') }}</p>
    </a>
</div>

<div class="recommended-item" data-key="memos">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\DocumentController::class, 'index']) .'?type=memos' }}" 
       title="{{ __('essentials::lang.memos') }}"  class="recommended-link">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/memos.svg') }}" 
             class="recommended-icon" 
             alt="{{ __('essentials::lang.memos') }}">
        <p class="text-base font-medium text-gray-800">{{ __('essentials::lang.memos') }}</p>
    </a>
</div>

<div class="recommended-item" data-key="reminders">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\ReminderController::class, 'index']) }}" 
       title="{{ __('essentials::lang.reminders') }}"  class="recommended-link">
        <img src="{{ asset('public/icons/' . (session('business.icon_pack') ?: 'v1') . '/modules/calendar.svg') }}" 
                     class="recommended-icon" 
                     alt="{{ __('essentials::lang.reminders') }}">
        <p class="text-base font-medium text-gray-800">{{ __('essentials::lang.reminders') }}</p>
    </a>
</div>

@if (auth()->user()->can('essentials.view_message') || auth()->user()->can('essentials.create_message'))
    <div class="recommended-item" data-key="messages">
        <a href="{{ action([\Modules\Essentials\Http\Controllers\EssentialsMessageController::class, 'index']) }}" 
           title="{{ __('essentials::lang.messages') }}"  class="recommended-link">
            <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/messages.svg') }}" 
                 class="recommended-icon" 
                 alt="{{ __('essentials::lang.messages') }}">
            <p class="text-base font-medium text-gray-800">{{ __('essentials::lang.messages') }}</p>
        </a>
    </div>
@endif

<div class="recommended-item" data-key="knowledge_base">
    <a href="{{ action([\Modules\Essentials\Http\Controllers\KnowledgeBaseController::class, 'index']) }}" 
       title="{{ __('essentials::lang.knowledge_base') }}"  class="recommended-link">
        <img src="{{ asset('public/icons/' . (!empty(session('business.icon_pack')) ? session('business.icon_pack') : 'v1') . '/modules/kb.svg') }}" 
             class="recommended-icon" 
             alt="{{ __('essentials::lang.knowledge_base') }}">
        <p class="text-base font-medium text-gray-800">{{ __('essentials::lang.knowledge_base') }}</p>
    </a>
</div>


