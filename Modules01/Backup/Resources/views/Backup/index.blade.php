@extends('layouts.app')
@section('title', __('backup::lang.Backup'))

{{-- Optional extra page styles  --}}
@push('css')
<style>
    /* ---------- Page tweaks ---------- */
    body {
        background-color: #f4f6f9;
    }

    .content-wrapper {
        background: #f4f6f9;
    }

    .card {
        border: 0;
        border-radius: .5rem;
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075);
    }

    .card-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
    }

    .table-responsive {
        border-radius: .35rem;
    }

    .btn-xs {
        padding: .25rem .5rem;
        font-size: .75rem;
    }

    .fade-in {
        animation: fadeIn .4s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0
        }

        to {
            opacity: 1
        }
    }

    /* ---------- Overlay ---------- */
    #processing-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0, 0, 0, .6);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    #processing-overlay .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* ---------- Modal styles ---------- */
    .modal-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
    }

    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }
</style>
@endpush

@section('content')
<!-- Top bar -->
<section class="content-header no-print">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="m-0 text-dark font-weight-bold">@lang('backup::lang.Backup')</h1>
            <div>
                <button type="button" class="btn btn-primary mr-2" data-toggle="modal" data-target="#createBackupModal">
                    <i class="fa fa-plus mr-1"></i> Create Backup
                </button>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#importSqlModal">
                    <i class="fa fa-upload mr-1"></i> Import SQL
                </button>
                <form class="btn btn-warning" action="{{ route('backup.export') }}" method="POST">
                    @csrf
                    <button type="submit">Export Database</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Page body -->
