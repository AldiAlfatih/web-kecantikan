let count = 0;

const cartCount = document.getElementById("cart-count");
const addButtons = document.querySelectorAll(".btn-add-cart");

addButtons.forEach(btn => {
  btn.addEventListener("click", () => {
    count++;
    cartCount.textContent = count;
  });
});
