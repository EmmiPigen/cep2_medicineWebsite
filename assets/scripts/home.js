document.addEventListener("DOMContentLoaded", function () {
  dynammiskIndlaesning();
  runScript();
});

function runScript() {
  updateMedicineStatus();
  highLightCurrentPage();
}

function updateMedicineStatus() {
  if (window.location.pathname === "/") {
    let medStatusBox = document.getElementById("medStatusBox");
    if (!medStatusBox) {
      console.error("Medicine status box not found");
      return;
    }

    let medicineStatus = medStatusBox.getAttribute("data-medicine-status");
    console.log("Medicine Status:", medicineStatus);

    medStatusBox.classList.toggle("green", medicineStatus === "1");
    medStatusBox.classList.toggle("red", medicineStatus !== "1");
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
        setTimeout(() => {
          contentDiv.innerHTML = newContent.innerHTML;
          contentDiv.classList.remove("fading");

          // Ensure the status update runs *after* the new content is loaded
          runScript();
        }, 250);
      }

      history.pushState({ url: url }, "", url);
    } catch (error) {
      console.error("Error loading page:", error);
    }
  }

  //Remove previous event listeners
  navLinks.forEach((link) => {
    link.removeEventListener("click", handleNavLinks);
    link.addEventListener("click", handleNavLinks);
  });

  function handleNavLinks(event) {
    event.preventDefault();
    const targetURL = this.getAttribute("href");
    
    if(!targetURL || targetURL === "null") {
      console.warn("Invalid link target:", targetURL);
      return;
    }
    loadPage(targetURL);
  }

  window.addEventListener("popstate", function (event) {
    if (event.state) {
      loadPage(event.state.url);
    } else {
      loadPage("/");
    }
  });
}

function highLightCurrentPage() {
  const navLinks = document.querySelectorAll("nav a");
  navLinks.forEach((link) => {
    const href = link.getAttribute("href");
    const node = link.closest("li"); // Find the closest <li> ancestor

    if(!href || !node) {
      console.warn("Skipping link due to missing href or node:", link);
      return;
    }
    node.classList.remove("active");
    if (window.location.pathname === href) {
      node.classList.add("active");
    }
  });
}

