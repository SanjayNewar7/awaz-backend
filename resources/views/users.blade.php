```html
@extends('superadmin.layout')

@section('title', 'Awaz - SuperAdmin Users')

@section('content')
<style>
    .btn-edit {
        background-color: #007bff; /* Blue background */
        color: #fff; /* White text */
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .view-toggle {
        display: flex;
        background-color: var(--background);
        border-radius: 0.5rem;
        padding: 0.3rem;
        margin-bottom: 2rem;
        gap: 0.5rem;
    }
    .view-toggle-btn {
        flex: 1;
        padding: 0.75rem;
        text-align: center;
        cursor: pointer;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        color: var(--text-light);
    }
    .view-toggle-btn.active {
        background-color: var(--primary-light);
        color: white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .view-toggle-btn:hover {
        background-color: var(--secondary);
        color: white;
    }
    .analytics-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }
    .chart-container {
        background-color: var(--card-bg);
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }
    .chart-container h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 1rem;
        text-align: center;
    }
    .chart-canvas {
        width: 50%;
        height: 200px;
    }
    .recent-users {
        background-color: var(--card-bg);
        border-radius: 0.75rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    .recent-users h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 1rem;
    }
    .recent-users-table {
        width: 100%;
        border-collapse: collapse;
    }
    .recent-users-table th,
    .recent-users-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }
    .recent-users-table th {
        font-weight: 600;
        color: var(--text-light);
        font-size: 0.8rem;
        text-transform: uppercase;
    }
    /* Edit User Modal */
    #edit-user-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        overflow-y: auto;
    }
    #edit-user-modal .modal-content {
        background-color: var(--card-bg);
        border-radius: 1rem;
        padding: 2rem;
        max-width: 800px;
        width: 90%;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        animation: slideIn 0.3s ease-out;
    }
    @keyframes slideIn {
        from { transform: translateY(-50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    #edit-user-modal .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    #edit-user-modal .modal-header h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--primary);
    }
    #edit-user-modal .modal-header .close {
        font-size: 1.5rem;
        font-weight: bold;
        cursor: pointer;
        color: var(--text-light);
        transition: color 0.2s ease;
    }
    #edit-user-modal .modal-header .close:hover {
        color: var(--danger);
    }
    #edit-user-modal .modal-body {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    #edit-user-modal form .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 1.5rem;
    }
    #edit-user-modal form .form-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        border-right: 1px solid var(--border);
        padding-right: 1rem;
    }
    #edit-user-modal form .form-section:last-child {
        border-right: none;
        padding-right: 0;
    }
    #edit-user-modal form .form-section-title {
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }
    #edit-user-modal form .form-group {
        display: flex;
        flex-direction: column;
    }
    #edit-user-modal form .form-group label {
        font-weight: 500;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
        color: var(--text-light);
    }
    #edit-user-modal form .form-group input,
    #edit-user-modal form .form-group select {
        padding: 0.5rem 0.75rem;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-dark);
        background-color: var(--input-bg);
        transition: border 0.2s ease, box-shadow 0.2s ease;
    }
    #edit-user-modal form .form-group input:focus,
    #edit-user-modal form .form-group select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(0,123,255,0.15);
        outline: none;
    }
    #edit-user-modal form .error-message {
        color: var(--danger);
        font-size: 0.75rem;
        margin-top: 0.25rem;
        min-height: 1em;
    }
    #edit-user-modal .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 1.5rem;
    }
    #edit-user-modal .form-actions .btn {
        padding: 0.5rem 1.25rem;
        font-size: 0.95rem;
        border-radius: 0.5rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    #edit-user-modal .form-actions .btn-primary {
        background-color: var(--primary);
        color: #fff;
        border: none;
    }
    #edit-user-modal .form-actions .btn-primary:hover {
        background-color: #0069d9;
    }
    #edit-user-modal .form-actions .btn-outline {
        background-color: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }
    #edit-user-modal .form-actions .btn-outline:hover {
        background-color: var(--primary);
        color: #fff;
    }
    /* Citizenship Images */
    .document-section {
        margin-top: 1.5rem;
    }
    .document-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 1rem;
    }
    .document-images {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    .document-image-container {
        position: relative;
        width: 250px; /* Increased size */
        cursor: pointer;
    }
    .document-image {
        width: 100%;
        height: 150px; /* Increased height */
        object-fit: cover;
        border-radius: 0.5rem;
        border: 1px solid var(--border);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .document-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .document-image-label {
        text-align: center;
        font-size: 0.9rem;
        color: var(--text-light);
        margin-top: 0.5rem;
    }
    /* Full-Screen Image Modal */
    #fullscreen-image-modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2000;
        overflow: auto;
    }
    #fullscreen-image-modal .modal-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }
    #fullscreen-image-modal .fullscreen-image {
        width: 100%;
        height: auto;
        max-height: 80vh;
        object-fit: contain;
        border-radius: 0.5rem;
    }
    #fullscreen-image-modal .close {
        position: absolute;
        top: -30px;
        right: -30px;
        font-size: 2rem;
        font-weight: bold;
        color: #fff;
        cursor: pointer;
        transition: color 0.2s ease;
    }
    #fullscreen-image-modal .close:hover {
        color: var(--danger);
    }
    #fullscreen-image-modal .image-label {
        text-align: center;
        font-size: 1rem;
        color: #fff;
        margin-top: 1rem;
    }
</style>

<header>
    <div class="logo">Users Management</div>
    <button class="btn btn-outline" id="refresh-btn">Refresh Data</button>
</header>

<!-- Analytics Section -->
<div class="analytics-section">
    <div class="chart-container">
        <h3>User Growth (Last 14 Days)</h3>
        <canvas id="userGrowthChart" class="chart-canvas"></canvas>
    </div>
    <div class="chart-container">
        <h3>Verified vs Unverified Users</h3>
        <canvas id="verificationChart" class="chart-canvas"></canvas>
    </div>
</div>

<!-- Recent Users -->
<div class="recent-users">
    <h3>Recently Registered Users</h3>
    <table class="recent-users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Registered At</th>
            </tr>
        </thead>
        <tbody id="recent-users-body">
            <tr><td colspan="3" class="loading">Loading recent users...</td></tr>
        </tbody>
    </table>
</div>

<div class="search-container">
    <div class="search-title">Search User</div>
    <div class="search-box">
        <input type="text" class="search-input" id="search-input" placeholder="Search by phone number">
        <button class="btn btn-primary" id="search-btn">Search</button>
    </div>
    <div class="search-hint">
        You can search by phone number (e.g., 9817262424 or partial like 9817)
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

<!-- Edit User Modal -->
<div class="modal" id="edit-user-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit User Profile</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <form id="edit-user-form">
                <input type="hidden" id="edit-user-id" name="user_id">

                <div class="form-grid">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Basic Information</h3>
                        <div class="form-group">
                            <label for="edit-first_name">First Name</label>
                            <input type="text" id="edit-first_name" name="first_name" required>
                            <div class="error-message" id="edit-first_name_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-last_name">Last Name</label>
                            <input type="text" id="edit-last_name" name="last_name" required>
                            <div class="error-message" id="edit-last_name_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-username">Username</label>
                            <input type="text" id="edit-username" name="username" required>
                            <div class="error-message" id="edit-username_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Email</label>
                            <input type="email" id="edit-email" name="email" required>
                            <div class="error-message" id="edit-email_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-phone_number">Phone Number</label>
                            <input type="tel" id="edit-phone_number" name="phone_number" required>
                            <div class="error-message" id="edit-phone_number_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-gender">Gender</label>
                            <select id="edit-gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="error-message" id="edit-gender_error"></div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Address Information</h3>
                        <div class="form-group">
                            <label for="edit-district">District</label>
                            <input type="text" id="edit-district" name="district" required>
                            <div class="error-message" id="edit-district_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-city">City</label>
                            <input type="text" id="edit-city" name="city" required>
                            <div class="error-message" id="edit-city_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-ward">Ward Number</label>
                            <input type="number" id="edit-ward" name="ward" required>
                            <div class="error-message" id="edit-ward_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-area_name">Area Name</label>
                            <input type="text" id="edit-area_name" name="area_name" required>
                            <div class="error-message" id="edit-area_name_error"></div>
                        </div>
                    </div>

                    <!-- Citizenship Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Citizenship Information</h3>
                        <div class="form-group">
                            <label for="edit-citizenship_id_number">Citizenship ID Number</label>
                            <input type="text" id="edit-citizenship_id_number" name="citizenship_id_number" required>
                            <div class="error-message" id="edit-citizenship_id_number_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="edit-is_verified">Verification Status</label>
                            <select id="edit-is_verified" name="is_verified" required>
                                <option value="0">Unverified</option>
                                <option value="1">Verified</option>
                            </select>
                            <div class="error-message" id="edit-is_verified_error"></div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="cancel-edit">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal" id="confirmation-modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="confirmation-title">Confirm Action</h3>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body">
            <p id="confirmation-message">Are you sure you want to perform this action?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-outline" id="cancel-action">Cancel</button>
            <button class="btn btn-danger" id="confirm-action">Confirm</button>
        </div>
    </div>
</div>

<!-- Full-Screen Image Modal -->
<div class="modal" id="fullscreen-image-modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <img id="fullscreen-image" class="fullscreen-image" src="" alt="Full-screen Image">
        <div id="fullscreen-image-label" class="image-label"></div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
    const editUserModal = document.getElementById('edit-user-modal');
    const confirmationModal = document.getElementById('confirmation-modal');
    const fullscreenImageModal = document.getElementById('fullscreen-image-modal');
    const closeModalButtons = document.querySelectorAll('.modal .close');
    const cancelEditBtn = document.getElementById('cancel-edit');
    const cancelActionBtn = document.getElementById('cancel-action');
    const confirmActionBtn = document.getElementById('confirm-action');
    const editUserForm = document.getElementById('edit-user-form');

    // Current state
    let currentPage = 1;
    let totalPages = 1;
    let selectedUserId = null;
    let isSearchView = false;
    let currentAction = null;
    let actionUserId = null;

    // Chart instances
    let userGrowthChart = null;
    let verificationChart = null;

    // Normalize image URL for current host
    const normalizeImageUrl = (url) => {
        if (!url) return '';
        const host = window.location.origin;
        return url.replace(/^http:\/\/[^/]+/, host);
    };

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
                    ${user.citizenship_front_image ? `
                    <div class="document-image-container" onclick="showFullScreenImage('${normalizeImageUrl(user.citizenship_front_image)}', 'Front Side')">
                        <img src="${normalizeImageUrl(user.citizenship_front_image)}" alt="Citizenship Front" class="document-image">
                        <div class="document-image-label">Front Side</div>
                    </div>
                    ` : ''}
                    ${user.citizenship_back_image ? `
                    <div class="document-image-container" onclick="showFullScreenImage('${normalizeImageUrl(user.citizenship_back_image)}', 'Back Side')">
                        <img src="${normalizeImageUrl(user.citizenship_back_image)}" alt="Citizenship Back" class="document-image">
                        <div class="document-image-label">Back Side</div>
                    </div>
                    ` : ''}
                </div>
            </div>
            ` : ''}

            <div class="action-bar">
                <button class="btn btn-success" onclick="showConfirmation('verify', ${user.user_id}, ${user.is_verified})">
                    ${user.is_verified ? 'Unverify' : 'Verify'} User
                </button>
                <button class="btn btn-outline" onclick="openEditModal(${user.user_id})">Edit Profile</button>
                <button class="btn btn-warning">Send Warning</button>
                <button class="btn btn-danger" onclick="showConfirmation('delete', ${user.user_id})">Delete Account</button>
            </div>
        `;
    };

    // Show full-screen image
    const showFullScreenImage = (imageUrl, label) => {
        const fullscreenImage = document.getElementById('fullscreen-image');
        const fullscreenImageLabel = document.getElementById('fullscreen-image-label');
        fullscreenImage.src = imageUrl;
        fullscreenImageLabel.textContent = label;
        fullscreenImageModal.style.display = 'block';
    };

    // Show confirmation modal
    const showConfirmation = (action, userId, currentStatus = null) => {
        currentAction = action;
        actionUserId = userId;

        const confirmationTitle = document.getElementById('confirmation-title');
        const confirmationMessage = document.getElementById('confirmation-message');

        if (action === 'verify') {
            const newStatus = currentStatus ? 'unverify' : 'verify';
            confirmationTitle.textContent = `${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)} User`;
            confirmationMessage.textContent = `Are you sure you want to ${newStatus} this user?`;
        } else if (action === 'delete') {
            confirmationTitle.textContent = 'Delete User';
            confirmationMessage.textContent = 'Are you sure you want to delete this user? This action cannot be undone.';
        }

        confirmationModal.style.display = 'block';
    };

    // Open edit modal
    const openEditModal = async (userId) => {
        try {
            const response = await fetch(`/usersdetail/${userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! Status: ${response.status}`);
            }

            const user = data.user || data;

            // Fill form with user data
            document.getElementById('edit-user-id').value = user.user_id;
            document.getElementById('edit-first_name').value = user.first_name || '';
            document.getElementById('edit-last_name').value = user.last_name || '';
            document.getElementById('edit-username').value = user.username || '';
            document.getElementById('edit-email').value = user.email || '';
            document.getElementById('edit-phone_number').value = user.phone_number || '';
            document.getElementById('edit-gender').value = user.gender || '';
            document.getElementById('edit-district').value = user.district || '';
            document.getElementById('edit-city').value = user.city || '';
            document.getElementById('edit-ward').value = user.ward || '';
            document.getElementById('edit-area_name').value = user.area_name || '';
            document.getElementById('edit-citizenship_id_number').value = user.citizenship_id_number || '';
            document.getElementById('edit-is_verified').value = user.is_verified ? '1' : '0';

            // Clear any previous errors
            clearEditErrors();

            editUserModal.style.display = 'block';
        } catch (error) {
            console.error('Error fetching user details for edit:', error);
            alert('Error loading user details for editing: ' + error.message);
        }
    };

    // Clear edit form errors
    const clearEditErrors = () => {
        document.querySelectorAll('[id$="_error"]').forEach(el => {
            if (el.id.startsWith('edit-')) {
                el.textContent = '';
            }
        });
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

    // Render recent users
    const renderRecentUsers = (users) => {
        const recentUsersBody = document.getElementById('recent-users-body');

        if (!users || users.length === 0) {
            recentUsersBody.innerHTML = '<tr><td colspan="3" class="empty-state">No recent users</td></tr>';
            return;
        }

        recentUsersBody.innerHTML = users.map(user => `
            <tr>
                <td>${user.user_id}</td>
                <td>@${user.username}</td>
                <td>${new Date(user.created_at).toLocaleDateString()}</td>
            </tr>
        `).join('');
    };

    // Fetch analytics data
    async function fetchAnalytics() {
        try {
            const res = await fetch('/api/analytics/users');
            const data = await res.json();

            // Recent Users
            const recentUsersBody = document.getElementById('recent-users-body');
            recentUsersBody.innerHTML = data.recent_users.map(u => `
                <tr>
                    <td>${u.user_id}</td>
                    <td>@${u.username}</td>
                    <td>${formatDate(u.created_at)}</td>
                </tr>`).join('');

            // User Growth
            const ctx1 = document.getElementById('userGrowthChart').getContext('2d');
            if (userGrowthChart) userGrowthChart.destroy();
            userGrowthChart = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: data.user_growth.labels,
                    datasets: [{
                        label: 'New Users',
                        data: data.user_growth.data,
                        borderColor: 'rgba(54,162,235,1)',
                        backgroundColor: 'rgba(54,162,235,0.2)',
                        fill: true
                    }]
                }
            });

            // Verification
            const ctx2 = document.getElementById('verificationChart').getContext('2d');
            if (verificationChart) verificationChart.destroy();
            verificationChart = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Verified', 'Unverified'],
                    datasets: [{
                        data: [data.verification_stats.verified, data.verification_stats.unverified],
                        backgroundColor: ['#28a745', '#dc3545']
                    }]
                }
            });
        } catch (err) {
            console.error(err);
        }
    }

    // Fetch users list from Laravel
    const fetchUsers = async () => {
        try {
            usersListBody.innerHTML = '<div class="loading">Loading users...</div>';
            userDetailCard.classList.remove('active');

            const response = await fetch(`${API_URL}?page=${currentPage}&limit=${ITEMS_PER_PAGE}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! Status: ${response.status}`);
            }

            totalPages = data.pages || data.last_page || 1;
            const users = data.users || data.data || [];

            if (users.length === 0) {
                usersListBody.innerHTML = '<div class="empty-state">No users found</div>';
            } else {
                usersListBody.innerHTML = users.map(user => renderUserRow(user)).join('');

                document.querySelectorAll('.user-row').forEach(row => {
                    row.addEventListener('click', async () => {
                        selectedUserId = parseInt(row.dataset.userId);
                        document.querySelectorAll('.user-row').forEach(r => r.classList.remove('selected'));
                        row.classList.add('selected');
                        await fetchUserDetails(selectedUserId);
                    });
                });
            }

            renderPagination();
        } catch (error) {
            console.error('Fetch users error:', error);
            usersListBody.innerHTML = `<div class="loading" style="color: var(--danger);">Error loading users: ${error.message}</div>`;
        }
    };

    // Fetch user by search term (phone number)
    const fetchUser = async (searchTerm) => {
        try {
            searchResultsBody.innerHTML = '<div class="loading">Searching user...</div>';
            userDetailCard.classList.remove('active');

            const response = await fetch(`/api/users/search?q=${encodeURIComponent(searchTerm)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! Status: ${response.status}`);
            }

            const users = data.users || data.data || [];

            if (users.length === 0) {
                searchResultsBody.innerHTML = '<div class="empty-state">' + (data.message || 'No user found with that phone number') + '</div>';
            } else {
                searchResultsBody.innerHTML = users.map(user => renderUserRow(user)).join('');

                document.querySelectorAll('.user-row').forEach(row => {
                    row.addEventListener('click', async () => {
                        selectedUserId = parseInt(row.dataset.userId);
                        document.querySelectorAll('.user-row').forEach(r => r.classList.remove('selected'));
                        row.classList.add('selected');
                        await fetchUserDetails(selectedUserId);
                    });
                });

                if (users.length === 1) {
                    selectedUserId = users[0].user_id;
                    const row = document.querySelector(`.user-row[data-user-id="${selectedUserId}"]`);
                    if (row) row.classList.add('selected');
                    await fetchUserDetails(selectedUserId);
                }
            }
        } catch (error) {
            console.error('Search user error:', error);
            searchResultsBody.innerHTML = `<div class="loading" style="color: var(--danger);">Error searching user: ${error.message}</div>`;
        }
    };

    // Fetch single user details
    const fetchUserDetails = async (userId) => {
        try {
            userDetailCard.innerHTML = '<div class="loading">Loading user details...</div>';
            userDetailCard.classList.add('active');

            const response = await fetch(`/usersdetail/${userId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! Status: ${response.status}`);
            }

            userDetailCard.innerHTML = renderUserDetails(data.user || data);
            userDetailCard.scrollIntoView({ behavior: 'smooth' });
        } catch (error) {
            console.error('Fetch user details error:', error);
            userDetailCard.innerHTML = `<div class="loading" style="color: var(--danger);">Error loading user details: ${error.message}</div>`;
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
        fetchAnalytics();
    };

    // Toggle user verification
    const toggleVerification = async (userId) => {
        try {
            const response = await fetch(`/users/${userId}/toggle-verification`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                alert(`User ${data.is_verified ? 'verified' : 'unverified'} successfully!`);
                fetchUserDetails(userId);
                fetchUsers();
            } else {
                throw new Error(data.message || 'Failed to update verification status');
            }
        } catch (error) {
            console.error('Toggle verification error:', error);
            alert('Error updating verification status: ' + error.message);
        }
    };

    // Delete user
    const deleteUser = async (userId) => {
        try {
            const response = await fetch(`/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                alert('User deleted successfully!');
                userDetailCard.classList.remove('active');
                fetchUsers();
            } else {
                throw new Error(data.message || 'Failed to delete user');
            }
        } catch (error) {
            console.error('Delete user error:', error);
            alert('Error deleting user: ' + error.message);
        }
    };

    // Update user
    const updateUser = async (formData) => {
        try {
            const response = await fetch(`/usersdetail/${formData.user_id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok && data.status === 'success') {
                alert('User updated successfully!');
                editUserModal.style.display = 'none';
                fetchUserDetails(formData.user_id);
                fetchUsers();
            } else {
                if (data.errors) {
                    // Display validation errors
                    Object.keys(data.errors).forEach(field => {
                        const errorElement = document.getElementById(`edit-${field}_error`);
                        if (errorElement) {
                            errorElement.textContent = data.errors[field][0];
                        }
                    });
                } else {
                    throw new Error(data.message || 'Failed to update user');
                }
            }
        } catch (error) {
            console.error('Update user error:', error);
            alert('Error updating user: ' + error.message);
        }
    };

    // Modal event listeners
    closeModalButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            editUserModal.style.display = 'none';
            confirmationModal.style.display = 'none';
            fullscreenImageModal.style.display = 'none';
        });
    });

    cancelEditBtn.addEventListener('click', () => {
        editUserModal.style.display = 'none';
    });

    cancelActionBtn.addEventListener('click', () => {
        confirmationModal.style.display = 'none';
    });

    confirmActionBtn.addEventListener('click', () => {
        if (currentAction === 'verify') {
            toggleVerification(actionUserId);
        } else if (currentAction === 'delete') {
            deleteUser(actionUserId);
        }
        confirmationModal.style.display = 'none';
    });

    // Edit form submission
    editUserForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const formData = {
            user_id: document.getElementById('edit-user-id').value,
            first_name: document.getElementById('edit-first_name').value,
            last_name: document.getElementById('edit-last_name').value,
            username: document.getElementById('edit-username').value,
            email: document.getElementById('edit-email').value,
            phone_number: document.getElementById('edit-phone_number').value,
            gender: document.getElementById('edit-gender').value,
            district: document.getElementById('edit-district').value,
            city: document.getElementById('edit-city').value,
            ward: document.getElementById('edit-ward').value,
            area_name: document.getElementById('edit-area_name').value,
            citizenship_id_number: document.getElementById('edit-citizenship_id_number').value,
            is_verified: document.getElementById('edit-is_verified').value
        };

        updateUser(formData);
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if (e.target === editUserModal) {
            editUserModal.style.display = 'none';
        }
        if (e.target === confirmationModal) {
            confirmationModal.style.display = 'none';
        }
        if (e.target === fullscreenImageModal) {
            fullscreenImageModal.style.display = 'none';
        }
    });

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

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        fetchUsers();
        fetchAnalytics();
    });
</script>
@endsection
```
