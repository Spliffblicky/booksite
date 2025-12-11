document.addEventListener("DOMContentLoaded", () => {
    const role = sessionStorage.getItem("role");
    const userId = sessionStorage.getItem("user_id");
    if (role !== "supplier") return;

    loadSupplierOrders(userId);
});

function loadSupplierOrders(supplierId) {
    fetch(API + "getsupplierorder.php?supplier_id=" + supplierId)
        .then(res => res.json())
        .then(displaySupplierOrders)
        .catch(err => {
            console.error(err);
            document.getElementById("supplierOrdersList").innerHTML =
                "<p>Error loading supplier orders</p>";
        });
}

function displaySupplierOrders(orders) {
    const container = document.getElementById("supplierOrdersList");
    container.innerHTML = "";

    if (!orders || orders.length === 0) {
        container.innerHTML = "<p>No supplier orders available.</p>";
        return;
    }

    orders.forEach(o => {
        const card = document.createElement("div");
        card.className = "w3-card w3-padding w3-margin w3-light-grey";

        card.innerHTML = `
            <h4>Order #${o.order_id}</h4>
            <p>Status: <b>${o.status.toUpperCase()}</b></p>
            <p>Amount: $${parseFloat(o.amount).toFixed(2)}</p>

            <button class="w3-button w3-green w3-margin-right"
                onclick="updateSupplierOrderStatus(${o.order_id}, 'approved')"
                ${o.status !== "pending" ? "disabled" : ""}>ACCEPT</button>

            <button class="w3-button w3-red"
                onclick="updateSupplierOrderStatus(${o.order_id}, 'rejected')"
                ${o.status !== "pending" ? "disabled" : ""}>DENY</button>
        `;

        container.appendChild(card);
    });
}

function updateSupplierOrderStatus(order_id, status) {
    fetch(API + "update_order_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ order_id, status })
    })
        .then(res => res.json())
        .then(result => {
            if (result.status === "success") {
                alert("Order updated to: " + status);
                loadSupplierOrders(sessionStorage.getItem("user_id"));
            } else {
                alert("Error updating order");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Server error updating order");
        });
}
