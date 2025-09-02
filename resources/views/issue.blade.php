@extends('superadmin.layout')

@section('title', 'Awaz - SuperAdmin Issues')

@section('content')
<style>
:root {
    --primary: #1e3a8a;
    --primary-light: #3b82f6;
    --secondary: #60a5fa;
    --accent: #93c5fd;
    --boxinreview: #ffaa00;
    --textstatus: #ffffff;
    --boxinpending: #fe7446;
    --danger: #dc2626;
    --success: #22c55e;
    --background: #f0f5ff;
    --card-bg: #ffffff;
    --border: #bfdbfe;
    --text: #1e293b;
    --text-light: #64748b;
}

/* General layout */
.analytics-section, .search-container, .issues-list, .pagination { margin-bottom: 1.5rem; }

/* Search bar */
.search-container { display:flex; gap:1rem; align-items:center; }
#search-input, #filter-status { padding:0.75rem; border:1px solid var(--border); border-radius:0.5rem; font-size:0.9rem; flex:1; }
#search-input:focus, #filter-status:focus { outline:none; border-color:var(--primary-light); box-shadow:0 0 0 2px rgba(59,130,246,0.15);}
#search-btn { padding:0.75rem 1rem; background-color:var(--primary); color:white; border:none; border-radius:0.5rem; cursor:pointer; transition:0.3s ease;}
#search-btn:hover { background-color:var(--primary-light); }

/* Issues table */
.issues-list table { width:100%; border-collapse:collapse; background-color:var(--card-bg); border-radius:0.75rem; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.05);}
.issues-list th, .issues-list td { padding:0.75rem 1rem; text-align:left; font-size:0.9rem; border-bottom:1px solid var(--border); color:var(--text);}
.issues-list th { background-color:var(--primary-light); color:white; font-weight:600;}
.issues-list tr:hover { background-color:var(--accent); color:white; }

/* Action buttons - text only */
.action-btn { padding:0.25rem 0.5rem; border-radius:0.35rem; border:1px solid var(--border); cursor:pointer; font-size:0.8rem; margin-right:0.25rem; background:none; color:var(--primary); transition:0.2s; }
.action-btn:hover { text-decoration:underline; }

/* Pagination */
.pagination { display:flex; justify-content:center; gap:0.5rem; margin-top:1rem;}
.pagination button { padding:0.5rem 1rem; border-radius:0.5rem; border:1px solid var(--border); background-color:var(--card-bg); cursor:pointer; font-size:0.9rem; transition:all 0.3s ease;}
.pagination button.active { background-color:var(--primary-light); color:white;}
.pagination button:hover:not(.active){ background-color:var(--secondary); color:white;}

/* Modal overlay */
.modal-overlay { position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); display:none; justify-content:center; align-items:center; z-index:9999; }
.modal { background-color:var(--card-bg); border-radius:0.75rem; width:90%; max-width:900px; max-height:90%; overflow-y:auto; padding:1.5rem; position:relative; box-shadow:0 4px 20px rgba(0,0,0,0.2);}
.modal-close { position:absolute; top:0.5rem; right:0.75rem; width:30px; height:30px; border:none; border-radius:50%; background:var(--danger); color:white; font-weight:bold; font-size:1rem; cursor:pointer;}
.modal-close:hover { opacity:0.85; }

/* Modal content layout */
.modal-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start; }
.modal-details h3 { color:var(--primary); margin-bottom:0.5rem; }
.modal-details p { margin:0.3rem 0; color:var(--text-light); font-size:0.9rem; }
.modal-photos { display:flex; flex-direction:column; gap:0.75rem;}
.modal-photos img { width:100%; max-height:200px; object-fit:cover; border-radius:0.5rem; }

/* Status badge */
.status-badge {
    display: inline-block;
    min-width: 100px;  /* adjust as needed */
    text-align: center;
    padding: 6px 12px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    white-space: nowrap;
}

.status-pending { color:var(--textstatus); background:var(--boxinpending); border:1px solid var(--border);}
.status-inreview { color:var(--textstatus); background:var(--boxinreview);}
.status-inprogress { color:var(--textstatus); background:var(--primary-light);}
.status-fixed { color:var(--textstatus); color:var(--textstatus); background:var(--success); }

