<div id="editModal" class="modal-overlay">

    <div class="modal-box modern-modal">
        <h2 class="section-title">Report Details</h2>
        <div class="action-bar">
            <button type="button" onclick="closeModal('editModal')" class="remove-btn">
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
                <select name="category" id="editCategory" class="form-input">
                    <option value="CBYDP (Comprehensive Barangay Youth Development Plan)">CBYDP (Comprehensive Barangay Youth Development Plan)</option>
                    <option value="ABYIP (Annual Barangay Youth Improvement Plan)">ABYIP (Annual Barangay Youth Improvement Plan)</option>
                    <option value="SK Annual Budget">SK Annual Budget</option>
                    <option value="Statement of Receipts">Statement of Receipts</option>
                    <option value="Katipunan ng Kabataan (KK) Assembly Reports">Katipunan ng Kabataan (KK) Assembly Reports</option>
                    <option value="Linggo ng Kabataan Reports">Linggo ng Kabataan Reports</option>
                    <option value="Accomplishment Reports">Accomplishment Reports</option>
                    <option value="SK Resolution and Ordinances">SK Resolution and Ordinances</option>
                    <option value="Attendance and Minutes of SK Meetings">Attendance and Minutes of SK Meetings</option>
                    <option value="M & E">M & E</option>
                    <option value="Special Reports">Special Reports</option>
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
