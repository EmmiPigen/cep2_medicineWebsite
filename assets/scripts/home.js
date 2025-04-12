document.addEventListener("DOMContentLoaded", function () {
  dynammiskIndlaesning();
  runScript();
});

function runScript() {
  updateMedicineStatus();
  highLightCurrentPage();
  medTakenStatus()
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

    medStatusBox.classList.toggle("med-status-box-green", medicineStatus === "1");
    medStatusBox.classList.toggle("med-status-box-red", medicineStatus !== "1");
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
    loadPage(this.getAttribute("href"));
    runScript();
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
    let node = link.firstElementChild;
    node.classList.remove("nav-active");
    if (window.location.pathname === link.getAttribute("href")) {
      node.classList.add("nav-active");
    }
  });
}

function medPriority() {
  if (window.location.pathname === "/") {
    let medPriorityText = document.getElementById("medPriorityText");
    if (!medPriorityText) {
      console.error("Medicine priority not found");
      return;
    }
    let medPriority = medPriorityText.getAttribute("data-med-priority");
    console.log("Medicine Priority:", medPriority);
    
    const medPriorityBox = document.getElementsByClassName("forside-box-next");
    console.log(medPriorityBox);
    
    if (medPriority === "1") {
      medPriorityText.innerText = "HØJ";
      for (let i = 0; i < medPriorityBox.length; i++) {
        medPriorityBox[i].style.backgroundColor = "red";
      }
    } else if (medPriority === "2") {
      medPriorityText.innerText = "MIDDEL";
      for (let i = 0; i < medPriorityBox.length; i++) {
        medPriorityBox[i].style.backgroundColor = "orange";
      }
    } else if (medPriority === "3") {
      medPriorityText.innerText = "LAV";
      for (let i = 0; i < medPriorityBox.length; i++) {
        medPriorityBox[i].style.backgroundColor = "green";
      }
    }
  }
}
