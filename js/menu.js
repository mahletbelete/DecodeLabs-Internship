const productsGrid     = document.getElementById("products-grid");
const filtersContainer = document.getElementById("menu-filters");
const BASE_URL         = window.location.href.replace(/\/[^\/]*$/, '/');

let allProducts = [];

async function fetchProducts() {
  try {
    const response = await fetch("php/products.php");
    const data = await response.json();

    if (!data.success) {
      showError(data.message || "Failed to load products.");
      return;
    }

    allProducts = data.products;
    buildFilters(allProducts);
    renderProducts(allProducts);

  } catch (err) {
    showError("Could not connect to the server. Make sure XAMPP is running.");
  }
}

function buildFilters(products) {
  const categories = [...new Set(products.map(p => p.category))];

  categories.forEach(category => {
    const btn = document.createElement("button");
    btn.className = "filter-btn";
    btn.dataset.category = category;
    btn.textContent = category;
    btn.addEventListener("click", () => filterByCategory(category, btn));
    filtersContainer.appendChild(btn);
  });

  filtersContainer.querySelector('[data-category="all"]').addEventListener("click", (e) => {
    filterByCategory("all", e.currentTarget);
  });
}

function filterByCategory(category, clickedBtn) {
  document.querySelectorAll(".filter-btn").forEach(btn => btn.classList.remove("active"));
  clickedBtn.classList.add("active");

  if (category === "all") {
    renderProducts(allProducts);
  } else {
    renderProducts(allProducts.filter(p => p.category === category));
  }
}

function renderProducts(products) {
  productsGrid.innerHTML = "";

  if (products.length === 0) {
    productsGrid.innerHTML = '<p class="loading-text">No products found.</p>';
    return;
  }

  products.forEach(product => {
    productsGrid.appendChild(createProductCard(product));
  });
}

function getImageSrc(filename) {
  if (!filename) return BASE_URL + "images/coffee-image.PNG";
  return BASE_URL + "images/products/" + filename;
}

function createProductCard(product) {
  const card      = document.createElement("div");
  card.className  = "product-card";
  const available = product.available == 1;

 const imgWrapper = document.createElement("div");
  imgWrapper.className = "product-img-wrapper";
  imgWrapper.style.backgroundImage =
    "url('" + getImageSrc(product.image) + "'), url('" + BASE_URL + "images/cardbackground.jpg')";

  const body = document.createElement("div");
  body.className = "product-card-body";
  body.innerHTML =
    "<h3>" + product.name + "</h3>" +
    "<p>" + (product.description || "") + "</p>";

  const footer = document.createElement("div");
  footer.className = "product-card-footer";
  footer.innerHTML = '<span class="product-price">$' + parseFloat(product.price).toFixed(2) + "</span>";

  const btn = document.createElement("button");
  btn.className   = "add-to-cart-btn";
  btn.dataset.id  = product.id;
  btn.textContent = available ? "Add to Cart" : "Unavailable";
  if (!available) btn.disabled = true;

  if (available) {
    btn.addEventListener("click", () => {
      addToCart({
        id:    product.id,
        name:  product.name,
        price: parseFloat(product.price),
        image: product.image || ""
      });
      btn.textContent = "Added!";
      setTimeout(() => { btn.textContent = "Add to Cart"; }, 1000);
    });
  }

  footer.appendChild(btn);
  card.appendChild(imgWrapper);
  card.appendChild(body);
  card.appendChild(footer);

  return card;
}

function showError(message) {
  productsGrid.innerHTML = '<p class="error-text">' + message + "</p>";
}

fetchProducts();
