<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Awaz - SuperAdmin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-500: #6b7280;
            --gray-700: #374151;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-700);
            line-height: 1.5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--gray-300);
            color: var(--gray-700);
        }

        .btn-outline:hover {
            background-color: var(--gray-100);
        }

        .search-container {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .search-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .search-box {
            display: flex;
            gap: 1rem;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .search-hint {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.5rem;
        }

        .view-toggle {
            display: flex;
            background-color: var(--gray-100);
            border-radius: 0.375rem;
            padding: 0.25rem;
            margin-bottom: 1.5rem;
        }

        .view-toggle-btn {
            flex: 1;
            padding: 0.5rem;
            text-align: center;
            cursor: pointer;
            border-radius: 0.25rem;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .view-toggle-btn.active {
            background-color: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .users-list {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .users-list-header {
            display: grid;
            grid-template-columns: 80px 1fr 1fr 1fr 1fr;
            padding: 1rem 1.5rem;
            background-color: var(--gray-100);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-500);
        }

        .user-row {
            display: grid;
            grid-template-columns: 80px 1fr 1fr 1fr 1fr;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .user-row:hover {
            background-color: var(--gray-50);
        }

        .user-row.selected {
            background-color: #e0e7ff;
        }

        .user-cell {
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }

        .user-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-top: 1.5rem;
            display: none;
        }

        .user-card.active {
            display: block;
        }

        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .user-title {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .user-subtitle {
            color: var(--gray-500);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .verified {
            background-color: #dcfce7;
            color: #166534;
        }

        .unverified {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .user-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .detail-group {
            margin-bottom: 0;
        }

        .detail-label {
            font-size: 0.75rem;
            color: var(--gray-500);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .detail-value.empty {
            color: var(--gray-500);
            font-style: italic;
        }

        .action-bar {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .document-section {
            margin-top: 1.5rem;
        }

        .document-title {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
            color: var(--gray-700);
        }

        .document-images {
            display: flex;
            gap: 1rem;
        }

        .document-image {
            width: 180px;
            height: 120px;
            object-fit: cover;
            border-radius: 0.375rem;
            border: 1px solid var(--gray-200);
            background-color: var(--gray-100);
        }

        .loading {
            display: flex;
            justify-content: center;
            padding: 2rem;
            color: var(--gray-500);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--gray-500);
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .page-btn {
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            border: 1px solid var(--gray-300);
            background-color: white;
            cursor: pointer;
        }

        .page-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">Awaz SuperAdmin</div>
            <button class="btn btn-outline" id="refresh-btn">Refresh Data</button>
        </header>

        <div class="search-container">
            <div class="search-title">Search User</div>
            <div class="search-box">
                <input type="text" class="search-input" id="search-input"
                       placeholder="Search by ID, username, phone, or citizenship number">
                <button class="btn btn-primary" id="search-btn">Search</button>
            </div>
            <div class="search-hint">
                You can search by: User ID, Username, Phone Number, or Citizenship Number
            </div>
        </div>

        <div class="view-toggle">
            <button class="view-toggle-btn active" id="list-view-btn">List View</button>
            <button class="view-toggle-btn" id="search-view-btn">Search Results</button>
        </div>

        <div id="list-view-container">
            <div class="users-list">
                <div class="users-list-header">
                    <div class="user-cell">ID</div>
                    <div class="user-cell">Username</div>
                    <div class="user-cell">Email</div>
                    <div class="user-cell">Citizenship No.</div>
                    <div class="user-cell">Phone</div>
                </div>
                <div id="users-list-body">
                    <div class="loading">Loading users...</div>
                </div>
            </div>

            <div class="pagination" id="pagination">
                <!-- Pagination will be inserted here -->
            </div>
        </div>

        <div id="search-view-container" style="display: none;">
            <div class="users-list">
                <div class="users-list-header">
                    <div class="user-cell">ID</div>
                    <div class="user-cell">Username</div>
                    <div class="user-cell">Email</div>
                    <div class="user-cell">Citizenship No.</div>
                    <div class="user-cell">Phone</div>
                </div>
                <div id="search-results-body">
                    <div class="empty-state">Enter search criteria to find users</div>
                </div>
            </div>
        </div>

        <div class="user-card" id="user-detail-card">
            <!-- User details will be inserted here -->
        </div>
    </div>

    <!-- [Previous HTML head and body structure remains unchanged] -->

<!-- [Previous HTML head and body structure remains unchanged] -->

<script>
    // API Configuration
    const API_URL = '/api/users';
    const ITEMS_PER_PAGE = 10;

    // DOM Elements
    const usersListBody = document.getElementById('users-list-body');
    const searchResultsBody = document.getElementById('search-results-body');
    const userDetailCard = document.getElementById('user-detail-card');
    const refreshBtn = document.getElementById('refresh-btn');
    const searchInput = document.getElementById('search-input');
    const searchBtn = document.getElementById('search-btn');
    const pagination = document.getElementById('pagination');
    const listViewBtn = document.getElementById('list-view-btn');
    const searchViewBtn = document.getElementById('search-view-btn');
    const listViewContainer = document.getElementById('list-view-container');
    const searchViewContainer = document.getElementById('search-view-container');

    // Current state
    let currentPage = 1;
    let totalPages = 1;
    let selectedUserId = null;
    let isSearchView = false;

    // Format date for display
    const formatDate = (dateString) => {
        const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
        return new Date(dateString).toLocaleDateString(undefined, options);
    };

    // Render user list row
    const renderUserRow = (user) => {
        const isSelected = selectedUserId === user.user_id;
        return `
            <div class="user-row ${isSelected ? 'selected' : ''}" data-user-id="${user.user_id}">
                <div class="user-cell">${user.user_id}</div>
                <div class="user-cell">@${user.username}</div>
                <div class="user-cell">${user.email || '-'}</div>
                <div class="user-cell">${user.citizenship_id_number || '-'}</div>
                <div class="user-cell">${user.phone_number || '-'}</div>
            </div>
        `;
    };

    // Render user details
    const renderUserDetails = (user) => {
        return `
            <div class="user-header">
                <div>
                    <div class="user-title">${user.first_name} ${user.last_name}</div>
                    <div class="user-subtitle">User ID: ${user.user_id} | @${user.username}</div>
                </div>
                <span class="status-badge ${user.is_verified ? 'verified' : 'unverified'}">
                    ${user.is_verified ? 'Verified' : 'Unverified'}
                </span>
            </div>

            <div class="user-details">
                <div class="detail-group">
                    <div class="detail-label">Email</div>
                    <div class="detail-value">${user.email || '<span class="empty">Not provided</span>'}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value">${user.phone_number || '<span class="empty">Not provided</span>'}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Gender</div>
                    <div class="detail-value">${user.gender || '<span class="empty">Not provided</span>'}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Location</div>
                    <div class="detail-value">${user.city || '<span class="empty">Not provided</span>'}, ${user.district || '<span class="empty">Not provided</span>'} - Ward ${user.ward || '<span class="empty">Not provided</span>'} (${user.area_name || '<span class="empty">Not provided</span>'})</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Citizenship ID</div>
                    <div class="detail-value">${user.citizenship_id_number || '<span class="empty">Not provided</span>'}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Account Created</div>
                    <div class="detail-value">${user.created_at ? formatDate(user.created_at) : '<span class="empty">Not provided</span>'}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Last Updated</div>
                    <div class="detail-value">${user.updated_at ? formatDate(user.updated_at) : '<span class="empty">Not provided</span>'}</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Activity</div>
                    <div class="detail-value">${user.posts_count || 0} Posts • ${user.likes_count || 0} Likes</div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Terms Accepted</div>
                    <div class="detail-value">${user.agreed_to_terms ? 'Yes' : 'No'}</div>
                </div>
            </div>

            ${user.citizenship_front_image || user.citizenship_back_image ? `
            <div class="document-section">
                <div class="document-title">Citizenship Documents</div>
                <div class="document-images">
                    ${user.citizenship_front_image ? `<img src="${user.citizenship_front_image}" alt="Citizenship Front" class="document-image">` : ''}
                    ${user.citizenship_back_image ? `<img src="${user.citizenship_back_image}" alt="Citizenship Back" class="document-image">` : ''}
                </div>
            </div>
            ` : ''}

            <div class="action-bar">
                <button class="btn btn-success">${user.is_verified ? 'Unverify' : 'Verify'} User</button>
                <button class="btn btn-outline">Edit Profile</button>
                <button class="btn btn-warning">Send Warning</button>
                <button class="btn btn-danger">Delete Account</button>
            </div>
        `;
    };

    // Render pagination
    const renderPagination = () => {
        let paginationHTML = '';

        if (currentPage > 1) {
            paginationHTML += `<button class="page-btn" data-page="${currentPage - 1}">Previous</button>`;
        }

        if (currentPage > 3) {
            paginationHTML += `<button class="page-btn" data-page="1">1</button>`;
            if (currentPage > 4) {
                paginationHTML += `<span class="page-btn">...</span>`;
            }
        }

        for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
            paginationHTML += `<button class="page-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">${i}</button>`;
        }

        if (currentPage < totalPages - 2) {
            if (currentPage < totalPages - 3) {
                paginationHTML += `<span class="page-btn">...</span>`;
            }
            paginationHTML += `<button class="page-btn" data-page="${totalPages}">${totalPages}</button>`;
        }

        if (currentPage < totalPages) {
            paginationHTML += `<button class="page-btn" data-page="${currentPage + 1}">Next</button>`;
        }

        pagination.innerHTML = paginationHTML;

        document.querySelectorAll('.page-btn[data-page]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                currentPage = parseInt(e.target.dataset.page);
                fetchUsers();
            });
        });
    };

    // Fetch users list from Laravel
    const fetchUsers = async () => {
        try {
            usersListBody.innerHTML = '<div class="loading">Loading users...</div>';
            userDetailCard.classList.remove('active');

            const response = await fetch(`${API_URL}?page=${currentPage}&limit=${ITEMS_PER_PAGE}`);
            const data = await response.json();

            totalPages = data.pages;

            if (data.users.length === 0) {
                usersListBody.innerHTML = '<div class="empty-state">No users found</div>';
            } else {
                usersListBody.innerHTML = data.users.map(user => renderUserRow(user)).join('');

                document.querySelectorAll('.user-row').forEach(row => {
                    row.addEventListener('click', async () => {
                        selectedUserId = parseInt(row.dataset.userId);

                        document.querySelectorAll('.user-row').forEach(r => {
                            r.classList.remove('selected');
                        });
                        row.classList.add('selected');

                        await fetchUserDetails(selectedUserId);
                    });
                });
            }

            renderPagination();
        } catch (error) {
            usersListBody.innerHTML = `<div class="loading">Error loading users: ${error.message}</div>`;
            console.error('Error fetching users:', error);
        }
    };

    // Fetch user by search term
    const fetchUser = async (searchTerm) => {
        try {
            searchResultsBody.innerHTML = '<div class="loading">Searching user...</div>';
            userDetailCard.classList.remove('active');

            const response = await fetch(`/api/users/search?q=${encodeURIComponent(searchTerm)}`);
            const data = await response.json();

            console.log('Search response:', data); // Log the response for debugging

            if (!data.users || data.users.length === 0) {
                searchResultsBody.innerHTML = '<div class="empty-state">' + (data.message || 'No user found with that identifier') + '</div>';
            } else {
                searchResultsBody.innerHTML = data.users.map(user => renderUserRow(user)).join('');

                document.querySelectorAll('.user-row').forEach(row => {
                    row.addEventListener('click', async () => {
                        selectedUserId = parseInt(row.dataset.userId);

                        document.querySelectorAll('.user-row').forEach(r => {
                            r.classList.remove('selected');
                        });
                        row.classList.add('selected');

                        await fetchUserDetails(selectedUserId);
                    });
                });

                if (data.users.length === 1) {
                    selectedUserId = data.users[0].user_id;
                    document.querySelector(`.user-row[data-user-id="${selectedUserId}"]`).classList.add('selected');
                    await fetchUserDetails(selectedUserId);
                }
            }
        } catch (error) {
            searchResultsBody.innerHTML = `<div class="loading">Error searching user: ${error.message}</div>`;
            console.error('Error searching user:', error);
        }
    };

    // Fetch single user details
    const fetchUserDetails = async (userId) => {
        try {
            userDetailCard.innerHTML = '<div class="loading">Loading user details...</div>';
            userDetailCard.classList.add('active');

            const response = await fetch(`/api/users/${userId}`);
            const data = await response.json();

            if (response.ok) {
                userDetailCard.innerHTML = renderUserDetails(data);
                userDetailCard.scrollIntoView({ behavior: 'smooth' });
            } else {
                userDetailCard.innerHTML = '<div class="loading">User not found</div>';
            }
        } catch (error) {
            userDetailCard.innerHTML = `<div class="loading">Error loading user details: ${error.message}</div>`;
            console.error('Error fetching user details:', error);
        }
    };

    // Handle search
    const handleSearch = () => {
        const searchTerm = searchInput.value.trim();

        if (!searchTerm) {
            searchResultsBody.innerHTML = '<div class="empty-state">Enter search criteria to find users</div>';
            switchToListView();
            return;
        }

        switchToSearchView();
        fetchUser(searchTerm);
    };

    // Switch to list view
    const switchToListView = () => {
        isSearchView = false;
        listViewBtn.classList.add('active');
        searchViewBtn.classList.remove('active');
        listViewContainer.style.display = 'block';
        searchViewContainer.style.display = 'none';
    };

    // Switch to search view
    const switchToSearchView = () => {
        isSearchView = true;
        listViewBtn.classList.remove('active');
        searchViewBtn.classList.add('active');
        listViewContainer.style.display = 'none';
        searchViewContainer.style.display = 'block';
    };

    // Reset view
    const resetView = () => {
        searchInput.value = '';
        switchToListView();
        currentPage = 1;
        selectedUserId = null;
        fetchUsers();
    };

    // Event Listeners
    refreshBtn.addEventListener('click', resetView);
    searchBtn.addEventListener('click', handleSearch);
    listViewBtn.addEventListener('click', switchToListView);
    searchViewBtn.addEventListener('click', () => {
        if (searchInput.value.trim()) {
            switchToSearchView();
        }
    });

    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            handleSearch();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        fetchUsers();
    });
</script>
</body>
</html>

