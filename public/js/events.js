function previewEventImage(event) {
    const reader = new FileReader();
    reader.onload = function () {
        document.getElementById('previewImage').src = reader.result;
    };
    reader.readAsDataURL(event.target.files[0]);
}

function confirmDelete() {
    return confirm("Are you sure you want to delete this event?");
}

// =============================
// OPEN EVENT MODAL
// =============================
function openEventModal(eventId) {
    const modal = document.getElementById('eventModal');
    const modalBody = document.getElementById('eventModalBody');
    const eventData = document.getElementById('event-data-' + eventId);

    if (!modal || !modalBody || !eventData) return;

    // Inject content
    modalBody.innerHTML = eventData.innerHTML;

    // OPEN modal (NEW WAY)
    modal.classList.add('active');

    // Lock background scroll
    document.body.style.overflow = 'hidden';
}

// =============================
// CLOSE EVENT MODAL
// =============================
function closeEventModal() {
    const modal = document.getElementById('eventModal');

    if (!modal) return;

    // CLOSE modal (NEW WAY)
    modal.classList.remove('active');

    // Restore scroll
    document.body.style.overflow = '';
}

// =============================
// CLOSE ON BACKDROP CLICK
// =============================
document.addEventListener('click', function (e) {
    const modal = document.getElementById('eventModal');

    if (!modal) return;

    if (e.target === modal) {
        closeEventModal();
    }
});

// =============================
// CLOSE ON ESC KEY
// =============================
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        closeEventModal();
    }
});
