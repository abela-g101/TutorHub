document.addEventListener("DOMContentLoaded", () => {
  //  NAVIGATION
  initNavigation()

  //  BACK TO TOP BUTTON 
  initBackToTop()

  //FORM VALIDATION 
  initFormValidation()

  // PASSWORD STRENGTH METER
  initPasswordStrength()

  // TESTIMONIAL SLIDER 
  initTestimonialSlider()

  // ANIMATE ON SCROLL 
  initAnimateOnScroll()

  // AUTO-HIDE ALERTS 
  initAutoHideAlerts()

  // FILTER TOGGLE
  initFilterToggle()

  // CONFIRM DELETE 
  initConfirmDelete()
})

function initNavigation() {

  const menuToggle = document.getElementById("menuToggle")
  const navMenu = document.getElementById("navMenu")

  if (menuToggle && navMenu) {
    menuToggle.addEventListener("click", () => {
      menuToggle.classList.toggle("active")
      navMenu.classList.toggle("active")


      const expanded = menuToggle.getAttribute("aria-expanded") === "true" || false
      menuToggle.setAttribute("aria-expanded", !expanded)
      navMenu.setAttribute("aria-hidden", expanded)
    })


    document.addEventListener("click", (event) => {
      if (
        navMenu &&
        navMenu.classList.contains("active") &&
        !event.target.closest(".main-nav") &&
        !event.target.closest("#menuToggle")
      ) {
        navMenu.classList.remove("active")
        menuToggle.classList.remove("active")
        menuToggle.setAttribute("aria-expanded", "false")
        navMenu.setAttribute("aria-hidden", "true")
      }
    })
  }

  const header = document.querySelector(".site-header")
  if (header) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 50) {
        header.classList.add("scrolled")
      } else {
        header.classList.remove("scrolled")
      }
    })
  }


  const userMenuButtons = document.querySelectorAll(".user-menu-button")
  const userMenuDropdowns = document.querySelectorAll(".user-menu-dropdown")

  if (userMenuButtons.length > 0 && userMenuDropdowns.length > 0) {
    userMenuButtons.forEach((button, index) => {
      const dropdown = userMenuDropdowns[index]

      button.addEventListener("click", (e) => {
        e.stopPropagation()
        dropdown.classList.toggle("active")

        const expanded = button.getAttribute("aria-expanded") === "true" || false
        button.setAttribute("aria-expanded", !expanded)
        dropdown.setAttribute("aria-hidden", expanded)
      })
    })

    document.addEventListener("click", (e) => {
      userMenuButtons.forEach((button, index) => {
        const dropdown = userMenuDropdowns[index]
        if (!button.contains(e.target) && !dropdown.contains(e.target)) {
          dropdown.classList.remove("active")
          button.setAttribute("aria-expanded", "false")
          dropdown.setAttribute("aria-hidden", "true")
        }
      })
    })

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        userMenuButtons.forEach((button, index) => {
          const dropdown = userMenuDropdowns[index]
          if (dropdown.classList.contains("active")) {
            dropdown.classList.remove("active")
            button.setAttribute("aria-expanded", "false")
            dropdown.setAttribute("aria-hidden", "true")
          }
        })
      }
    })
  }
}


function initBackToTop() {
  const backToTopButton = document.createElement("button")
  backToTopButton.className = "back-to-top"
  backToTopButton.innerHTML = '<i class="fas fa-arrow-up"></i>'
  backToTopButton.setAttribute("aria-label", "Back to top")
  document.body.appendChild(backToTopButton)


  window.addEventListener("scroll", () => {
    if (window.scrollY > 300) {
      backToTopButton.classList.add("visible")
    } else {
      backToTopButton.classList.remove("visible")
    }
  })


  backToTopButton.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    })
  })
}

function initFormValidation() {
  const forms = document.querySelectorAll(".needs-validation")

  if (forms.length > 0) {
    Array.from(forms).forEach((form) => {
      form.addEventListener(
        "submit",
        (event) => {
          if (!form.checkValidity()) {
            event.preventDefault()
            event.stopPropagation()


            const invalidInput = form.querySelector(":invalid")
            if (invalidInput) {
              invalidInput.focus()

       
              if (
                !invalidInput.nextElementSibling ||
                !invalidInput.nextElementSibling.classList.contains("error-message")
              ) {
                const errorMessage = document.createElement("div")
                errorMessage.className = "error-message"
                errorMessage.textContent = invalidInput.validationMessage
                invalidInput.parentNode.insertBefore(errorMessage, invalidInput.nextSibling)
              }
            }
          }

          form.classList.add("was-validated")
        },
        false,
      )


      const inputs = form.querySelectorAll("input, select, textarea")
      inputs.forEach((input) => {
        input.addEventListener("blur", () => {

          const existingError = input.nextElementSibling
          if (existingError && existingError.classList.contains("error-message")) {
            existingError.remove()
          }

          if (!input.validity.valid) {
            const errorMessage = document.createElement("div")
            errorMessage.className = "error-message"
            errorMessage.textContent = input.validationMessage
            input.parentNode.insertBefore(errorMessage, input.nextSibling)
          }
        })
      })
    })
  }
}


