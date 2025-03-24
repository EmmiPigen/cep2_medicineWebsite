document.addEventListener("DOMContentLoaded", function () {
  dynammiskIndlaesning();
  updateMedicineStatus();
});

// Opdater medicinstatus på forsiden

function updateMedicineStatus() {
  if (window.location.pathname == "/") {
    let medStatusBox = document.getElementById("medStatusBox");
    if (!medStatusBox) {
      console.error("Medicine status box not found");
      return;
    }

    // Twig variabelen bliver korrekt indsat med escape('js')
    let medicineStatus = medStatusBox.getAttribute("data-medicine-status");

    console.log("Medicine Status: " + medicineStatus);

    // Ændre baggrundsfarven baseret på medicinstatus
    if (medicineStatus == "1") {
      medStatusBox.setAttribute('class', "forside-box-green"); // Grøn
    } else {
      medStatusBox.setAttribute('class', "forside-box-red");// Rød
    }
  }
}

function dynammiskIndlaesning() {
  const navLinks = document.querySelectorAll("nav a");
  const contentDiv = document.getElementById("content");

  async function loadPage(url) {
    console.log("Loading page:", url);
    if (window.location.pathname === url) {
      return;
    }
    contentDiv.classList.add("fading");
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const html = await response.text();
      const tempDiv = document.createElement("div");
      tempDiv.innerHTML = html;

      // Hent kun det indhold, der skal opdateres
      const newContent = tempDiv.querySelector("#content");
      if (newContent) {
        setTimeout(function () {
          contentDiv.innerHTML = newContent.innerHTML;
          contentDiv.classList.remove("fading");

          // Ensure the status update runs *after* the new content is loaded
          updateMedicineStatus();
        }, 250);
      }

      history.pushState({ url: url }, "", url);
    } catch (error) {
      console.error("Error loading page:", error);
    }
  }

  navLinks.forEach((link) => {
    link.addEventListener("click", function (event) {
      event.preventDefault();
      loadPage(this.getAttribute("href"));
      updateMedicineStatus();
    });
  });

  window.addEventListener("popstate", function (event) {
    if (event.state) {
      loadPage(event.state.url);
    } else {
      loadPage("/");
    }
  });
}

document.addEventListener("turbo:load", function () {
  let updateLink = document.getElementById("medStatusFrame");

  if (updateLink.onclick) {
    updateLink.addEventListener("click", function (event) {
      fetch(this.getAttribute("href"))
        .then((response) => response.text())
        .then((html) => {
          document.getElementById("medStatusFrame").innerHTML = html;
        })
        .catch((error) => console.error("Error updating med status:", error));
    });
  }
});
