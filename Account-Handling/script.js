import {
    MatrixRain,
    ScanlineEffect,
    EnhancedParticleSystem,
    GlitchEffect,
    NeonTextEffect,
    HoverCardEffect,
    ParallaxEffect
} from './effects.js';

// Particle Background Effect
class ParticleBackground {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.init();
    }

    init() {
        this.canvas.style.position = 'fixed';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        this.canvas.style.zIndex = '-1';
        document.body.appendChild(this.canvas);
        this.resize();
        this.createParticles();
        this.animate();
        window.addEventListener('resize', () => this.resize());
    }

    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }

    createParticles() {
        const numberOfParticles = Math.floor((this.canvas.width * this.canvas.height) / 9000);
        for (let i = 0; i < numberOfParticles; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                size: Math.random() * 2 + 1,
                speedX: Math.random() * 0.5 - 0.25,
                speedY: Math.random() * 0.5 - 0.25,
                color: `rgba(0, 255, 157, ${Math.random() * 0.5})`
            });
        }
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        this.particles.forEach(particle => {
            particle.x += particle.speedX;
            particle.y += particle.speedY;

            if (particle.x < 0) particle.x = this.canvas.width;
            if (particle.x > this.canvas.width) particle.x = 0;
            if (particle.y < 0) particle.y = this.canvas.height;
            if (particle.y > this.canvas.height) particle.y = 0;

            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            this.ctx.fillStyle = particle.color;
            this.ctx.fill();
        });

        requestAnimationFrame(() => this.animate());
    }
}

// Neon Text Effect
class NeonTextEffect {
    constructor(element) {
        this.element = element;
        this.init();
    }

    init() {
        this.element.style.textShadow = `
            0 0 5px #fff,
            0 0 10px #fff,
            0 0 20px var(--primary-color),
            0 0 30px var(--primary-color),
            0 0 40px var(--primary-color)
        `;
    }
}

// Hover Card Effect
class HoverCardEffect {
    constructor(element) {
        this.element = element;
        this.init();
    }

    init() {
        this.element.addEventListener('mousemove', (e) => {
            const rect = this.element.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;

            this.element.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
        });

        this.element.addEventListener('mouseleave', () => {
            this.element.style.transform = 'perspective(1000px) rotateX(0) rotateY(0)';
        });
    }
}

// Matrix Rain Effect
class MatrixRain {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#$%^&*()';
        this.fontSize = 14;
        this.columns = 0;
        this.drops = [];
        this.init();
    }

    init() {
        this.canvas.style.position = 'fixed';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        this.canvas.style.zIndex = '-1';
        this.canvas.style.opacity = '0.1';
        document.body.appendChild(this.canvas);
        this.resize();
        this.animate();
        window.addEventListener('resize', () => this.resize());
    }

    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
        this.columns = Math.floor(this.canvas.width / this.fontSize);
        this.drops = Array(this.columns).fill(1);
    }

    animate() {
        this.ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.ctx.fillStyle = '#0F0';
        this.ctx.font = this.fontSize + 'px monospace';

        for (let i = 0; i < this.drops.length; i++) {
            const text = this.characters[Math.floor(Math.random() * this.characters.length)];
            this.ctx.fillText(text, i * this.fontSize, this.drops[i] * this.fontSize);
            
            if (this.drops[i] * this.fontSize > this.canvas.height && Math.random() > 0.975) {
                this.drops[i] = 0;
            }
            this.drops[i]++;
        }

        requestAnimationFrame(() => this.animate());
    }
}

