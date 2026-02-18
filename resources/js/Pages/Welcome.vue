<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { router } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'

// Define props
const props = defineProps({
    announcements: {
        type: Array,
        default: () => []
    },
    discussions: {
        type: Array,
        default: () => []
    }
})

// Hero carousel state
const currentSlide = ref(0);
const heroImages = [
  '/assets/HERO1.png',
  '/assets/HERO2.png',
  '/assets/HERO3.png'
];

let heroInterval;

// Scroll animation state
const visibleSections = ref(new Set());

// Helper function to truncate text
const truncateText = (text, maxLength = 150) => {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Contact form
const contactForm = useForm({
    first_name: '',
    last_name: '',
    email: '',
    message: ''
})

const submitContact = () => {
    if (!contactForm.first_name.trim() || !contactForm.last_name.trim() || !contactForm.email.trim() || !contactForm.message.trim()) {
        alert('Please fill in all required fields.')
        return
    }

    if (contactForm.message.trim().length < 10) {
        alert('Please enter a message with at least 10 characters.')
        return
    }

    contactForm.post(route('contact.submit'), {
        preserveScroll: true,
        onSuccess: () => {
            alert('Thank you for your message! We will get back to you as soon as possible.')
            contactForm.reset()
        },
        onError: (errors) => {
            if (errors.message) {
                alert('Error: ' + errors.message)
            } else {
                alert('An error occurred while submitting your message. Please try again.')
            }
        }
    })
}

// Hero carousel functions
const advanceSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % heroImages.length;
};

const nextSlide = () => {
  advanceSlide();
  resetCarouselInterval();
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + heroImages.length) % heroImages.length;
  resetCarouselInterval();
};

const goToSlide = (index) => {
  currentSlide.value = index;
  resetCarouselInterval();
};

// Reset carousel interval to prevent fast changes
const resetCarouselInterval = () => {
  if (heroInterval) {
    clearInterval(heroInterval);
  }
  heroInterval = setInterval(() => {
    advanceSlide();
  }, 5000);
};


// Smooth scroll function
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

// Intersection Observer for scroll animations
const setupScrollAnimations = () => {
  // Use requestIdleCallback if available for better performance
  const setupObserver = () => {
    const observerOptions = {
      root: null,
      rootMargin: '50px',
      threshold: 0.05
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          requestAnimationFrame(() => {
            visibleSections.value.add(entry.target.id || entry.target.className);
            entry.target.classList.add('animate-in');
            // Unobserve after animation to improve performance
            observer.unobserve(entry.target);
          });
        }
      });
    }, observerOptions);

    // Observe all sections
    const sections = document.querySelectorAll('.animate-on-scroll');
    sections.forEach(section => observer.observe(section));

    return observer;
  };

  // Use requestIdleCallback for non-critical animations
  if ('requestIdleCallback' in window) {
    requestIdleCallback(setupObserver, { timeout: 2000 });
  } else {
    // Fallback for browsers without requestIdleCallback
    setTimeout(setupObserver, 0);
  }
};

onMounted(() => {
  // Start carousel immediately (non-blocking)
  heroInterval = setInterval(() => {
    advanceSlide();
  }, 5000);

  // Setup scroll animations asynchronously (non-blocking)
  // Use double requestAnimationFrame to ensure it runs after initial render
  requestAnimationFrame(() => {
    requestAnimationFrame(() => {
      setupScrollAnimations();
    });
  });
});

onUnmounted(() => {
  clearInterval(heroInterval);
});
</script>

