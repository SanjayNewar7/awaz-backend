<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Awaz - SuperAdmin @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            --sidebar-width: 280px;
            --sidebar-collapsed-width: 80px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--primary) 0%, var(--primary-light) 100%);
            color: white;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            height: var(--header-height);
        }

        .sidebar-logo {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 1rem 1.5rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .menu-item:hover,
        .menu-item.active {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .menu-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background-color: white;
        }

        .menu-icon {
            width: 24px;
            height: 24px;
            margin-right: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .menu-text {
            flex: 1;
        }

        .sidebar.collapsed .menu-text {
            display: none;
        }

        .sidebar.collapsed .menu-item {
            justify-content: center;
            padding: 1rem;
        }

        .sidebar.collapsed .menu-icon {
            margin-right: 0;
        }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .main-content.expanded {
            margin-left: var(--sidebar-collapsed-width);
        }

        .content-header {
            background-color: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-right: 1rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-weight: 600;
        }

        .user-name {
            font-weight: 500;
        }

        .logout-btn {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: var(--background);
        }

        .content-body {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--card-bg);
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid var(--border);
            color: var(--text);
        }

        .btn-outline:hover {
            background-color: var(--accent);
            border-color: var(--primary-light);
            color: var(--primary);
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background-color: #b91c1c;
        }

        .btn-warning {
            background-color: var(--warning);
            color: white;
        }

        .btn-warning:hover {
            background-color: #d97706;
        }

        .btn-success {
            background-color: var(--success);
            color: white;
        }

        .btn-success:hover {
            background-color: #16a34a;
        }

        .analytics-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-container {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .recent-users {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
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

        .search-container {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .search-box {
            display: flex;
            gap: 1rem;
        }

        .search-input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            font-size: 0.9rem;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary-light);
        }

        .users-list {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .users-list-header {
            display: grid;
            grid-template-columns: 80px 1fr 1fr 1fr 1fr;
            padding: 1rem;
            background-color: var(--background);
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-light);
        }

        .user-row {
            display: grid;
            grid-template-columns: 80px 1fr 1fr 1fr 1fr;
            padding: 1rem;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
        }

        .user-row:hover {
            background-color: var(--accent);
        }

        .user-row.selected {
            background-color: rgba(59, 130, 246, 0.1);
        }

        .user-cell {
            font-size: 0.9rem;
        }

        .user-card {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
        }

        .user-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .detail-label {
            font-size: 0.8rem;
            color: var(--text-light);
            text-transform: uppercase;
        }

        .detail-value {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .detail-value.empty {
            color: var(--text-light);
            font-style: italic;
        }

        .document-section {
            margin-top: 1.5rem;
        }

        .document-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .document-images {
            display: flex;
            gap: 1rem;
        }

        .document-image {
            width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
        }

        .posts-section {
            margin-top: 1.5rem;
        }

        .posts-list {
            list-style: none;
        }

        .post-item {
            background-color: var(--background);
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .action-bar {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            justify-content: flex-end;
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
        }

        .page-btn.active {
            background-color: var(--primary-light);
            color: white;
        }

        .page-btn:hover {
            background-color: var(--secondary);
            color: white;
        }

        .loading {
            text-align: center;
            padding: 1rem;
            color: var(--text-light);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-light);
        }

        .warning-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .warning-input:focus {
            outline: none;
            border-color: var(--primary-light);
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main-content {
                margin-left: 0;
            }
            .users-list-header,
            .user-row {
                grid-template-columns: 80px 1fr 1fr;
            }
            .user-cell:nth-child(4),
            .user-cell:nth-child(5) {
                display: none;
            }
            
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        @include('superadmin.partials.sidebar')
        <div class="main-content" id="main-content">
            <header class="content-header">
                <div>
                    <h1 class="page-title" id="page-title">@yield('title')</h1>
                </div>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="user-name">Super Admin</div>
                    </div>
                    <form action="{{ route('superadmin.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </header>
            <div class="content-body">
                @yield('content')
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
