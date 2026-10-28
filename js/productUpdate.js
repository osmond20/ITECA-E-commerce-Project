const editButtons = document.querySelectorAll('.btn');

editButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const product = button.closest(".product"); // returns the next article element within the DOM
    // stores the product-data element in the current article element with class "product"
    const data = product.querySelector(".product-data"); 
    // stores the product-data-update element in the current article element with class "product"
    const update = product.querySelector(".product-data-update");
    // stores the boolean result of existence of whether the update has the class 'show'
    const isEditing = update.classList.contains("show"); 

    // removes all relevant class from data and update and keep original state
    document.querySelectorAll(".product").forEach((product) => {
      product.querySelector(".product-data").classList.remove("hide");
      product.querySelector(".product-data-update").classList.remove("show");
      product.querySelector(".btn").textContent = "Edit";
    });

    // if the existence of 'show' is false update the btn and add relevant classes to data and update
    if (!isEditing) {
      data.classList.add("hide");
      update.classList.add("show");
      //button.type = 'submit';
      button.textContent = "Update";
    }
  });
});