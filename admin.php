<?php
session_start();
include "includes/db.php";

if(!isset($_SESSION['user_id'])){
    header("Location:index.html");
    exit();
}

$result = mysqli_query($conn,"
SELECT users.name, users.email, expenses.date,
expenses.category, expenses.amount
FROM expenses
JOIN users ON expenses.user_id = users.id
ORDER BY expenses.id DESC
");

// Calculate summary stats
$totalAmount = 0;
$totalEntries = 0;
$userSet = [];
$rows = [];

while($row = mysqli_fetch_assoc($result)){
    $rows[] = $row;
    $totalAmount += $row['amount'];
    $totalEntries++;
    $userSet[$row['email']] = true;
}
$totalUsers = count($userSet);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MoneyMate — Admin Panel</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<style>

/* ── RESET & BASE ── */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
    --c-bg:       #07071a;
    --c-surface:  #0f0f28;
    --c-surface2: #141430;
    --c-border:   rgba(255,255,255,0.07);
    --c-border-hi:rgba(255,255,255,0.13);
    --g-main:     linear-gradient(135deg, #7c3aed, #06b6d4);
    --g-hot:      linear-gradient(135deg, #ec4899, #f97316);
    --g-cool:     linear-gradient(135deg, #06b6d4, #10b981);
    --g-gold:     linear-gradient(135deg, #f59e0b, #ef4444);
    --c-neon-a:   #a78bfa;
    --c-neon-b:   #22d3ee;
    --c-neon-c:   #34d399;
    --c-neon-d:   #f472b6;
    --c-neon-e:   #fb923c;
    --c-text:     #f0f0ff;
    --c-muted:    rgba(240,240,255,0.45);
    --c-dim:      rgba(240,240,255,0.2);
    --font:       'Outfit', sans-serif;
    --mono:       'JetBrains Mono', monospace;
    --radius:     18px;
    --radius-sm:  12px;
}

body {
    font-family: var(--font);
    background: var(--c-bg);
    color: var(--c-text);
    min-height: 100vh;
    overflow-x: hidden;
}

/* ── Background orbs ── */
body::before, body::after {
    content: '';
    position: fixed;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
    animation: orbFloat 20s ease-in-out infinite alternate;
}
body::before {
    width: 700px; height: 700px;
    background: radial-gradient(circle, rgba(124,58,237,0.14) 0%, transparent 65%);
    top: -250px; left: -150px;
}
body::after {
    width: 600px; height: 600px;
    background: radial-gradient(circle, rgba(6,182,212,0.1) 0%, transparent 65%);
    bottom: -200px; right: -120px;
    animation-duration: 26s;
    animation-direction: alternate-reverse;
}
@keyframes orbFloat {
    from { transform: translate(0,0) scale(1); }
    to   { transform: translate(40px,30px) scale(1.08); }
}

/* Grid lines */
.bg-grid {
    position: fixed; inset: 0; z-index: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
    background-size: 60px 60px;
    mask-image: radial-gradient(ellipse at center, rgba(0,0,0,0.5) 0%, transparent 80%);
    pointer-events: none;
}

/* ── Layout ── */
.page {
    position: relative; z-index: 2;
    max-width: 1100px;
    margin: 0 auto;
    padding: 36px 28px 80px;
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* ── Header ── */
.admin-header {
    display: flex;
    align-items: center;
    gap: 16px;
}

.logo-mark {
    width: 52px; height: 52px;
    border-radius: 18px;
    background: var(--g-main);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800;
    font-size: 22px;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 0 40px rgba(124,58,237,0.4), inset 0 1px 0 rgba(255,255,255,0.2);
}

.header-info { flex: 1; }

.header-info h1 {
    font-size: 26px;
    font-weight: 800;
    letter-spacing: -0.5px;
    background: var(--g-main);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.header-info p {
    font-size: 12.5px;
    color: var(--c-muted);
    margin-top: 3px;
    font-weight: 500;
}

.admin-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 14px;
    border-radius: 50px;
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    background: linear-gradient(135deg, rgba(236,72,153,0.2), rgba(249,115,22,0.2));
    border: 1px solid rgba(236,72,153,0.35);
    color: var(--c-neon-d);
    margin-top: 6px;
}

.logout-btn {
    padding: 11px 22px;
    background: var(--g-main);
    color: #fff;
    text-decoration: none;
    border-radius: 13px;
    font-size: 13px;
    font-weight: 600;
    box-shadow: 0 0 30px rgba(124,58,237,0.35);
    transition: all 0.25s;
    font-family: var(--font);
    letter-spacing: 0.2px;
}
.logout-btn:hover {
    opacity: 0.88;
    transform: translateY(-2px);
    box-shadow: 0 0 50px rgba(124,58,237,0.5);
}

/* ── Stat cards ── */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}

.stat-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    padding: 22px 24px;
    display: flex;
    align-items: center;
    gap: 18px;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}
.stat-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 2.5px;
    border-radius: var(--radius) var(--radius) 0 0;
    opacity: 0;
    transition: opacity 0.3s;
}
.stat-card:nth-child(1)::before { background: var(--g-main); }
.stat-card:nth-child(2)::before { background: var(--g-cool); }
.stat-card:nth-child(3)::before { background: var(--g-hot); }

.stat-card:hover { border-color: var(--c-border-hi); transform: translateY(-2px); }
.stat-card:hover::before { opacity: 1; }

.stat-icon {
    width: 48px; height: 48px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.stat-icon.purple { background: linear-gradient(135deg, rgba(124,58,237,0.2), rgba(6,182,212,0.2)); }
.stat-icon.teal   { background: linear-gradient(135deg, rgba(6,182,212,0.2), rgba(16,185,129,0.2)); }
.stat-icon.pink   { background: linear-gradient(135deg, rgba(236,72,153,0.2), rgba(249,115,22,0.2)); }

.stat-body { flex: 1; }

.stat-label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    color: var(--c-muted);
    font-weight: 600;
    margin-bottom: 6px;
}

.stat-val {
    font-family: var(--mono);
    font-size: 24px;
    font-weight: 600;
    color: var(--c-text);
    letter-spacing: -0.5px;
    line-height: 1;
}

/* ── Table card ── */
.table-card {
    background: var(--c-surface);
    border: 1px solid var(--c-border);
    border-radius: var(--radius);
    overflow: hidden;
    transition: border-color 0.3s;
}
.table-card:hover { border-color: var(--c-border-hi); }

.table-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 22px 26px 20px;
    border-bottom: 1px solid var(--c-border);
    gap: 16px;
    flex-wrap: wrap;
}

.table-title {
    font-size: 11.5px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--c-muted);
}