<template>
  <Head title="IKONEK 176B - Welcome" />
  
  <div class="landing-page">
    <!-- Navigation Bar -->
    <nav class="navbar">
      <div class="nav-container">
        <div class="logo">
          <img src="/assets/LOGO.png" alt="IKONEK 176B Logo" class="logo-image">
        </div>
        <div class="nav-links">
          <a @click.prevent="scrollToSection('hero')" href="#hero" class="nav-link">HOME</a>
          <a @click.prevent="scrollToSection('about')" href="#about" class="nav-link">ABOUT</a>
          <a @click.prevent="scrollToSection('contact')" href="#contact" class="nav-link">CONTACT US</a>
          <Link :href="route('login')" class="login-btn">LOG IN</Link>
        </div>
      </div>
    </nav>

    <!-- Hero Section with Image Carousel -->
    <section id="hero" class="hero-section">
      <div class="carousel-wrapper">
        <div class="carousel-slides">
          <div 
            v-for="(image, index) in heroImages" 
            :key="index"
            class="carousel-slide"
            :class="{ active: currentSlide === index }"
          >
            <img :src="image" :alt="`Hero Image ${index + 1}`" class="hero-image">
          </div>
        </div>

        <button class="carousel-arrow carousel-arrow-left" @click="prevSlide" aria-label="Previous slide">
          <span class="arrow-icon"></span>
        </button>

        <button class="carousel-arrow carousel-arrow-right" @click="nextSlide" aria-label="Next slide">
          <span class="arrow-icon"></span>
        </button>

        <div class="carousel-dots">
          <button 
            v-for="(image, index) in heroImages" 
            :key="index"
            class="dot" 
            :class="{ active: currentSlide === index }"
            @click="goToSlide(index)"
            :aria-label="'Go to slide ' + (index + 1)"
          ></button>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section animate-on-scroll">
      <div class="about-container">
        <div class="about-image animate-on-scroll">
          <div class="image-wrapper">
            <img src="/assets/NEW-BG.jpg" alt="About IKONEK176B" class="about-img">
          </div>
        </div>
        <div class="about-content animate-on-scroll">
          <div class="section-badge">ABOUT US</div>
          <h2 class="about-title">
            <span class="about-highlight">iKonek176B</span>
          </h2>
          <p class="about-text">
            IKonek176B is a web-based platform designed to bring the community of Barangay 176B closer and make local governance more accessible. Through this website, users can connect and interact with barangay officials and fellow residents in real time, facilitating better communication and active participation in community matters. The platform allows residents to conveniently request essential documents and services without needing to visit the barangay hall in person. With these features, IKonek176B aims to make community engagement simpler, faster, and more efficient for everyone.
          </p>
          <div class="about-features">
            <div class="feature-item">
              <div class="feature-icon feature-icon-digital">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
              </div>
              <div class="feature-text">Digital Community</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon feature-icon-document">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
              </div>
              <div class="feature-text">Document Request</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon feature-icon-event">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
              </div>
              <div class="feature-text">Event Assistance</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Announcements Section -->
    <section class="announcements-section animate-on-scroll">
      <div class="section-content">
        <h2 class="section-title">
          Recent Announcements
        </h2>
        <div v-if="announcements && announcements.length > 0" class="posts-grid">
          <div 
            v-for="(post, index) in announcements.slice(0, 3)" 
            :key="post.id"
            class="post-card"
            :style="{ animationDelay: `${index * 0.1}s` }"
          >
            <div class="post-card-inner">
              <div v-if="post.images && post.images.length > 0" class="post-image-wrapper">
                <img :src="post.images[0]" :alt="post.header || 'Announcement'" class="post-image">
                <div class="post-overlay"></div>
              </div>
              <div v-else class="post-image-wrapper post-image-placeholder">
              </div>
              <div class="post-content">
                <div class="post-header-info">
                  <div class="post-author">
                    <img :src="post.avatar" :alt="post.author" class="author-avatar">
                    <div class="author-info">
                      <div class="author-name">{{ post.author }}</div>
                      <div class="author-role">{{ post.role }}</div>
                    </div>
                  </div>
                  <div class="post-date">{{ post.date }}</div>
                </div>
                <h3 class="post-title">{{ post.header || 'Announcement' }}</h3>
                <p class="post-text">{{ truncateText(post.content) }}</p>
                <div class="post-footer">
                  <div class="post-tags">
                    <span v-for="tag in post.tags.slice(0, 2)" :key="tag" class="post-tag" :class="tag.toLowerCase()">{{ tag }}</span>
                  </div>
                  <div class="post-stats">
                    <span class="stat-item">{{ post.likes }} Likes</span>
                    <span class="stat-item">{{ post.comments }} Comments</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="empty-state">
          <p>No announcements yet. Check back soon!</p>
        </div>
        <div v-if="announcements && announcements.length > 0" class="view-more-container">
          <Link :href="route('login')" class="view-more-btn">
            View All Announcements
          </Link>
        </div>
      </div>
    </section>

    <!-- Discussions Section -->
    <section class="discussions-section animate-on-scroll">
      <div class="section-content">
        <h2 class="section-title">
          Recent Discussions
        </h2>
        <div v-if="discussions && discussions.length > 0" class="posts-grid">
          <div 
            v-for="(post, index) in discussions.slice(0, 3)" 
            :key="post.id"
            class="post-card"
            :style="{ animationDelay: `${index * 0.1}s` }"
          >
            <div class="post-card-inner">
              <div v-if="post.images && post.images.length > 0" class="post-image-wrapper">
                <img :src="post.images[0]" :alt="post.header || 'Discussion'" class="post-image">
                <div class="post-overlay"></div>
              </div>
              <div v-else class="post-image-wrapper post-image-placeholder">
              </div>
              <div class="post-content">
                <div class="post-header-info">
                  <div class="post-author">
                    <img :src="post.avatar" :alt="post.author" class="author-avatar">
                    <div class="author-info">
                      <div class="author-name">{{ post.author }}</div>
                      <div class="author-role">{{ post.role }}</div>
                    </div>
                  </div>
                  <div class="post-date">{{ post.date }}</div>
                </div>
                <h3 class="post-title">{{ post.header || 'Discussion' }}</h3>
                <p class="post-text">{{ truncateText(post.content) }}</p>
                <div class="post-footer">
                  <div class="post-tags">
                    <span v-for="tag in post.tags.slice(0, 2)" :key="tag" class="post-tag" :class="tag.toLowerCase()">{{ tag }}</span>
                  </div>
                  <div class="post-stats">
                    <span class="stat-item">{{ post.likes }} Likes</span>
                    <span class="stat-item">{{ post.comments }} Comments</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else class="empty-state">
          <p>No discussions yet. Start a conversation!</p>
        </div>
        <div v-if="discussions && discussions.length > 0" class="view-more-container">
          <Link :href="route('login')" class="view-more-btn">
            View All Discussions
          </Link>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section animate-on-scroll">
      <div class="contact-container">
        <div class="contact-info animate-on-scroll">
          <div class="section-badge-light">CONTACT</div>
          <h2 class="contact-title">Let's get in touch!</h2>
          <p class="contact-subtitle">We'd like to hear from you!</p>
          <div class="contact-details">
            <div class="contact-item">
              <div class="contact-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                </svg>
              </div>
              <div class="contact-text">ikonek176b@dev.ph</div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                </svg>
              </div>
              <div class="contact-text">+639176222133</div>
            </div>
          </div>
        </div>
        <div class="contact-form animate-on-scroll">
          <div class="form-wrapper">
            <div class="form-row">
              <div class="form-group">
                <label>Last Name*</label>
                <input type="text" v-model="contactForm.last_name" placeholder="Enter your last name" />
                <div v-if="contactForm.errors.last_name" class="error-text">{{ contactForm.errors.last_name }}</div>
              </div>
              <div class="form-group">
                <label>First Name*</label>
                <input type="text" v-model="contactForm.first_name" placeholder="Enter your first name" />
                <div v-if="contactForm.errors.first_name" class="error-text">{{ contactForm.errors.first_name }}</div>
              </div>
            </div>
            <div class="form-group">
              <label>Email Address*</label>
              <input type="email" v-model="contactForm.email" placeholder="your.email@example.com" />
              <div v-if="contactForm.errors.email" class="error-text">{{ contactForm.errors.email }}</div>
            </div>
            <div class="form-group">
              <label>Message*</label>
              <textarea rows="4" v-model="contactForm.message" placeholder="Tell us how we can help you..."></textarea>
              <div v-if="contactForm.errors.message" class="error-text">{{ contactForm.errors.message }}</div>
            </div>
            <button type="button" class="submit-btn" @click="submitContact" :disabled="contactForm.processing">
              <span v-if="!contactForm.processing">SUBMIT</span>
              <span v-else>SUBMITTING...</span>
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  margin: 0;
  padding: 0;
  overflow-x: hidden;
  overflow-y: auto;
}

