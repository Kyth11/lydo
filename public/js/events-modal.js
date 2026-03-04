function openEventModal(eventId) {
    const modal = document.getElementById('eventModal');
    const body = document.getElementById('eventModalBody');
    const data = document.getElementById(`event-data-${eventId}`);

    body.innerHTML = data.innerHTML;
    modal.style.display = 'flex';
}

function closeEventModal() {
    document.getElementById('eventModal').style.display = 'none';
}

window.addEventListener('click', function (e) {
    const modal = document.getElementById('eventModal');
    if (e.target === modal) {
        closeEventModal();
    }
});