<section class="content no-print">
    <div class="container-fluid">
        <div id="alert-container" class="fade-in"></div>

        {{-- EXISTING BACKUPS CARD ---------------------------------------}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Existing backups</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="backups-table">
                        <thead class="thead-light">
                            <tr>
                                <th>File</th>
                                <th width="120">Size</th>
                                <th width="170">Created</th>
                                <th width="160">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $b)
                            <tr>
                                <td class="text-monospace small">{{ $b['filename'] }}</td>
                                <td>{{ number_format($b['size']/1024,2) }} KB</td>
                                <td>{{ date('Y-m-d H:i',$b['created_at']) }}</td>
                                <td>
                                    <a href="{{ route('backup.backup.download',$b['filename']) }}" class="btn btn-xs btn-primary">
                                        <i class="fa fa-download"></i>
                                    </a>
                                    <form action="{{ route('backup.backup.delete',$b['filename']) }}" method="POST" class="d-inline delete-form">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-xs btn-danger" onclick="return confirm('Delete this backup?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                    No backups created yet.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($backups->hasPages())
            <div class="card-footer pb-0">{{ $backups->links() }}</div>
            @endif
        </div>
    </div>
</section>

{{-- CREATE BACKUP MODAL ----------------------------------------}}
<div class="modal fade" id="createBackupModal" tabindex="-1" role="dialog" aria-labelledby="createBackupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBackupModalLabel">Create a new backup</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="backup-form" method="POST" action="{{ route('backup.backup') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Table to backup *</label>
                                <select name="table" id="tables" class="form-control select2" required>
                                    <option value="">Select a table</option>
                                    @foreach($tables as $table)
                                    <option value="{{ $table }}">{{ str_replace('_',' ',$table) }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback d-block" id="table-error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Business (optional)</label>
                                <select name="business" id="business" class="form-control select2">
                                    <option value="">Current user business</option>
                                    @foreach($businesses as $business)
                                    <option value="{{ $business->id }}">{{ $business->name }} ({{ $business->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Custom Query (Optional)</label>
                        <textarea name="custom_query" id="custom_query" rows="4" class="form-control" placeholder="SELECT * FROM table WHERE …"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Limit rows</label>
                                <select id="limit" name="limit" class="form-control select2">
                                    <option value="none">No limit</option>
                                    <option value="10">LIMIT 10</option>
                                    <option value="50">LIMIT 50</option>
                                    <option value="100">LIMIT 100</option>
                                    <option value="custom">Custom</option>
                                </select>
                                <div class="mt-2 limit-container" style="display:none">
                                    <input type="number" id="custom_limit" name="custom_limit" class="form-control" placeholder="e.g. 25" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date filter</label>
                                <select id="date_filter" name="date_filter" class="form-control select2">
                                    <option value="none">No filter</option>
                                    <option value="today">Today</option>
                                    <option value="last7days">Last 7 days</option>
                                    <option value="custom">Custom range</option>
                                </select>
                                <div class="mt-2 datepicker-container" style="display:none">
                                    <div class="input-group mb-1">
                                        <input type="text" id="start_date" name="start_date" class="form-control datepicker" placeholder="Start">
                                        <div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                                    </div>
                                    <div class="input-group">
                                        <input type="text" id="end_date" name="end_date" class="form-control datepicker" placeholder="End">
                                        <div class="input-group-append"><span class="input-group-text"><i class="fa fa-calendar"></i></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="backup-btn" class="btn btn-primary">
                        <i class="fa fa-download mr-1"></i> Create Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- IMPORT SQL MODAL ---------------------------------------------}}
<div class="modal fade" id="importSqlModal" tabindex="-1" role="dialog" aria-labelledby="importSqlModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importSqlModalLabel">Import SQL file</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="import-form" action="{{ route('backup.import.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Upload SQL file *</label>
                        <input type="file" name="backup_file" id="backup_file" class="form-control" accept=".sql,.txt" required>
                        <small class="form-text text-muted">Please select a .sql or .txt file to import.</small>
                    </div>

                    <div class="form-group">
                        <label>Select business *</label>
                        <select name="business_identifier" id="business_identifier" class="form-control select2" required>
                            <option value="">-- Select Business --</option>
                            @foreach($businesses as $business)
                            <option value="{{ $business->id }}">{{ $business->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="import-btn" class="btn btn-success">
                        <i class="fa fa-upload mr-1"></i> Import
                    </button>
                    <span id="import-spinner" class="d-none ml-2">
                        <i class="fa fa-spinner fa-spin"></i>
                    </span>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('javascript')
<script>
    $(function() {

        function toggleOverlay(show = true) {
            $('#processing-overlay').toggleClass('d-none', !show);
        }

        /* ---------- Initial UI ---------- */
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: 'Select…',
            allowClear: true,
            dropdownParent: $('#createBackupModal')
        });

        // Separate select2 for import modal
        $('#business_identifier').select2({
            theme: 'bootstrap4',
            placeholder: 'Select…',
            allowClear: true,
            dropdownParent: $('#importSqlModal')
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });

        const userBusinessId = '{{ auth()->user()->business_id ?? "" }}';

        /* ---------- Modal events ---------- */
        $('#createBackupModal').on('shown.bs.modal shown.bs.select2', function() {
            $('.select2', this).select2({
                theme: 'bootstrap4',
                placeholder: 'Select…',
                allowClear: true,
                dropdownParent: $(this)
            });
        });

        $('#importSqlModal').on('shown.bs.modal', function() {
            $('#business_identifier').select2({
                theme: 'bootstrap4',
                placeholder: 'Select…',
                allowClear: true,
                dropdownParent: $(this)
            });
        });

        /* ---------- Query builder ---------- */
        function buildQuery() {
            const table = $('#tables').val();
            const bizId = $('#business').val() || userBusinessId;
            const limitOpt = $('#limit').val();
            const customL = $('#custom_limit').val();
            const dateOpt = $('#date_filter').val();
            const start = $('#start_date').val();
            const end = $('#end_date').val();
            if (!table) return '';

            let q = `SELECT * FROM ${table}`,
                w = [];
            if (bizId) w.push(`business_id = '${bizId}'`);

            if (dateOpt === 'today') {
                const t = new Date(),
                    y = t.getFullYear(),
                    m = String(t.getMonth() + 1).padStart(2, '0'),
                    d = String(t.getDate()).padStart(2, '0');
                w.push(`created_at BETWEEN '${y}-${m}-${d} 00:00:00' AND '${y}-${m}-${d} 23:59:59'`);
            } else if (dateOpt === 'last7days') {
                const to = new Date(),
                    sev = new Date();
                sev.setDate(to.getDate() - 7);
                w.push(`created_at BETWEEN '${sev.toISOString().slice(0,10)} 00:00:00' AND '${to.toISOString().slice(0,10)} 23:59:59'`);
            } else if (dateOpt === 'custom' && start && end) {
                w.push(`created_at BETWEEN '${start} 00:00:00' AND '${end} 23:59:59'`);
            }

            if (w.length) q += ' WHERE ' + w.join(' AND ');
            const l = (limitOpt === 'custom' && customL && !isNaN(customL)) ? parseInt(customL) :
                (limitOpt && limitOpt !== 'none' && limitOpt !== 'custom') ? parseInt(limitOpt) : null;
            if (l) q += ` LIMIT ${l}`;
            return q;
        }

        function updateQuery() {
            $('#custom_query').val(buildQuery());
        }

        /* ---------- Events ---------- */
        $('#business, #tables').on('change.select2', updateQuery);
        $('#limit').on('change', function() {
            $('.limit-container').toggle($(this).val() === 'custom');
            updateQuery();
        });
        $('#custom_limit').on('input', () => {
            $('#limit').val('custom').trigger('change');
        });
        $('#date_filter').on('change', function() {
            $('.datepicker-container').toggle($(this).val() === 'custom');
            updateQuery();
        });
        $('.datepicker').on('changeDate', updateQuery);

        /* ---------- Alerts ---------- */
        function showAlert(msg, type = 'danger') {
            const al = $(`<div class="alert alert-${type} fade-in">${msg}</div>`);
            $('#alert-container').empty().append(al);
            $('html,body').animate({
                scrollTop: 0
            }, 300);
            setTimeout(() => al.fadeOut(), 4000);
        }

        /* ---------- AJAX forms ---------- */
        $('#backup-form').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#backup-btn');
            toggleOverlay(true);
            $btn.prop('disabled', true);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: res => {
                    toggleOverlay(false);
                    $btn.prop('disabled', false);

                    if (res.success) {
                        showAlert(res.message, 'success');
                        if (res.backups) renderBackups(res.backups);

                        this.reset();
                        $('#createBackupModal').modal('hide');
                        $('.select2').val(null).trigger('change');
                        $('.limit-container, .datepicker-container').hide();
                    } else {
                        showAlert(res.message);
                    }
                },
                error: xhr => {
                    toggleOverlay(false);
                    $btn.prop('disabled', false);
                    showAlert(xhr.responseJSON?.message || 'Backup failed.');
                }
            });
        });

        $('#import-form').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#import-btn');
            toggleOverlay(true);
            $btn.prop('disabled', true);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: res => {
                    toggleOverlay(false);
                    $btn.prop('disabled', false);

                    showAlert(res.message, res.success ? 'success' : 'danger');
                    if (res.success) {
                        this.reset();
                        $('#importSqlModal').modal('hide');
                        $('#business_identifier').val(null).trigger('change');
                    }
                },
                error: xhr => {
                    toggleOverlay(false);
                    $btn.prop('disabled', false);
                    showAlert(xhr.responseJSON?.message || 'Import failed.');
                }
            });
        });

        /* ---------- Render backups ---------- */
        function renderBackups(backups) {
            let html = '';
            if (backups && backups.length > 0) {
                backups.forEach(b => {
                    html += `
                <tr class="fade-in">
                    <td class="text-monospace small">${b.filename}</td>
                    <td>${(b.size/1024).toFixed(2)} KB</td>
                    <td>${new Date(b.created_at*1000).toISOString().replace('T',' ').slice(0,16)}</td>
                    <td>
                        <a href="${b.downloadUrl}" class="btn btn-xs btn-primary"><i class="fa fa-download"></i></a>
                        <form action="${b.deleteUrl}" method="POST" class="d-inline delete-form">
                            <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-xs btn-danger" onclick="return confirm('Delete?')"><i class="fa fa-trash"></i></button>
                        </form>
                    </td>
                </tr>`;
                });
            } else {
                html = `
            <tr><td colspan="4" class="text-center py-5 text-muted">
                <i class="fa fa-inbox fa-2x mb-2 d-block"></i>No backups created yet.
            </td></tr>`;
            }
            $('#backups-table tbody').html(html);
        }

        /* ---------- Delete ---------- */
        $(document).on('submit', '.delete-form', function(e) {
            e.preventDefault();
            if (!confirm('Delete this backup?')) return;
            toggleOverlay(true);
            $.post($(this).attr('action'), $(this).serialize())
                .done(res => {
                    toggleOverlay(false);
                    showAlert(res.message, res.success ? 'success' : 'danger');
                    if (res.success) renderBackups(res.backups);
                })
                .fail(xhr => {
                    toggleOverlay(false);
                    showAlert(xhr.responseJSON?.message || 'Delete failed.');
                });
        });

        /* ---------- Clear forms when modals are closed ---------- */
        $('#createBackupModal').on('hidden.bs.modal', function() {
            $('#backup-form')[0].reset();
            $('.select2').val(null).trigger('change');
            $('.limit-container, .datepicker-container').hide();
        });

        $('#importSqlModal').on('hidden.bs.modal', function() {
            $('#import-form')[0].reset();
            $('#business_identifier').val(null).trigger('change');
        });
    });
</script>
@endsection