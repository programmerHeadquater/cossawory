// toggle the class zero in addNewReviewbtn and form

document.addEventListener("DOMContentLoaded", function () {
      const form = document.getElementById("addNewReview");
      const formContent = document.getElementById("addNewReviewContent");
      const btnWrapper = document.getElementById("addNewReviewBtnWrapper");
      const addBtn = document.getElementById("addNewReviewBtn");
      const cancelBtn = document.getElementById("cancelReviewBtn");

      addBtn.addEventListener("click", () => {
        // Smoothly collapse the button wrapper:

        // Get current height
        const currentHeight = btnWrapper.scrollHeight + "px";
        btnWrapper.style.height = currentHeight;

        // Force reflow
        btnWrapper.offsetHeight;

        // Animate height to 0
        btnWrapper.style.height = "0px";

        btnWrapper.addEventListener("transitionend", function handler(e) {
          if (e.propertyName === "height") {
            btnWrapper.classList.add("collapsed");
            btnWrapper.style.height = "";
            btnWrapper.removeEventListener("transitionend", handler);
          }
        });

        // Show form and animate its height
        form.style.display = "block";
        requestAnimationFrame(() => {
          const fullHeight = formContent.scrollHeight + "px";
          form.style.height = fullHeight;
        });
      });

      cancelBtn.addEventListener("click", () => {
        // Collapse form height
        form.style.height = "0px";

        // Smoothly expand button wrapper height from 0 to full height:
        btnWrapper.style.height = "0px";
        btnWrapper.classList.remove("collapsed");

        requestAnimationFrame(() => {
          const fullHeight = btnWrapper.scrollHeight + "px";
          btnWrapper.style.height = fullHeight;
        });

        btnWrapper.addEventListener("transitionend", function handler(e) {
          if (e.propertyName === "height") {
            btnWrapper.style.height = "";
            btnWrapper.removeEventListener("transitionend", handler);
          }
        });

        // Hide form after collapse animation (~400ms)
        setTimeout(() => {
          form.style.display = "none";
        }, 400);
      });
    });