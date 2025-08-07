<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .registration-container {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .registration-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .registration-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .form-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-section-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .form-group label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-700);
        }

        .form-group input,
        .form-group select {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.375rem;
            font-size: 0.875rem;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .password-group {
            position: relative;
        }

        .password-wrapper {
            display: flex;
            align-items: center;
            position: relative;
        }

        .password-wrapper input {
            width: 100%;
            padding-right: 2.5rem;
        }

        .toggle-password {
            position: absolute;
            right: 0.75rem;
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            color: var(--gray-500);
        }

        .toggle-password:hover {
            color: var(--primary);
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        .file-upload-group {
            display: flex;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            border: 1px dashed var(--gray-300);
            border-radius: 0.375rem;
            background-color: var(--gray-50);
            cursor: pointer;
            transition: all 0.2s;
            width: 100%;
            text-align: center;
            position: relative;
            height: 120px;
        }

        .file-upload-label:hover {
            background-color: var(--gray-100);
            border-color: var(--gray-400);
        }

        .file-upload-label span {
            font-size: 0.875rem;
            color: var(--gray-700);
        }

        .file-upload-label input[type="file"] {
            display: none;
        }

        .file-upload-label img.preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0.375rem;
            display: none;
            position: absolute;
            top: 0;
            left: 0;
        }

        .file-upload-label.has-image span {
            display: none;
        }

        .file-upload-label.has-image img.preview-image {
            display: block;
        }

        .checkbox-group {
            flex-direction: row;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-group input {
            width: auto;
        }

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid var(--gray-200);
        }

        .error-message {
            color: var(--danger);
            font-size: 0.75rem;
            margin-top: 0.25rem;
            min-height: 1rem;
        }

        .success-message {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            background-color: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 0.375rem;
            color: #166534;
            margin-bottom: 1rem;
        }

        .success-message svg {
            color: #22c55e;
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
                       placeholder="Search by phone number">
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

        <div class="registration-container">
            <div class="registration-header">
                <h2>Register New User</h2>
                <button class="btn btn-outline" id="toggle-registration">Show Registration Form</button>
            </div>

            <form id="user-registration-form" style="display: none;">
                <div class="form-grid">
                    <!-- Basic Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Basic Information</h3>
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" required>
                            <div class="error-message" id="first_name_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" required>
                            <div class="error-message" id="last_name_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                            <div class="error-message" id="username_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                            <div class="error-message" id="email_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                            <input type="tel" id="phone_number" name="phone_number" required>
                            <div class="error-message" id="phone_number_error"></div>
                        </div>
                        <div class="form-group password-group">
                            <label for="password">Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" required>
                                <button type="button" class="toggle-password" data-target="password">
                                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-slash-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <div class="error-message" id="password_error"></div>
                        </div>
                        <div class="form-group password-group">
                            <label for="password_confirmation">Confirm Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="password_confirmation" name="password_confirmation" required>
                                <button type="button" class="toggle-password" data-target="password_confirmation">
                                    <svg class="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    <svg class="eye-slash-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
                                        <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                                        <line x1="1" y1="1" x2="23" y2="23"></line>
                                    </svg>
                                </button>
                            </div>
                            <div class="error-message" id="password_confirmation_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                            <div class="error-message" id="gender_error"></div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Address Information</h3>
                        <div class="form-group">
                            <label for="district">District</label>
                            <input type="text" id="district" name="district" required>
                            <div class="error-message" id="district_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" id="city" name="city" required>
                            <div class="error-message" id="city_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="ward">Ward Number</label>
                            <input type="number" id="ward" name="ward" required>
                            <div class="error-message" id="ward_error"></div>
                        </div>
                        <div class="form-group">
                            <label for="area_name">Area Name</label>
                            <input type="text" id="area_name" name="area_name" required>
                            <div class="error-message" id="area_name_error"></div>
                        </div>
                    </div>

                    <!-- Citizenship Information -->
                    <div class="form-section">
                        <h3 class="form-section-title">Citizenship Information</h3>
                        <div class="form-group">
                            <label for="citizenship_id_number">Citizenship ID Number</label>
                            <input type="text" id="citizenship_id_number" name="citizenship_id_number" required>
                            <div class="error-message" id="citizenship_id_number_error"></div>
                        </div>
                        <div class="form-group">
                            <label>Citizenship Documents</label>
                            <div class="file-upload-group">
                                <label for="citizenship_front_image" class="file-upload-label">
                                    <span>Front Image</span>
                                    <img class="preview-image" id="front-image-preview" alt="Front Image Preview">
                                    <input type="file" id="citizenship_front_image" name="citizenship_front_image" accept="image/*" required>
                                    <div class="error-message" id="citizenship_front_image_error"></div>
                                </label>
                                <label for="citizenship_back_image" class="file-upload-label">
                                    <span>Back Image</span>
                                    <img class="preview-image" id="back-image-preview" alt="Back Image Preview">
                                    <input type="file" id="citizenship_back_image" name="citizenship_back_image" accept="image/*" required>
                                    <div class="error-message" id="citizenship_back_image_error"></div>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="is_verified">Verification Status</label>
                            <select id="is_verified" name="is_verified" required>
                                <option value="0">Unverified</option>
                                <option value="1">Verified</option>
                            </select>
                            <div class="error-message" id="is_verified_error"></div>
                        </div>
                        <div class="form-group checkbox-group">
                            <input type="checkbox" id="agreed_to_terms" name="agreed_to_terms" required>
                            <label for="agreed_to_terms">User has agreed to terms and conditions</label>
                            <div class="error-message" id="agreed_to_terms_error"></div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="cancel-registration">Cancel</button>
                    <button type="submit" class="btn btn-primary">Register User</button>
                </div>
            </form>

            <div id="registration-success" style="display: none;">
                <div class="success-message">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <p>User registered successfully!</p>
                </div>
                <button class="btn btn-primary" id="register-another">Register Another User</button>
            </div>
        </div>

        <div class="user-card" id="user-detail-card">
            <!-- User details will be inserted here -->
        </div>
    </div>

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
        const toggleRegistrationBtn = document.getElementById('toggle-registration');
        const registrationForm = document.getElementById('user-registration-form');
        const cancelRegistrationBtn = document.getElementById('cancel-registration');
        const registerAnotherBtn = document.getElementById('register-another');
        const registrationSuccess = document.getElementById('registration-success');
        const frontImageInput = document.getElementById('citizenship_front_image');
        const backImageInput = document.getElementById('citizenship_back_image');
        const frontImagePreview = document.getElementById('front-image-preview');
        const backImagePreview = document.getElementById('back-image-preview');

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

        // Fetch user by search term (phone number)
        const fetchUser = async (searchTerm) => {
            try {
                searchResultsBody.innerHTML = '<div class="loading">Searching user...</div>';
                userDetailCard.classList.remove('active');

                const response = await fetch(`/api/users/search?q=${encodeURIComponent(searchTerm)}`);
                const data = await response.json();

                console.log('Search response:', data);

                if (!data.users || data.users.length === 0) {
                    searchResultsBody.innerHTML = '<div class="empty-state">' + (data.message || 'No user found with that phone number') + '</div>';
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

                console.log('User details response:', data);

                if (response.ok) {
                    userDetailCard.innerHTML = renderUserDetails(data.user || data);
                    userDetailCard.scrollIntoView({ behavior: 'smooth' });
                } else {
                    userDetailCard.innerHTML = '<div class="loading">' + (data.message || 'User not found') + '</div>';
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

        // Convert file to Base64
        const fileToBase64 = (file) => {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = () => resolve(reader.result);
                reader.onerror = (error) => reject(error);
                reader.readAsDataURL(file);
            });
        };

        // Clear all error messages
        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => {
                el.textContent = '';
            });
        }

        // Validate form
        function validateForm() {
            let isValid = true;
            clearErrors();

            const requiredFields = [
                'first_name', 'last_name', 'username', 'email', 'phone_number',
                'password', 'password_confirmation', 'district', 'city',
                'ward', 'area_name', 'citizenship_id_number', 'citizenship_front_image',
                'citizenship_back_image', 'gender', 'is_verified', 'agreed_to_terms'
            ];

            requiredFields.forEach(field => {
                const element = document.getElementById(field);
                const value = field.includes('image') ? element.files[0] : element.value.trim();
                if (!value) {
                    document.getElementById(`${field}_error`).textContent = 'This field is required';
                    isValid = false;
                }
            });

            // Validate email format
            const email = document.getElementById('email').value.trim();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('email_error').textContent = 'Please enter a valid email address';
                isValid = false;
            }

            // Validate phone number
            const phone_number = document.getElementById('phone_number').value.trim();
            if (phone_number && !/^\d{10}$/.test(phone_number)) {
                document.getElementById('phone_number_error').textContent = 'Phone number must be 10 digits';
                isValid = false;
            }

            // Validate password match
            const password = document.getElementById('password').value.trim();
            const passwordConfirmation = document.getElementById('password_confirmation').value.trim();
            console.log('Password:', JSON.stringify(password));
            console.log('Password Confirmation:', JSON.stringify(passwordConfirmation));
            if (password !== passwordConfirmation) {
                document.getElementById('password_confirmation_error').textContent = 'Passwords do not match';
                isValid = false;
            } else {
                document.getElementById('password_confirmation_error').textContent = '';
            }

            // Validate password length
            if (password.length > 0 && password.length < 8) {
                document.getElementById('password_error').textContent = 'Password must be at least 8 characters';
                isValid = false;
            }

            // Validate gender
            const gender = document.getElementById('gender').value;
            if (!['Male', 'Female', 'Other'].includes(gender)) {
                document.getElementById('gender_error').textContent = 'Please select a valid gender';
                isValid = false;
            }

            // Validate agreed to terms
            const agreedToTerms = document.getElementById('agreed_to_terms').checked;
            if (!agreedToTerms) {
                document.getElementById('agreed_to_terms_error').textContent = 'You must agree to the terms and conditions';
                isValid = false;
            }

            return isValid;
        }

        // Image preview handlers
        frontImageInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            const label = frontImageInput.closest('.file-upload-label');
            if (file) {
                try {
                    const dataUrl = await fileToBase64(file);
                    frontImagePreview.src = dataUrl;
                    label.classList.add('has-image');
                } catch (error) {
                    console.error('Error reading front image:', error);
                    document.getElementById('citizenship_front_image_error').textContent = 'Error loading image preview';
                }
            } else {
                frontImagePreview.src = '';
                label.classList.remove('has-image');
            }
        });

        backImageInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            const label = backImageInput.closest('.file-upload-label');
            if (file) {
                try {
                    const dataUrl = await fileToBase64(file);
                    backImagePreview.src = dataUrl;
                    label.classList.add('has-image');
                } catch (error) {
                    console.error('Error reading back image:', error);
                    document.getElementById('citizenship_back_image_error').textContent = 'Error loading image preview';
                }
            } else {
                backImagePreview.src = '';
                label.classList.remove('has-image');
            }
        });

        // Password toggle functionality
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', () => {
                const targetId = button.dataset.target;
                const input = document.getElementById(targetId);
                const eyeIcon = button.querySelector('.eye-icon');
                const eyeSlashIcon = button.querySelector('.eye-slash-icon');

                if (input.type === 'password') {
                    input.type = 'text';
                    eyeIcon.style.display = 'none';
                    eyeSlashIcon.style.display = 'block';
                } else {
                    input.type = 'password';
                    eyeIcon.style.display = 'block';
                    eyeSlashIcon.style.display = 'none';
                }
            });
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

        toggleRegistrationBtn.addEventListener('click', () => {
            registrationForm.style.display = registrationForm.style.display === 'none' ? 'block' : 'none';
            toggleRegistrationBtn.textContent = registrationForm.style.display === 'none' ? 'Show Registration Form' : 'Hide Registration Form';
        });

        cancelRegistrationBtn.addEventListener('click', () => {
            registrationForm.reset();
            registrationForm.style.display = 'none';
            toggleRegistrationBtn.textContent = 'Show Registration Form';
            clearErrors();
            frontImagePreview.src = '';
            backImagePreview.src = '';
            document.querySelectorAll('.file-upload-label').forEach(label => label.classList.remove('has-image'));
        });

        registerAnotherBtn.addEventListener('click', () => {
            registrationSuccess.style.display = 'none';
            registrationForm.style.display = 'block';
            registrationForm.reset();
            clearErrors();
            frontImagePreview.src = '';
            backImagePreview.src = '';
            document.querySelectorAll('.file-upload-label').forEach(label => label.classList.remove('has-image'));
        });

        // Handle form submission
        registrationForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!validateForm()) {
                return;
            }

            try {
                // Get form data
                const form = registrationForm;
                const data = {
                    username: form.username.value.trim(),
                    first_name: form.first_name.value.trim(),
                    last_name: form.last_name.value.trim(),
                    email: form.email.value.trim(),
                    phone_number: form.phone_number.value.trim(),
                    password: form.password.value.trim(),
                    password_confirmation: form.password_confirmation.value.trim(),
                    district: form.district.value.trim(),
                    city: form.city.value.trim(),
                    ward: parseInt(form.ward.value),
                    area_name: form.area_name.value.trim(),
                    citizenship_id_number: form.citizenship_id_number.value.trim(),
                    gender: form.gender.value,
                    is_verified: form.is_verified.value === '1',
                    agreed_to_terms: form.agreed_to_terms.checked
                };

                // Convert images to Base64
                const frontImageFile = form.citizenship_front_image.files[0];
                const backImageFile = form.citizenship_back_image.files[0];

                if (frontImageFile) {
                    data.citizenship_front_image = await fileToBase64(frontImageFile);
                }
                if (backImageFile) {
                    data.citizenship_back_image = await fileToBase64(backImageFile);
                }

                // Send request
                const response = await fetch('/api/users', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    registrationForm.style.display = 'none';
                    registrationSuccess.style.display = 'block';
                    toggleRegistrationBtn.textContent = 'Show Registration Form';
                    registrationForm.reset();
                    clearErrors();
                    frontImagePreview.src = '';
                    backImagePreview.src = '';
                    document.querySelectorAll('.file-upload-label').forEach(label => label.classList.remove('has-image'));
                    fetchUsers();
                } else {
                    if (result.errors) {
                        Object.keys(result.errors).forEach(field => {
                            const errorElement = document.getElementById(`${field}_error`);
                            if (errorElement) {
                                errorElement.textContent = result.errors[field][0];
                            }
                        });
                    } else {
                        alert(result.message || 'Error registering user');
                    }
                }
            } catch (error) {
                console.error('Error registering user:', error);
                alert('An error occurred while registering the user: ' + error.message);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            fetchUsers();
        });
    </script>
</body>
</html>
