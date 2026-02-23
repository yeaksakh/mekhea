<h3 class="profile-username" style="padding-left: 20px; padding-top: 20px;">
    <strong>{{ $contact->full_name_with_business }}</strong>
    <small>
        @if($contact->type == 'both')
            {{__('role.customer')}} & {{__('role.supplier')}}
        @elseif(($contact->type != 'lead'))
            {{__('role.'.$contact->type)}}
        @endif
    </small>
    
</h3>