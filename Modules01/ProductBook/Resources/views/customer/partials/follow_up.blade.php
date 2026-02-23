<div class="col-sm-12">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#all_followup_tab" data-toggle="tab" aria-expanded="true"> @lang('crm::lang.follow_ups')</a>
            </li>

        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="all_followup_tab">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="follow_up_table" style="width: 100%">
                        <thead>
                            <tr>
                                <th>@lang('messages.action')</th>
                                <th>#</th>
                                <th>
                                    @lang('contact.contact')
                                </th>
                                <th>@lang('crm::lang.start_datetime')</th>
                                <th>@lang('crm::lang.end_datetime')</th>
                                <th>@lang('sale.status')</th>
                                <th>@lang('crm::lang.schedule_type')</th>
                                <th>@lang('crm::lang.followup_category')</th>
                                <th>@lang('lang_v1.assigned_to')</th>
                                <th>
                                    @lang('crm::lang.description')
                                </th>
                                <th>
                                    @lang('crm::lang.additional_info')
                                </th>
                                <th>@lang('crm::lang.title')</th>
                                <th>
                                    @lang('lang_v1.added_by')
                                </th>
                                <th>
                                    @lang('lang_v1.added_on')
                                </th>
                                <th>
                                    Phone Number
                                </th>
                                <th>
                                    Address
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr class="bg-gray font-17 footer-total text-center">
                                <td colspan="5">
                                    <strong>@lang('sale.total'):</strong>
                                </td>
                                <td class="footer_follow_up_status_count"></td>
                                <td class="footer_follow_up_type_count"></td>
                                <td colspan="6"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
</div>