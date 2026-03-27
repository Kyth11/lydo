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
                    <option>CBYDP (Comprehensive Barnagay Youth Development Plan)</option>
                    <option>ABYIP (Annual Barangay Youth Improvement Plan)</option>
                    <option>SK Annual Budget</option>
                    <option>Statement of Reciepts</option>
                    <option>Katipunan ng Kabataan (KK) Assembly Reports</option>
                    <option>Linggo ng Kabataan Reports</option>
                    <option>Accomplishment Reports</option>
                    <option>SK Resolution and Ordinances</option>
                    <option>Attendance and Minutes of SK Meetings</option>
                    <option>M & E</option>
                    <option>Special Reports</option>
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
