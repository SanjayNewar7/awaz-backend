@extends('superadmin.layout')

@section('title', 'Awaz - User Verification')

@section('content')
<style>
    :root {
        --primary: #1e3a8a;
        --primary-light: #3b82f6;
        --secondary: #60a5fa;
        --accent: #93c5fd;
        --danger: #dc2626;
        --warning: #f59e0b;
        --success: #22c55e;
        --background: #f0f5ff;
        --card-bg: #ffffff;
        --border: #bfdbfe;
        --text: #1e293b;
        --text-light: #64748b;
    }

    .verification-container {
        padding: 1.5rem;
    }

    .search-container {
        background-color: var(--card-bg);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    .search-input {
        flex: 1;
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        font-size: 0.9rem;
    }

    .filter-select {
        padding: 0.75rem;
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        font-size: 0.9rem;
        background-color: var(--card-bg);
        color: var(--text);
    }

    .users-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }

    .users-table th,
    .users-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--border);
    }

    .users-table th {
        background-color: var(--primary-light);
        color: white;
        font-weight: 600;
    }

    .users-table td {
        background-color: var(--card-bg);
    }

    .users-table tr:hover td {
        background-color: var(--background);
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

    .btn-view {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-view:hover {
        background-color: #0056b3;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        cursor: pointer;
        border: none;
        transition: all 0.3s ease;
        font-size: 0.85rem;
    }

    .btn-view {
        background-color: var(--primary-light);
        color: white;
    }

    .btn-view:hover {
        background-color: var(--primary);
    }

    .btn-update {
        background-color: var(--secondary);
        color: white;
    }

    .btn-update:hover {
        background-color: var(--accent);
    }

    .user-detail-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        overflow-y: auto;
        padding: 2rem;
    }

    .user-detail-modal.active {
        display: block;
    }

    .modal-content {
        background-color: var(--card-bg);
        border-radius: 0.75rem;
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem;
        position: relative;
    }

    .close-modal {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 1.5rem;
        color: var(--text-light);
        cursor: pointer;
        background: none;
        border: none;
    }

    .modal-header {
        display: flex;
        align-items: center;
        margin-bottom: 2rem;
    }

    .modal-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 1.5rem;
        border: 3px solid var(--border);
    }

    .modal-user-info {
        flex: 1;
    }

    .modal-user-name {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--text);
        margin-bottom: 0.5rem;
    }

    .modal-user-email {
        font-size: 1rem;
        color: var(--text-light);
        margin-bottom: 0.5rem;
    }

    .citizenship-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border);
    }

    .citizenship-images {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .citizenship-image-container {
        position: relative;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    .citizenship-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .citizenship-image-container:hover .citizenship-image {
        transform: scale(1.05);
    }

    .image-label {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.7);
        color: white;
        padding: 0.5rem;
        text-align: center;
        font-size: 0.9rem;
    }

    .user-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .detail-group {
        background-color: var(--background);
        padding: 1rem;
        border-radius: 0.5rem;
    }

    .detail-label {
        font-weight: 600;
        color: var(--text-light);
        font-size: 0.85rem;
        margin-bottom: 0.5rem;
    }

    .detail-value {
        font-size: 1rem;
        color: var(--text);
    }

    .action-buttons-modal {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border);
    }

    .btn-update-modal {
        background-color: var(--success);
        color: white;
    }

    .btn-update-modal:hover {
        background-color: #16a34a;
    }

    .fullscreen-image-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
        z-index: 2000;
        overflow: auto;
    }

    .fullscreen-image-modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .fullscreen-image-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }

    .fullscreen-image {
        max-width: 100%;
        max-height: 80vh;
        object-fit: contain;
        border-radius: 0.5rem;
    }

    .fullscreen-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 2rem;
        font-weight: bold;
        color: white;
        cursor: pointer;
        background: none;
        border: none;
    }

    .fullscreen-caption {
        text-align: center;
        font-size: 1rem;
        color: white;
        margin-top: 1rem;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }

    .page-btn {
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        border: 1px solid var(--border);
        background-color: var(--card-bg);
        cursor: pointer;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .page-btn.active {
        background-color: var(--primary-light);
        color: white;
    }

    .page-btn:hover {
        background-color: var(--secondary);
        color: white;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background-color: var(--card-bg);
        border-radius: 0.75rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }

    .stat-label {
        font-size: 0.9rem;
        color: var(--text-light);
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-light);
    }

    .loading {
        text-align: center;
        padding: 2rem;
        color: var(--text-light);
    }

    @media (max-width: 768px) {
        .users-table th,
        .users-table td {
            padding: 0.75rem;
            font-size: 0.85rem;
        }

        .citizenship-images {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="verification-container">
    <h1>User Verification Management</h1>

    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-value" id="total-users">0</div>
            <div class="stat-label">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="verified-users">0</div>
            <div class="stat-label">Verified Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="pending-users">0</div>
            <div class="stat-label">Pending Verification</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="rejected-users">0</div>
            <div class="stat-label">Rejected Verifications</div>
        </div>
    </div>

    <div class="search-container">
        <input type="text" id="search-input" class="search-input" placeholder="Search by name, email, or citizenship ID">
        <select id="filter-status" class="filter-select">
            <option value="">All Statuses</option>
            <option value="pending">pending</option>
            <option value="verified">verified</option>
            <option value="rejected">rejected</option>
        </select>
        <button class="btn btn-outline" id="search-btn">Search</button>
        <button class="btn btn-outline" id="export-btn">Export to CSV</button>
    </div>

    <table class="users-table" id="users-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>District</th>
                <th>Citizenship ID</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="6" class="loading">Loading users...</td></tr>
        </tbody>
    </table>

    <div class="pagination" id="pagination"></div>
</div>

<!-- User Detail Modal -->
<div class="user-detail-modal" id="user-detail-modal">
    <div class="modal-content">
        <button class="close-modal" id="close-modal">&times;</button>

        <div class="modal-header">
            <img src="" alt="User Avatar" class="modal-avatar" id="modal-avatar">
            <div class="modal-user-info">
                <h2 class="modal-user-name" id="modal-user-name"></h2>
                <div class="modal-user-email" id="modal-user-email"></div>
                <div class="verification-status" id="modal-verification-status"></div>
            </div>
        </div>

        <div class="citizenship-section">
            <h3 class="section-title">Citizenship Verification</h3>
            <div class="citizenship-images">
                <div class="citizenship-image-container" id="citizenship-front-container">
                    <img src="" alt="Citizenship Front" class="citizenship-image" id="citizenship-front-image">
                    <div class="image-label">Citizenship Front Side</div>
                </div>
                <div class="citizenship-image-container" id="citizenship-back-container">
                    <img src="" alt="Citizenship Back" class="citizenship-image" id="citizenship-back-image">
                    <div class="image-label">Citizenship Back Side</div>
                </div>
            </div>
            <div class="detail-group">
                <div class="detail-label">Citizenship ID Number</div>
                <div class="detail-value" id="citizenship-id-number"></div>
            </div>
        </div>

        <div class="user-details-section">
            <h3 class="section-title">User Details</h3>
            <div class="user-details-grid">
                <div class="detail-group">
                    <div class="detail-label">Full Name</div>
                    <div class="detail-value" id="user-full-name"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Phone Number</div>
                    <div class="detail-value" id="user-phone"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Gender</div>
                    <div class="detail-value" id="user-gender"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Location</div>
                    <div class="detail-value" id="user-location"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">District</div>
                    <div class="detail-value" id="user-district"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">City</div>
                    <div class="detail-value" id="user-city"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Ward</div>
                    <div class="detail-value" id="user-ward"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Area Name</div>
                    <div class="detail-value" id="user-area"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Member Since</div>
                    <div class="detail-value" id="user-joined"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Posts Count</div>
                    <div class="detail-value" id="user-posts"></div>
                </div>
            </div>
        </div>

        <div class="action-buttons-modal">
            <select id="verification-select" class="filter-select">
                <option value="">Select Status</option>
                <option value="pending">pending</option>
                <option value="verified">verified</option>
                <option value="rejected">rejected</option>
            </select>
            <button class="btn btn-update-modal" id="update-verification-btn">Update Status</button>
        </div>
    </div>
</div>

<!-- Fullscreen Image Modal -->
<div class="fullscreen-image-modal" id="fullscreen-image-modal">
    <div class="fullscreen-image-content">
        <button class="fullscreen-close" id="fullscreen-close">&times;</button>
        <img src="" alt="Fullscreen Image" class="fullscreen-image" id="fullscreen-image">
        <div class="fullscreen-caption" id="fullscreen-caption"></div>
    </div>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const API_URL = '/api/verification';
    const ITEMS_PER_PAGE = 8;
    let currentPage = 1;
    let totalPages = 1;
    let selectedUserId = null;
    let currentUser = null;

    // Cache DOM elements
    const usersTable = document.getElementById('users-table');
    const paginationEl = document.getElementById('pagination');
    const searchInput = document.getElementById('search-input');
    const filterStatus = document.getElementById('filter-status');
    const searchBtn = document.getElementById('search-btn');
    const exportBtn = document.getElementById('export-btn');
    const userModal = document.getElementById('user-detail-modal');
    const fullscreenModal = document.getElementById('fullscreen-image-modal');
    const fullscreenClose = document.getElementById('fullscreen-close');

    const statsElements = {
        total: document.getElementById('total-users'),
        verified: document.getElementById('verified-users'),
        pending: document.getElementById('pending-users'),
        rejected: document.getElementById('rejected-users')
    };

    const modalElements = {
        avatar: document.getElementById('modal-avatar'),
        name: document.getElementById('modal-user-name'),
        email: document.getElementById('modal-user-email'),
        status: document.getElementById('modal-verification-status'),
        citizenshipFront: document.getElementById('citizenship-front-image'),
        citizenshipBack: document.getElementById('citizenship-back-image'),
        citizenshipId: document.getElementById('citizenship-id-number'),
        userDetails: {
            fullName: document.getElementById('user-full-name'),
            phone: document.getElementById('user-phone'),
            gender: document.getElementById('user-gender'),
            location: document.getElementById('user-location'),
            district: document.getElementById('user-district'),
            city: document.getElementById('user-city'),
            ward: document.getElementById('user-ward'),
            area: document.getElementById('user-area'),
            joined: document.getElementById('user-joined'),
            posts: document.getElementById('user-posts')
        },
        verificationSelect: document.getElementById('verification-select'),
        updateBtn: document.getElementById('update-verification-btn'),
        closeBtn: document.getElementById('close-modal'),
        citizenshipFrontContainer: document.getElementById('citizenship-front-container'),
        citizenshipBackContainer: document.getElementById('citizenship-back-container')
    };

    const fullscreenElements = {
        modal: fullscreenModal,
        image: document.getElementById('fullscreen-image'),
        caption: document.getElementById('fullscreen-caption')
    };

    // Helper functions
    const formatDate = (date) => new Date(date).toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
    const normalizeImageUrl = (url) => {
        if (!url) return '';
        const host = window.location.origin;
        return url.replace(/^http:\/\/[^/]+/, host);
    };

    const getCurrentStatus = (user) => user.is_verified ? 'approved' : user.verification_status || 'pending';
    const statusClass = (status) => status === 'approved' ? 'status-verified' :
                                  status === 'rejected' ? 'status-rejected' : 'status-pending';
    const statusText = (status) => status === 'approved' ? 'Verified' :
                                 status === 'rejected' ? 'Rejected' : 'Pending Verification';

    const renderUserRow = (user) => {
        const currentStatus = getCurrentStatus(user);
        const fullName = `${user.first_name} ${user.last_name}`;
        return `
            <tr data-user-id="${user.user_id}">
                <td>${user.user_id}</td>
                <td>${fullName}</td>
                <td>${user.district || 'N/A'}</td>
                <td>${user.citizenship_id_number || 'N/A'}</td>
                <td><span class="verification-status ${statusClass(currentStatus)}">${statusText(currentStatus)}</span></td>
                <td><button class="btn btn-view" data-user-id="${user.user_id}">View</button></td>
            </tr>
        `;
    };

    // Fetch users with pagination
    const fetchUsers = async () => {
        const tbody = usersTable.querySelector('tbody');
        tbody.innerHTML = '<tr><td colspan="6" class="loading">Loading users...</td></tr>';

        try {
            const params = new URLSearchParams({
                page: currentPage,
                limit: ITEMS_PER_PAGE,
                status: filterStatus.value || '',
                search: searchInput.value || ''
            });

            const res = await fetch(`${API_URL}/users?${params.toString()}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });

            const data = await res.json();
            if (data.status !== 'success') throw new Error(data.message || 'Failed to fetch users');

            const users = data.users.data || data.users || [];
            totalPages = data.users.last_page || 1;

            tbody.innerHTML = users.length
                ? users.map(renderUserRow).join('')
                : '<tr><td colspan="6" class="empty-state">No users found</td></tr>';

            renderPagination();
            renderStats(data.stats);
        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="6" class="loading" style="color: var(--danger);">Error: ${err.message}</td></tr>`;
        }
    };

    const renderStats = (stats) => {
        if (!stats) return;
        statsElements.total.textContent = stats.total || 0;
        statsElements.verified.textContent = stats.verified || 0;
        statsElements.pending.textContent = stats.pending || 0;
        statsElements.rejected.textContent = stats.rejected || 0;
    };

    // Event delegation for user rows
    usersTable.addEventListener('click', async (e) => {
        const viewBtn = e.target.closest('.btn-view');
        if (viewBtn) {
            selectedUserId = parseInt(viewBtn.dataset.userId);
            await fetchUserDetails(selectedUserId);
        }
    });

    // Fetch single user details
    const fetchUserDetails = async (id) => {
        try {
            const res = await fetch(`${API_URL}/users/${id}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            });
            const data = await res.json();
            if (data.status !== 'success') throw new Error(data.message || 'Failed to fetch user details');
            currentUser = data.user;
            displayUserDetails(currentUser);
        } catch (err) {
            alert('Error loading user details: ' + err.message);
        }
    };

    const displayUserDetails = (user) => {
        const currentStatus = getCurrentStatus(user);
        userModal.classList.add('active');
        modalElements.avatar.src = normalizeImageUrl(user.profile_image) || '/images/default-avatar.png';
        modalElements.name.textContent = `${user.first_name} ${user.last_name}`;
        modalElements.email.textContent = user.email;
        modalElements.status.className = `verification-status ${statusClass(currentStatus)}`;
        modalElements.status.textContent = statusText(currentStatus);

        modalElements.citizenshipFront.src = normalizeImageUrl(user.citizenship_front_image) || '/images/default-citizenship.png';
        modalElements.citizenshipBack.src = normalizeImageUrl(user.citizenship_back_image) || '/images/default-citizenship.png';
        modalElements.citizenshipId.textContent = user.citizenship_id_number || 'Not provided';

        modalElements.userDetails.fullName.textContent = `${user.first_name} ${user.last_name}`;
        modalElements.userDetails.phone.textContent = user.phone_number || 'Not provided';
        modalElements.userDetails.gender.textContent = user.gender || 'Not specified';
        modalElements.userDetails.location.textContent = [user.district, user.city, `Ward ${user.ward}`].filter(Boolean).join(', ') || 'Not specified';
        modalElements.userDetails.district.textContent = user.district || 'Not specified';
        modalElements.userDetails.city.textContent = user.city || 'Not specified';
        modalElements.userDetails.ward.textContent = user.ward || 'Not specified';
        modalElements.userDetails.area.textContent = user.area_name || 'Not specified';
        modalElements.userDetails.joined.textContent = user.created_at ? formatDate(user.created_at) : 'Unknown';
        modalElements.userDetails.posts.textContent = user.posts_count || 0;

        modalElements.verificationSelect.value = currentStatus;

        // Add event listeners only if elements exist
        if (modalElements.closeBtn) {
            modalElements.closeBtn.removeEventListener('click', closeModal);
            modalElements.closeBtn.addEventListener('click', closeModal);
        }
        if (modalElements.citizenshipFrontContainer) {
            modalElements.citizenshipFrontContainer.removeEventListener('click', showFrontImage);
            modalElements.citizenshipFrontContainer.addEventListener('click', showFrontImage);
        }
        if (modalElements.citizenshipBackContainer) {
            modalElements.citizenshipBackContainer.removeEventListener('click', showBackImage);
            modalElements.citizenshipBackContainer.addEventListener('click', showBackImage);
        }

        modalElements.updateBtn.onclick = () => {
            const newStatus = modalElements.verificationSelect.value;
            if (!newStatus || newStatus === currentStatus) return;
            if (confirm(`Are you sure you want to set this user's status to ${statusText(newStatus)}?`)) {
                updateVerification(user.user_id, newStatus);
            }
        };

        function closeModal() {
            userModal.classList.remove('active');
        }

        function showFrontImage() {
            showFullscreenImage(modalElements.citizenshipFront.src, 'Citizenship Front Side');
        }

        function showBackImage() {
            showFullscreenImage(modalElements.citizenshipBack.src, 'Citizenship Back Side');
        }
    };

    const showFullscreenImage = (url, caption) => {
        fullscreenElements.modal.classList.add('active');
        fullscreenElements.image.src = url;
        fullscreenElements.caption.textContent = caption;
        // Add close event listener for the fullscreen close button
        if (fullscreenClose) {
            fullscreenClose.removeEventListener('click', closeFullscreen);
            fullscreenClose.addEventListener('click', closeFullscreen);
        }
    };

    const closeFullscreen = () => {
        fullscreenElements.modal.classList.remove('active');
    };

    const updateVerification = async (userId, status) => {
        try {
            const payload = { status };
            const res = await fetch(`${API_URL}/users/${userId}/verify`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const data = await res.json();
            if (data.status !== 'success') throw new Error(data.message || 'Verification failed');
            alert('User status updated successfully');
            fetchUsers();
            fetchUserDetails(userId);
        } catch (err) {
            alert('Error: ' + err.message);
        }
    };

    // Pagination
    const renderPagination = () => {
        if (!paginationEl) return;
        paginationEl.innerHTML = '';

        if (currentPage > 1) {
            const prevBtn = document.createElement('button');
            prevBtn.className = 'page-btn';
            prevBtn.textContent = 'Previous';
            prevBtn.addEventListener('click', () => {
                currentPage--;
                fetchUsers();
            });
            paginationEl.appendChild(prevBtn);
        }

        for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
            const btn = document.createElement('button');
            btn.className = `page-btn ${i === currentPage ? 'active' : ''}`;
            btn.textContent = i;
            btn.addEventListener('click', () => {
                currentPage = i;
                fetchUsers();
            });
            paginationEl.appendChild(btn);
        }

        if (currentPage < totalPages) {
            const nextBtn = document.createElement('button');
            nextBtn.className = 'page-btn';
            nextBtn.textContent = 'Next';
            nextBtn.addEventListener('click', () => {
                currentPage++;
                fetchUsers();
            });
            paginationEl.appendChild(nextBtn);
        }
    };

    // Search and filter
    searchBtn.addEventListener('click', () => { currentPage = 1; fetchUsers(); });
    filterStatus.addEventListener('change', () => { currentPage = 1; fetchUsers(); });

    // Export CSV
    exportBtn.addEventListener('click', () => {
        window.location.href = `${API_URL}/export`;
    });

    // Initial fetch
    fetchUsers();
});
</script>
@endsection
