document.addEventListener("DOMContentLoaded", function () {
  dynammiskIndlaesning();
  runScript();
});

function runScript() {
  updateMedicineStatus();
  highLightCurrentPage();
  setupRoomFiltering();
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
    const targetURL = this.getAttribute("href");
    if (targetURL && targetURL.includes("logout")) {
      return; // Do not load via AJAX if it's a logout link
    }

    // Check if the link is an internal link
    event.preventDefault(); // Prevent default link behavior
    if(!targetURL || targetURL === "null") {
      console.warn("Invalid link target:", targetURL);
      return;
    }
    //Lad the page dynamically
    loadPage(targetURL);
  }

  // Handle the back/forward button
  window.addEventListener("popstate", function (event) {
    // Check if the event has a state and load the corresponding page
    if (event.state) {
      //load the page from the history state
      loadPage(event.state.url);
    } else {
      // If no state, load the default page (home page)
      loadPage("/");
    }
  });
}

// Highlight the current page in the navigation bar
function highLightCurrentPage() {
  // Get all navigation links
  const navLinks = document.querySelectorAll("nav a");
  // Loop through each link and check if it matches the current URL
  navLinks.forEach((link) => {
    // Get the href attribute of the link
    const href = link.getAttribute("href");
    const node = link.closest("li"); // Find the closest <li> ancestor

    // Check if the href matches the current URL
    // If the href is null or empty, skip this link
    if(!href || !node) {
      console.warn("Skipping link due to missing href or node:", link);
      return;
    }
    // Remove the "active" class from all links
    node.classList.remove("active");
    // Add the "active" class to the current link
    if (window.location.pathname === href) {
      node.classList.add("active");
    }
  });
}


// Setup room filtering
function setupRoomFiltering() {
  const buttons = document.querySelectorAll(".filter-button");
  const roomSections = document.querySelectorAll(".room-section");
  const noSelectionMsg = document.getElementById("no-selection-msg");

  // Skjul alt ved start
  roomSections.forEach(section => {
    section.style.display = "none";
  });

  if (noSelectionMsg) {
    noSelectionMsg.style.display = "block";
  }

  buttons.forEach(button => {
    button.addEventListener("click", () => {
      const selectedRoom = button.getAttribute("data-room");

      let anyVisible = false;
      roomSections.forEach(section => {
      const match = selectedRoom === "Alle" || section.dataset.room === selectedRoom;
      section.style.display = match ? "block" : "none";
      if (match) anyVisible = true;
    });

      if (noSelectionMsg) {
        noSelectionMsg.style.display = anyVisible ? "none" : "block";
      }
    });
  });
}