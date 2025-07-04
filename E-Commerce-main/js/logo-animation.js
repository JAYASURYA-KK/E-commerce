// Shop Management Clean Logo JavaScript (Background Removed)

// Wait for DOM to be fully loaded
document.addEventListener("DOMContentLoaded", function () {
  // Create and inject the logo into the page
  createDynamicLogo();

  // Initialize logo animations
  initializeLogoAnimations();

  // Add interactive effects
  addLogoInteractivity();
});

// Main function to create the dynamic logo
function createDynamicLogo() {
  // Create logo container
  const logoContainer = document.createElement("div");
  logoContainer.id = "dynamicLogo";
  logoContainer.className = "dynamic-logo-container";

  // Logo HTML structure - Clean version without background
  logoContainer.innerHTML = `
          <div class="logo-wrapper">
              <div class="logo-icon">
                  <div class="logo-circle">
                      <i class="fa fa-shopping-cart logo-cart"></i>
                      <div class="orbit-dot orbit-1"></div>
                      <div class="orbit-dot orbit-2"></div>
                      <div class="orbit-dot orbit-3"></div>
                  </div>
              </div>
              <div class="logo-text">
                  <div class="brand-name">
                      <span class="letter" data-letter="J">J</span>
                      <span class="letter" data-letter="S">S</span>
                      <span class="letter" data-letter=" "> </span>
                      <span class="letter" data-letter="W">W</span>
                      <span class="letter" data-letter="E">E</span>
                      <span class="letter" data-letter="B">B</span>
                      <span class="letter" data-letter="Y">Y</span>
                  </div>
                  <div class="brand-subtitle">
                      <span class="subtitle-word">Shop</span>
                      <span class="subtitle-word">Management</span>
                  </div>
                  <div class="brand-tagline">
                      <span class="tagline-text">Browse • Purchase • Enjoy</span>
                  </div>
              </div>
          </div>
          <div class="logo-particles">
              <div class="particle"></div>
              <div class="particle"></div>
              <div class="particle"></div>
              <div class="particle"></div>
              <div class="particle"></div>
          </div>
      `;

  // Add CSS styles for the logo
  addLogoStyles();

  // Insert logo at the top of the page
  const header = document.querySelector(".header");
  if (header) {
    header.insertBefore(logoContainer, header.firstChild);
  } else {
    document.body.insertBefore(logoContainer, document.body.firstChild);
  }
}