.landing-page {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  width: 100%;
  overflow-x: hidden;
  position: relative;
  /* Background applied without fixed attachment to prevent scrollbar issues */
  background: url('/assets/BG MAIN.png') no-repeat center center;
  background-size: cover;
  background-attachment: scroll;
  /* Prevent scrollbar from landing-page itself */
  min-height: 100vh;
}

/* Scroll Animation Classes */
/* Scroll Animation Classes - Start visible to prevent layout shift */
.animate-on-scroll {
  opacity: 1;
  transform: translateY(0);
  transition: opacity 0.4s ease-out, transform 0.4s ease-out;
}

/* Only hide if not yet animated in (for progressive enhancement) */
.animate-on-scroll:not(.animate-in):not(:first-child) {
  opacity: 0;
  transform: translateY(0);
}

.animate-on-scroll.animate-in {
  opacity: 1;
  transform: translateY(0);
}

/* Navigation Bar */
.navbar {
  background: rgba(46, 46, 46, 0.98);
  padding: 15px 0;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  transition: background 0.3s ease;
}

.nav-container {
  max-width: 1450px;
  margin: 0 auto;
  padding: 0 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.05);
}

.logo-image {
  height: 60px;
  width: auto;
  object-fit: contain;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 40px;
}

.nav-link {
  color: white;
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  transition: all 0.3s ease;
  cursor: pointer;
  position: relative;
  padding: 5px 0;
}