.search-wrap {
    position: relative;
}
.search-wrap input {
    height: 38px;
    width: 240px;
    background: rgba(255,255,255,0.04);
    border: 1.5px solid var(--c-border-hi);
    border-radius: 10px;
    color: var(--c-text);
    font-family: var(--font);
    font-size: 13.5px;
    padding: 0 14px 0 38px;
    outline: none;
    transition: all 0.25s;
}
.search-wrap input::placeholder { color: var(--c-dim); }
.search-wrap input:focus {
    border-color: #7c3aed;
    background: rgba(124,58,237,0.07);
    box-shadow: 0 0 0 3px rgba(124,58,237,0.15);
}
.search-icon {
    position: absolute;
    left: 12px; top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    color: var(--c-dim);
    pointer-events: none;
}

.table-wrap { overflow-x: auto; }

table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

thead tr {
    background: rgba(255,255,255,0.02);
}

thead th {
    text-align: left;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.7px;
    color: var(--c-muted);
    padding: 14px 20px;
    border-bottom: 1px solid var(--c-border);
    white-space: nowrap;
}

tbody tr {
    border-bottom: 1px solid var(--c-border);
    transition: background 0.15s;
    animation: rowIn 0.3s ease both;
}
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: rgba(124,58,237,0.05); }

@keyframes rowIn {
    from { opacity: 0; transform: translateY(-4px); }
    to   { opacity: 1; transform: translateY(0); }
}

