document.addEventListener("DOMContentLoaded", () => {
  const slider = document.querySelector(".slider");
  const slides = document.querySelectorAll(".slide");

  if (slider && slides.length > 0) {
    let currentIndex = 0;
    const totalSlides = slides.length;

    const updateSlider = () => {
      slider.style.transform = `translateX(-${currentIndex * 100}%)`;
    };

    const showNextSlide = () => {
      currentIndex = (currentIndex + 1) % totalSlides;
      updateSlider();
    };

    const showPrevSlide = () => {
      currentIndex = (currentIndex - 1 + totalSlides) % totalSlides;
      updateSlider();
    };

    setInterval(showNextSlide, 5000);

    const prevButton = document.querySelector(".slider-button.prev");
    const nextButton = document.querySelector(".slider-button.next");

    if (prevButton && nextButton) {
      prevButton.addEventListener("click", showPrevSlide);
      nextButton.addEventListener("click", showNextSlide);
    }
  } else {
    console.error("Slider or slides not properly initialized.");
  }

  // Tab functionality
  function openTab(event, tabId) {
    const tabContents = document.querySelectorAll(".tabcontent");
    tabContents.forEach((tabContent) => {
      tabContent.style.display = "none";
    });

    const tabLinks = document.querySelectorAll(".tablinks");
    tabLinks.forEach((tabLink) => {
      tabLink.classList.remove("active");
    });

    const activeTab = document.getElementById(tabId);
    if (activeTab) {
      activeTab.style.display = "block";
      if (event) {
        event.currentTarget.classList.add("active");
      }
    } else {
      console.error(`Tab with ID '${tabId}' not found.`);
    }
  }

  openTab(null, "login");

  const tabLinks = document.querySelectorAll(".tablinks");
  tabLinks.forEach((tabLink) => {
    tabLink.addEventListener("click", (event) => {
      const tabId = event.currentTarget.getAttribute("data-tab-id");
      openTab(event, tabId);
    });
  });
});
