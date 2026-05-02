<div id="editModal" class="modal-overlay">

    <div class="modal-box modern-modal">
        <h2 class="section-title">Report Details</h2>
        <div class="action-bar">
            <button type="button" onclick="closeEditModal()" class="remove-btn">
                Cancel
            </button>

            <button form="editForm" type="submit" class="save-btn">
                Update
            </button>
        </div>

        <form method="POST" id="editForm" enctype="multipart/form-data" class="announcement-form">
            @csrf
            @method('PUT')

            <input type="hidden" id="editReportId">

            <!-- I. DETAILS -->


            <div class="form-grid">
                <select name="category_id" id="editCategory" class="form-input">
                    <option value="">Select Category</option>
                    @foreach ($categories as $cat)
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
                        <option value="{{ $cat->id }}">
                            {{ $cat->name }}@if ($durationLabel)
                                ({{ $durationLabel }})
                            @endif
                        </option>
                    @endforeach
                </select>

                <textarea name="description" id="editDescription" class="form-input"
                    placeholder="Description"></textarea>
            </div>

            <!-- II. FILES -->
            <h4 class="section-title mt-4">Attachments</h4>

            <input type="hidden" name="existing_files" id="existingFilesInput">

            <div class="form-group">
                <label class="text-muted">Existing Files</label>
                <div id="existingFiles" class="preview-grid"></div>
            </div>

            <div class="form-group">
                <label class="text-muted">Upload New Files</label>
                <input type="file" id="editFileInput" name="attachment[]" multiple class="form-input">
                <div id="editPreview" class="preview-grid"></div>
            </div>

        </form>
    </div>
</div>
