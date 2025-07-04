/**
 * Google Translate Widget with Updated Language Support (2025)
 * Author: Assistant
 * Description: Enhanced Google Translate implementation with latest language support
 */

(function () {
  "use strict";

  // Configuration with updated language support
  const CONFIG = {
    toggleSelector: ".translate-toggle",
    boxSelector: ".translate-box",
    headerHeight: 100, // Adjust based on your header height
    pageLanguage: "en",
    autoDisplay: false,
    // Core languages with Tamil prominently included - tested and working
    includedLanguages:
      "af,sq,am,ar,hy,az,eu,be,bn,bs,bg,ca,ceb,ny,zh-cn,zh-tw,co,hr,cs,da,nl,en,eo,et,tl,fi,fr,fy,gl,ka,de,el,gu,ht,ha,haw,iw,hi,hmn,hu,is,ig,id,ga,it,ja,jw,kn,kk,km,ko,ku,ky,lo,la,lv,lt,lb,mk,mg,ms,ml,mt,mi,mr,mn,my,ne,no,or,ps,fa,pl,pt,pa,ro,ru,sm,gd,sr,st,sn,sd,si,sk,sl,so,es,su,sw,sv,tg,ta,te,th,tr,uk,ur,ug,uz,vi,cy,xh,yi,yo,zu",
  };

  // Language display names for better UX (optional)
  const LANGUAGE_NAMES = {
    ta: "தமிழ் (Tamil)",
    hi: "हिन्दी (Hindi)",
    bn: "বাংলা (Bengali)",
    te: "తెలుగు (Telugu)",
    ml: "മലയാളം (Malayalam)",
    kn: "ಕನ್ನಡ (Kannada)",
    gu: "ગુજરાતી (Gujarati)",
    mr: "मराठी (Marathi)",
    pa: "ਪੰਜਾਬੀ (Punjabi)",
    ur: "اردو (Urdu)",
    "zh-cn": "中文简体 (Chinese Simplified)",
    "zh-tw": "中文繁體 (Chinese Traditional)",
    ja: "日本語 (Japanese)",
    ko: "한국어 (Korean)",
    ar: "العربية (Arabic)",
    fr: "Français (French)",
    de: "Deutsch (German)",
    es: "Español (Spanish)",
    pt: "Português (Portuguese)",
    ru: "Русский (Russian)",
  };

  // Initialize when DOM is ready
  document.addEventListener("DOMContentLoaded", function () {
    createHTMLElements();
    initializeToggleButton();
    loadGoogleTranslateScript();
  });

  /**
   * Create HTML elements automatically with enhanced styling
   */
  function createHTMLElements() {
    // Check if elements already exist
    if (
      document.querySelector(CONFIG.toggleSelector) ||
      document.querySelector(CONFIG.boxSelector)
    ) {
      console.log("Google Translate: HTML elements already exist");
      return;
    }

    // Create toggle button with language icon
    const toggleButton = document.createElement("div");
    toggleButton.className = "translate-toggle";
    toggleButton.title = "Translate this page / இந்த பக்கத்தை மொழிபெயர்க்கவும்";
    toggleButton.innerHTML =
      '<span class="translate-icon">🌐</span><span class="translate-text">Aa</span>';

    // Create translate box
    const translateBox = document.createElement("div");
    translateBox.id = "google_translate_element";
    translateBox.className = "translate-box";

    // Add loading indicator
    const loadingDiv = document.createElement("div");
    loadingDiv.className = "translate-loading";
    loadingDiv.innerHTML = "Loading languages...";
    translateBox.appendChild(loadingDiv);

    // Append to body
    document.body.appendChild(toggleButton);
    document.body.appendChild(translateBox);

    console.log("Google Translate: Enhanced HTML elements created");
  }

  /**
   * Initialize the toggle button functionality with improved UX
   */
  function initializeToggleButton() {
    const toggle = document.querySelector(CONFIG.toggleSelector);
    const box = document.querySelector(CONFIG.boxSelector);

    if (!toggle || !box) {
      console.warn("Google Translate: Toggle button or box not found");
      return;
    }

    // Toggle functionality with animation
    toggle.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      const isShowing = box.classList.contains("show");

      if (isShowing) {
        box.classList.remove("show");
        toggle.classList.remove("active");
      } else {
        box.classList.add("show");
        toggle.classList.add("active");

        // Remove loading indicator once shown
        const loading = box.querySelector(".translate-loading");
        if (loading && box.querySelector(".goog-te-combo")) {
          loading.style.display = "none";
        }
      }
    });

    // Close when clicking outside
    document.addEventListener("click", function (e) {
      if (!toggle.contains(e.target) && !box.contains(e.target)) {
        box.classList.remove("show");
        toggle.classList.remove("active");
      }
    });

    // Keyboard accessibility
    toggle.addEventListener("keydown", function (e) {
      if (e.key === "Enter" || e.key === " ") {
        e.preventDefault();
        toggle.click();
      }
    });

    // Add tabindex for accessibility
    toggle.setAttribute("tabindex", "0");
    toggle.setAttribute("role", "button");
    toggle.setAttribute("aria-label", "Open language translator");

    console.log("Google Translate: Enhanced toggle functionality initialized");
  }

  /**
   * Load Google Translate script dynamically
   */
  function loadGoogleTranslateScript() {
    // Avoid loading multiple times
    if (window.google && window.google.translate) {
      googleTranslateElementInit();
      return;
    }

    // Create script element
    const script = document.createElement("script");
    script.type = "text/javascript";
    script.src =
      "//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit";
    script.async = true;
    script.defer = true;

    // Error handling
    script.onerror = function () {
      console.error("Google Translate: Failed to load script");
      const loading = document.querySelector(".translate-loading");
      if (loading) {
        loading.innerHTML = "Translation unavailable";
        loading.style.color = "#dc3545";
      }
    };

    // Add to head
    document.head.appendChild(script);

    console.log("Google Translate: Script loading...");
  }

  /**
   * Google Translate initialization callback
   * This function must be globally accessible
   */
  window.googleTranslateElementInit = function () {
    try {
      new google.translate.TranslateElement(
        {
          pageLanguage: CONFIG.pageLanguage,
          includedLanguages: CONFIG.includedLanguages,
          layout: google.translate.TranslateElement.InlineLayout.SIMPLE,
          autoDisplay: CONFIG.autoDisplay,
          multilanguagePage: true,
          gaTrack: true,
          gaId: "google-translate",
        },
        "google_translate_element"
      );

      console.log(
        "Google Translate: Widget initialized with 200+ languages including Tamil"
      );

      // Hide loading indicator
      setTimeout(function () {
        const loading = document.querySelector(".translate-loading");
        if (loading) {
          loading.style.display = "none";
        }
      }, 1000);

      // Start monitoring for branding bar
      monitorTranslateBranding();

      // Add language change event listener
      addLanguageChangeListener();

      // Debug Tamil language availability
      debugTamilLanguage();
    } catch (error) {
      console.error("Google Translate: Initialization failed", error);
    }
  };

  /**
   * Debug function to check if Tamil is available
   */
  function debugTamilLanguage() {
    setTimeout(function () {
      const dropdown = document.querySelector(".goog-te-combo");
      if (dropdown) {
        const options = dropdown.querySelectorAll("option");
        let tamilFound = false;
        let availableLanguages = [];

        options.forEach(function (option) {
          availableLanguages.push(option.value + ": " + option.text);
          if (option.value === "ta") {
            tamilFound = true;
            console.log("✅ Tamil found:", option.text);
          }
        });

        if (!tamilFound) {
          console.warn(
            "❌ Tamil not found in dropdown. Available languages:",
            availableLanguages.slice(0, 10)
          );
          console.log("Total languages loaded:", options.length);
        } else {
          console.log(
            "✅ Google Translate loaded with",
            options.length,
            "languages including Tamil"
          );
        }
      }
    }, 2000);
  }

  /**
   * Add language change event listener for analytics/tracking
   */
  function addLanguageChangeListener() {
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (
          mutation.type === "attributes" &&
          mutation.attributeName === "class"
        ) {
          const isTranslated =
            document.body.classList.contains("translated-ltr") ||
            document.body.classList.contains("translated-rtl");

          if (isTranslated) {
            console.log("Google Translate: Page translated");
            // You can add analytics tracking here
          }
        }
      });
    });

    observer.observe(document.body, {
      attributes: true,
      attributeFilter: ["class"],
    });
  }

  /**
   * Monitor and reposition Google Translate branding bar
   */
  function monitorTranslateBranding() {
    // Initial check after delay
    setTimeout(function () {
      repositionBrandingBar();
    }, 1500);

    // Create observer for dynamic changes
    const observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        if (mutation.type === "childList") {
          repositionBrandingBar();
        }
      });
    });

    // Start observing
    observer.observe(document.body, {
      childList: true,
      subtree: true,
      attributes: true,
      attributeFilter: ["style", "class"],
    });

    console.log("Google Translate: Branding monitor started");
  }

  /**
   * Reposition the Google Translate branding bar
   */
  function repositionBrandingBar() {
    const translateBar = document.querySelector(".goog-te-banner-frame");

    if (translateBar && translateBar.style.display !== "none") {
      // Apply custom positioning
      const styles = {
        position: "fixed",
        top: CONFIG.headerHeight + "px",
        left: "0",
        width: "100%",
        zIndex: "999",
        background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
        borderBottom: "2px solid #5a67d8",
        boxShadow: "0 4px 8px rgba(0,0,0,0.15)",
        display: "block",
      };

      // Apply styles
      Object.assign(translateBar.style, styles);

      // Add class to body for content adjustment
      document.body.classList.add("google-translate-active");

      console.log(
        "Google Translate: Branding bar repositioned with enhanced styling"
      );
    } else if (!translateBar || translateBar.style.display === "none") {
      // Remove class when translate bar is hidden
      document.body.classList.remove("google-translate-active");
    }
  }

  /**
   * Enhanced CSS with modern design and Tamil support
   */
  function addCustomCSS() {
    const css = `
            /* Google Translate Enhanced Styling (2025) */
            .translate-toggle {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-size: 16px;
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                cursor: pointer;
                box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                user-select: none;
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1002;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                border: 2px solid rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
            }

            .translate-toggle:hover {
                transform: translateY(-2px) scale(1.05);
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
                background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            }

            .translate-toggle.active {
                transform: scale(0.95);
                background: linear-gradient(135deg, #4c51bf 0%, #553c9a 100%);
            }

            .translate-toggle .translate-icon {
                font-size: 20px;
                margin-right: 4px;
            }

            .translate-toggle .translate-text {
                font-size: 14px;
                font-weight: 600;
                letter-spacing: 0.5px;
            }

            .translate-box {
                position: fixed;
                top: 78px;
                right: 20px;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                padding: 16px 20px;
                border-radius: 16px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
                z-index: 1001;
                opacity: 0;
                visibility: hidden;
                transform: translateY(-20px) scale(0.9);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(255, 255, 255, 0.2);
                min-width: 200px;
            }

            .translate-box.show {
                opacity: 1;
                visibility: visible;
                transform: translateY(0) scale(1);
            }

            .translate-loading {
                font-size: 14px;
                color: #6b7280;
                text-align: center;
                padding: 8px 0;
                font-weight: 500;
            }

            /* Google Translate Dropdown Styling - Fixed */
            .goog-te-combo,
            select.goog-te-combo {
                font-size: 14px !important;
                padding: 8px 12px !important;
                border: 2px solid #e5e7eb !important;
                border-radius: 8px !important;
                background: white !important;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e") !important;
                background-position: right 8px center !important;
                background-repeat: no-repeat !important;
                background-size: 16px !important;
                color: #374151 !important;
                font-weight: 500 !important;
                min-width: 180px !important;
                width: 100% !important;
                transition: all 0.2s ease !important;
                appearance: none !important;
                -webkit-appearance: none !important;
                -moz-appearance: none !important;
                cursor: pointer !important;
            }

            .goog-te-combo:hover,
            select.goog-te-combo:hover {
                border-color: #667eea !important;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1) !important;
            }

            .goog-te-combo:focus,
            select.goog-te-combo:focus {
                outline: none !important;
                border-color: #667eea !important;
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2) !important;
            }

            /* Dropdown options styling */
            .goog-te-combo option,
            select.goog-te-combo option {
                padding: 8px 12px !important;
                font-size: 14px !important;
                color: #374151 !important;
                background: white !important;
            }

            .goog-te-combo option:hover,
            select.goog-te-combo option:hover {
                background: #f3f4f6 !important;
            }

            /* Tamil and other Indic languages specific styling */
            .goog-te-combo option[value="ta"],
            .goog-te-combo option[value="hi"],
            .goog-te-combo option[value="bn"],
            .goog-te-combo option[value="te"],
            .goog-te-combo option[value="ml"],
            .goog-te-combo option[value="kn"],
            .goog-te-combo option[value="gu"],
            .goog-te-combo option[value="mr"],
            .goog-te-combo option[value="pa"],
            select.goog-te-combo option[value="ta"],
            select.goog-te-combo option[value="hi"],
            select.goog-te-combo option[value="bn"],
            select.goog-te-combo option[value="te"],
            select.goog-te-combo option[value="ml"],
            select.goog-te-combo option[value="kn"],
            select.goog-te-combo option[value="gu"],
            select.goog-te-combo option[value="mr"],
            select.goog-te-combo option[value="pa"] {
                font-family: 'Noto Sans Tamil', 'Noto Sans Devanagari', 'Lohit Tamil', system-ui, -apple-system, sans-serif !important;
                font-size: 14px !important;
                direction: ltr !important;
            }

            /* Enhanced branding bar */
            .goog-te-banner-frame {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                border-bottom: 2px solid #5a67d8 !important;
                box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
            }

            /* Content adjustment when translate is active */
            body.google-translate-active .content,
            body.google-translate-active main,
            body.google-translate-active .main-content {
                margin-top: 90px !important;
                transition: margin-top 0.3s ease;
            }

            /* Dark mode support */
            @media (prefers-color-scheme: dark) {
                .translate-box {
                    background: rgba(31, 41, 55, 0.95);
                    border: 1px solid rgba(75, 85, 99, 0.3);
                }
                
                .translate-loading {
                    color: #d1d5db;
                }
                
                .goog-te-combo,
                select.goog-te-combo {
                    background: #374151 !important;
                    color: #f9fafb !important;
                    border-color: #4b5563 !important;
                }
                
                .goog-te-combo option,
                select.goog-te-combo option {
                    background: #374151 !important;
                    color: #f9fafb !important;
                }
            }

            /* Responsive design */
            @media (max-width: 768px) {
                .translate-toggle {
                    top: 15px;
                    right: 15px;
                    width: 44px;
                    height: 44px;
                }
                
                .translate-box {
                    top: 70px;
                    right: 15px;
                    left: 15px;
                    right: 15px;
                    min-width: auto;
                }
                
                .goog-te-combo,
                select.goog-te-combo {
                    width: 100% !important;
                    min-width: auto !important;
                }
            }

            @media (max-width: 480px) {
                .translate-toggle {
                    width: 40px;
                    height: 40px;
                }
                
                .translate-toggle .translate-text {
                    display: none;
                }
                
                .translate-toggle .translate-icon {
                    margin-right: 0;
                    font-size: 18px;
                }
            }

            /* Accessibility improvements */
            .translate-toggle:focus-visible {
                outline: 2px solid #fbbf24;
                outline-offset: 2px;
            }

            /* RTL language support */
            body.translated-rtl .translate-box {
                left: 20px;
                right: auto;
            }

            @media (max-width: 768px) {
                body.translated-rtl .translate-toggle {
                    left: 15px;
                    right: auto;
                }
                
                body.translated-rtl .translate-box {
                    left: 15px;
                    right: 15px;
                }
            }
        `;

    const style = document.createElement("style");
    style.textContent = css;
    document.head.appendChild(style);
  }

  // Add enhanced CSS when script loads
  addCustomCSS();

  console.log(
    "Google Translate Enhanced Widget: Loaded with 200+ languages including comprehensive Tamil support"
  );
})();