// Scanline Effect
class ScanlineEffect {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.init();
    }

    init() {
        this.canvas.style.position = 'fixed';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        this.canvas.style.zIndex = '9999';
        this.canvas.style.pointerEvents = 'none';
        document.body.appendChild(this.canvas);
        this.resize();
        this.animate();
        window.addEventListener('resize', () => this.resize());
    }

    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        const gradient = this.ctx.createLinearGradient(0, 0, 0, this.canvas.height);
        gradient.addColorStop(0, 'rgba(0, 255, 157, 0.1)');
        gradient.addColorStop(0.5, 'rgba(0, 255, 157, 0.05)');
        gradient.addColorStop(1, 'rgba(0, 255, 157, 0.1)');
        
        this.ctx.fillStyle = gradient;
        this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);

        requestAnimationFrame(() => this.animate());
    }
}

// Enhanced Particle System
class EnhancedParticleSystem {
    constructor() {
        this.canvas = document.createElement('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.particles = [];
        this.init();
    }

    init() {
        this.canvas.style.position = 'fixed';
        this.canvas.style.top = '0';
        this.canvas.style.left = '0';
        this.canvas.style.width = '100%';
        this.canvas.style.height = '100%';
        this.canvas.style.zIndex = '-1';
        document.body.appendChild(this.canvas);
        this.resize();
        this.createParticles();
        this.animate();
        window.addEventListener('resize', () => this.resize());
        window.addEventListener('mousemove', (e) => this.handleMouseMove(e));
    }

    resize() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }

    createParticles() {
        const numberOfParticles = Math.floor((this.canvas.width * this.canvas.height) / 9000);
        for (let i = 0; i < numberOfParticles; i++) {
            this.particles.push({
                x: Math.random() * this.canvas.width,
                y: Math.random() * this.canvas.height,
                size: Math.random() * 2 + 1,
                speedX: Math.random() * 0.5 - 0.25,
                speedY: Math.random() * 0.5 - 0.25,
                color: `rgba(0, 255, 157, ${Math.random() * 0.5})`,
                connections: []
            });
        }
    }

    handleMouseMove(e) {
        const mouseX = e.clientX;
        const mouseY = e.clientY;
        
        this.particles.forEach(particle => {
            const dx = mouseX - particle.x;
            const dy = mouseY - particle.y;
            const distance = Math.sqrt(dx * dx + dy * dy);
            
            if (distance < 100) {
                const angle = Math.atan2(dy, dx);
                const force = (100 - distance) / 100;
                particle.speedX -= Math.cos(angle) * force * 0.5;
                particle.speedY -= Math.sin(angle) * force * 0.5;
            }
        });
    }

    animate() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        this.particles.forEach(particle => {
            particle.x += particle.speedX;
            particle.y += particle.speedY;

            if (particle.x < 0) particle.x = this.canvas.width;
            if (particle.x > this.canvas.width) particle.x = 0;
            if (particle.y < 0) particle.y = this.canvas.height;
            if (particle.y > this.canvas.height) particle.y = 0;

            this.ctx.beginPath();
            this.ctx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
            this.ctx.fillStyle = particle.color;
            this.ctx.fill();

            // Draw connections
            particle.connections = [];
            this.particles.forEach(otherParticle => {
                if (particle === otherParticle) return;
                
                const dx = particle.x - otherParticle.x;
                const dy = particle.y - otherParticle.y;
                const distance = Math.sqrt(dx * dx + dy * dy);
                
                if (distance < 150) {
                    this.ctx.beginPath();
                    this.ctx.strokeStyle = `rgba(0, 255, 157, ${0.2 * (1 - distance / 150)})`;
                    this.ctx.lineWidth = 1;
                    this.ctx.moveTo(particle.x, particle.y);
                    this.ctx.lineTo(otherParticle.x, otherParticle.y);
                    this.ctx.stroke();
                }
            });
        });

        requestAnimationFrame(() => this.animate());
    }
}

// Parallax Effect
class ParallaxEffect {
    constructor(element, speed = 0.5) {
        this.element = element;
        this.speed = speed;
        this.init();
    }

    init() {
        window.addEventListener('scroll', () => this.handleScroll());
        window.addEventListener('mousemove', (e) => this.handleMouseMove(e));
    }

