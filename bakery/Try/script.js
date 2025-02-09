// Shared functionality
function getContent() {
    return JSON.parse(localStorage.getItem('websiteContent')) || [
        { 
            title: 'Welcome', 
            body: 'This is the main content of the website. It can be updated by the admin.',
            imageUrl: '/placeholder.svg?height=200&width=200',  //adjust the image
            stock: 10
        }
    ];
}

function setContent(content) {
    localStorage.setItem('websiteContent', JSON.stringify(content));    //parse
}

function displayContent(contentContainer) {
    const content = getContent();
    contentContainer.innerHTML = ''; // Clear existing content

    content.forEach((item, index) => {
        const section = document.createElement('div');
        section.className = 'content-item';
        section.innerHTML = `
            <img src="${item.imageUrl || '/placeholder.svg?height=200&width=200'}" alt="${item.title}">
            <div class="content-details">
                <h3>${item.title}</h3>
                <p>${item.body}</p>
                <p class="stock-info">In stock: ${item.stock}</p>
            </div>
        `;
        contentContainer.appendChild(section);
    });
}

// User view functionality
if (document.getElementById('content-container')) {
    const contentContainer = document.getElementById('content-container');
    displayContent(contentContainer);
}

// Admin view functionality
if (document.getElementById('admin-login')) {
    const adminPassword = 'pangetmanghack'; // This is not secure, just for demonstration

    document.getElementById('admin-login').addEventListener('submit', function(e) {
        e.preventDefault();
        const password = document.getElementById('password').value;
        if (password === adminPassword) {
            document.getElementById('login-form').style.display = 'none';
            document.getElementById('admin-panel').style.display = 'block';
            loadAdminContent();
        } else {
            alert('Incorrect password');
        }
    });

    function loadAdminContent() {
        const content = getContent();
        
        // Display content items with edit and remove buttons
        const contentList = document.getElementById('content-list');
        contentList.innerHTML = '';
        content.forEach((item, index) => {
            const listItem = document.createElement('div');
            listItem.className = 'content-item';
            listItem.innerHTML = `
                <img src="${item.imageUrl || '/placeholder.svg?height=100&width=100'}" alt="${item.title}">
                <div class="content-details">
                    <h3>${item.title}</h3>
                    <p>${item.body}</p>
                    <p>Stock: ${item.stock}</p>
                    <button class="edit-content" data-index="${index}">Edit</button>
                    <button class="remove-content" data-index="${index}">Remove</button>
                </div>
            `;
            contentList.appendChild(listItem);
        });

        // Add event listeners to edit and remove buttons
        document.querySelectorAll('.edit-content').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                editContent(index);
            });
        });

        document.querySelectorAll('.remove-content').forEach(button => {
            button.addEventListener('click', function() {
                const index = parseInt(this.getAttribute('data-index'));
                removeContent(index);
            });
        });
    }

    function editContent(index) {
        const content = getContent();
        const item = content[index];
        document.getElementById('new-content-title').value = item.title;
        document.getElementById('new-content-body').value = item.body;
        document.getElementById('new-content-image').value = item.imageUrl || '';
        document.getElementById('new-content-stock').value = item.stock;
        document.getElementById('add-new-content').textContent = 'Update Content';
        document.getElementById('add-new-content').onclick = function() {
            updateContent(index);
        };
    }

    function updateContent(index) {
        const content = getContent();
        content[index] = {
            title: document.getElementById('new-content-title').value.trim(),
            body: document.getElementById('new-content-body').value.trim(),
            imageUrl: document.getElementById('new-content-image').value.trim(),
            stock: parseInt(document.getElementById('new-content-stock').value) || 0
        };
        setContent(content);
        resetForm();
        loadAdminContent();
        document.getElementById('update-message').textContent = 'Content updated successfully!';
    }

    function removeContent(index) {
        const content = getContent();
        content.splice(index, 1);
        setContent(content);
        loadAdminContent();
        document.getElementById('update-message').textContent = 'Content removed successfully!';
    }

    function resetForm() {
        document.getElementById('new-content-title').value = '';
        document.getElementById('new-content-body').value = '';
        document.getElementById('new-content-image').value = '';
        document.getElementById('new-content-stock').value = '';
        document.getElementById('add-new-content').textContent = 'Add New Content';
        document.getElementById('add-new-content').onclick = addNewContent;
    }

    function addNewContent() {
        const newTitle = document.getElementById('new-content-title').value.trim();
        const newBody = document.getElementById('new-content-body').value.trim();
        const newImageUrl = document.getElementById('new-content-image').value.trim();
        const newStock = parseInt(document.getElementById('new-content-stock').value) || 0;

        if (newTitle && newBody) {
            const content = getContent();
            content.push({ 
                title: newTitle, 
                body: newBody, 
                imageUrl: newImageUrl,
                stock: newStock
            });
            setContent(content);
            loadAdminContent();
            resetForm();
            document.getElementById('update-message').textContent = 'New content added successfully!';
        } else {
            alert('Please enter both title and body for the new content.');
        }
    }

    document.getElementById('add-new-content').onclick = addNewContent;
}