function initPasswordStrength() {
  const passwordInput = document.getElementById("password")
  const passwordStrength = document.getElementById("password-strength")
  const confirmPasswordInput = document.getElementById("confirm_password")
  const passwordMatch = document.getElementById("password-match")

  if (passwordInput && passwordStrength) {
    passwordInput.addEventListener("input", () => {
      const password = passwordInput.value
      let strength = 0

      if (password.length >= 8) strength += 1
      if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 1
      if (password.match(/\d/)) strength += 1
      if (password.match(/[^a-zA-Z\d]/)) strength += 1

      const strengthText = ["Weak", "Fair", "Good", "Strong"]
      const strengthClass = ["text-danger", "text-warning", "text-info", "text-success"]

      if (password.length === 0) {
        passwordStrength.textContent = ""
        passwordStrength.className = ""
      } else {
        passwordStrength.textContent = strengthText[strength - 1] || "Very Weak"
        passwordStrength.className = strengthClass[strength - 1] || "text-danger"
      }

      
      if (confirmPasswordInput && confirmPasswordInput.value) {
        checkPasswordMatch()
      }
    })
  }

  if (confirmPasswordInput && passwordInput && passwordMatch) {
    const checkPasswordMatch = () => {
      if (confirmPasswordInput.value === "") {
        passwordMatch.textContent = ""
        passwordMatch.className = ""
        return
      }

      if (passwordInput.value === confirmPasswordInput.value) {
        passwordMatch.textContent = "Passwords match"
        passwordMatch.className = "text-success"
      } else {
        passwordMatch.textContent = "Passwords do not match"
        passwordMatch.className = "text-danger"
      }
    }

    passwordInput.addEventListener("input", checkPasswordMatch)
    confirmPasswordInput.addEventListener("input", checkPasswordMatch)
  }
}


function initTestimonialSlider() {
  const testimonials = document.querySelectorAll(".testimonial")
  const dots = document.querySelectorAll(".testimonial-dots .dot")
  const prevBtn = document.getElementById("prevTestimonial")
  const nextBtn = document.getElementById("nextTestimonial")

  if (testimonials.length > 0) {
    let currentIndex = 0
    let interval

    function showTestimonial(index) {

      testimonials.forEach((testimonial) => {
        testimonial.classList.remove("active")
      })

      dots.forEach((dot) => {
        dot.classList.remove("active")
      })

  
      testimonials[index].classList.add("active")
      if (dots[index]) {
        dots[index].classList.add("active")
      }

      currentIndex = index
    }

    
    showTestimonial(0)

  
    dots.forEach((dot, index) => {
      dot.addEventListener("click", () => {
        showTestimonial(index)
        resetInterval()
      })
    })

  
    if (prevBtn) {
      prevBtn.addEventListener("click", () => {
        let newIndex = currentIndex - 1
        if (newIndex < 0) {
          newIndex = testimonials.length - 1
        }
        showTestimonial(newIndex)
        resetInterval()
      })
    }

    if (nextBtn) {
      nextBtn.addEventListener("click", () => {
        let newIndex = currentIndex + 1
        if (newIndex >= testimonials.length) {
          newIndex = 0
        }
        showTestimonial(newIndex)
        resetInterval()
      })
    }


    function startInterval() {
      interval = setInterval(() => {
        let newIndex = currentIndex + 1
        if (newIndex >= testimonials.length) {
          newIndex = 0
        }
        showTestimonial(newIndex)
      }, 5000)
    }

    function resetInterval() {
      clearInterval(interval)
      startInterval()
    }

    startInterval()


    const testimonialSlider = document.getElementById("testimonialSlider")
    if (testimonialSlider) {
      testimonialSlider.addEventListener("mouseenter", () => {
        clearInterval(interval)
      })

      testimonialSlider.addEventListener("mouseleave", () => {
        startInterval()
      })
    }


    document.addEventListener("keydown", (e) => {
      if (e.key === "ArrowLeft") {
        prevBtn.click()
      } else if (e.key === "ArrowRight") {
        nextBtn.click()
      }
    })
  }
}


