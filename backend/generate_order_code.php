<?php
function generateOrderCode($conn)
{
    $q = $conn->query("SELECT order_id FROM orders ORDER BY order_id DESC LIMIT 1");

    if ($q && $q->num_rows > 0) {
        $last = intval($q->fetch_assoc()["order_id"]);
        $new = $last + 1;
        if ($new > 0999) $new = 01000;
    } else {
        $new = 1;
    }

    return str_pad($new, 4, "0", STR_PAD_LEFT);
}