.chart-container {
    margin-bottom: 2rem;
    padding: 1rem;
    background: var(--card-bg);
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.chart-container canvas {
    width: 100% !important;
    max-height: 400px; /* fixed height so chart is visible */
}

/* Counts badges */
.counts-row { display:flex; flex-wrap:wrap; gap:0.5rem; margin-top:0.5rem; }
.count-badge { padding:0.25rem 0.5rem; border-radius:0.4rem; font-size:0.75rem; background:var(--primary-light); color:white; }

@media(max-width:700px){ .modal-grid { grid-template-columns:1fr; } }
</style>

<div class="analytics-section">
<!-- Chart 1: Issues by Report Type -->
    <div class="chart-container">
        <h3>Issues by Report Type</h3>
        <canvas id="reportTypeChart"></canvas>
    </div>

    <!-- Chart 2: Issue Growth (last 14 days) -->
    <div class="chart-container">
        <h3>Issue Growth (Last 14 Days)</h3>
        <canvas id="issueGrowthChart"></canvas>
    </div>

  <!-- Chart 3: Issues by Status -->
<div class="chart-container">
    <h3>Issues by Status</h3>
    <canvas id="issueStatusChart"></canvas>
</div>


</div>

<div class="search-container">
    <input type="text" id="search-input" placeholder="Search heading, description, district">
    <select id="filter-status">
        <option value="">All Statuses</option>
        <option value="Pending">Pending</option>
        <option value="In Review">In Review</option>
        <option value="In Progress">In Progress</option>
        <option value="Fixed">Fixed</option>
    </select>
    <button id="search-btn">Search</button>
</div>

<div class="issues-list" id="issues-list-body">
    <div class="loading">Loading issues...</div>
</div>

<div class="pagination" id="pagination"></div>

<!-- Modal -->
<div class="modal-overlay" id="issue-modal">
    <div class="modal" id="issue-modal-content">
        <button class="modal-close" id="modal-close">&times;</button>
        <div id="modal-body" class="modal-grid"></div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const API_URL = '/api/issues';
let currentPage = 1;
let totalPages = 1;


// Chart instances
let reportTypeChart, issueGrowthChart, topReportersChart;

// Fetch & Render "Issues by Report Type"
async function fetchReportTypeChart(){
    try {
        const res = await fetch('/api/analytics/issues-by-type');
        const data = await res.json();

        const ctx = document.getElementById('reportTypeChart').getContext('2d');
        if(reportTypeChart) reportTypeChart.destroy();
        reportTypeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Issues',
                    data: data.counts,
                    backgroundColor: '#3b82f6'
                }]
            },
            options: { responsive:true, plugins:{legend:{display:false}} }
        });
    } catch(e){ console.error("Report Type Chart Error:", e); }
}

// Fetch & Render "Issue Growth (14 days)"
async function fetchIssueGrowthChart(){
    try {
        const res = await fetch('/api/analytics/issue-growth');
        const data = await res.json();

        const ctx = document.getElementById('issueGrowthChart').getContext('2d');
        if(issueGrowthChart) issueGrowthChart.destroy();
        issueGrowthChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'New Issues',
                    data: data.counts,
                    borderColor: '#1e3a8a',
                    backgroundColor: 'rgba(30,58,138,0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive:true, plugins:{legend:{display:true}} }
        });
    } catch(e){ console.error("Issue Growth Chart Error:", e); }
}

// Chart instance
let issueStatusChart;

// Fetch & Render "Issues by Status"
// Fetch & Render "Issues by Status"
async function fetchIssueStatusChart(){
    try {
        const res = await fetch('/api/analytics/issues-by-status');
        const data = await res.json();

        const maxValue = Math.max(...data.counts);
        const ctx = document.getElementById('issueStatusChart').getContext('2d');
        if(issueStatusChart) issueStatusChart.destroy();

        issueStatusChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Issues',
                    data: data.counts,
                    backgroundColor: [
                        '#fe7446', // Pending
                        '#ffaa00', // In Review
                        '#3b82f6', // In Progress
                        '#22c55e'  // Fixed
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: '#1e293b' },
                        suggestedMax: maxValue + Math.ceil(maxValue * 0.5) // add 10% headroom
                    },
                    x: {
                        ticks: { color: '#1e293b' }
                    }
                }
            }
        });
    } catch(e){ console.error("Issue Status Chart Error:", e); }
}



// Load all charts
fetchReportTypeChart();
fetchIssueGrowthChart();
fetchIssueStatusChart();

// Status badge class
function getStatusClass(status){
    switch(status.toLowerCase()){
        case 'pending': return 'status-pending';
        case 'in review': return 'status-inreview';
        case 'in progress': return 'status-inprogress';
        case 'fixed': return 'status-fixed';
        default: return '';
    }
}

// Render issue rows descending by newest first
function renderIssuesTable(issues){
    issues.sort((a,b)=>new Date(b.created_at) - new Date(a.created_at));
    let table = `<table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>District</th>
                <th>Location</th>
                <th>Report Type</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            ${issues.map(issue=>{
                return `<tr>
                    <td>${issue.id}</td>
                    <td>${issue.heading}</td>
                    <td>${issue.district}</td>
                    <td>${issue.location||'N/A'}</td>
                    <td>${issue.report_type||'N/A'}</td>
                    <td><span class="status-badge ${getStatusClass(issue.status)}">${issue.status}</span></td>
                    <td>
                        <div style="display:flex; gap:0.5rem;">
                            <button class="action-btn action-view" data-id="${issue.id}">View</button>
                            <button class="action-btn action-update" onclick="alert('Update ID ${issue.id}')">Update</button>
                            <button class="action-btn action-delete" onclick="alert('Delete ID ${issue.id}')">Delete</button>
                        </div>
                    </td>
                </tr>`;
            }).join('')}
        </tbody>
    </table>`;
    document.getElementById('issues-list-body').innerHTML = table;

    document.querySelectorAll('.action-view').forEach(btn=>{
        btn.addEventListener('click', ()=>fetchIssueDetails(btn.dataset.id));
    });
}

