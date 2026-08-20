function getCart() {
  const data = localStorage.getItem("morningmug_cart");
  return data ? JSON.parse(data) : [];
}

function saveCart(cart) {
  localStorage.setItem("morningmug_cart", JSON.stringify(cart));
  updateCartCountBadge();
}

function addToCart(product) {
  const cart = getCart();
  const existing = cart.find(item => item.id === product.id);

  if (existing) {
    existing.quantity += 1;
  } else {
    cart.push({ ...product, quantity: 1 });
  }

  saveCart(cart);
}

function removeFromCart(productId) {
  const cart = getCart().filter(item => item.id !== productId);
  saveCart(cart);
}

function updateQuantity(productId, quantity) {
  const cart = getCart();
  const item = cart.find(i => i.id === productId);

  if (item) {
    item.quantity = quantity;
    if (item.quantity <= 0) {
      removeFromCart(productId);
      return;
    }
  }

  saveCart(cart);
}

function getCartTotal() {
  return getCart().reduce((sum, item) => sum + item.price * item.quantity, 0);
}

function getCartCount() {
  return getCart().reduce((sum, item) => sum + item.quantity, 0);
}

function clearCart() {
  localStorage.removeItem("morningmug_cart");
  updateCartCountBadge();
}

function updateCartCountBadge() {
  const badge = document.getElementById("cart-count");
  if (badge) {
    badge.textContent = getCartCount();
  }
}

updateCartCountBadge();
