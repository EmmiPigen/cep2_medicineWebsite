document.addEventListener("DOMContentLoaded", function () {
  dynammiskIndlaesning();
  runScript();
});

function runScript() {
  updateMedicineStatus();
  highLightCurrentPage();
  setupRoomFiltering();
  setupHelpToggle();
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
    node.classList.remove("active");
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

//åbne og lukke spørgsmål i hjælp
function setupHelpToggle() {
  // Find alle elementer med klassen 'help-title' som er de spørgsmål
  const titles = document.querySelectorAll('.help-title');

  // Gå igennem alle spørgsmålene og tilføj en klik-hændelse
  titles.forEach(function(title) {
    title.addEventListener('click', function() {
      const description = this.nextElementSibling; // Find beskrivelsen (næste element)
      const icon = this.querySelector('.toggle-icon'); // Find ikonet
      const box = this.parentElement; 

      const isOpen = description.style.display === 'block';

      if (isOpen) {
        description.style.display = 'none';
        icon.textContent = '+';
        box.classList.remove('active'); // Fjern 'active' klasse når den lukkes
      } else {
        description.style.display = 'block';
        icon.textContent = '-';
        box.classList.add('active'); // Tilføj 'active' klasse når den åbnes
      }
    });
  });
}