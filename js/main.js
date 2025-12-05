const API = "http://localhost:8080/backend/";

let isLoggedIn = false;
let currentUser = null;
let orderItems = [];

const signupForm = document.getElementById('signupForm');
const loginForm = document.getElementById('loginForm');
const addressForm = document.getElementById('addressForm');
const roleContainer = document.getElementById('roleContainer');
const booksList = document.getElementById('booksList');
const itemsList = document.getElementById('itemsList');
const loginBtn = document.getElementById('loginBtn');
const signupBtn = document.getElementById('signupBtn');
const logoutBtn = document.getElementById('logoutBtn');
const otherItemsContainer = document.getElementById('otherItemsContainer');

document.addEventListener("DOMContentLoaded", () => {
    const savedId = sessionStorage.getItem("user_id");
    const savedRole = sessionStorage.getItem("role");
    const savedName = sessionStorage.getItem("username");

    if (savedId && savedRole) {
        currentUser = { id: savedId, role: savedRole, username: savedName };
        isLoggedIn = true;
        applyLoginUI();
        redirectRole(savedRole);
    }

    loadBooks();
});

if (signupForm) {
    signupForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(API + "signup.php", { method: "POST", body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success") {
                    loginUser(data.user_id, formData.get("username"), formData.get("role"));
                    this.reset();
                    document.getElementById("signupModal").style.display = "none";
                } else {
                    alert(data.message);
                }
            });
    });
}

if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch(API + "login.php", { method: "POST", body: formData })
            .then(res => res.json())
            .then(result => {
                if (result.status === "success") {
                    loginUser(result.user_id, formData.get("username"), result.role);
                    this.reset();
                } else {
                    alert(result.message);
                }
            });
    });
}

logoutBtn?.addEventListener("click", () => {
    sessionStorage.clear();
    location.reload();
});

function loginUser(id, username, role) {
    currentUser = { id, username, role };
    isLoggedIn = true;

    sessionStorage.setItem("user_id", id);
    sessionStorage.setItem("username", username);
    sessionStorage.setItem("role", role);

    applyLoginUI();
    redirectRole(role);
    loadBooks();
}

function applyLoginUI() {
    loginBtn.style.display = "none";
    signupBtn.style.display = "none";
    logoutBtn.style.display = "inline-block";
    roleContainer.innerHTML = `<h3>Logged in as <b>${currentUser.role.toUpperCase()}</b> (${currentUser.username})</h3>`;
    otherItemsContainer.style.display = currentUser.role === "user" ? "block" : "none";
}

function redirectRole(role) {
    document.getElementById("mainContainer").style.display = role === "user" ? "block" : "none";
    document.getElementById("adminContainer").style.display = role === "admin" ? "block" : "none";
    document.getElementById("supplierContainer").style.display = role === "supplier" ? "block" : "none";
}

if (addressForm) {
    addressForm.addEventListener("submit", function (e) {
        e.preventDefault();
        if (!currentUser) return;

        const formData = new FormData(this);
        formData.append("user_id", currentUser.id);

        fetch(API + "address.php", { method: "POST", body: formData })
            .then(res => res.json())
            .then(result => {
                if (result.status === "success") {
                    alert("Address saved");
                    this.reset();
                    document.getElementById("addressModal").style.display = "none";
                } else {
                    alert(result.message);
                }
            });
    });
}

async function loadBooks() {
    if (!booksList) return;
    booksList.innerHTML = "Loading...";

    try {
        const res = await fetch(API + "get_books.php");
        const books = await res.json();
        booksList.innerHTML = "";

        books.forEach(book => {
            const div = document.createElement("div");
            div.className = "w3-col s12 m6 l4 w3-margin-bottom";

            div.innerHTML = `
                <div class="w3-padding">
                    <img src="${book.image_path}" style="width:100%; height:200px; object-fit:cover">
                    <h4>${book.bookname}</h4>
                    <p>${book.author}</p>
                    <p>$${parseFloat(book.price).toFixed(2)}</p>
                    <p>Stock: ${book.quantity}</p>
                    ${currentUser?.role === "user"
                    ? `<button class="w3-button w3-teal" onclick="addToOrder(${book.book_id}, '${book.bookname}', ${book.price}, ${book.quantity})">Order</button>`
                    : `<button class="w3-button w3-gray" disabled>Login as User</button>`
                }
                </div>
            `;

            booksList.appendChild(div);
        });
    } catch (err) {
        booksList.innerHTML = "Error loading books.";
    }
}

function addToOrder(book_id, bookname, price, stock) {
    const q = parseInt(prompt(`Enter quantity (1-${stock})`));
    if (!q || q < 1 || q > stock) return;

    const existing = orderItems.find(i => i.book_id === book_id);

    if (existing) {
        existing.quantity += q;
    } else {
        orderItems.push({ book_id, bookname, price, quantity: q });
    }

    updateOrderList();
}

function updateOrderList() {
    itemsList.innerHTML = "";
    orderItems.forEach(i => {
        const li = document.createElement("li");
        li.innerHTML = `
            ${i.bookname} - $${i.price} x ${i.quantity}
            <button class="w3-button w3-red w3-small" onclick="removeFromOrder(${i.book_id})">Remove</button>
        `;
        itemsList.appendChild(li);
    });
}

function removeFromOrder(id) {
    orderItems = orderItems.filter(i => i.book_id !== id);
    updateOrderList();
}
