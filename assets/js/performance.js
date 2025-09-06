/**
 * 🚀 Performance & Optimization JavaScript
 * Includes infinite scroll, lazy loading, intersection observer, and more
 */

class PerformanceOptimizer {
    constructor() {
        this.init();
    }

    init() {
        this.setupIntersectionObserver();
        this.setupLazyLoading();
        this.setupInfiniteScroll();
        this.setupVirtualScrolling();
        this.setupPerformanceMonitoring();
        this.setupSmoothScrolling();
        this.setupImageOptimization();
        this.setupDebouncedResize();
        this.setupThrottledScroll();
    }

    /**
     * 🔍 Intersection Observer for scroll animations
     */
    setupIntersectionObserver() {
        if (!('IntersectionObserver' in window)) return;

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target); // Stop observing once visible
                }
            });
        }, observerOptions);

        // Observe all elements with animation classes
        const animatedElements = document.querySelectorAll(
            '.observe-fade-in, .observe-slide-left, .observe-slide-right, .observe-scale, .animate-on-scroll'
        );
        
        animatedElements.forEach(el => observer.observe(el));
    }

    /**
     * 🖼️ Lazy Loading for images
     */
    setupLazyLoading() {
        if (!('IntersectionObserver' in window)) return;

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    this.loadImage(img);
                    imageObserver.unobserve(img);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });

        const lazyImages = document.querySelectorAll('img[data-src], .lazy-image');
        lazyImages.forEach(img => imageObserver.observe(img));
    }

    loadImage(img) {
        const src = img.dataset.src || img.src;
        if (!src) return;

        // Create a new image to preload
        const tempImage = new Image();
        tempImage.onload = () => {
            img.src = src;
            img.classList.add('loaded');
            img.classList.remove('lazy-image');
            
            // Remove data-src attribute
            img.removeAttribute('data-src');
        };
        
        tempImage.src = src;
    }

    /**
     * ♾️ Infinite Scroll Implementation
     */
    setupInfiniteScroll() {
        const containers = document.querySelectorAll('.infinite-scroll-container');
        
        containers.forEach(container => {
            const content = container.querySelector('.infinite-scroll-content');
            const loader = container.querySelector('.infinite-scroll-loader');
            
            if (!content || !loader) return;

            let page = 1;
            let loading = false;
            let hasMore = true;

            const loadMore = async () => {
                if (loading || !hasMore) return;
                
                loading = true;
                loader.classList.add('active');
                
                try {
                    // Simulate API call delay
                    await this.delay(1000);
                    
                    // Generate mock data for demonstration
                    const newItems = this.generateMockItems(page);
                    
                    if (newItems.length === 0) {
                        hasMore = false;
                        loader.innerHTML = '<p>No more items to load</p>';
                        return;
                    }
                    
                    // Add new items to the grid
                    newItems.forEach(item => {
                        const itemElement = this.createInfiniteScrollItem(item);
                        content.appendChild(itemElement);
                    });
                    
                    page++;
                    
                } catch (error) {
                    console.error('Error loading more items:', error);
                } finally {
                    loading = false;
                    loader.classList.remove('active');
                }
            };

            // Intersection Observer for infinite scroll trigger
            const triggerObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && hasMore) {
                        loadMore();
                    }
                });
            }, { threshold: 0.1 });

            triggerObserver.observe(loader);
        });
    }

    generateMockItems(page) {
        const items = [];
        const baseId = (page - 1) * 10;
        
        for (let i = 0; i < 10; i++) {
            items.push({
                id: baseId + i,
                title: `Species ${baseId + i}`,
                description: `This is a description for species ${baseId + i}`,
                image: `https://picsum.photos/300/200?random=${baseId + i}`,
                category: `Category ${Math.floor(Math.random() * 5) + 1}`
            });
        }
        
        return items;
    }

    createInfiniteScrollItem(item) {
        const div = document.createElement('div');
        div.className = 'infinite-scroll-item';
        div.style.animationDelay = `${Math.random() * 0.5}s`;
        
        div.innerHTML = `
            <img src="${item.image}" alt="${item.title}" loading="lazy">
            <div class="content">
                <h3>${item.title}</h3>
                <p>${item.description}</p>
                <span class="badge bg-primary">${item.category}</span>
            </div>
        `;
        
        return div;
    }

    /**
     * 📜 Virtual Scrolling for large lists
     */
    setupVirtualScrolling() {
        const containers = document.querySelectorAll('.virtual-scroll-container');
        
        containers.forEach(container => {
            const content = container.querySelector('.virtual-scroll-content');
            if (!content) return;

            const itemHeight = 80; // Height of each item
            const visibleItems = Math.ceil(container.clientHeight / itemHeight);
            const totalItems = 1000; // Total number of items
            
            let startIndex = 0;
            let endIndex = visibleItems + 5; // Buffer

            const renderItems = () => {
                content.innerHTML = '';
                content.style.height = `${totalItems * itemHeight}px`;
                
                for (let i = startIndex; i < endIndex; i++) {
                    if (i >= totalItems) break;
                    
                    const item = document.createElement('div');
                    item.className = 'virtual-scroll-item';
                    item.style.top = `${i * itemHeight}px`;
                    item.innerHTML = `Item ${i + 1}`;
                    
                    content.appendChild(item);
                }
            };

            container.addEventListener('scroll', this.throttle(() => {
                const scrollTop = container.scrollTop;
                startIndex = Math.floor(scrollTop / itemHeight);
                endIndex = startIndex + visibleItems + 10;
                
                renderItems();
            }, 16));

            renderItems();
        });
    }

    /**
     * 📊 Performance Monitoring
     */
    setupPerformanceMonitoring() {
        // Monitor Core Web Vitals
        if ('PerformanceObserver' in window) {
            try {
                const observer = new PerformanceObserver((list) => {
                    list.getEntries().forEach((entry) => {
                        if (entry.entryType === 'largest-contentful-paint') {
                            console.log('LCP:', entry.startTime);
                        }
                        if (entry.entryType === 'first-input') {
                            console.log('FID:', entry.processingStart - entry.startTime);
                        }
                    });
                });
                
                observer.observe({ entryTypes: ['largest-contentful-paint', 'first-input'] });
            } catch (e) {
                console.warn('Performance monitoring not supported');
            }
        }

        // Monitor memory usage
        if ('memory' in performance) {
            setInterval(() => {
                const memory = performance.memory;
                if (memory.usedJSHeapSize > memory.jsHeapSizeLimit * 0.8) {
                    console.warn('High memory usage detected');
                }
            }, 10000);
        }
    }

    /**
     * 🎯 Smooth Scrolling
     */
    setupSmoothScrolling() {
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Smooth scroll to top button
        const scrollToTopBtn = document.createElement('button');
        scrollToTopBtn.innerHTML = '↑';
        scrollToTopBtn.className = 'scroll-to-top-btn';
        scrollToTopBtn.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            border: none;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1000;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;

        document.body.appendChild(scrollToTopBtn);

        window.addEventListener('scroll', this.throttle(() => {
            if (window.pageYOffset > 300) {
                scrollToTopBtn.style.opacity = '1';
            } else {
                scrollToTopBtn.style.opacity = '0';
            }
        }, 100));

        scrollToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    /**
     * 🖼️ Image Optimization
     */
    setupImageOptimization() {
        // Responsive images
        const images = document.querySelectorAll('img');
        images.forEach(img => {
            if (img.complete) {
                this.optimizeImage(img);
            } else {
                img.addEventListener('load', () => this.optimizeImage(img));
            }
        });

        // WebP support detection
        this.checkWebPSupport();
    }

    optimizeImage(img) {
        // Add loading="lazy" if not present
        if (!img.hasAttribute('loading')) {
            img.setAttribute('loading', 'lazy');
        }

        // Add decoding="async" for better performance
        if (!img.hasAttribute('decoding')) {
            img.setAttribute('decoding', 'async');
        }

        // Add error handling
        img.addEventListener('error', () => {
            img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vdCBmb3VuZDwvdGV4dD48L3N2Zz4=';
        });
    }

    async checkWebPSupport() {
        const webP = new Image();
        webP.onload = webP.onerror = () => {
            const isSupported = webP.height === 2;
            if (isSupported) {
                document.documentElement.classList.add('webp');
            } else {
                document.documentElement.classList.add('no-webp');
            }
        };
        webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
    }

    /**
     * 📱 Debounced Resize Handler
     */
    setupDebouncedResize() {
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                this.handleResize();
            }, 250);
        });
    }

    handleResize() {
        // Handle responsive breakpoints
        const width = window.innerWidth;
        
        if (width < 576) {
            document.body.classList.add('mobile');
            document.body.classList.remove('tablet', 'desktop');
        } else if (width < 992) {
            document.body.classList.add('tablet');
            document.body.classList.remove('mobile', 'desktop');
        } else {
            document.body.classList.add('desktop');
            document.body.classList.remove('mobile', 'tablet');
        }

        // Trigger custom resize event
        window.dispatchEvent(new CustomEvent('responsiveChange', { detail: { width } }));
    }

    /**
     * 🚀 Throttled Scroll Handler
     */
    setupThrottledScroll() {
        let ticking = false;
        
        window.addEventListener('scroll', () => {
            if (!ticking) {
                requestAnimationFrame(() => {
                    this.handleScroll();
                    ticking = false;
                });
                ticking = true;
            }
        });
    }

    handleScroll() {
        const scrolled = window.pageYOffset;
        const parallax = document.querySelectorAll('.parallax');
        
        parallax.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });

        // Add scroll-based classes
        if (scrolled > 100) {
            document.body.classList.add('scrolled');
        } else {
            document.body.classList.remove('scrolled');
        }
    }

    /**
     * 🛠️ Utility Functions
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    debounce(func, wait, immediate) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            const later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            const callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new PerformanceOptimizer();
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PerformanceOptimizer;
}
