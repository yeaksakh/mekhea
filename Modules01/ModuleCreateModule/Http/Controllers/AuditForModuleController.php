<?php

namespace Modules\ModuleCreateModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Filesystem\Filesystem;

class AuditForModuleController extends Controller
{
    protected $files;

    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    public function createAudit($moduleName, $title, $type)
    {
        // Your function implementation here
        // Example content creation code
        $viewPath = base_path("Modules/{$moduleName}/Resources/views/{$moduleName}/audit.blade.php");
        $moduleNameLower = strtolower($moduleName);

        $audit = [];    

        if (!is_null($title) && is_array($title) && !is_null($type) && is_array($type)) {
            foreach ($title as $index => $fieldTitle) {
                $fieldType = $type[$index];
                if ($fieldType == 'audit') {
                    $audit[] = "\${$moduleNameLower}->{$fieldTitle} ?? null,";
                }
            }
        }
        $audit = implode("\n", $audit);

        if (!$this->files->exists($viewPath)) {
            $content = <<<EOT
<div class="modal-dialog" role="document">
    {!! Form::open([
        'url' => action([Modules\\{$moduleName}\\Http\\Controllers\\{$moduleName}Controller::class, 'updateAuditStatus'], [\${$moduleNameLower}->id]),
        'method' => 'put',
        'id' => 'audit_{$moduleName}_form', // Make sure this ID matches your jQuery selector
    ]) !!}
    @csrf
    @method('PUT')
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('{$moduleNameLower}::lang.audit')</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('audit_status', __('{$moduleNameLower}::lang.audit_status') . ':') !!}
                        {!! Form::select(
                            'audit_status',
                            ['Done' => __('Done'), 'Pending' => __('Pending'), 'Problem' => __('Problem')],
                            {$audit}
                            ['class' => 'form-control select2', 'placeholder' => __('messages.please_select')],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('audit_note', __('{$moduleNameLower}::lang.audit_note') . ':') !!}
                        {!! Form::text('audit_note', null, [
                            'class' => 'form-control',
                            'placeholder' => __('{$moduleNameLower}::lang.audit_note'),
                            'rows' => '4',
                        ]) !!}
                    </div>
                </div>
            </div>
            @if (!empty(\$audit))
                <div class="row">
                    <div class="col-md-12">
                        <strong>{{ __('{$moduleNameLower}::lang.activity_audit') }}:</strong><br>
                        @includeIf('activity_log.activity_audit', ['activity_type' => 'sell'])
                    </div>
                </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('messages.update')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.cancel')</button>
        </div>
    </div>
</div>

EOT;
            $this->files->put($viewPath, $content);
        }
    }
}
