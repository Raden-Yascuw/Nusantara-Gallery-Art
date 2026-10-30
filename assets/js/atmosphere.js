/**
 * Nusantara Gallery Art - Ambient Atmosphere Engine
 * Hallmark-compliant canvas particle engine
 * - Mode Terang: Guguran Daun Tropis Melayang Tenang (Warm Day Canvas)
 * - Mode Gelap  : Rintik Hujan Syahdu Malam Galeri (Obsidian Night Exhibition)
 *
 * Lightweight, 60fps, zero-layout-thrashing, battery-conscious.
 */

(function () {
  'use strict';

  // Cek preferensi aksesibilitas
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return;
  }

  class AtmosphereEngine {
    constructor() {
      this.canvas = document.createElement('canvas');
      this.canvas.id = 'atmosphere-canvas';
      this.canvas.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        pointer-events: none;
        z-index: 1;
        opacity: 0.85;
        transition: opacity 1.8s cubic-bezier(0.22, 1, 0.36, 1);
      `;
      document.body.appendChild(this.canvas);

      this.ctx = this.canvas.getContext('2d', { alpha: true });
      this.width = 0;
      this.height = 0;
      this.particles = [];
      this.animId = null;
      this.isDark = false;
      this.opacity = 1;
      this.isTransitioning = false;

      this.initDimensions();
      this.detectTheme();
      this.spawnParticles();
      this.bindEvents();
      this.start();
    }

    initDimensions() {
      this.width = window.innerWidth;
      this.height = window.innerHeight;
      this.canvas.width = this.width;
      this.canvas.height = this.height;
    }

    detectTheme() {
      const modeAttr = document.documentElement.getAttribute('data-mode') || document.documentElement.getAttribute('data-theme');
      this.isDark = modeAttr === 'dark';
    }

    getParticleCount() {
      const isMobile = window.innerWidth < 768;
      if (this.isDark) {
        // Rain particles
        return isMobile ? 32 : 64;
      } else {
        // Falling leaves particles
        return isMobile ? 12 : 22;
      }
    }

    createLeaf() {
      const colors = [
        'rgba(184, 115, 51, 0.28)',  // Warm amber
        'rgba(168, 86, 48, 0.24)',   // Terracotta tint
        'rgba(195, 145, 60, 0.26)',  // Dried ochre
        'rgba(140, 110, 60, 0.22)',  // Olive browning
        'rgba(212, 163, 89, 0.25)'   // Golden leaf
      ];

      return {
        type: 'leaf',
        x: Math.random() * (this.width + 100) - 50,
        y: Math.random() * this.height - this.height,
        size: 9 + Math.random() * 12,
        speedY: 0.6 + Math.random() * 0.9,
        speedX: 0.3 + Math.random() * 0.7,
        swaySpeed: 0.015 + Math.random() * 0.02,
        swayOffset: Math.random() * Math.PI * 2,
        rotation: Math.random() * Math.PI * 2,
        rotSpeed: (Math.random() - 0.5) * 0.025,
        tilt: Math.random() * Math.PI,
        color: colors[Math.floor(Math.random() * colors.length)]
      };
    }

    createRaindrop() {
      return {
        type: 'rain',
        x: Math.random() * (this.width + 120),
        y: Math.random() * this.height - this.height,
        len: 14 + Math.random() * 18,
        speedY: 8 + Math.random() * 7,
        speedX: -1.2 - Math.random() * 0.8,
        opacity: 0.12 + Math.random() * 0.22,
        width: 0.8 + Math.random() * 0.6
      };
    }

    spawnParticles() {
      this.particles = [];
      const count = this.getParticleCount();
      for (let i = 0; i < count; i++) {
        const p = this.isDark ? this.createRaindrop() : this.createLeaf();
        // Sebarkan posisi awal secara vertikal
        p.y = Math.random() * this.height;
        this.particles.push(p);
      }
    }

    drawLeaf(p) {
      this.ctx.save();
      this.ctx.translate(p.x, p.y);
      this.ctx.rotate(p.rotation);
      this.ctx.scale(Math.cos(p.tilt), 1);

      this.ctx.fillStyle = p.color;
      this.ctx.beginPath();
      // Bentuk daun tropis meliuk lembut
      this.ctx.moveTo(0, -p.size);
      this.ctx.quadraticCurveTo(p.size * 0.6, -p.size * 0.3, 0, p.size);
      this.ctx.quadraticCurveTo(-p.size * 0.6, -p.size * 0.3, 0, -p.size);
      this.ctx.fill();

      // Tangkai daun halus
      this.ctx.strokeStyle = 'rgba(100, 70, 40, 0.18)';
      this.ctx.lineWidth = 0.6;
      this.ctx.beginPath();
      this.ctx.moveTo(0, -p.size * 0.8);
      this.ctx.lineTo(0, p.size * 0.9);
      this.ctx.stroke();

      this.ctx.restore();
    }

    drawRaindrop(p) {
      this.ctx.save();
      this.ctx.strokeStyle = `rgba(180, 205, 230, ${p.opacity})`;
      this.ctx.lineWidth = p.width;
      this.ctx.lineCap = 'round';

      this.ctx.beginPath();
      this.ctx.moveTo(p.x, p.y);
      this.ctx.lineTo(p.x + (p.speedX * 2.2), p.y + p.len);
      this.ctx.stroke();
      this.ctx.restore();
    }

    update() {
      for (let i = 0; i < this.particles.length; i++) {
        const p = this.particles[i];

        if (p.type === 'leaf') {
          p.swayOffset += p.swaySpeed;
          p.x += Math.sin(p.swayOffset) * p.speedX + 0.4; // drifting kanan
          p.y += p.speedY;
          p.rotation += p.rotSpeed;
          p.tilt += 0.015;

          // Reset saat melewati bawah layar
          if (p.y > this.height + 20) {
            p.y = -20;
            p.x = Math.random() * (this.width + 100) - 50;
          }
          if (p.x > this.width + 50) {
            p.x = -30;
          }
        } else if (p.type === 'rain') {
          p.y += p.speedY;
          p.x += p.speedX;

          // Reset saat melewati bawah layar
          if (p.y > this.height) {
            p.y = -p.len;
            p.x = Math.random() * (this.width + 120);
          }
          if (p.x < -20) {
            p.x = this.width + 20;
          }
        }
      }
    }

    render() {
      this.ctx.clearRect(0, 0, this.width, this.height);

      for (let i = 0; i < this.particles.length; i++) {
        const p = this.particles[i];
        if (p.type === 'leaf') {
          this.drawLeaf(p);
        } else if (p.type === 'rain') {
          this.drawRaindrop(p);
        }
      }
    }

    loop() {
      this.update();
      this.render();
      this.animId = requestAnimationFrame(() => this.loop());
    }

    start() {
      if (!this.animId) {
        this.loop();
      }
    }

    stop() {
      if (this.animId) {
        cancelAnimationFrame(this.animId);
        this.animId = null;
      }
    }

    switchMode(isDark) {
      if (this.isDark === isDark) return;

      // Slow, gentle crossfade synced with the 1.8s theme transition
      this.canvas.style.opacity = '0';
      setTimeout(() => {
        this.isDark = isDark;
        this.spawnParticles();
        this.canvas.style.opacity = '0.85';
      }, 950);
    }

    bindEvents() {
      window.addEventListener('resize', () => {
        this.initDimensions();
        this.spawnParticles();
      });

      // Pause saat tab browser tidak aktif (hemat daya)
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          this.stop();
        } else {
          this.start();
        }
      });

      // Dengarkan perubahan atribut data-mode atau data-theme pada <html>
      const observer = new MutationObserver(() => {
        const currentMode = document.documentElement.getAttribute('data-mode') || document.documentElement.getAttribute('data-theme');
        const dark = currentMode === 'dark';
        this.switchMode(dark);
      });

      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['data-mode', 'data-theme']
      });
    }
  }

  // Jalankan saat DOM siap
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      window.__galleryAtmosphere = new AtmosphereEngine();
    });
  } else {
    window.__galleryAtmosphere = new AtmosphereEngine();
  }
})();
