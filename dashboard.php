<?php
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location:index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MoneyMate — Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="bg-grid"></div>
<div class="noise-overlay"></div>

<div class="container">

    <!-- HEADER -->
    <header class="app-header">
        <div class="logo-mark">M</div>
        <div class="header-text">
            <h1>MoneyMate</h1>
            <p class="tagline">Hey <?php echo htmlspecialchars($_SESSION['name']); ?>, here's your spending 👋</p>
        </div>
        <a href="logout.php" class="logout-link">Logout</a>
        <button id="darkModeToggle" class="icon-btn" title="Toggle Dark Mode">
            <span class="toggle-icon">◑</span>
        </button>
    </header>

    <!-- STATS ROW -->
    <div class="stats-row">
        <div class="stat-chip">
            <div class="stat-icon-wrap purple">💸</div>
            <div class="stat-body">
                <span class="stat-label">Total Spent</span>
                <span class="stat-value">₹<span id="total">0</span></span>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-icon-wrap teal">📋</div>
            <div class="stat-body">
                <span class="stat-label">Entries</span>
                <span class="stat-value" id="entryCount">0</span>
            </div>
        </div>
        <div class="stat-chip">
            <div class="stat-icon-wrap pink">🏆</div>
            <div class="stat-body">
                <span class="stat-label">Top Category</span>
                <span class="stat-value" id="topCat">—</span>
            </div>
        </div>
    </div>

    <!-- FORM CARD -->
    <div class="card form-card">
        <div class="card-header-row">
            <h2 class="card-title">Add Expense</h2>
            <span class="card-badge">+ New</span>
        </div>
        <form id="expense-form">
            <div class="form-row">
                <div class="field-wrap">
                    <label>Date</label>
                    <input type="date" id="date" required>
                </div>
                <div class="field-wrap">
                    <label>Category</label>
                    <select id="category" required>
                        <option value="">Select…</option>
                        <option value="Food">🍜 Food</option>
                        <option value="Transport">🚌 Transport</option>
                        <option value="Shopping">🛍️ Shopping</option>
                        <option value="Bills">📄 Bills</option>
                        <option value="Entertainment">🎮 Entertainment</option>
                        <option value="Health">💊 Health</option>
                        <option value="Others">📦 Others</option>
                    </select>
                </div>
                <div class="field-wrap">
                    <label>Amount (₹)</label>
                    <input type="number" id="amount" placeholder="0.00" min="0" step="0.01" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <span>+ Add Expense</span>
            </button>
        </form>
    </div>

    <!-- DASHBOARD GRID -->
    <div class="dashboard-grid">
        <div class="card chart-card">
            <div class="card-header-row">
                <h2 class="card-title">Spending Breakdown</h2>
                <span class="card-dot">📊</span>
            </div>
            <div class="chart-wrap">
                <canvas id="chart"></canvas>
                <div id="chartEmpty" class="chart-empty">
                    <span>📭</span>
                    <p>No data yet</p>
                </div>
            </div>
        </div>

        <div class="card insight-panel">
            <div class="card-header-row">
                <h2 class="card-title">AI Insight</h2>
                <span class="card-dot">🧠</span>
            </div>
            <div id="insight" class="insight-content">
                <div class="insight-empty">
                    <span class="insight-icon">🧠</span>
                    <p>Add expenses to get personalized insights.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- LIST SECTION -->
    <div class="card list-card">
        <div class="list-header">
            <div class="card-header-row" style="flex:1;margin-bottom:0;">
                <h2 class="card-title" style="margin-bottom:0;">Expense History</h2>
            </div>
            <div class="list-actions">
                <input type="text" id="search" placeholder="🔍 Search…" class="search-input">
                <button id="downloadBtn" class="btn btn-ghost">↓ Export CSV</button>
                <button id="resetBtn" class="btn btn-danger-ghost">✕ Reset</button>
            </div>
        </div>

        <div class="table-wrap">
            <table id="expense-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="expense-list">
                    <tr id="emptyRow">
                        <td colspan="4" class="empty-state">No expenses recorded yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script src="assets/js/script.js"></script>
</body>
</html>