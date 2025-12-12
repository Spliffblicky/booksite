document.addEventListener("DOMContentLoaded", () => {
    if (sessionStorage.getItem("role") === "admin") {
        loadAdminBooks();
        loadUserOrders();
    }
});

async function loadAdminBooks() {
    const list = document.getElementById("adminBooksList");
    list.innerHTML = "Loading...";

    try {
        const res = await fetch(API + "get_books.php");
        const books = await res.json();

        list.innerHTML = "";

        books.forEach(b => {
            const div = document.createElement("div");
            div.className = "w3-container w3-col s12 m6 l4 w3-padding w3-margin-bottom book-card";
            div.style.height = "100%";

            div.innerHTML = `
                <div class="w3-card w3-padding" style="height:100%;">
                    <h4>${b.bookname}</h4>
                    <p>${b.author}</p>

                    <p class="book-description">${b.description}</p>

                    <p>Stock: ${b.quantity}</p>
                    <p>Price: $${parseFloat(b.price).toFixed(2)}</p>
                    <p>Supplier ID: ${b.supplier_id}</p>

                    <input type="number" id="qty_${b.book_id}" class="w3-input w3-margin-top" placeholder="Order quantity">

                    <button class="w3-button w3-blue w3-margin-top"
                    onclick="orderFromSupplier(${b.book_id}, ${b.supplier_id}, '${b.bookname}', ${b.price})">
                        Order More
                    </button>
                </div>
            `;

            list.appendChild(div);
        });

    } catch {
        list.innerHTML = "Error loading books";
    }
}


function orderFromSupplier(book_id, supplier_id, bookname, price) {
    const qty = parseInt(document.getElementById(`qty_${book_id}`).value);
    if (!qty || qty < 1) return alert("Enter a valid quantity");

    const amount = qty * price;

    const data = {
        book_id,
        supplier_id,
        quantity: qty,
        amount,
        bookname
    };

    fetch(API + "admin_order_books.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data)
    })
        .then(res => res.json())
        .then(r => {
            if (r.status === "success") {
                alert("Order sent to supplier");
            } else {
                alert("Failed to send order");
            }
        })
}

function loadUserOrders() {
    fetch(API + "get_user_orders.php")
        .then(res => res.json())
        .then(displayUserOrders)
}

function displayUserOrders(orders) {
    const box = document.getElementById("adminUserOrders");
    box.innerHTML = "";

    if (!orders || orders.length === 0) {
        box.innerHTML = "<p>No orders available.</p>";
        return;
    }

    orders.forEach(o => {
        const div = document.createElement("div");
        div.className = "w3-card w3-padding w3-margin-bottom";

        div.innerHTML = `
            <button onclick="toggleOrder(${o.order_id})" class="w3-button w3-block w3-light-grey">
                Order #${o.order_id} - ${o.username} - $${parseFloat(o.total).toFixed(2)} 
            </button>

            <div id="order_details_${o.order_id}" class="w3-hide w3-padding w3-white">
                ${o.items.map(i =>
            `<p>${i.bookname} x ${i.quantity} = $${(i.price * i.quantity).toFixed(2)}</p>`
        ).join("")}

                <p>Status: <b>${o.status}</b></p>

                <button class="w3-button w3-green" 
                onclick="completeOrder(${o.order_id})"
                ${o.status !== "approved" ? "disabled" : ""}>
                    Mark Completed
                </button>
            </div>
        `;

        box.appendChild(div);
    });
}

function toggleOrder(id) {
    const d = document.getElementById("order_details_" + id);
    d.classList.toggle("w3-hide");
}

function completeOrder(order_id) {
    fetch(API + "update_order_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ order_id })
    })
        .then(res => res.json())
        .then(r => {
            if (r.status === "success") {
                alert("Order marked as completed");
                loadUserOrders();
            }
        });
}

function showSummary() {
    const box = document.getElementById("summaryContainer");
    box.style.display = box.style.display === "none" ? "block" : "none";
}

async function getDailySummary() {
    const date = document.getElementById("summaryDate").value;
    const result = document.getElementById("summaryResult");
    if (!date) return;

    try {
        const res = await fetch(API + "adminsummary.php?date=" + date);
        const data = await res.json();

        result.innerHTML = `
            <h4>Summary for ${date}</h4>
            <p>Total Orders: ${data.orders}</p>
            <p>Total Revenue: $${parseFloat(data.revenue).toFixed(2)}</p>
        `;
    } catch {
        result.innerHTML = "Error";
    }
}
