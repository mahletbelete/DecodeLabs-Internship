
const ordersList = document.getElementById("orders-list");

async function fetchOrders() {
  try {
    const response = await fetch("php/orders.php");
    const data     = await response.json();

    if (!data.success) {
      ordersList.innerHTML =
        '<p class="empty-cart-text">You must be <a href="login.html">logged in</a> to view your orders.</p>';
      return;
    }

    if (data.orders.length === 0) {
      ordersList.innerHTML =
        '<p class="empty-cart-text">You have no orders yet. <a href="menu.html">Browse the menu</a></p>';
      return;
    }

    renderOrders(data.orders);

  } catch (err) {
    ordersList.innerHTML = '<p class="error-text">Could not load orders. Make sure XAMPP is running.</p>';
  }
}

function renderOrders(orders) {
  ordersList.innerHTML = "";

  orders.forEach(order => {
    const card = document.createElement("div");
    card.className = "order-card";

    const date = new Date(order.created_at).toLocaleDateString("en-US", {
      year: "numeric", month: "long", day: "numeric"
    });

    const itemsHtml = order.items.map(item =>
      "<li>" +
        item.product_name + " &times; " + item.quantity +
        " &mdash; $" + (parseFloat(item.unit_price) * item.quantity).toFixed(2) +
      "</li>"
    ).join("");

    const statusLabel = order.status.charAt(0).toUpperCase() + order.status.slice(1);

    card.innerHTML =
      '<div class="order-card-header">' +
        '<span class="order-id">Order #' + order.id + "</span>" +
        '<span class="order-date">' + date + "</span>" +
        '<span class="order-status status-' + order.status + '">' + statusLabel + "</span>" +
      "</div>" +
      '<ul class="order-items-list">' + itemsHtml + "</ul>" +
      '<div class="order-total">Total: <strong>$' + parseFloat(order.total_price).toFixed(2) + "</strong></div>";

    ordersList.appendChild(card);
  });
}

fetchOrders();
