@extends('superadmin.layout')

@section('title', 'Awaz - SuperAdmin Analytics')

@section('content')
    <style>
        .filter-section {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            align-items: center;
        }
        .filter-select {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid var(--border);
            font-size: 0.9rem;
            background-color: var(--card-bg);
            color: var(--text);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .filter-select:disabled {
            background-color: var(--background);
            color: var(--text-light);
        }
        .filter-select:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .filter-select option {
            padding: 0.5rem;
        }
        .analytics-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
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
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary);
        }
        canvas {
            width: 100%;
            height: auto;
        }
        .analytics-table {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .analytics-table h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--primary);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }
        th {
            font-weight: 600;
            color: var(--text-light);
            text-transform: uppercase;
        }
        td {
            font-size: 0.9rem;
        }
        .loading {
            text-align: center;
            padding: 2rem;
            color: var(--text-light);
        }
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-light);
        }
        @media (max-width: 768px) {
            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-select {
                width: 100%;
            }
        }
        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn.btn-outline {
            background-color: transparent;
            border: 1px solid var(--border);
            color: var(--primary);
        }
        .btn.btn-outline:hover {
            background-color: var(--primary-light);
            color: var(--text);
        }
        .btn.btn-primary {
            background-color: #3b82f6;
            color: #ffffff;
            font-weight: 500;
        }
        .btn.btn-primary:hover {
            background-color: #2563eb;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        .region-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        .region-card {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            padding: 1.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .region-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .modal {
            position: fixed;
            padding-left: 16rem;
            inset: 0;
            background-color: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
        }
        .modal-content {
            background-color: var(--card-bg);
            border-radius: 0.75rem;
            padding: 2rem;
            max-height: 80vh;
            overflow-y: auto;
            width: 90%;
            max-width: 4xl;
            position: relative;
        }
        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: #dc2626;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 500;
        }
        .issues-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        .issue-card {
            border: 1px solid var(--border);
            border-radius: 0.5rem;
            padding: 0.75rem;
            background-color: var(--background);
        }
        .issue-card img {
            margin-top: 0.5rem;
            border-radius: 0.5rem;
            max-height: 120px;
            width: 100%;
            object-fit: cover;
        }
    </style>

    <header class="flex justify-between items-center mb-6">
        <div class="logo font-bold text-xl">Awaz Analytics</div>
        <button class="btn btn-outline" id="refresh-btn">Refresh Data</button>
    </header>

    <div class="filter-section">
        <select class="filter-select" id="district-filter">
            <option value="">Select District</option>
            <option value="Kathmandu">Kathmandu</option>
            <option value="Lalitpur">Lalitpur</option>
            <option value="Bhaktapur">Bhaktapur</option>
            <option value="Chitwan">Chitwan</option>
            <option value="Pokhara (Kaski)">Pokhara (Kaski)</option>
            <option value="Morang">Morang</option>
            <option value="Rupandehi">Rupandehi</option>
            <option value="Dang">Dang</option>
            <option value="Dharan (Sunsari)">Dharan (Sunsari)</option>
            <option value="Birgunj (Parsa)">Birgunj (Parsa)</option>
        </select>

        <select class="filter-select" id="region-filter" disabled>
            <option value="">Select Region</option>
        </select>

        <select class="filter-select" id="ward-filter" disabled>
            <option value="">Select Ward</option>
        </select>

        <button class="btn btn-outline" id="clear-filters">Clear Filters</button>
        <button class="btn btn-primary" id="load-data-btn">Load Data</button>
    </div>

    <div class="analytics-section">
        <div class="chart-container">
            <h3>Issues by District</h3>
            <canvas id="districtChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Issues by Report Type</h3>
            <canvas id="reportTypeChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Issues by Status</h3>
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <div class="analytics-table">
        <h3>Issue Statistics</h3>
        <table>
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody id="issueSummary">
                <tr><td colspan="2" class="loading">Loading...</td></tr>
            </tbody>
        </table>
    </div>

    <div class="analytics-table">
        <h3>Issues by Region</h3>
        <div id="regionContainer" class="region-grid">
            <p class="loading col-span-full">Loading...</p>
        </div>
    </div>

    <div id="modalsContainer"></div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const API_URL     = '{{ route("superadmin.analytics.data") }}';
    const REGIONS_URL = '{{ route("superadmin.regions", ":district") }}';
    const WARDS_URL   = '{{ route("superadmin.wards", [":district", ":region"]) }}';

    const regionContainer = document.getElementById('regionContainer');
    const modalsContainer = document.getElementById('modalsContainer');

    let districtChart, reportTypeChart, statusChart;

    const districtFilter   = document.getElementById('district-filter');
    const regionFilter     = document.getElementById('region-filter');
    const wardFilter       = document.getElementById('ward-filter');
    const refreshBtn       = document.getElementById('refresh-btn');
    const clearFiltersBtn  = document.getElementById('clear-filters');
    const loadDataBtn      = document.getElementById('load-data-btn');

    const generateColors = (count) => {
        const colors = [];
        for (let i = 0; i < count; i++) {
            const hue = Math.floor(Math.random() * 360);
            colors.push(`hsl(${hue}, 70%, 60%)`);
        }
        return colors;
    };

    // Build params ONLY when pressing "Load Data" or on initial page load
    const fetchAnalytics = async () => {
        regionContainer.innerHTML = '<p class="loading col-span-full">Loading...</p>';
        modalsContainer.innerHTML = '';

        try {
            const params = new URLSearchParams();
            if (districtFilter.value) params.append('district', districtFilter.value);
            if (regionFilter.value)   params.append('region', regionFilter.value);
            if (wardFilter.value)     params.append('ward', wardFilter.value);

            const response = await fetch(`${API_URL}?${params.toString()}`);
            const data = await response.json();
            if (data.status !== 'success') throw new Error('Failed to fetch analytics');

            // Charts
            if (districtChart) districtChart.destroy();
            if (reportTypeChart) reportTypeChart.destroy();
            if (statusChart) statusChart.destroy();

            districtChart = new Chart(document.getElementById('districtChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.top_districts.map(d => d.district),
                    datasets: [{
                        label: 'Issues',
                        data: data.top_districts.map(d => d.issue_count),
                        backgroundColor: 'rgba(59,130,246,0.8)'
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });

            reportTypeChart = new Chart(document.getElementById('reportTypeChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: data.report_types.map(r => r.report_type ?? 'Unknown'),
                    datasets: [{
                        data: data.report_types.map(r => r.count),
                        backgroundColor: generateColors(data.report_types.length)
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 20, padding: 15 } } }
                }
            });

            statusChart = new Chart(document.getElementById('statusChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Fixed'],
                    datasets: [{
                        data: [data.status_counts.pending, data.status_counts.fixed],
                        backgroundColor: ['#dc2626', '#22c55e']
                    }]
                },
                options: { responsive: true }
            });

            // Issue Summary (filtered)
            document.getElementById('issueSummary').innerHTML = `
                <tr><td>Total Issues</td><td>${data.total_issues}</td></tr>
                <tr><td>Pending Issues</td><td>${data.status_counts.pending}</td></tr>
                <tr><td>Fixed Issues</td><td>${data.status_counts.fixed}</td></tr>
                <tr><td>Total Support Count</td><td>${data.support_count}</td></tr>
                <tr><td>Total Affected Count</td><td>${data.affected_count}</td></tr>
            `;

            // Issues by Region (uses "location"; overall on initial load, or restricted to selected district only)
            regionContainer.innerHTML = '';
            if (!data.issues_by_region || data.issues_by_region.length === 0) {
                regionContainer.innerHTML = '<p class="empty-state col-span-full">No issues found.</p>';
            } else {
                data.issues_by_region.forEach(region => {
                    const regionId = `modal-${region.region.replace(/\s+/g, '-').replace(/[()]/g,'')}`;
                    const card = document.createElement('div');
                    card.className = 'region-card';
                    card.innerHTML = `<h4 class="font-semibold mb-1">${region.region}</h4><p>${region.issue_count} issue(s)</p>`;
                    card.addEventListener('click', () => { document.getElementById(regionId).style.display = 'flex'; });
                    regionContainer.appendChild(card);

                    const modal = document.createElement('div');
                    modal.id = regionId;
                    modal.className = 'modal';
                    modal.innerHTML = `
                        <div class="modal-content">
                            <h3 class="text-xl font-bold mb-4">${region.region} Issues</h3>
                            <button class="modal-close" onclick="document.getElementById('${regionId}').style.display='none'">Close</button>
                            <div class="issues-grid">
                                ${region.issues.map(issue => `
                                    <div class="issue-card">
                                        <h4 class="font-semibold mb-1">${issue.heading ?? ''}</h4>
                                        <p class="text-sm text-gray-600">${issue.description ?? ''}</p>
                                        <p class="text-xs text-gray-400 mt-1">Report Type: ${issue.report_type ?? 'N/A'}</p>
                                        <p class="text-xs text-gray-400">District: ${issue.district ?? 'N/A'}</p>
                                        <p class="text-xs text-gray-400">Ward: ${issue.ward ?? 'N/A'}</p>
                                        ${issue.photo1 ? `<img src="${issue.photo1}" alt="Photo">` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `;
                    modalsContainer.appendChild(modal);
                });
            }

        } catch (err) {
            console.error(err);
            regionContainer.innerHTML = '<p class="empty-state col-span-full">Error loading data</p>';
        }
    };

    // ----- Events -----
    // Populate Regions when District changes (no auto-fetch of analytics)
    districtFilter.addEventListener('change', () => {
        regionFilter.disabled = true;
        wardFilter.disabled = true;
        regionFilter.innerHTML = '<option value="">Select Region</option>';
        wardFilter.innerHTML = '<option value="">Select Ward</option>';
        if (districtFilter.value) { fetchRegions(districtFilter.value); }
    });

    // Populate Wards when Region changes (no auto-fetch of analytics)
    regionFilter.addEventListener('change', () => {
        wardFilter.disabled = true;
        wardFilter.innerHTML = '<option value="">Select Ward</option>';
        if (districtFilter.value && regionFilter.value) {
            fetchWards(districtFilter.value, regionFilter.value);
        }
    });

    // "Load Data" explicitly fetches analytics using the current filters
    loadDataBtn.addEventListener('click', fetchAnalytics);

    // Refresh: keep current filters and reload analytics
    refreshBtn.addEventListener('click', fetchAnalytics);

    // Clear filters and reload overall analytics
    clearFiltersBtn.addEventListener('click', () => {
        districtFilter.value = '';
        regionFilter.value = '';
        wardFilter.value = '';
        regionFilter.disabled = true;
        wardFilter.disabled = true;
        regionFilter.innerHTML = '<option value="">Select Region</option>';
        wardFilter.innerHTML = '<option value="">Select Ward</option>';
        fetchAnalytics();
    });

    // Initial load: overall analytics
    document.addEventListener('DOMContentLoaded', fetchAnalytics);

    // Close all modals on Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
        }
    });

    // ----- Helpers -----
    const fetchRegions = async (district) => {
        regionFilter.disabled = true;
        regionFilter.innerHTML = '<option value="">Select Region</option>';
        try {
            const url = REGIONS_URL.replace(':district', encodeURIComponent(district));
            const res = await fetch(url);
            const data = await res.json();
            if (data.status === 'success') {
                Object.keys(data.regions).forEach(region => {
                    regionFilter.innerHTML += `<option value="${region}">${region}</option>`;
                });
                regionFilter.disabled = false;
            }
        } catch (err) {
            console.error("Error fetching regions:", err);
        }
    };

    const fetchWards = async (district, region) => {
        wardFilter.disabled = true;
        wardFilter.innerHTML = '<option value="">Select Ward</option>';
        try {
            const url = WARDS_URL
                .replace(':district', encodeURIComponent(district))
                .replace(':region', encodeURIComponent(region));
            const res = await fetch(url);
            const data = await res.json();
            if (data.status === 'success') {
                data.wards.forEach(ward => {
                    wardFilter.innerHTML += `<option value="${ward}">${ward}</option>`;
                });
                wardFilter.disabled = false;
            }
        } catch (err) {
            console.error("Error fetching wards:", err);
        }
    };
</script>
@endsection
