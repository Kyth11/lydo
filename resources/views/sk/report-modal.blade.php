<div id="reportModal" class="modal-overlay">
    <div class="modal-box modern-modal">
            <h2 class="section-title">Report Details</h2>
        <!-- ACTION BAR -->
        <div class="action-bar">
            <button type="button" onclick="closeModal('reportModal')" class="remove-btn">
                Cancel
            </button>

            <button form="reportForm" type="submit" class="save-btn">
                Submit
            </button>
        </div>

        <!-- FORM -->
        <form id="reportForm"
              method="POST"
              action="{{ route('sk.report.store') }}"
              enctype="multipart/form-data"
              class="announcement-form">

            @csrf

            <!-- I. REPORT DETAILS -->


            <div class="form-grid">
                <select name="category" class="form-input" required>
                    <option value="">Select Category</option>
                    <option>CBYDP (Comprehensive Barnagay Youth Development Plan)</option>
                    <option>ABYIP (Annual Barangay Youth Improvement Plan)</option>
                    <option>SK Annual Budget</option>
                    <option>Statement of Receipts</option>
                    <option>Katipunan ng Kabataan (KK) Assembly Reports</option>
                    <option>Linggo ng Kabataan Reports</option>
                    <option>Accomplishment Reports</option>
                    <option>SK Resolution and Ordinances</option>
                    <option>Attendance and Minutes of SK Meetings</option>
                    <option>M & E</option>
                    <option>Special Reports</option>
                </select>

                <textarea name="description"
                          class="form-input"
                          placeholder="Description (optional)"></textarea>
            </div>

            <!-- II. ATTACHMENTS -->
            <h4 class="section-title mt-4">Attachments</h4>

            <div class="form-group">
                <input type="file"
                       id="fileInput"
                       name="attachment[]"
                       multiple
                       class="form-input">

                <div id="uploadPreview" class="preview-grid mt-2"></div>
            </div>

        </form>

    </div>
</div>
