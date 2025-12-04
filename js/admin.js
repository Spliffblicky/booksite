const base = "http://localhost:8080/backend/";

document.addEventListener('DOMContentLoaded', () => {
    if (sessionStorage.getItem('role') === 'admin') loadAdminBooks();
});

async function loadAdminBooks() {
    const list = document.getElementById('adminBooksList');
    list.innerHTML = 'Loading...';
    try {
        const res = await fetch(base + 'get_books.php');
        const books = await res.json();
        list.innerHTML = '';
        books.forEach(b => {
            const div = document.createElement('div');
            div.className = 'w3-card w3-padding w3-margin-bottom';
            div.innerHTML = `
                <h4>${b.bookname}</h4>
                <p>${b.author}</p>
                <p>${b.description}</p>
                <p>Stock: ${b.quantity}</p>
                <p>Price: $${parseFloat(b.price).toFixed(2)}</p>`;
            list.appendChild(div);
        });
    } catch {
        list.innerHTML = 'Error loading books';
    }
}

function showSummary() {
    const box = document.getElementById('summaryContainer');
    box.style.display = box.style.display === 'none' ? 'block' : 'none';
}

async function getDailySummary() {
    const date = document.getElementById('summaryDate').value;
    const result = document.getElementById('summaryResult');
    if (!date) return;
    try {
        const res = await fetch(base + 'adminsummary.php?date=' + date);
        const data = await res.json();
        result.innerHTML = `
            <h4>Summary for ${date}</h4>
            <p>Total Orders: ${data.orders}</p>
            <p>Total Revenue: $${parseFloat(data.revenue).toFixed(2)}</p>`;
    } catch {
        result.innerHTML = 'Error';
    }
}
