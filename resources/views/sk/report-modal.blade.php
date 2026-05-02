<div id="reportModal" class="modal-overlay">
    <div class="modal-box modern-modal">
        <h2 class="section-title">Report Details</h2>
        <!-- ACTION BAR -->
        <div class="action-bar">
          <button type="button" onclick="closeReportModal()" class="remove-btn">
                Cancel
            </button>

            <button form="reportForm" type="submit" class="save-btn" onclick="confirmReportSubmit(event)">
                Submit
            </button>
        </div>

        <!-- FORM -->
        <form id="reportForm" method="POST" action="{{ route('sk.report.store') }}" enctype="multipart/form-data"
            class="announcement-form">

            @csrf

            <!-- I. REPORT DETAILS -->


            <div class="form-grid">
                <select name="category_id" id="editCategory" class="form-input">
                    <option value="">Select Category</option>
                    @foreach($categories as $cat)
                        @php
                            $start = $cat->start_date ? \Carbon\Carbon::parse($cat->start_date) : null;
                            $end = $cat->end_date ? \Carbon\Carbon::parse($cat->end_date) : null;
                            $durationLabel = '';
                            
                            if ($start && $end) {
                                if ($start->year === $end->year) {
                                    if ($start->month === $end->month) {
                                        $durationLabel = $start->format('F Y');
                                    } else {
                                        $durationLabel = $start->format('F Y') . ' - ' . $end->format('F Y');
                                    }
                                } else {
                                    $durationLabel = $start->format('Y') . ' - ' . $end->format('Y');
                                }
                            }
                        @endphp
                        <option value="{{ $cat->id }}">{{ $cat->name }}@if($durationLabel) ({{ $durationLabel }})@endif</option>
                    @endforeach
                </select>

                <textarea name="description" class="form-input" placeholder="Description (optional)"></textarea>
            </div>

            <!-- II. ATTACHMENTS -->
            <h4 class="section-title mt-4">Attachments</h4>

            <div class="form-group">
                <input type="file" id="fileInput" name="attachment[]" multiple class="form-input">

                <div id="uploadPreview" class="preview-grid mt-2"></div>
            </div>

        </form>

    </div>
</div>