function initAnimateOnScroll() {
  const animateElements = document.querySelectorAll(".animate-on-scroll")

  if (animateElements.length > 0) {
    
    function isInViewport(element) {
      const rect = element.getBoundingClientRect()
      return rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.8 && rect.bottom >= 0
    }


    function checkScroll() {
      animateElements.forEach((element) => {
        if (isInViewport(element)) {
          element.classList.add("active")
        }
      })
    }

  
    window.addEventListener("scroll", checkScroll)

   
    checkScroll()
  }
}


function initAutoHideAlerts() {
  const alerts = document.querySelectorAll(".alert:not(.alert-permanent)")

  if (alerts.length > 0) {
    alerts.forEach((alert) => {
      setTimeout(() => {
        alert.style.opacity = "0"
        alert.style.transform = "translateY(-10px)"
        setTimeout(() => {
          alert.style.display = "none"
        }, 500)
      }, 5000)
    })
  }
}

function initFilterToggle() {
  const filterToggle = document.getElementById("filterToggle")
  const advancedFilters = document.getElementById("advancedFilters")

  if (filterToggle && advancedFilters) {
    filterToggle.addEventListener("click", () => {
      const icon = filterToggle.querySelector(".fa-chevron-down, .fa-chevron-up")

      if (advancedFilters.style.display === "none" || !advancedFilters.style.display) {
        advancedFilters.style.display = "grid"
        if (icon) {
          icon.classList.replace("fa-chevron-down", "fa-chevron-up")
        }
      } else {
        advancedFilters.style.display = "none"
        if (icon) {
          icon.classList.replace("fa-chevron-up", "fa-chevron-down")
        }
      }
    })

    
    const filterInputs = advancedFilters.querySelectorAll("input, select")
    let hasActiveFilter = false

    filterInputs.forEach((input) => {
      if ((input.tagName === "SELECT" && input.value !== "") || (input.tagName === "INPUT" && input.value !== "")) {
        hasActiveFilter = true
      }
    })

    if (hasActiveFilter) {
      advancedFilters.style.display = "grid"
      const icon = filterToggle.querySelector(".fa-chevron-down")
      if (icon) {
        icon.classList.replace("fa-chevron-down", "fa-chevron-up")
      }
    }
  }
}


function initConfirmDelete() {
  const deleteButtons = document.querySelectorAll(".delete-confirm")

  if (deleteButtons.length > 0) {
    deleteButtons.forEach((button) => {
      button.addEventListener("click", (event) => {
        if (!confirm("Are you sure you want to delete this item? This action cannot be undone.")) {
          event.preventDefault()
        }
      })
    })
  }
}

function togglePasswordVisibility(inputId) {
  const passwordInput = document.getElementById(inputId)
  const toggleButton = passwordInput.nextElementSibling.querySelector("i")

  if (passwordInput.type === "password") {
    passwordInput.type = "text"
    toggleButton.classList.replace("fa-eye", "fa-eye-slash")
  } else {
    passwordInput.type = "password"
    toggleButton.classList.replace("fa-eye-slash", "fa-eye")
  }
}


function searchJobs(event) {
  event.preventDefault()

  const form = event.target
  const formData = new FormData(form)
  const searchParams = new URLSearchParams()
  const jobsContainer = document.getElementById("jobsContainer")

 
  for (const [key, value] of formData.entries()) {
    if (value) {
      searchParams.append(key, value)
    }
  }

  jobsContainer.innerHTML = `
    <div class="loading-spinner">
      <div class="spinner"></div>
      <p>Searching for jobs...</p>
    </div>
  `

  fetch(`search-jobs.php?${searchParams.toString()}`)
    .then((response) => response.text())
    .then((html) => {
      jobsContainer.innerHTML = html

   
      const jobCards = jobsContainer.querySelectorAll(".job-card")
      jobCards.forEach((card, index) => {
        card.style.opacity = "0"
        card.style.transform = "translateY(20px)"

        setTimeout(() => {
          card.style.transition = "opacity 0.5s ease, transform 0.5s ease"
          card.style.opacity = "1"
          card.style.transform = "translateY(0)"
        }, 100 * index)
      })
    })
    .catch((error) => {
      console.error("Error searching jobs:", error)
      jobsContainer.innerHTML = `
        <div class="error-message">
          <i class="fas fa-exclamation-circle"></i>
          <p>An error occurred while searching for jobs. Please try again.</p>
        </div>
      `
    })
}
