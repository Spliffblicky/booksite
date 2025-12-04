const base = "http://localhost:8080/backend/";

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

let isLoggedIn = false;
let currentUser = null;
let orderItems = [];

document.addEventListener('DOMContentLoaded', loadBooks);

signupForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const data = new FormData(signupForm);
    try {
        const res = await fetch(base + 'signup.php', { method: 'POST', body: data });
        const result = await res.json();
        if (result.status === 'success') {
            loginUser(result.user_id, data.get('username'), data.get('role'));
            signupForm.reset();
        } else alert(result.message);
    } catch {
        alert('Signup failed.');
    }
});

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const f = new FormData(loginForm);
    const payload = { username: f.get('username'), id: f.get('id'), password: f.get('password') };
    try {
        const res = await fetch(base + 'login.php', { method: 'POST', body: JSON.stringify(payload) });
        const result = await res.json();
        if (result.status === 'success') {
            loginUser(result.user_id, payload.username, result.role);
            loginForm.reset();
        } else alert(result.message);
    } catch {
        alert('Login failed.');
    }
});

logoutBtn.addEventListener('click', () => {
    sessionStorage.clear();
    location.reload();
});

function loginUser(id, username, role) {
    isLoggedIn = true;
    currentUser = { id, role, username };
    sessionStorage.setItem('user_id', id);
    sessionStorage.setItem('role', role);
    sessionStorage.setItem('username', username);
    loginBtn.style.display = 'none';
    signupBtn.style.display = 'none';
    logoutBtn.style.display = 'inline-block';
    roleContainer.innerHTML = `<h3>Logged in as <b>${role.toUpperCase()}</b> (${username})</h3>`;
    otherItemsContainer.style.display = role === 'user' ? 'block' : 'none';
    loadBooks();
    redirectRole(role);
}

function redirectRole(role) {
    document.getElementById('mainContainer').style.display = role === 'user' ? 'block' : 'none';
    document.getElementById('adminContainer').style.display = role === 'admin' ? 'block' : 'none';
    document.getElementById('supplierContainer').style.display = role === 'supplier' ? 'block' : 'none';
}

addressForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (!isLoggedIn) return;
    const f = new FormData(addressForm);
    const payload = { street: f.get('street'), city: f.get('city'), state: f.get('state'), zip: f.get('zip') };
    try {
        const res = await fetch(base + 'address.php', { method: 'POST', body: JSON.stringify(payload) });
        const result = await res.json();
        if (result.status === 'success') {
            alert('Address saved');
            addressForm.reset();
            document.getElementById('addressModal').style.display = 'none';
        } else alert(result.message);
    } catch {
        alert('Error');
    }
});

async function loadBooks() {
    booksList.innerHTML = 'Loading...';
    try {
        const res = await fetch(base + 'get_books.php');
        const books = await res.json();
        booksList.innerHTML = '';
        books.forEach(book => {
            const div = document.createElement('div');
            div.className = 'w3-col s12 m6 l4 w3-margin-bottom';
            div.innerHTML = `
                <div class="w3-card w3-padding">
                    <img src="${book.image_path}" style="width:100%; height:200px; object-fit:cover">
                    <h4>${book.bookname}</h4>
                    <p>${book.author}</p>
                    <p>$${parseFloat(book.price).toFixed(2)}</p>
                    <p>Stock: ${book.quantity}</p>
                    ${sessionStorage.getItem('role') === 'user'
                    ? `<button class="w3-button w3-teal" onclick="addToOrder(${book.book_id}, '${book.bookname}', ${book.price}, ${book.quantity})">Order</button>`
                    : '<button class="w3-button w3-gray" disabled>Login as User</button>'}
                </div>`;
            booksList.appendChild(div);
        });
    } catch {
        booksList.innerHTML = 'Error';
    }
}

function addToOrder(book_id, bookname, price, stock) {
    const q = parseInt(prompt(`Enter quantity (1-${stock})`));
    if (!q || q < 1 || q > stock) return;
    const exist = orderItems.find(i => i.book_id === book_id);
    if (exist) exist.quantity += q;
    else orderItems.push({ book_id, bookname, price, quantity: q });
    updateOrderList();
}

function updateOrderList() {
    itemsList.innerHTML = '';
    orderItems.forEach(i => {
        const li = document.createElement('li');
        li.innerHTML = `${i.bookname} - $${i.price} x ${i.quantity} 
        <button class="w3-button w3-red w3-small" onclick="removeFromOrder(${i.book_id})">Remove</button>`;
        itemsList.appendChild(li);
    });
}

function removeFromOrder(id) {
    orderItems = orderItems.filter(i => i.book_id !== id);
    updateOrderList();
}