.nav-link::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 0;
  height: 2px;
  background-color: #ff8c42;
  transition: width 0.3s ease;
}

.nav-link:hover {
  color: #ff8c42;
}

.nav-link:hover::after {
  width: 100%;
}

.login-btn {
  background: #ff8c42;
  color: white;
  padding: 10px 30px;
  text-decoration: none;
  font-weight: 600;
  font-size: 15px;
  border: none;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: background 0.3s ease;
}

.login-btn:hover {
  background: #e6763a;
}

/* Hero Section with Carousel - Redesigned */
.hero-section {
  width: 100%;
  height: 100vh;
  position: relative;
  background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
  overflow: hidden;
  /* Strict containment to prevent scrollbars */
  contain: strict;
  isolation: isolate;
}

.carousel-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  /* Ensure no scrollbars */
  contain: layout style paint;
}

.carousel-slides {
  position: relative;
  width: 100%;
  height: 100%;
  overflow: hidden;
  /* Prevent any overflow */
  contain: strict;
}

.carousel-slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 0.8s ease-in-out;
  overflow: hidden;
  /* Contain each slide */
  contain: strict;
  pointer-events: none;
}

.carousel-slide.active {
  opacity: 1;
  pointer-events: auto;
  z-index: 1;
}

.hero-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  filter: brightness(0.9);
  display: block;
  /* Prevent image from causing overflow */
  max-width: 100%;
  max-height: 100%;
}

.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: rgba(255, 255, 255, 0.15);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  width: 50px;
  height: 50px;
  cursor: pointer;
  z-index: 20;
  transition: background 0.3s ease, border-color 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  margin: 0;
  /* Prevent overflow */
  contain: layout style paint;
}

.carousel-arrow:hover {
  background: rgba(255, 255, 255, 0.25);
  border-color: rgba(255, 255, 255, 0.5);
}

.carousel-arrow:active {
  transform: translateY(-50%) scale(0.95);
}

.arrow-icon {
  width: 8px;
  height: 8px;
  border-top: 2px solid white;
  border-right: 2px solid white;
  display: block;
}

.carousel-arrow-left .arrow-icon {
  transform: rotate(-135deg);
}

.carousel-arrow-right .arrow-icon {
  transform: rotate(45deg);
}

.carousel-arrow-left {
  left: 30px;
}

.carousel-arrow-right {
  right: 30px;
}

.carousel-dots {
  position: absolute;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 20;
  display: flex;
  gap: 12px;
  align-items: center;
  justify-content: center;
  /* Prevent overflow */
  contain: layout style paint;
}