// Add comprehensive CSS styles for the logo - Clean version
function addLogoStyles() {
  const styles = `
          <style>
          .dynamic-logo-container {
              position: relative;
              background: transparent;
              padding: 10px;
              margin: 0 auto;
              max-width: 500px;
              text-align: center;
              overflow: visible;
              z-index: 1000;
          }
          
          .logo-wrapper {
              display: flex;
              align-items: center;
              justify-content: center;
              gap: 20px;
              flex-wrap: wrap;
          }
          
          .logo-icon {
              position: relative;
          }
          
          .logo-circle {
              width: 60px;
              height: 60px;
              border: 3px solid #e74c3c;
              border-radius: 50%;
              display: flex;
              align-items: center;
              justify-content: center;
              position: relative;
              background: transparent;
              animation: logoRotate 10s linear infinite;
              box-shadow: 0 0 20px rgba(231, 76, 60, 0.6);
          }
          
          .logo-cart {
              font-size: 20px;
              color: #e74c3c;
              animation: cartBob 3s ease-in-out infinite;
              text-shadow: 0 2px 8px rgba(231, 76, 60, 0.4);
          }
          
          .orbit-dot {
              position: absolute;
              width: 8px;
              height: 8px;
              border-radius: 50%;
              background: linear-gradient(45deg, #f39c12, #e67e22);
              box-shadow: 0 0 15px rgba(243, 156, 18, 0.8);
          }
          
          .orbit-1 {
              top: -4px;
              left: 50%;
              transform: translateX(-50%);
              animation: orbitRotate1 4s linear infinite;
          }
          
          .orbit-2 {
              top: 50%;
              right: -4px;
              transform: translateY(-50%);
              animation: orbitRotate2 6s linear infinite;
          }
          
          .orbit-3 {
              bottom: -4px;
              left: 50%;
              transform: translateX(-50%);
              animation: orbitRotate3 5s linear infinite;
          }
          
          .logo-text {
              text-align: left;
          }
          
          .brand-name {
              font-size: 2rem;
              font-weight: 700;
              font-family: 'Oswald', sans-serif;
              margin-bottom: 2px;
          }
          
          .letter {
              display: inline-block;
              background: linear-gradient(45deg, #e74c3c, #f39c12, #27ae60, #3498db);
              background-size: 300% 300%;
              -webkit-background-clip: text;
              -webkit-text-fill-color: transparent;
              background-clip: text;
              animation: letterGradient 3s ease-in-out infinite, letterBounce 2s ease-in-out infinite;
              transition: transform 0.3s ease;
              filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
          }
          
          .letter:hover {
              transform: scale(1.2) rotate(10deg);
          }
          
          .brand-subtitle {
              font-size: 0.9rem;
              font-weight: 500;
              color: #34495e;
              margin-bottom: 5px;
              font-family: 'Raleway', sans-serif;
              text-shadow: 0 1px 2px rgba(0,0,0,0.1);
          }
          
          .subtitle-word {
              display: inline-block;
              margin-right: 10px;
              opacity: 0;
              animation: fadeInUp 1s ease forwards;
          }
          
          .subtitle-word:nth-child(2) {
              animation-delay: 0.5s;
          }
          
          .brand-tagline {
              font-size: 0.8rem;
              color: #7f8c8d;
              font-family: 'Raleway', sans-serif;
              font-style: italic;
              text-shadow: 0 1px 2px rgba(0,0,0,0.1);
          }
          
          .tagline-text {
              opacity: 0;
              animation: fadeInUp 1s ease 1s forwards;
          }
          
          .logo-particles {
              position: absolute;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              pointer-events: none;
              z-index: -1;
          }
          
          .particle {
              position: absolute;
              width: 4px;
              height: 4px;
              background: radial-gradient(circle, rgba(231, 76, 60, 0.6), transparent);
              border-radius: 50%;
              animation: particleFloat 15s linear infinite;
          }
          
          .particle:nth-child(1) {
              left: 10%;
              animation-delay: 0s;
              animation-duration: 12s;
          }
          
          .particle:nth-child(2) {
              left: 25%;
              animation-delay: 2s;
              animation-duration: 15s;
          }
          
          .particle:nth-child(3) {
              left: 50%;
              animation-delay: 4s;
              animation-duration: 10s;
          }
          
          .particle:nth-child(4) {
              left: 75%;
              animation-delay: 6s;
              animation-duration: 13s;
          }
          
          .particle:nth-child(5) {
              left: 90%;
              animation-delay: 8s;
              animation-duration: 14s;
          }
          
          /* Animations */
          @keyframes logoRotate {
              from { transform: rotate(0deg); }
              to { transform: rotate(360deg); }
          }
          
          @keyframes cartBob {
              0%, 100% { transform: translateY(0px) rotate(0deg); }
              50% { transform: translateY(-5px) rotate(5deg); }
          }
          
          @keyframes orbitRotate1 {
              from { transform: translateX(-50%) rotate(0deg) translateX(40px) rotate(0deg); }
              to { transform: translateX(-50%) rotate(360deg) translateX(40px) rotate(-360deg); }
          }
          
          @keyframes orbitRotate2 {
              from { transform: translateY(-50%) rotate(0deg) translateY(40px) rotate(0deg); }
              to { transform: translateY(-50%) rotate(360deg) translateY(40px) rotate(-360deg); }
          }
          
          @keyframes orbitRotate3 {
              from { transform: translateX(-50%) rotate(0deg) translateX(40px) rotate(0deg); }
              to { transform: translateX(-50%) rotate(360deg) translateX(40px) rotate(-360deg); }
          }
          
          @keyframes letterGradient {
              0%, 100% { background-position: 0% 50%; }
              50% { background-position: 100% 50%; }
          }
          
          @keyframes letterBounce {
              0%, 100% { transform: translateY(0px); }
              50% { transform: translateY(-3px); }
          }
          
          @keyframes fadeInUp {
              from {
                  opacity: 0;
                  transform: translateY(20px);
              }
              to {
                  opacity: 1;
                  transform: translateY(0px);
              }
          }
          
          @keyframes particleFloat {
              0% {
                  transform: translateY(100px) rotate(0deg);
                  opacity: 0;
              }
              10% {
                  opacity: 1;
              }
              90% {
                  opacity: 1;
              }
              100% {
                  transform: translateY(-100px) rotate(360deg);
                  opacity: 0;
              }
          }
          
          /* Responsive Design */
          @media (max-width: 768px) {
              .dynamic-logo-container {
                  margin: 5px;
                  padding: 8px;
              }
              
              .logo-wrapper {
                  flex-direction: column;
                  gap: 8px;
              }
              
              .brand-name {
                  font-size: 1.6rem;
              }
              
              .logo-circle {
                  width: 45px;
                  height: 45px;
                  border-width: 2px;
              }
              
              .logo-cart {
                  font-size: 16px;
              }
              
              .logo-text {
                  text-align: center;
              }
          }
          
          /* Clean hover effect for entire logo */
          .dynamic-logo-container:hover {
              transform: scale(1.05);
              transition: all 0.3s ease;
          }
          
          .dynamic-logo-container:hover .logo-circle {
              animation-duration: 2s;
              box-shadow: 0 0 30px rgba(231, 76, 60, 0.8);
              border-color: #27ae60;
          }
          
          .dynamic-logo-container:hover .logo-cart {
              color: #27ae60;
          }
          </style>
      `;

  document.head.insertAdjacentHTML("beforeend", styles);
}

