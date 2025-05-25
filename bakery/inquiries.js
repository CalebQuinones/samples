document.addEventListener("DOMContentLoaded", function() {
    const modalOverlay = document.getElementById('modalOverlay');
    const modal = document.getElementById('inquiryModal');
    
    // Initialize modal overlay
    if (modalOverlay) {
        modalOverlay.style.display = 'none';
        modalOverlay.style.visibility = 'hidden';
    }

    // Show modal function
    window.showModal = function(modal) {
        if (!modal) return;
        
        // Reset any existing modals
        document.querySelectorAll('.modal.active').forEach(m => {
            m.classList.remove('active');
        });
        
        // Show modal overlay first
        if (modalOverlay) {
            modalOverlay.style.display = 'flex';
            modalOverlay.style.visibility = 'visible';
            // Force reflow
            modalOverlay.offsetHeight;
            modalOverlay.classList.add('active');
        }
        
        // Show modal
        modal.style.display = 'block';
        // Force reflow
        modal.offsetHeight;
        modal.classList.add('active');
        
        // Prevent body scrolling
        document.body.style.overflow = 'hidden';
    };

    // Close modal function
    window.closeModal = function(modal) {
        if (!modal || !modalOverlay) return;
        
        // Remove active classes
        modal.classList.remove('active');
        modalOverlay.classList.remove('active');
        
        // Hide modal after transition
        setTimeout(() => {
            modal.style.display = 'none';
            hideOverlayIfNoModal();
        }, 300);
        
        // Restore body scrolling
        document.body.style.overflow = '';
    };

    // Hide overlay if no modals are active
    function hideOverlayIfNoModal() {
        if (!modalOverlay) return;
        
        const hasActiveModal = document.querySelector('.modal.active');
        if (!hasActiveModal) {
            modalOverlay.style.display = 'none';
            modalOverlay.style.visibility = 'hidden';
        }
    }

    // Close modal when clicking outside
    if (modalOverlay) {
        modalOverlay.addEventListener('click', function(e) {
            if (e.target === modalOverlay) {
                const activeModal = document.querySelector('.modal.active');
                if (activeModal) {
                    closeModal(activeModal);
                }
            }
        });
    }

    // Close modal when clicking close button
    const closeButtons = document.querySelectorAll('.close-modal');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                closeModal(modal);
            }
        });
    });

    // View Inquiry Modal
    const inquiryModal = document.getElementById('inquiryModal');
    const closeInquiry = document.getElementById('closeInquiry');
    const sendReply = document.getElementById('sendReply');
    const inquiriesTableBody = document.getElementById('inquiriesTableBody');

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
                    showModal(inquiryModal);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to load inquiry details');
            });
    }

    // Add event listeners for action buttons
    if (inquiriesTableBody) {
        inquiriesTableBody.addEventListener('click', function(e) {
            const target = e.target.closest('.action-button');
            if (!target) return;

            const inquiryId = target.getAttribute('data-id');
            
            if (target.classList.contains('view-button')) {
                showInquiryDetails(inquiryId);
            } else if (target.classList.contains('delete-button')) {
                if (confirm('Are you sure you want to archive this inquiry?')) {
                    archiveInquiry(inquiryId);
                }
            }
        });
    }

    function archiveInquiry(inquiryId) {
        fetch('archive_inquiry.php', {
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
                alert('Error archiving inquiry: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while archiving the inquiry');
        });
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
});
