<div class="draggable data" style="position: absolute; top: 450px; left: 300px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>{{ $contact->name }}</h5>
</div>
<div class="draggable data" style="position: absolute; top: 515px; left: 200px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5> {{ $contact->supplier_business_name }}
    </h5>
</div>


<div class="draggable data" style="position: absolute; top: 640px; left: 80px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>  @lang('contact.expired_at'): {{ \Carbon\Carbon::parse($contact->expired_date)->format('d-m-Y') }}   </h5>
</div>

<div class="draggable data" style="position: absolute; top: 640px; left: 320px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>  @lang('contact.register_date'):  {{ \Carbon\Carbon::parse($contact->register_date)->format('d-m-Y') }}   </h5>
</div>
<div class="draggable data" style="position: absolute; top: 370px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>  @lang('contact.study_date'):
                                {{ \Carbon\Carbon::parse($contact->study_date)->format('d-m-Y') }}   </h5>
</div>


<div class="draggable data" style="position: absolute; top: 110px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5> address    {!! $contact->contact_address !!}
    </h5>
</div>


<div class="draggable data" style="position: absolute; top: 150px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>Tel: {{ $contact->mobile }}</h5>
</div>
<div class="draggable data" style="position: absolute; top: 180px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5> Tel: {{ $contact->alternate_number }}</h5>
</div>
<div class="draggable data" style="position: absolute; top: 210px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5> DOB  {{ @format_date($contact->dob) }}     </h5>
</div>
<div class="draggable data" style="position: absolute; top: 240px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5> 
               </h5>
</div>

<div class="draggable data" style="position: absolute; top: 270px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5> 
                  </h5>
</div>


<div class="draggable data" style="position: absolute; top: 400px; left: -400px; width: auto; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
    <h5>  @lang('lang_v1.created_at'):
                                {{ \Carbon\Carbon::parse($contact->created_at)->format('d-m-Y') }}   </h5>
</div>