.dot {
  display: block;
  width: 12px;
  height: 12px;
  background-color: rgba(255, 255, 255, 0.4);
  border-radius: 50%;
  border: 2px solid transparent;
  cursor: pointer;
  transition: all 0.3s ease;
  padding: 0;
  margin: 0;
  /* Prevent overflow */
  flex-shrink: 0;
}

.dot.active {
  background-color: #ff8c42;
  width: 32px;
  border-radius: 6px;
  border-color: rgba(255, 255, 255, 0.3);
}

.dot:hover {
  background-color: rgba(255, 140, 66, 0.7);
}

.dot:active {
  transform: scale(0.9);
}

/* Announcements Section */
.announcements-section {
  background: transparent;
  padding: 60px 20px;
  position: relative;
  overflow: visible;
  /* Ensure proper containment */
  contain: layout style;
}

.announcements-section::before {
  content: '';
  position: absolute;
  top: -50%;
  right: -10%;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(255, 140, 66, 0.08) 0%, transparent 70%);
  border-radius: 50%;
  z-index: 0;
}

.announcements-section::after {
  content: '';
  position: absolute;
  bottom: -30%;
  left: -5%;
  width: 500px;
  height: 500px;
  background: radial-gradient(circle, rgba(255, 140, 66, 0.06) 0%, transparent 70%);
  border-radius: 50%;
  z-index: 0;
}

.section-content {
  position: relative;
  z-index: 1;
  max-width: 1400px;
  margin: 0 auto;
  overflow: hidden;
}

.section-title {
  text-align: center;
  font-size: 42px;
  color: #1a1a1a;
  margin-bottom: 60px;
  line-height: 1.2;
  font-weight: 600;
  letter-spacing: -0.5px;
  position: relative;
  z-index: 1;
}

.announcements-section .section-title {
  color: #1a1a1a;
}

.announcements-section .section-title::after {
  content: '';
  position: absolute;
  bottom: -15px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, transparent, #ff8c42, transparent);
  border-radius: 2px;
}

.discussions-section .section-title {
  color: #1a1a1a;
}

.discussions-section .section-title::after {
  content: '';
  position: absolute;
  bottom: -15px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, transparent, #239640, transparent);
  border-radius: 2px;
}

/* Posts Grid */
.posts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
  gap: 30px;
  margin-top: 40px;
  overflow: visible;
}

.post-card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border: 1px solid rgba(240, 240, 240, 0.8);
  transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
  opacity: 0;
  transform: translate3d(0, 30px, 0);
  animation: fadeInUp 0.6s ease-out forwards;
  position: relative;
  z-index: 1;
  will-change: transform;
  /* Prevent overflow during animation */
  margin-bottom: 30px;
}

.announcements-section .post-card {
  border-top: 3px solid transparent;
  background: rgba(255, 255, 255, 0.95);
}

.announcements-section .post-card:hover {
  border-top-color: #ff8c42;
  box-shadow: 0 4px 16px rgba(255, 140, 66, 0.15);
}

.discussions-section .post-card {
  border-top: 3px solid transparent;
  background: rgba(255, 255, 255, 0.95);
}

.discussions-section .post-card:hover {
  border-top-color: #239640;
  box-shadow: 0 4px 16px rgba(35, 150, 64, 0.15);
}

@keyframes fadeInUp {
  to {
    opacity: 1;
    transform: translate3d(0, 0, 0);
    will-change: auto;
  }
}


.post-card-inner {
  display: flex;
  flex-direction: column;
  height: 100%;
  overflow: hidden;
}

.post-image-wrapper {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
  background: linear-gradient(135deg, #f5f5f5, #e0e0e0);
}

.post-image-placeholder {
  background: #f5f5f5;
}

.post-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.5s ease;
}

.post-card:hover .post-image {
  transform: scale3d(1.1, 1.1, 1);
}

.post-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.3));
}