// Pagination
function renderPagination(){
    let html='';
    if(currentPage>1) html+=`<button data-page="${currentPage-1}">Prev</button>`;
    for(let i=1;i<=totalPages;i++) html+=`<button class="${i===currentPage?'active':''}" data-page="${i}">${i}</button>`;
    if(currentPage<totalPages) html+=`<button data-page="${currentPage+1}">Next</button>`;
    document.getElementById('pagination').innerHTML = html;

    document.querySelectorAll('#pagination button[data-page]').forEach(btn=>{
        btn.addEventListener('click',()=>{currentPage=parseInt(btn.dataset.page); fetchIssues();});
    });
}

// Fetch issues
async function fetchIssues(){
    const listEl = document.getElementById('issues-list-body');
    listEl.innerHTML='<div class="loading">Loading...</div>';
    const status = document.getElementById('filter-status').value;
    const search = document.getElementById('search-input').value;
    try{
        const res = await fetch(`${API_URL}?page=${currentPage}&limit=10${status?`&status=${status}`:''}${search?`&search=${encodeURIComponent(search)}`:''}`);
        const data = await res.json();
        totalPages = data.last_page;
        if(data.issues.length===0){
            listEl.innerHTML='<div class="empty-state">No issues found</div>';
        } else renderIssuesTable(data.issues);
        renderPagination();
    } catch(e){ listEl.innerHTML=`<div style="color:red">${e.message}</div>`; }
}

// Fetch single issue for modal
async function fetchIssueDetails(id){
    const modal = document.getElementById('issue-modal');
    const body = document.getElementById('modal-body');
    modal.style.display='flex';
    body.innerHTML='Loading...';
    try{
        const res = await fetch(`${API_URL}/${id}`);
        const data = await res.json();
        if(data.status!=='success') throw new Error('Failed to fetch issue');
        const issue = data.issue;

        body.innerHTML = `
        <div class="modal-details">
            <h3>${issue.heading}</h3>
            <p><strong>Description:</strong> ${issue.description||'N/A'}</p>
            <p><strong>District:</strong> ${issue.district}</p>
            <p><strong>Ward:</strong> ${issue.ward}</p>
            <p><strong>Location:</strong> ${issue.location||'N/A'}</p>
            <p><strong>Report Type:</strong> ${issue.report_type||'N/A'}</p>
            <p><strong>Status:</strong> <span class="status-badge ${getStatusClass(issue.status)}" id="current-status">${issue.status}</span></p>
            <div class="counts-row">
                <span class="count-badge">Support: ${issue.support_count}</span>
                <span class="count-badge">Affected: ${issue.affected_count}</span>
                <span class="count-badge">Not Sure: ${issue.not_sure_count}</span>
                <span class="count-badge">Invalid: ${issue.invalid_count}</span>
                <span class="count-badge">Fixed: ${issue.fixed_count}</span>
            </div>

            <!-- Status Update -->
            <div style="margin-top:1rem;">
                <select id="status-update" style="padding:0.5rem;border:1px solid var(--border);border-radius:0.5rem;">
                    <option value="">--Update Status--</option>
                    <option value="Pending">Pending</option>
                    <option value="In Review">In Review</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Fixed">Fixed</option>
                </select>
                <button id="update-status-btn" style="padding:0.5rem 1rem;margin-left:0.5rem;background:var(--primary);color:white;border:none;border-radius:0.5rem;cursor:pointer;">Update Status</button>
            </div>
        </div>

        <div class="modal-photos">
            ${issue.photo1 ? `<img src="${issue.photo1}" alt="Photo 1">` : ''}
            ${issue.photo2 ? `<img src="${issue.photo2}" alt="Photo 2">` : ''}
        </div>
        `;

        // Status update handler
        document.getElementById('update-status-btn').addEventListener('click', async () => {
            const select = document.getElementById('status-update');
            const newStatus = select.value;
            if(!newStatus) return alert('Please select a status to update.');

            const confirmUpdate = confirm(`Are you sure you want to update status to "${newStatus}"?`);
            if(!confirmUpdate) return;

            try {
                const res = await fetch(`${API_URL}/${issue.id}/status`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ status: newStatus })
                });
                const data = await res.json();
                if(data.success){
                    alert('Status updated successfully!');
                    document.getElementById('current-status').textContent = newStatus;
                    document.getElementById('current-status').className = `status-badge ${getStatusClass(newStatus)}`;
                    select.value = '';
                    fetchIssues(); // refresh table
                } else throw new Error(data.message || 'Failed to update status');
            } catch(e){ alert(`Error: ${e.message}`); }
        });

    } catch(e){ body.innerHTML=`<div style="color:red">${e.message}</div>`; }
}

// Close modal
document.getElementById('modal-close').addEventListener('click',()=>{document.getElementById('issue-modal').style.display='none';});

// Search
document.getElementById('search-btn').addEventListener('click',()=>{currentPage=1;fetchIssues();});

// Initial load
fetchIssues();
</script>
@endsection
