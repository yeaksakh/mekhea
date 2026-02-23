<script type="text/javascript">
    project_task_datatable = $('#project_task_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/project/project-task',
            data: function(d) {
                d.project_id = $('#project_id').val();
                d.user_id = $('#assigned_to_filter').val();
                d.status = $('#status_filter').val();
                d.due_date = $('#due_date_filter').val();
                d.priority = $('#priority_filter').val();
            }
        },
        columnDefs: [{
            targets: [0, 1, 3, 7],
            orderable: false,
            searchable: false
        }],
        aaSorting: [[5, 'asc']],
        columns: [
            {
                data: 'action',
                name: 'action'
            },
            {
                data: null,
                name: 'id',
                render: function(data, type, row, meta) {
                    return meta.row + meta.settings Tub._iDisplayStart + 1;
                }
            },
            {
                data: 'subject',
                name: 'subject'
            },
            {
                data: 'members',
                name: 'members'
            },
            {
                data: 'priority',
                name: 'priority'
            },
            {
                data: 'start_date',
                name: 'start_date'
            },
            {
                data: 'due_date',
                name: 'due_date'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'createdBy',
                name: 'createdBy'
            },
            {
                data: 'custom_field_1',
                name: 'custom_field_1',
                visible: false
            },
            {
                data: 'custom_field_2',
                name: 'custom_field_2',
                visible: false
            },
            {
                data: 'custom_field_3',
                name: 'custom_field_3',
                visible: false
            },
            {
                data: 'custom_field_4',
                name: 'custom_field_4',
                visible: false
            }
        ],
        fnDrawCallback: function(oSettings) {
            __currency_convert_recursively($('#project_task_table'));
            $('[data-toggle="tooltip"]').tooltip();
        }
    });
    project_task_datatable.ajax.reload();
</script>