.post-content {
  padding: 25px;
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.post-header-info {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 15px;
}

.post-author {
  display: flex;
  align-items: center;
  gap: 12px;
}

.author-avatar {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #f0f0f0;
}

.author-info {
  display: flex;
  flex-direction: column;
}

.author-name {
  font-weight: 500;
  font-size: 14px;
  color: #1a1a1a;
}

.author-role {
  font-size: 12px;
  color: #666;
}

.post-date {
  font-size: 12px;
  color: #999;
  white-space: nowrap;
}

.post-title {
  font-size: 18px;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 12px;
  line-height: 1.4;
}

.post-text {
  font-size: 15px;
  color: #555;
  line-height: 1.6;
  margin-bottom: 20px;
  flex: 1;
}

.post-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 15px;
  border-top: 1px solid #f0f0f0;
}

.post-tags {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
  align-items: center;
}

/* Category indicator — dark outline, light fill, dark text, not interactive */
.post-tag {
  font-size: 12px;
  padding: 6px 14px;
  border-radius: 999px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  cursor: default;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  border: 2px solid #5a6a6b;
  background: #e8eaeb;
  color: #3d4849;
}

.post-tag.business { border-color: #5f3dc4; background: #ede9fc; color: #5f3dc4; }
.post-tag.education { border-color: #2980b9; background: #e3f2fd; color: #2980b9; }
.post-tag.emergency { border-color: #c0392b; background: #ffebee; color: #c0392b; }
.post-tag.employment { border-color: #1e7b4a; background: #e8f5e9; color: #1e7b4a; }
.post-tag.environment { border-color: #27ae60; background: #e8f5e9; color: #27ae60; }
.post-tag.governance { border-color: #8e44ad; background: #f3e5f5; color: #8e44ad; }
.post-tag.health { border-color: #c2185b; background: #fce4ec; color: #c2185b; }
.post-tag.incident { border-color: #a93226; background: #ffebee; color: #a93226; }
.post-tag.infrastructure { border-color: #e67e22; background: #fff3e0; color: #e67e22; }
.post-tag.inquiries { border-color: #b8860b; background: #fff8e1; color: #b8860b; }
.post-tag.livelihood { border-color: #16a085; background: #e0f2f1; color: #16a085; }
.post-tag.maintenance { border-color: #ba4a00; background: #fbe9e7; color: #ba4a00; }
.post-tag.sanitation { border-color: #0097a7; background: #e0f7fa; color: #0097a7; }
.post-tag.sports { border-color: #388e3c; background: #e8f5e9; color: #388e3c; }
.post-tag.traffic { border-color: #e65100; background: #fff3e0; color: #e65100; }
.post-tag.weather { border-color: #0288d1; background: #e1f5fe; color: #0288d1; }
.post-tag.welfare { border-color: #c2185b; background: #fce4ec; color: #c2185b; }
.post-tag.youth { border-color: #ad1457; background: #fce4ec; color: #ad1457; }

.post-stats {
  display: flex;
  gap: 15px;
}

.stat-item {
  font-size: 13px;
  color: #999;
  font-weight: 400;
  margin-left: 15px;
}

.stat-item:first-child {
  margin-left: 0;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #999;
  font-size: 16px;
}

/* Discussions Section */
.discussions-section {
  background: transparent;
  padding: 60px 20px 120px 20px;
  position: relative;
  border-top: 1px solid rgba(240, 240, 240, 0.3);
  overflow: visible;
  /* Ensure proper containment */
  contain: layout style;
}

.discussions-section::before {
  content: '';
  position: absolute;
  top: -40%;
  left: -8%;
  width: 550px;
  height: 550px;
  background: radial-gradient(circle, rgba(35, 150, 64, 0.08) 0%, transparent 70%);
  border-radius: 50%;
  z-index: 0;
}

.discussions-section::after {
  content: '';
  position: absolute;
  bottom: -35%;
  right: -8%;
  width: 600px;
  height: 600px;
  background: radial-gradient(circle, rgba(35, 150, 64, 0.06) 0%, transparent 70%);
  border-radius: 50%;
  z-index: 0;
}

.view-more-container {
  text-align: center;
  margin-top: 50px;
}

.view-more-btn {
  display: inline-block;
  padding: 12px 30px;
  border-radius: 4px;
  text-decoration: none;
  font-weight: 600;
  font-size: 15px;
  transition: all 0.3s ease;
  border: none;
  position: relative;
  z-index: 1;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.announcements-section .view-more-btn {
  background: #ff8c42;
  color: white;
}

.announcements-section .view-more-btn:hover {
  background: #e6763a;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(255, 140, 66, 0.3);
}

.discussions-section .view-more-btn {
  background: #239640;
  color: white;
}

.discussions-section .view-more-btn:hover {
  background: #1a7a2e;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(35, 150, 64, 0.3);
}

/* About Section */
.about-section {
  padding: 120px 20px 60px 20px;
  background: transparent;
  position: relative;
  overflow: visible;
  /* Ensure proper containment */
  contain: layout style;
}

.about-container {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 80px;
  overflow: hidden;
}

.about-image {
  flex: 1;
}

.image-wrapper {
  position: relative;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0,0,0,0.15);
  transition: transform 0.5s ease;
}

.image-wrapper:hover {
  transform: translate3d(0, -10px, 0);
}

.about-img {
  width: 100%;
  height: auto;
  display: block;
  transition: transform 0.5s ease;
}

.image-wrapper:hover .about-img {
  transform: scale3d(1.05, 1.05, 1);
}

.about-content {
  flex: 1;
}

.section-badge {
  display: inline-block;
  background: linear-gradient(135deg, #239640, #1a7a2e);
  color: white;
  padding: 8px 20px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 1px;
  margin-bottom: 20px;
  text-transform: uppercase;
}

.about-title {
  font-size: 42px;
  color: #239640;
  font-weight: 700;
  margin-bottom: 30px;
  line-height: 1.2;
  letter-spacing: -1px;
}

.about-highlight {
  font-size: 72px;
  font-weight: 800;
  color: #ff8c42;
  display: block;
  margin-top: 10px;
  background: linear-gradient(135deg, #ff8c42, #e55a2b);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.about-text {
  color: #4a4a4a;
  line-height: 1.9;
  font-size: 18px;
  text-align: justify;
  margin-bottom: 40px;
}

.about-features {
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
}

.feature-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.08);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  flex: 1;
  min-width: 200px;
}

.feature-item:hover {
  transform: translate3d(0, -5px, 0);
  box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.feature-icon {
  width: 32px;
  height: 32px;
  color: #1a1a1a;
  flex-shrink: 0;
}

.feature-icon svg {
  width: 100%;
  height: 100%;
}

.feature-icon-digital {
  color: #ff8c42;
}

.feature-icon-document {
  color: #239640;
}

.feature-icon-event {
  color: #3498db;
}

.feature-text {
  font-size: 16px;
  font-weight: 600;
  color: #2d2d2d;
}

/* Contact Section */
.contact-section {
  background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
  padding: 120px 20px;
  position: relative;
  overflow: visible;
  /* Ensure proper containment */
  contain: layout style;
}

.contact-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: radial-gradient(circle at 20% 50%, rgba(255, 140, 66, 0.1) 0%, transparent 50%),
              radial-gradient(circle at 80% 80%, rgba(35, 150, 64, 0.1) 0%, transparent 50%);
  pointer-events: none;
}

.contact-container {
  max-width: 1400px;
  margin: 0 auto;
  display: flex;
  gap: 80px;
  position: relative;
  z-index: 1;
  overflow: hidden;
}

.contact-info {
  flex: 1;
  color: white;
  overflow: hidden;
}

.section-badge-light {
  display: inline-block;
  background: rgba(255, 140, 66, 0.2);
  color: #ff8c42;
  padding: 8px 20px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
  letter-spacing: 1px;
  margin-bottom: 20px;
  text-transform: uppercase;
  border: 1px solid rgba(255, 140, 66, 0.3);
}

.contact-title {
  font-size: 56px;
  font-weight: 700;
  margin-bottom: 20px;
  line-height: 1.2;
  letter-spacing: -1px;
  background: linear-gradient(135deg, #ffffff, #e0e0e0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.contact-subtitle {
  font-size: 20px;
  margin-bottom: 40px;
  opacity: 0.85;
  color: #e0e0e0;
}

.contact-details {
  display: flex;
  flex-direction: column;
  gap: 25px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 20px;
  padding: 20px;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  transition: all 0.3s ease;
}

.contact-item:hover {
  background: rgba(255, 255, 255, 0.08);
  transform: translate3d(10px, 0, 0);
  border-color: rgba(255, 140, 66, 0.3);
}

.contact-icon {
  width: 24px;
  height: 24px;
  color: #e0e0e0;
  flex-shrink: 0;
}

.contact-icon svg {
  width: 100%;
  height: 100%;
}

.contact-text {
  font-size: 18px;
  color: #e0e0e0;
  font-weight: 500;
}

.contact-form {
  flex: 1;
  overflow: hidden;
}

.form-wrapper {
  width: 100%;
  background: rgba(255, 255, 255, 0.08);
  padding: 40px;
  border-radius: 24px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  overflow: hidden;
}

.form-row {
  display: flex;
  gap: 20px;
}

.form-group {
  margin-bottom: 25px;
  flex: 1;
}

.form-group label {
  display: block;
  color: #e0e0e0;
  margin-bottom: 10px;
  font-size: 14px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 16px 20px;
  border: 2px solid rgba(255, 255, 255, 0.1);
  background: rgba(255, 255, 255, 0.05);
  color: white;
  border-radius: 12px;
  font-size: 15px;
  transition: all 0.3s ease;
  font-family: inherit;
}

.form-group input::placeholder,
.form-group textarea::placeholder {
  color: rgba(255, 255, 255, 0.4);
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #ff8c42;
  background: rgba(255, 255, 255, 0.08);
  box-shadow: 0 0 0 4px rgba(255, 140, 66, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

.submit-btn {
  background: #ff8c42;
  color: white;
  padding: 14px;
  border: none;
  font-weight: 600;
  cursor: pointer;
  font-size: 18px;
  transition: background 0.3s ease;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  width: 100%;
}

.submit-btn:hover:not(:disabled) {
  background: #e6763a;
}

.submit-btn:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.error-text {
  color: #ff6b6b;
  font-size: 13px;
  margin-top: 8px;
  font-weight: 500;
}
/* Responsive Design */
@media (max-width: 1024px) {
  .about-container,
  .contact-container {
    gap: 50px;
  }
  
  .about-highlight {
    font-size: 56px;
  }
  
  .contact-title {
    font-size: 42px;
  }
}

@media (max-width: 768px) {
  .nav-container {
    padding: 0 20px;
  }
  
  .nav-links {
    gap: 20px;
  }
  
  .nav-link {
    font-size: 13px;
  }
  
  .login-btn {
    padding: 8px 20px;
    font-size: 13px;
  }
  
  .carousel-arrow {
    width: 50px;
    height: 50px;
    font-size: 24px;
  }
  
  .carousel-arrow-left {
    left: 15px;
  }
  
  .carousel-arrow-right {
    right: 15px;
  }
  
  .section-title,
  .section-title-green {
    font-size: 36px;
  }
  
  .title-highlight,
  .title-highlight-white {
    font-size: 42px;
  }
  
  .posts-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .post-card {
    margin-bottom: 20px;
  }
  
  .announcements-section,
  .discussions-section {
    padding: 80px 20px;
  }
  
  .about-section {
    padding: 80px 20px;
  }
  
  .about-container {
    flex-direction: column;
    gap: 40px;
  }
  
  .about-title {
    font-size: 32px;
  }
  
  .about-highlight {
    font-size: 48px;
  }
  
  .about-text {
    font-size: 16px;
  }
  
  .about-features {
    flex-direction: column;
  }
  
  .contact-section {
    padding: 80px 20px;
  }
  
  .contact-container {
    flex-direction: column;
    gap: 50px;
  }
  
  .contact-title {
    font-size: 36px;
  }
  
  .form-wrapper {
    padding: 30px 20px;
  }
  
  .form-row {
    flex-direction: column;
  }
  
  .announcements-section,
  .discussions-section {
    padding: 60px 20px;
  }
}
</style>
<style>
body {
  margin: 0;
  padding: 0;
  overflow-x: hidden;
  overflow-y: auto;
}
</style>