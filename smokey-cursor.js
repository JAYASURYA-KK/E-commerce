(function () {
  "use strict";

  class BlackInkCursor {
    constructor() {
      this.cursor = null;
      this.amount = 20;
      this.sineDots = Math.floor(this.amount * 0.3);
      this.width = 26;
      this.idleTimeout = 150;
      this.lastFrame = 0;
      this.mousePosition = { x: 0, y: 0 };
      this.dots = [];
      this.timeoutID = null;
      this.idle = false;

      this.init();
    }

    init() {
      if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", () => this.setup());
      } else {
        this.setup();
      }
    }

    setup() {
      this.createGooFilter();
      this.createCursor();
      this.bindEvents();
      this.buildDots();
      this.startIdleTimer();
      this.render();
    }

    createGooFilter() {
      // Create SVG filter for gooey ink effect
      const svg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
      svg.style.cssText = `
                position: absolute;
                width: 0;
                height: 0;
                visibility: hidden;
            `;

      const defs = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "defs"
      );
      const filter = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "filter"
      );
      filter.setAttribute("id", "ink-goo");
      filter.setAttribute("x", "-50%");
      filter.setAttribute("y", "-50%");
      filter.setAttribute("width", "200%");
      filter.setAttribute("height", "200%");

      // Gaussian blur for the gooey effect
      const feGaussianBlur = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "feGaussianBlur"
      );
      feGaussianBlur.setAttribute("in", "SourceGraphic");
      feGaussianBlur.setAttribute("stdDeviation", "8");
      feGaussianBlur.setAttribute("result", "blur");

      // Color matrix to create the ink effect
      const feColorMatrix = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "feColorMatrix"
      );
      feColorMatrix.setAttribute("in", "blur");
      feColorMatrix.setAttribute("mode", "matrix");
      feColorMatrix.setAttribute(
        "values",
        "1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 20 -10"
      );
      feColorMatrix.setAttribute("result", "goo");

      // Composite to combine with original
      const feComposite = document.createElementNS(
        "http://www.w3.org/2000/svg",
        "feComposite"
      );
      feComposite.setAttribute("in", "SourceGraphic");
      feComposite.setAttribute("in2", "goo");
      feComposite.setAttribute("operator", "atop");

      filter.appendChild(feGaussianBlur);
      filter.appendChild(feColorMatrix);
      filter.appendChild(feComposite);
      defs.appendChild(filter);
      svg.appendChild(defs);
      document.body.appendChild(svg);
    }

    createCursor() {
      this.cursor = document.createElement("div");
      this.cursor.className = "black-ink-cursor";
      this.cursor.style.cssText = `
                pointer-events: none;
                position: fixed;
                display: block;
                top: 0;
                left: 0;
                z-index: 9999;
                filter: url("#ink-goo");
                transform-origin: center center;
            `;

      document.body.appendChild(this.cursor);

      // Hide default cursor
      const style = document.createElement("style");
      style.textContent = `
                * {
                    cursor: none !important;
                }
                a, button, input, textarea, select {
                    cursor: none !important;
                }
            `;
      document.head.appendChild(style);
    }

    buildDots() {
      for (let i = 0; i < this.amount; i++) {
        const dot = new InkDot(i, this.width, this.sineDots);
        this.dots.push(dot);
        this.cursor.appendChild(dot.element);
      }
    }

    bindEvents() {
      document.addEventListener("mousemove", this.onMouseMove);
      document.addEventListener("touchmove", this.onTouchMove);
      document.addEventListener("touchstart", this.onTouchMove);
      window.addEventListener("resize", this.onResize);
    }

    onMouseMove = (event) => {
      this.mousePosition.x = event.clientX - this.width / 2;
      this.mousePosition.y = event.clientY - this.width / 2;
      this.resetIdleTimer();
    };

    onTouchMove = (event) => {
      event.preventDefault();
      if (event.touches && event.touches[0]) {
        this.mousePosition.x = event.touches[0].clientX - this.width / 2;
        this.mousePosition.y = event.touches[0].clientY - this.width / 2;
        this.resetIdleTimer();
      }
    };

    onResize = () => {
      // Handle resize if needed
    };

    startIdleTimer() {
      this.timeoutID = setTimeout(() => this.goInactive(), this.idleTimeout);
      this.idle = false;
    }

    resetIdleTimer() {
      clearTimeout(this.timeoutID);
      this.startIdleTimer();
    }

    goInactive() {
      this.idle = true;
      for (let dot of this.dots) {
        dot.lock();
      }
    }

    render = (timestamp) => {
      if (!this.lastFrame) this.lastFrame = timestamp;
      const delta = timestamp - this.lastFrame;
      this.positionCursor(delta);
      this.lastFrame = timestamp;
      requestAnimationFrame(this.render);
    };

    positionCursor(delta) {
      let x = this.mousePosition.x;
      let y = this.mousePosition.y;

      this.dots.forEach((dot, index) => {
        const nextDot = this.dots[index + 1] || this.dots[0];
        dot.x = x;
        dot.y = y;
        dot.draw(delta, this.idle);

        if (!this.idle || index <= this.sineDots) {
          const dx = (nextDot.x - dot.x) * 0.35;
          const dy = (nextDot.y - dot.y) * 0.35;
          x += dx;
          y += dy;
        }
      });
    }
  }

  class InkDot {
    constructor(index, width, sineDots) {
      this.index = index;
      this.anglespeed = 0.05;
      this.x = 0;
      this.y = 0;
      this.scale = 1 - 0.05 * index;
      this.range = width / 2 - (width / 2) * this.scale + 2;
      this.limit = width * 0.75 * this.scale;
      this.sineDots = sineDots;

      this.element = document.createElement("span");
      this.element.style.cssText = `
                position: absolute;
                display: block;
                width: ${26 * this.scale}px;
                height: ${26 * this.scale}px;
                border-radius: 50%;
                background-color: #000000;
                transform-origin: center center;
                transform: translate(-50%, -50%);
                opacity: ${0.8 - index * 0.03};
            `;
    }

    lock() {
      this.lockX = this.x;
      this.lockY = this.y;
      this.angleX = Math.PI * 2 * Math.random();
      this.angleY = Math.PI * 2 * Math.random();
    }

    draw(delta, idle) {
      if (!idle || this.index <= this.sineDots) {
        this.element.style.transform = `translate(${this.x}px, ${this.y}px) translate(-50%, -50%)`;
      } else {
        this.angleX += this.anglespeed;
        this.angleY += this.anglespeed;
        this.y = this.lockY + Math.sin(this.angleY) * this.range;
        this.x = this.lockX + Math.sin(this.angleX) * this.range;
        this.element.style.transform = `translate(${this.x}px, ${this.y}px) translate(-50%, -50%)`;
      }
    }
  }

  // Initialize the black ink cursor
  new BlackInkCursor();
})();