    handleScroll() {
        const scrolled = window.pageYOffset;
        const yPos = -(scrolled * this.speed);
        this.element.style.transform = `translateY(${yPos}px)`;
    }

    handleMouseMove(e) {
        const mouseX = e.clientX;
        const mouseY = e.clientY;
        const rect = this.element.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        
        const moveX = (mouseX - centerX) * this.speed * 0.01;
        const moveY = (mouseY - centerY) * this.speed * 0.01;
        
        this.element.style.transform = `translate(${moveX}px, ${moveY}px)`;
    }
}

// Danger Zone Toggle
class DangerZoneToggle {
    constructor() {
        this.toggleButton = document.getElementById('toggleDangerZone');
        this.dangerZone = document.getElementById('dangerZone');
        this.init();
    }

    init() {
        if (this.toggleButton && this.dangerZone) {
            this.toggleButton.addEventListener('click', () => this.toggle());
        }
    }

    toggle() {
        const isHidden = this.dangerZone.style.display === 'none';
        this.dangerZone.style.display = isHidden ? 'block' : 'none';
        this.toggleButton.innerHTML = isHidden ? 
            '<i class="fas fa-exclamation-triangle"></i> Hide Danger Zone' : 
            '<i class="fas fa-exclamation-triangle"></i> Show Danger Zone';
        
        // Add animation
        if (isHidden) {
            this.dangerZone.style.opacity = '0';
            this.dangerZone.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                this.dangerZone.style.opacity = '1';
                this.dangerZone.style.transform = 'translateY(0)';
            }, 10);
        }
    }
}

// Initialize all effects
document.addEventListener('DOMContentLoaded', () => {
    // Initialize background effects
    new MatrixRain();
    new ScanlineEffect();
    new EnhancedParticleSystem();

    // Initialize parallax effects
    const parallaxElements = document.querySelectorAll('.parallax');
    parallaxElements.forEach(element => {
        const speed = parseFloat(element.dataset.speed) || 0.5;
        new ParallaxEffect(element, speed);
    });

    // Initialize glitch effect on titles
    document.querySelectorAll('.section-title, .profile-name').forEach(title => {
        new GlitchEffect(title);
    });

    // Initialize neon text effect on important elements
    document.querySelectorAll('.profile-username, .stat-value').forEach(text => {
        new NeonTextEffect(text);
    });

    // Initialize hover card effect on profile sections
    document.querySelectorAll('.profile-section').forEach(section => {
        new HoverCardEffect(section);
    });

    // Add typing effect to profile bio
    const bioElement = document.querySelector('.profile-bio');
    if (bioElement) {
        const text = bioElement.textContent;
        bioElement.textContent = '';
        let i = 0;
        const typeWriter = () => {
            if (i < text.length) {
                bioElement.textContent += text.charAt(i);
                i++;
                setTimeout(typeWriter, 50);
            }
        };
        typeWriter();
    }

    // Add ripple effect to buttons
    document.querySelectorAll('button, .action-button').forEach(button => {
        button.addEventListener('click', (e) => {
            const ripple = document.createElement('div');
            ripple.className = 'ripple';
            button.appendChild(ripple);

            const rect = button.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;

            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Profile picture preview functionality
    const profilePictureInput = document.getElementById('profile_picture');
    const previewImage = document.querySelector('.preview-image');
    
    if (profilePictureInput && previewImage) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file size (10MB)
                if (file.size > 10 * 1024 * 1024) {
                    alert('File is too large. Maximum size is 10MB.');
                    this.value = ''; // Clear the input
                    return;
                }

                // Validate file type
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    alert('Invalid file type. Only JPG, PNG, and GIF files are allowed.');
                    this.value = ''; // Clear the input
                    return;
                }

                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    // Add a subtle animation to indicate the preview update
                    previewImage.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        previewImage.style.transform = 'scale(1)';
                    }, 200);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Initialize danger zone toggle
    new DangerZoneToggle();
}); 