@php
    // Use the fully qualified class names for the models
    $projectModel = \Modules\Project\Entities\Project::class;
    
    // Fetch the latest project under the category "វេនសម្អាត" for the current business
    $project = $projectModel::where('business_id', session('business.id'))
        ->whereHas('categories', function ($query) {
            $query->where('name', 'វេនសម្អាត');
        })
        ->with(['members']) // Eager load project members
        ->latest()
        ->first();

    // Check if the project exists and if the current user (available as $user from parent view) is a member
    $userIsMember = false;
    if ($project) {
        // The $user variable is expected to be available from the show.blade.php view
        $userIsMember = $project->members->contains($user->id);
    }
@endphp

@if ($project && $userIsMember)
    {{-- Load required libraries for PDF generation --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <style>
        /* General styles for the avatars - Extra large size for A4 landscape */
        .task-member-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin: 0;
            object-fit: cover;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            display: block;
        }
        .task-member {
            display: inline-block;
            margin: 0;
            padding: 2px;
        }
        .task-members-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 0;
            padding: 0;
            margin: 0;
        }
        /* Card styling - remove background, add border only */
        .team-up-card {
            border: 3px solid #3c8dbc;
            border-radius: 8px;
            background-color: #ffffff !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .team-up-card .inner {
            background-color: #ffffff !important;
        }
        .team-up-card .inner p {
            color: #333333 !important;
        }
        /* Styles for cloned element during PDF generation */
        .team-up-download-clone {
            position: fixed;
            left: -9999px;
            top: 0;
            width: auto;
            height: auto;
            z-index: -9999;
            background-color: #ffffff;
        }
        .team-up-download-clone .no-print {
            display: none !important;
        }

        /* NEW LAYOUT (added as you requested) ------------------------------ */
        .member-layout {
            display: grid;
            gap: 10px;
            justify-content: center;
        }
        .layout-1 { grid-template-columns: 1fr; }
        .layout-2 { grid-template-columns: repeat(2, 1fr); }
        .layout-3 { grid-template-columns: repeat(2, 120px); }
        .layout-3 .member:nth-child(3) {
            grid-column: span 2;
            justify-self: center;
        }
        .layout-4 { grid-template-columns: repeat(2, 1fr); }

        .member img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #fff;
            object-fit: cover;
            box-shadow: 0 3px 6px rgba(0,0,0,.3);
        }
        /* ------------------------------------------------------------------ */

    </style>

    <div id="team-up-printable-area">
        <div class="container-fluid" style="padding: 10px; width: 100%;">
            <div class="row" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px;">
                {{-- Invisible spacer --}}
                <div style="width: 80px;"></div>
                
                {{-- Centered Title --}}
                <div class="text-center" style="flex-grow: 1;">
                    <h3 class="modal-title">{{ $project->name }}</h3>
                </div>

                {{-- Download PDF Button --}}
                <div style="width: 80px;" class="text-right no-print">
                    <button type="button" class="btn btn-primary btn-sm no-print" id="download-team-up-pdf" onclick="downloadTeamUpPDF()">
                        <i class="fa fa-download"></i> @lang('messages.print')
                    </button>
                </div>
            </div>
            
            @php
                $tasks = $project->tasks()->with('members.media')->orderBy('due_date')->take(6)->get();
            @endphp

            <div class="row" style="margin: 0 -20px;">
                @if($tasks->isEmpty())
                    <div class="col-md-12">
                        <p class="text-center">No tasks found for this project.</p>
                    </div>
                @else
                    @foreach ($tasks as $task)
                        @php
                            $count = $task->members->count();
                            $layoutClass = "layout-" . min($count, 4);
                        @endphp

                        <div class="col-md-4 col-sm-6 col-xs-12" style="padding: 20px;">
                            <div class="small-box team-up-card">
                                <div class="inner" style="min-height: 350px; padding: 15px;">
                                    <p style="font-weight: bold; font-size: 20px; margin-bottom: 15px; color: #333333;">
                                        {{ Str::limit($task->subject, 50) }}
                                    </p>

                                    <hr style="margin-top: 10px; margin-bottom: 10px; border-color: #3c8dbc; border-width: 2px;">

                                    <!-- NEW LAYOUT PLACED HERE (ONLY CHANGE) -->
                                    <div class="member-layout {{ $layoutClass }}">
                                        @foreach($task->members->take(4) as $member)
                                            <div class="member">
                                                @if(isset($member->media->display_url))
                                                    <img src="{{ $member->media->display_url }}">
                                                @else
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($member->first_name) }}&size=120&background=random">
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    <!-- END NEW LAYOUT -->

                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <!-- (ALL YOUR JS BELOW REMAINS 100% UNCHANGED) -->
    <script>
        function downloadTeamUpPDF() {
            const button = document.getElementById('download-team-up-pdf');
            if (!button) {
                console.error('Download button not found');
                return;
            }
            
            const originalText = button.innerHTML;
            
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> @lang("messages.print")';
            
            const element = document.getElementById('team-up-printable-area');
            if (!element) {
                console.error('Element not found');
                button.disabled = false;
                button.innerHTML = originalText;
                return;
            }
            
            const projectName = '{{ $project->name }}';
            
            try {
                const downloadButtonDiv = element.querySelector('.no-print');
                const originalDisplay = downloadButtonDiv ? downloadButtonDiv.style.display : '';
                
                if (downloadButtonDiv) {
                    downloadButtonDiv.style.display = 'none';
                }
                
                const originalWidth = element.style.width;
                const originalMaxWidth = element.style.maxWidth;
                
                const images = element.querySelectorAll('img');
                const imagePromises = Array.from(images).map(img => {
                    if (img.complete) {
                        return Promise.resolve();
                    }
                    return new Promise((resolve) => {
                        img.onload = resolve;
                        img.onerror = resolve;
                        setTimeout(resolve, 2000);
                    });
                });
                
                Promise.all(imagePromises).then(() => {
                    setTimeout(() => {
                        element.style.width = '100%';
                        element.style.maxWidth = '100%';
                        
                        const renderedWidth = element.offsetWidth || element.scrollWidth;
                        const renderedHeight = element.scrollHeight;
                        
                        const scale = 3;
                        
                        html2canvas(element, {
                            scale: scale,
                            useCORS: true,
                            allowTaint: false,
                            backgroundColor: '#ffffff',
                            logging: false,
                        }).then(function(canvas) {
                            
                        const imgWidth = canvas.width;
                        const imgHeight = canvas.height;
                        
                        const a4Width = 297;
                        const a4Height = 210;
                        
                        const margin = 10;
                        const availableWidth = a4Width - (2 * margin);
                        const availableHeight = a4Height - (2 * margin);
                        
                        const pixelsPerMM = 96 / 25.4;
                        const imgWidthMM = imgWidth / pixelsPerMM;
                        const imgHeightMM = imgHeight / pixelsPerMM;
                        
                        const imgAspectRatio = imgHeightMM / imgWidthMM;
                        const availableAspectRatio = availableHeight / availableWidth;
                        
                        let pdfWidth, pdfHeight;
                        if (imgAspectRatio > availableAspectRatio) {
                            pdfHeight = availableHeight;
                            pdfWidth = availableHeight / imgAspectRatio;
                        } else {
                            pdfWidth = availableWidth;
                            pdfHeight = availableWidth * imgAspectRatio;
                        }
                        
                        const { jsPDF } = window.jspdf;
                        const pdf = new jsPDF({
                            orientation: 'landscape',
                            unit: 'mm',
                            format: 'a4'
                        });
                        
                        const xOffset = margin + (availableWidth - pdfWidth) / 2;
                        const yOffset = margin + (availableHeight - pdfHeight) / 2;
                            
                            const imgData = canvas.toDataURL('image/png', 1.0);
                            
                            pdf.addImage(imgData, 'PNG', xOffset, yOffset, pdfWidth, pdfHeight, undefined, 'FAST');
                            
                            const fileName = 'Team_UP_' + projectName.replace(/[^a-z0-9]/gi, '_') + '_' 
                                + new Date().toISOString().split('T')[0] + '.pdf';
                            
                            pdf.save(fileName);
                            
                            element.style.width = originalWidth;
                            element.style.maxWidth = originalMaxWidth;
                            
                            if (downloadButtonDiv) {
                                downloadButtonDiv.style.display = originalDisplay;
                            }
                            
                            button.disabled = false;
                            button.innerHTML = originalText;

                        }).catch(function(error) {
                            console.error('Error generating PDF:', error);
                            alert('Error generating PDF: ' + (error.message || 'Unknown error.'));
                            
                            element.style.width = originalWidth;
                            element.style.maxWidth = originalMaxWidth;
                            
                            if (downloadButtonDiv) {
                                downloadButtonDiv.style.display = originalDisplay;
                            }
                            
                            button.disabled = false;
                            button.innerHTML = originalText;
                        });
                    }, 100);
                });
                
            } catch (error) {
                console.error('Error initiating PDF download:', error);
                alert('Error initiating PDF download: ' + (error.message || 'Unknown error'));
                
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
    </script>
@else
    {{-- Default content if user is not a member or project doesn't exist --}}
    <div>
        <h3>Team UP Content</h3>
        <p>This is the content for the Team UP section.</p>
    </div>
@endif
