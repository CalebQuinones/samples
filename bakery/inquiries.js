document.addEventListener("DOMContentLoaded", function() {
    // View Inquiry Modal
    const inquiryModal = document.getElementById('inquiryModal');
    const closeInquiry = document.getElementById('closeInquiry');
    const sendReply = document.getElementById('sendReply');

    // Attach event listeners to view buttons
    document.querySelectorAll('.view-button').forEach(button => {
        button.addEventListener('click', function() {
            const inquiryId = this.getAttribute('data-id');
            fetch(`get_inquiry.php?id=${inquiryId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('inquirySubject').textContent = data.subject;
                    document.getElementById('inquiryCustomer').textContent = `${data.Fname} ${data.Lname}`;
                    document.getElementById('inquiryDate').textContent = new Date(data.dateSubmitted).toLocaleDateString();
                    document.getElementById('inquiryMessage').textContent = data.msg;
                    document.getElementById('inquiryEmail').textContent = data.email;
                    document.getElementById('inquiryPhone').textContent = data.Pnum;
                    inquiryModal.style.display = 'block';
                });
        });
    });

    // Attach event listeners to delete buttons
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this inquiry?')) {
                const inquiryId = this.getAttribute('data-id');
                fetch('delete_inquiry.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: inquiryId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('tr').remove();
                    } else {
                        alert('Error deleting inquiry: ' + data.message);
                    }
                });
            }
        });
    });

    // Modal close
    closeInquiry.addEventListener('click', function() {
        inquiryModal.style.display = 'none';
    });

    // Send reply (dummy)
    sendReply.addEventListener('click', function() {
        const replyText = document.getElementById('replyText').value;
        if (replyText.trim() !== '') {
            alert('Reply sent successfully!');
            document.getElementById('replyText').value = '';
            inquiryModal.style.display = 'none';
        } else {
            alert('Please enter a reply message.');
        }
    });
});
  