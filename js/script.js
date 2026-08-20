const menuOpenButton  = document.querySelector("#menu-open-button");
const menuCloseButton = document.querySelector("#menu-close-button");

menuOpenButton.addEventListener("click", () => {
  document.body.classList.toggle("show-mobile-menu");
});

menuCloseButton.addEventListener("click", () => menuOpenButton.click());

async function updateNavAccount() {
  const accountBtn = document.querySelector(".account-btn");
  if (!accountBtn) return;

  try {
    const response = await fetch("php/session.php");
    const data     = await response.json();

    if (!data.loggedIn) return;

    const firstName  = data.name.split(" ")[0];
    const isAdmin    = data.role === "admin";
    const profileUrl = isAdmin ? "admin/index.php" : "orders.html";

    const wrapper = document.createElement("div");
    wrapper.className = "account-wrapper";

    accountBtn.href = "#";
    accountBtn.title = "";
    accountBtn.innerHTML =
      '<i class="fa-solid fa-user button-icon"></i>' +
      '<span style="font-size:0.78rem;color:var(--third-color);margin-left:4px;">' + firstName + '</span>';

    const dropdown = document.createElement("div");
    dropdown.className = "account-dropdown";
    dropdown.innerHTML =
      '<a href="' + profileUrl + '">' +
        '<i class="fa-solid fa-' + (isAdmin ? "gauge" : "receipt") + '"></i>' +
        (isAdmin ? "Dashboard" : "My Orders") +
      "</a>" +
      '<a href="php/logout.php" class="logout-link">' +
        '<i class="fa-solid fa-right-from-bracket"></i>Logout' +
      "</a>";

    accountBtn.parentNode.insertBefore(wrapper, accountBtn);
    wrapper.appendChild(accountBtn);
    wrapper.appendChild(dropdown);

    accountBtn.addEventListener("click", (e) => {
      e.preventDefault();
      dropdown.classList.toggle("open");
    });

    document.addEventListener("click", (e) => {
      if (!wrapper.contains(e.target)) {
        dropdown.classList.remove("open");
      }
    });

  } catch (e) {
    
  }
}

updateNavAccount();
