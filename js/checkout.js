
const cartItemsContainer = document.getElementById("cart-items");
const summarySubtotal    = document.getElementById("summary-subtotal");
const summaryTotal       = document.getElementById("summary-total");
const placeOrderBtn      = document.getElementById("place-order-btn");
const orderMessage       = document.getElementById("order-message");

const BASE_URL = window.location.href.replace(/\/[^\/]*$/, '/');

function renderCartPage() {
  const cart = getCart();
  cartItemsContainer.innerHTML = "";

  if (cart.length === 0) {
    cartItemsContainer.innerHTML = '<p class="empty-cart-text">Your cart is empty. <a href="menu.html">Browse the menu</a></p>';
    placeOrderBtn.disabled = true;
    updateSummary(0);
    return;
  }

  placeOrderBtn.disabled = false;

  cart.forEach(item => {
    cartItemsContainer.appendChild(createCartItemRow(item));
  });

  updateSummary(getCartTotal());
}

function createCartItemRow(item) {
  const row = document.createElement("div");
  row.className = "cart-item";
  row.id = "cart-item-" + item.id;

  const productImg = item.image
    ? BASE_URL + "images/products/" + item.image
    : BASE_URL + "images/coffee-image.PNG";
  const cardBg = BASE_URL + "images/cardbackground.jpg";

  const imgWrapper = document.createElement("div");
  imgWrapper.className = "cart-item-img";
  imgWrapper.style.backgroundImage = "url('" + productImg + "'), url('" + cardBg + "')";

  const details = document.createElement("div");
  details.className = "cart-item-details";
  details.innerHTML =
    "<h3>" + item.name + "</h3>" +
    '<span class="item-price">$' + (item.price * item.quantity).toFixed(2) + "</span>" +
    '<div class="quantity-controls">' +
      '<button class="qty-btn" data-action="decrease" data-id="' + item.id + '">−</button>' +
      '<span class="qty-value">' + item.quantity + "</span>" +
      '<button class="qty-btn" data-action="increase" data-id="' + item.id + '">+</button>' +
    "</div>";

  const removeBtn = document.createElement("button");
  removeBtn.className = "remove-item-btn";
  removeBtn.title = "Remove item";
  removeBtn.innerHTML = '<i class="fa-solid fa-trash"></i>';

  details.querySelector('[data-action="increase"]').addEventListener("click", () => {
    updateQuantity(item.id, item.quantity + 1);
    renderCartPage();
  });

  details.querySelector('[data-action="decrease"]').addEventListener("click", () => {
    updateQuantity(item.id, item.quantity - 1);
    renderCartPage();
  });

  removeBtn.addEventListener("click", () => {
    removeFromCart(item.id);
    renderCartPage();
  });

  row.appendChild(imgWrapper);
  row.appendChild(details);
  row.appendChild(removeBtn);

  return row;
}

function updateSummary(total) {
  summarySubtotal.textContent = "$" + total.toFixed(2);
  summaryTotal.textContent    = "$" + total.toFixed(2);
}

placeOrderBtn.addEventListener("click", placeOrder);

async function placeOrder() {
  const cart = getCart();
  if (cart.length === 0) return;

  placeOrderBtn.disabled    = true;
  placeOrderBtn.textContent = "Placing order...";
  orderMessage.textContent  = "";
  orderMessage.className    = "order-message";

  const payload = {
    items: cart.map(item => ({
      product_id: item.id,
      quantity:   item.quantity,
      price:      item.price
    }))
  };

  try {
    const response = await fetch("php/orders.php", {
      method:  "POST",
      headers: { "Content-Type": "application/json" },
      body:    JSON.stringify(payload)
    });

    const data = await response.json();

    if (data.success) {
      orderMessage.textContent = data.message;
      orderMessage.classList.add("success");
      clearCart();
      renderCartPage();
    } else {
      orderMessage.textContent = data.message || "Something went wrong.";
      orderMessage.classList.add("error");
      placeOrderBtn.disabled    = false;
      placeOrderBtn.textContent = "Place Order";
    }

  } catch (err) {
    orderMessage.textContent  = "Could not connect to the server.";
    orderMessage.classList.add("error");
    placeOrderBtn.disabled    = false;
    placeOrderBtn.textContent = "Place Order";
  }
}

renderCartPage();