// Initialize logo animations and effects
function initializeLogoAnimations() {
  // Staggered letter animation
  const letters = document.querySelectorAll(".letter");
  letters.forEach((letter, index) => {
    letter.style.animationDelay = `${index * 0.1}s`;
  });

  // Add typing effect to brand name
  setTimeout(() => {
    addTypingEffect();
  }, 2000);
}

// Add interactive effects
function addLogoInteractivity() {
  const logoContainer = document.getElementById("dynamicLogo");

  if (logoContainer) {
    // Mouse enter effect
    logoContainer.addEventListener("mouseenter", function () {
      this.style.transform = "scale(1.08)";
      this.style.transition = "transform 0.3s ease";
    });

    // Mouse leave effect
    logoContainer.addEventListener("mouseleave", function () {
      this.style.transform = "scale(1)";
    });

    // Click effect
    logoContainer.addEventListener("click", function (event) {
      createClickRipple(event);
      animateLogoClick();
    });
  }
}

// Create ripple effect on click - Clean version
function createClickRipple(event) {
  const ripple = document.createElement("div");
  const rect = event.currentTarget.getBoundingClientRect();
  const x = event.clientX - rect.left;
  const y = event.clientY - rect.top;

  ripple.style.cssText = `
          position: absolute;
          border-radius: 50%;
          background: rgba(231, 76, 60, 0.2);
          transform: scale(0);
          animation: ripple 0.6s linear;
          left: ${x - 25}px;
          top: ${y - 25}px;
          width: 50px;
          height: 50px;
          pointer-events: none;
          z-index: 1000;
      `;

  event.currentTarget.appendChild(ripple);

  // Add ripple animation if not exists
  if (!document.querySelector("#ripple-styles")) {
    const rippleStyle = document.createElement("style");
    rippleStyle.id = "ripple-styles";
    rippleStyle.innerHTML = `
          @keyframes ripple {
              to {
                  transform: scale(4);
                  opacity: 0;
              }
          }
      `;
    document.head.appendChild(rippleStyle);
  }

  setTimeout(() => {
    ripple.remove();
  }, 600);
}

// Animate logo on click
function animateLogoClick() {
  const logoCircle = document.querySelector(".logo-circle");
  const letters = document.querySelectorAll(".letter");

  if (logoCircle) {
    logoCircle.style.animation = "logoRotate 0.5s ease-in-out";
    setTimeout(() => {
      logoCircle.style.animation = "logoRotate 10s linear infinite";
    }, 500);
  }

  letters.forEach((letter, index) => {
    setTimeout(() => {
      letter.style.transform = "scale(1.3) rotate(360deg)";
      letter.style.transition = "transform 0.3s ease";

      setTimeout(() => {
        letter.style.transform = "scale(1) rotate(0deg)";
      }, 300);
    }, index * 50);
  });
}

// Add typing effect
function addTypingEffect() {
  const tagline = document.querySelector(".tagline-text");
  if (tagline) {
    const text = tagline.textContent;
    tagline.textContent = "";
    tagline.style.opacity = "1";

    let i = 0;
    const typeInterval = setInterval(() => {
      if (i < text.length) {
        tagline.textContent += text.charAt(i);
        i++;
      } else {
        clearInterval(typeInterval);
        // Add blinking cursor
        const cursor = document.createElement("span");
        cursor.textContent = "|";
        cursor.style.animation = "blink 1s infinite";
        cursor.style.marginLeft = "2px";
        cursor.style.color = "#e74c3c";
        tagline.appendChild(cursor);

        // Add blink animation if not exists
        if (!document.querySelector("#blink-styles")) {
          const blinkStyle = document.createElement("style");
          blinkStyle.id = "blink-styles";
          blinkStyle.innerHTML = `
                      @keyframes blink {
                          0%, 50% { opacity: 1; }
                          51%, 100% { opacity: 0; }
                      }
                  `;
          document.head.appendChild(blinkStyle);
        }

        // Remove cursor after 3 seconds
        setTimeout(() => {
          if (cursor && cursor.parentNode) {
            cursor.remove();
          }
        }, 3000);
      }
    }, 100);
  }
}

// Add scroll-based logo effects - Clean version
window.addEventListener("scroll", function () {
  const logoContainer = document.getElementById("dynamicLogo");
  if (logoContainer) {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const opacity = Math.max(0.4, 1 - scrollTop / 400);
    logoContainer.style.opacity = opacity;

    // Subtle parallax effect
    logoContainer.style.transform = `translateY(${scrollTop * 0.05}px)`;
  }
});

// Prevent memory leaks - Clean up functions
window.addEventListener("beforeunload", function () {
  // Clear any running intervals
  const intervals = window.setInterval(function () {}, Number.MAX_SAFE_INTEGER);
  for (let i = 1; i < intervals; i++) {
    window.clearInterval(i);
  }
});

// Console log for debugging
console.log("🛒 Shop Management Clean Logo Loaded Successfully! 🛍️");