tbody td {
    padding: 15px 20px;
    vertical-align: middle;
    white-space: nowrap;
}

/* User cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar {
    width: 34px; height: 34px;
    border-radius: 10px;
    background: var(--g-main);
    display: flex; align-items: center; justify-content: center;
    font-weight: 700;
    font-size: 13px;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 0 14px rgba(124,58,237,0.3);
}

.user-name {
    font-weight: 600;
    font-size: 14px;
    color: var(--c-text);
}
.user-email {
    font-size: 12px;
    color: var(--c-muted);
    margin-top: 1px;
}

/* Date */
.date-cell {
    font-family: var(--mono);
    font-size: 12.5px;
    color: var(--c-muted);
}

/* Category pill */
.cat-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 12.5px;
    font-weight: 500;
    border: 1px solid var(--c-border-hi);
    background: rgba(255,255,255,0.04);
    color: var(--c-text);
}

/* Amount */
.amount-cell {
    font-family: var(--mono);
    font-size: 14px;
    font-weight: 600;
    background: var(--g-main);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Empty */
.empty-state {
    text-align: center;
    padding: 50px;
    color: var(--c-muted);
    font-style: italic;
    font-size: 14px;
}

/* ── Responsive ── */
@media (max-width: 700px) {
    .stats-grid { grid-template-columns: 1fr; }
    .page { padding: 20px 16px 60px; }
    .search-wrap input { width: 100%; }
}
</style>
</head>
<body>

<div class="bg-grid"></div>

<div class="page">

    <!-- HEADER -->
    <header class="admin-header">
        <div class="logo-mark">M</div>
        <div class="header-info">
            <h1>MoneyMate Admin</h1>
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?></p>
            <span class="admin-badge">⚡ Admin Panel</span>
        </div>
        <a href="logout.php" class="logout-btn">Logout</a>
    </header>

    <!-- STATS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon purple">💸</div>
            <div class="stat-body">
                <div class="stat-label">Total Expenses</div>
                <div class="stat-val">₹<?php echo number_format($totalAmount, 2); ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon teal">📋</div>
            <div class="stat-body">
                <div class="stat-label">Total Entries</div>
                <div class="stat-val"><?php echo $totalEntries; ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon pink">👥</div>
            <div class="stat-body">
                <div class="stat-label">Active Users</div>
                <div class="stat-val"><?php echo $totalUsers; ?></div>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-card">
        <div class="table-header">
            <span class="table-title">All Transactions</span>
            <div class="search-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Search user, category…" oninput="filterTable()">
            </div>
        </div>

        <div class="table-wrap">
            <table id="adminTable">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                <?php
                $catEmoji = [
                    'Food'=>'🍜','Transport'=>'🚌','Shopping'=>'🛍️',
                    'Bills'=>'📄','Entertainment'=>'🎮','Health'=>'💊','Others'=>'📦'
                ];
                foreach($rows as $row):
                    $initial = strtoupper(substr($row['name'], 0, 1));
                    $emoji = isset($catEmoji[$row['category']]) ? $catEmoji[$row['category']] : '📦';
                ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="avatar"><?php echo $initial; ?></div>
                            <div>
                                <div class="user-name"><?php echo htmlspecialchars($row['name']); ?></div>
                                <div class="user-email"><?php echo htmlspecialchars($row['email']); ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="date-cell"><?php echo $row['date']; ?></span></td>
                    <td><span class="cat-pill"><?php echo $emoji . ' ' . htmlspecialchars($row['category']); ?></span></td>
                    <td><span class="amount-cell">₹<?php echo number_format($row['amount'], 2); ?></span></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($rows)): ?>
                <tr><td colspan="4" class="empty-state">No expense entries found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
function filterTable() {
    const q = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(q) ? '' : 'none';
    });
}
</script>

</body>
</html>