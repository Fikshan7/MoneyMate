/* =============================================
   MONEYMATE — Backend Integrated script.js
   Keep same filename: script.js
   ============================================= */

const form          = document.getElementById("expense-form");
const categoryInput = document.getElementById("category");
const amountInput   = document.getElementById("amount");
const dateInput     = document.getElementById("date");
const expenseList   = document.getElementById("expense-list");
const totalDisplay  = document.getElementById("total");
const entryCount    = document.getElementById("entryCount");
const topCat        = document.getElementById("topCat");
const searchInput   = document.getElementById("search");
const resetBtn      = document.getElementById("resetBtn");
const darkModeToggle = document.getElementById("darkModeToggle");
const downloadBtn   = document.getElementById("downloadBtn");
const insightBox    = document.getElementById("insight");
const chartEmpty    = document.getElementById("chartEmpty");

let expenses = [];
let isDark = localStorage.getItem("darkMode") !== "false";

dateInput.valueAsDate = new Date();
applyTheme();

const catEmoji = {
    Food: "🍜", Transport: "🚌", Shopping: "🛍️",
    Bills: "📄", Entertainment: "🎮", Health: "💊", Others: "📦"
};

/* ---------------- LOAD DATA ---------------- */
loadExpenses();

function loadExpenses() {
    fetch("api/fetch_expense.php")
    .then(res => res.json())
    .then(data => {
        expenses = data.map(row => ({
            id: row.id,
            date: row.date,
            category: row.category,
            amount: parseFloat(row.amount)
        }));
        updateUI();
    });
}

/* ---------------- FORM SUBMIT ---------------- */
form.addEventListener("submit", e => {
    e.preventDefault();

    const formData = new FormData();
    formData.append("date", dateInput.value);
    formData.append("category", categoryInput.value);
    formData.append("amount", amountInput.value);

    fetch("api/add_expense.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(() => {
        form.reset();
        dateInput.valueAsDate = new Date();
        loadExpenses();
    });
});

/* ---------------- DELETE ---------------- */
expenseList.addEventListener("click", e => {
    if (e.target.classList.contains("del-btn")) {
        const id = e.target.dataset.id;

        const formData = new FormData();
        formData.append("id", id);

        fetch("api/delete_expense.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.text())
        .then(() => loadExpenses());
    }
});

/* ---------------- SEARCH ---------------- */
searchInput.addEventListener("input", () => {
    updateUI(searchInput.value);
});

/* ---------------- RESET ALL ---------------- */
resetBtn.addEventListener("click", () => {

    if(confirm("Delete all expenses?")){

        fetch("api/reset_expense.php")
        .then(res => res.text())
        .then(() => loadExpenses());

    }

});

/* ---------------- DARK MODE ---------------- */
darkModeToggle.addEventListener("click", () => {
    isDark = !isDark;
    localStorage.setItem("darkMode", isDark);
    applyTheme();
    updateChart();
});

function applyTheme() {
    document.body.classList.toggle("light", !isDark);
    darkModeToggle.querySelector(".toggle-icon").textContent = isDark ? "☀" : "◑";
}

/* ---------------- UPDATE UI ---------------- */
function updateUI(filter = "") {

    const filtered = filter
        ? expenses.filter(e => e.category.toLowerCase().includes(filter.toLowerCase()))
        : expenses;

    expenseList.innerHTML = "";

    if (filtered.length === 0) {
        expenseList.innerHTML = `<tr><td colspan="4" class="empty-state">No expenses found.</td></tr>`;
    }

    filtered.forEach(exp => {
        expenseList.innerHTML += `
        <tr>
            <td>${exp.date}</td>
            <td><span class="category-pill">${catEmoji[exp.category]} ${exp.category}</span></td>
            <td class="td-amount">₹${exp.amount}</td>
            <td class="td-del">
                <button class="del-btn" data-id="${exp.id}">✕</button>
            </td>
        </tr>`;
    });

    let total = expenses.reduce((sum, e) => sum + e.amount, 0);
    totalDisplay.textContent = total.toFixed(2);
    entryCount.textContent = expenses.length;

    if (expenses.length > 0) {
        let catTotals = {};
        expenses.forEach(e => {
            catTotals[e.category] = (catTotals[e.category] || 0) + e.amount;
        });
        let best = Object.entries(catTotals).sort((a,b)=>b[1]-a[1])[0];
        topCat.textContent = best[0];
    } else {
        topCat.textContent = "—";
    }

    generateInsights();
    updateChart();
}

/* ---------------- INSIGHTS ---------------- */
function generateInsights() {
    if (expenses.length === 0) {
        insightBox.innerHTML = `
            <div class="insight-empty">
                <span class="insight-icon">🧠</span>
                <p>Add expenses to get personalized insights.</p>
            </div>`;
        return;
    }

    let total = expenses.reduce((s,e)=>s+e.amount,0);
    let catTotals = {};
    expenses.forEach(e=>{
        catTotals[e.category]=(catTotals[e.category]||0)+e.amount;
    });

    let sorted = Object.entries(catTotals).sort((a,b)=>b[1]-a[1]);
    let top = sorted[0];
    let pct = ((top[1]/total)*100).toFixed(0);
    let avgPerEntry = (total/expenses.length).toFixed(2);

    insightBox.innerHTML = `
        <div class="insight-box">
            <div class="insight-box-icon">${catEmoji[top[0]] || '📦'}</div>
            <div class="insight-box-body">
                <div class="insight-box-label">Top Spending</div>
                <div class="insight-highlight">${top[0]} — ${pct}% of total</div>
                <div class="insight-sub">₹${top[1].toFixed(2)} spent across ${expenses.filter(e=>e.category===top[0]).length} entries</div>
            </div>
        </div>
        <div class="insight-box" style="background:linear-gradient(135deg,rgba(6,182,212,0.1),rgba(16,185,129,0.07));border-color:rgba(6,182,212,0.2);">
            <div class="insight-box-icon">📊</div>
            <div class="insight-box-body">
                <div class="insight-box-label" style="color:var(--c-neon-b);">Average Entry</div>
                <div class="insight-highlight">₹${avgPerEntry} per expense</div>
                <div class="insight-sub">Total ₹${total.toFixed(2)} across ${expenses.length} entries</div>
            </div>
        </div>`;
}

/* ---------------- CHART ---------------- */
let chart;
const PALETTE = ["#a78bfa","#38bdf8","#34d399","#f472b6","#fb923c","#22d3ee","#94a3b8"];

function updateChart() {

    const ctx = document.getElementById("chart");

    if(chart){
        chart.destroy();
    }

    let catTotals = {};
    expenses.forEach(e=>{
        catTotals[e.category]=(catTotals[e.category]||0)+e.amount;
    });

    const labels = Object.keys(catTotals);
    const data = Object.values(catTotals);

    chartEmpty.style.display = labels.length ? "none" : "block";

    if(!labels.length) return;

    chart = new Chart(ctx,{
        type:"doughnut",
        data:{
            labels:labels,
            datasets:[{
                data:data,
                backgroundColor:PALETTE
            }]
        },
        options:{
            responsive:true,
            maintainAspectRatio:false
        }
    });
}

/* ---------------- CSV EXPORT ---------------- */
downloadBtn.addEventListener("click", () => {
    let csv = "Date,Category,Amount\n";

    expenses.forEach(e => {
        csv += `${e.date},${e.category},${e.amount}\n`;
    });

    const blob = new Blob([csv], {type:"text/csv"});
    const url = URL.createObjectURL(blob);

    const a = document.createElement("a");
    a.href = url;
    a.download = "expenses.csv";
    a.click();
});