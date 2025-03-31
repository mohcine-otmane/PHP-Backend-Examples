// Particle Background Effect
export class ParticleBackground {
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

// Glitch Effect
export class GlitchEffect {
    constructor(element) {
        this.element = element;
        this.originalText = element.textContent;
        this.init();
    }

    init() {
        this.element.addEventListener('mouseover', () => this.startGlitch());
        this.element.addEventListener('mouseout', () => this.stopGlitch());
    }

    startGlitch() {
        this.glitchInterval = setInterval(() => {
            this.element.textContent = this.originalText
                .split('')
                .map(char => Math.random() > 0.9 ? String.fromCharCode(33 + Math.floor(Math.random() * 94)) : char)
                .join('');
        }, 50);
    }

    stopGlitch() {
        clearInterval(this.glitchInterval);
        this.element.textContent = this.originalText;
    }
}

// Neon Text Effect
export class NeonTextEffect {
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
export class HoverCardEffect {
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
export class MatrixRain {
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
export class ScanlineEffect {
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
export class EnhancedParticleSystem {
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
export class ParallaxEffect {
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