document.addEventListener("DOMContentLoaded", function() {
    var modalOverlay = document.getElementById('modalOverlay');
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.classList.remove('active');
    }
    document.body.style.overflow = 'auto';

    // View Inquiry Modal
    const inquiryModal = document.getElementById('inquiryModal');
    const closeInquiry = document.getElementById('closeInquiry');
    const sendReply = document.getElementById('sendReply');
    const inquiriesTableBody = document.getElementById('inquiriesTableBody');

    // Add event listeners for action buttons
    if (inquiriesTableBody) {
        inquiriesTableBody.addEventListener('click', function(e) {
            const target = e.target.closest('.action-button');
            if (!target) return;

            const inquiryId = target.getAttribute('data-id');
            
            if (target.classList.contains('view-button')) {
                showInquiryDetails(inquiryId);
            } else if (target.classList.contains('delete-button')) {
                if (confirm('Are you sure you want to delete this inquiry?')) {
                    deleteInquiry(inquiryId);
                }
            }
        });
    }

    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                const openModal = document.querySelector('.modal.active, .modal-container.active');
                if (openModal) {
                    closeModal(openModal);
                }
            }
        });
    }

    function showInquiryDetails(inquiryId) {
        fetch(`get_inquiry.php?id=${inquiryId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('inquirySubject').textContent = data.subject;
                document.getElementById('inquiryCustomer').textContent = `${data.Fname} ${data.Lname}`;
                document.getElementById('inquiryDate').textContent = new Date(data.dateSubmitted).toLocaleDateString();
                document.getElementById('inquiryMessage').textContent = data.msg;
                document.getElementById('inquiryEmail').textContent = data.email;
                document.getElementById('inquiryPhone').textContent = data.Pnum;
                
                if (inquiryModal) {
                    inquiryModal.style.display = 'block';
                    if (modalOverlay) {
                        modalOverlay.style.display = 'flex';
                        modalOverlay.style.visibility = 'visible';
                        setTimeout(() => {
                            inquiryModal.classList.add('active');
                            modalOverlay.classList.add('active');
                        }, 10);
                        document.body.style.overflow = 'hidden';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load inquiry details');
            });
    }

    function closeModal(modal) {
        if (modal) {
            modal.classList.remove('active');
            if (modalOverlay) {
                modalOverlay.classList.remove('active');
                setTimeout(() => {
                    modal.style.display = 'none';
                    modalOverlay.style.display = 'none';
                    modalOverlay.style.visibility = 'hidden';
                    hideOverlayIfNoModal();
                }, 300);
            }
        }
    }

    function deleteInquiry(inquiryId) {
        fetch('delete_inquiry.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: inquiryId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${inquiryId}"]`);
                if (row) {
                    row.remove();
                }
            } else {
                alert('Error deleting inquiry: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the inquiry');
        });
    }

    // Modal close event listeners
    if (closeInquiry) {
        closeInquiry.addEventListener('click', () => closeModal(inquiryModal));
    }

    // Send reply event listener
    if (sendReply) {
        sendReply.addEventListener('click', function() {
            const replyText = document.getElementById('replyText').value;
            if (replyText.trim() !== '') {
                alert('Reply sent successfully!');
                document.getElementById('replyText').value = '';
                closeModal(inquiryModal);
            } else {
                alert('Please enter a reply message.');
            }
        });
    }

    function hideOverlayIfNoModal() {
        const anyOpen = Array.from(document.querySelectorAll('.modal, .modal-container'))
            .some(m => m.classList.contains('active') || m.style.display === 'block' || m.style.display === 'flex');
        if (modalOverlay) {
            if (!anyOpen) {
                modalOverlay.style.display = 'none';
                modalOverlay.style.visibility = 'hidden';
                modalOverlay.classList.remove('active');
                document.body.style.overflow = 'auto';
            } else {
                modalOverlay.style.display = 'flex';
                modalOverlay.style.visibility = 'visible';
                modalOverlay.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        }
    }
});
  