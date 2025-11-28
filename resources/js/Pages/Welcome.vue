<script setup>
import { Link } from '@inertiajs/vue3'
import { Head } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3'

// Hero carousel state
const currentSlide = ref(0);
const heroImages = [
  '/assets/HERO1.png',
  '/assets/HERO2.png',
  '/assets/HERO3.png'
];

// Announcements carousel state
const currentAnnouncement = ref(0);
const announcementImages = [
  '/assets/AN1.png',
  '/assets/AN3.png',
  '/assets/AN1.png'
];

// Discussions carousel state
const currentDiscussion = ref(0);
const discussionImages = [
  '/assets/DI1.png',
  '/assets/DI2.png',
  '/assets/DI1.png'
];

let heroInterval;
let announcementInterval;
let discussionInterval;

// Hero carousel functions
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % heroImages.length;
};

const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + heroImages.length) % heroImages.length;
};

const goToSlide = (index) => {
  currentSlide.value = index;
};

// Announcement carousel functions
const nextAnnouncement = () => {
  currentAnnouncement.value = (currentAnnouncement.value + 1) % announcementImages.length;
};

const prevAnnouncement = () => {
  currentAnnouncement.value = (currentAnnouncement.value - 1 + announcementImages.length) % announcementImages.length;
};

// Discussion carousel functions
const nextDiscussion = () => {
  currentDiscussion.value = (currentDiscussion.value + 1) % discussionImages.length;
};

const prevDiscussion = () => {
  currentDiscussion.value = (currentDiscussion.value - 1 + discussionImages.length) % discussionImages.length;
};

// Smooth scroll function
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

onMounted(() => {
  // Auto-advance hero carousel every 2 seconds
  heroInterval = setInterval(() => {
    nextSlide();
  }, 4000);

  // Auto-advance announcements every 3 seconds
  announcementInterval = setInterval(() => {
    nextAnnouncement();
  }, 4000);

  // Auto-advance discussions every 3 seconds
  discussionInterval = setInterval(() => {
    nextDiscussion();
  }, 4000);
});

onUnmounted(() => {
  clearInterval(heroInterval);
  clearInterval(announcementInterval);
  clearInterval(discussionInterval);
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
      <div class="carousel-container">
        <button class="carousel-arrow carousel-arrow-left" @click="prevSlide">
          &#8249;
        </button>
        
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

        <button class="carousel-arrow carousel-arrow-right" @click="nextSlide">
          &#8250;
        </button>

        <div class="carousel-dots">
          <span 
            v-for="(image, index) in heroImages" 
            :key="index"
            class="dot" 
            :class="{ active: currentSlide === index }"
            @click="goToSlide(index)"
          ></span>
        </div>
      </div>
    </section>

    <!-- Announcements Section -->
    <section class="announcements-section">
      <h2 class="section-title">
        Recent<br>
        <span class="title-highlight">Announcements</span>
      </h2>
      <div class="carousel-container-small">
        <button class="carousel-arrow-small carousel-arrow-left" @click="prevAnnouncement">
          &#8249;
        </button>
        
        <div class="carousel-slides-small">
          <div 
            v-for="(image, index) in announcementImages" 
            :key="index"
            class="carousel-slide-small"
            :class="{ active: currentAnnouncement === index }"
          >
            <img :src="image" :alt="`Announcement ${index + 1}`" class="announcement-image">
          </div>
        </div>

        <button class="carousel-arrow-small carousel-arrow-right" @click="nextAnnouncement">
          &#8250;
        </button>
      </div>
    </section>

    <!-- Discussions Section -->
    <section class="discussions-section">
      <h2 class="section-title-green">
        Recent<br>
        <span class="title-highlight-white">Discussions</span>
      </h2>
      <div class="carousel-container-small">
        <button class="carousel-arrow-small carousel-arrow-left" @click="prevDiscussion">
          &#8249;
        </button>
        
        <div class="carousel-slides-small">
          <div 
            v-for="(image, index) in discussionImages" 
            :key="index"
            class="carousel-slide-small"
            :class="{ active: currentDiscussion === index }"
          >
            <img :src="image" :alt="`Discussion ${index + 1}`" class="discussion-image">
          </div>
        </div>

        <button class="carousel-arrow-small carousel-arrow-right" @click="nextDiscussion">
          &#8250;
        </button>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
      <div class="about-container">
        <div class="about-image">
          <img src="/assets/ABOUT.png" alt="About IKONEK176B" class="about-img">
        </div>
        <div class="about-content">
          <h2 class="about-title">
            ABOUT<br>
            <span class="about-highlight">iKonek176B</span>
          </h2>
          <p class="about-text">
            IKonek176B is a web-based platform designed to bring the community of Barangay 176B closer and make local governance more accessible. Through this website, users can connect and interact with barangay officials and fellow residents in real time, facilitating better communication and active participation in community matters. The platform allows residents to conveniently request essential documents and services without needing to visit the barangay hall in person. With these features, IKonek176B aims to make community engagement simpler, faster, and more efficient for everyone.
          </p>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact-section">
      <div class="contact-container">
        <div class="contact-info">
          <h2 class="contact-title">Let's get in touch!</h2>
          <p class="contact-subtitle">We'd like to hear from you!</p>
          <div class="contact-details">
            <p>📧 ikonek176b@dev.ph</p>
            <p>📞 ikonek176B Official</p>
          </div>
        </div>
        <div class="contact-form">
          <div class="form-wrapper">
            <div class="form-row">
              <div class="form-group">
                <label>Last Name*</label>
                <input type="text" />
              </div>
              <div class="form-group">
                <label>First Name*</label>
                <input type="text" />
              </div>
            </div>
            <div class="form-group">
              <label>Email Address*</label>
              <input type="email" />
            </div>
            <div class="form-group">
              <label>Message*</label>
              <textarea rows="4"></textarea>
            </div>
            <button type="button" class="submit-btn">SUBMIT</button>
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

.landing-page {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  overflow-x: hidden;
}

/* Navigation Bar */
.navbar {
  background: linear-gradient(135deg, #2e2e2e, #2e2e2e);
  padding: 15px 0;
  position: fixed;
  top: 0;
  width: 100%;
  z-index: 1000;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.nav-container {
  max-width: 1450px;
  margin: 0 auto;
  padding: 0 10px;          /* increased left-right spacing */
  display: flex;
  justify-content: space-between;  /* pushes logo & nav apart */
  align-items: center;
}

.logo {
  display: flex;
  align-items: center;
}

.logo-image {
  height: 60px;
  width: auto;
  object-fit: contain;
}

.nav-links {
  display: flex;
  align-items: center;
  gap: 30px;
}

.nav-link {
  color: white;
  text-decoration: none;
  font-size: 15px;
  font-weight: 500;
  transition: color 0.3s;
  cursor: pointer;
}

.nav-link:hover {
  color: #ff8c42;
}

.login-btn {
  background-color: #ff8c42;
  color: white;
  padding: 8px 25px;
  text-decoration: none;
  font-weight: bold;
  font-size: 15px;
  transition: background-color 0.3s;
}

.login-btn:hover {
  background-color: #e55a2b;
}

/* Hero Section with Carousel */
.hero-section {
  height: 100vh;           /* full screen hero */
  width: 100%;
  padding-top: 0;
  position: relative;
  background-color: #f5f5f5;
  overflow: hidden;
}

.carousel-container {
  position: relative;
  width: 100%;
  height: 100%;            /* fill hero fully */
  overflow: hidden;
}

.carousel-slides {
  position: relative;
  width: 100%;
  height: 100%;
}

.carousel-slide {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 0.8s ease-in-out;
}

.carousel-slide.active {
  opacity: 1;
}

.hero-image {
  width: 100%;
  height: 100%;
  object-fit: cover;        /* keeps images clean */
}


.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  color: white;
  border: none;
  font-size: 48px;
  padding: 10px 20px;
  cursor: pointer;
  z-index: 10;
  transition: background-color 0.3s;
}

.carousel-arrow:hover {
  color: #ff8c42;
}

.carousel-arrow-left {
  left: 20px;
}

.carousel-arrow-right {
  right: 20px;
}

.carousel-dots {
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  text-align: center;
  z-index: 10;
}

.dot {
  display: inline-block;
  width: 12px;
  height: 12px;
  background-color: rgba(255, 255, 255, 0.5);
  border-radius: 50%;
  margin: 0 5px;
  cursor: pointer;
  transition: background-color 0.3s;
}

.dot.active {
  background-color: white;
}

.dot:hover {
  background-color: rgba(255, 255, 255, 0.8);
}

/* Announcements Section */
.announcements-section {
  background-color: #ff8c42;
  padding: 60px 20px;
}

.section-title {
  text-align: center;
  font-size: 36px;
  color: white;
  margin-bottom: 30px;
  line-height: 1.2;
}

.title-highlight {
  font-size: 42px;
  font-weight: bold;
}

.carousel-container-small {
  position: relative;
  max-width: 1550px;   /* wider */
  margin: 0 auto;
  height: 260px;        /* taller */
  overflow: hidden;
}

.carousel-slides-small {
  position: relative;
  width: 100%;
  height: 100%;
}

.carousel-slide-small {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: opacity 0.8s ease-in-out;
}

.carousel-slide-small.active {
  opacity: 1;
}

.announcement-image,
.discussion-image {
  width: 100%;
  height: 100%;
  object-fit: cover;      /* keeps it clean */
  border-radius: 20px;    /* optional bigger rounding */
}
.carousel-arrow-small {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  color: white;
  border: none;
  font-size: 36px;
  padding: 8px 16px;
  cursor: pointer;
  z-index: 10;
  border-radius: 50%;
  transition: background-color 0.3s;
}

.carousel-arrow-small.carousel-arrow-left {
  left: 10px;
}

.carousel-arrow-small.carousel-arrow-right {
  right: 10px;
}

/* Discussions Section */
.discussions-section {
  background-color: #239640;
  padding: 60px 20px;
}

.section-title-green {
  text-align: center;
  font-size: 36px;
  color: white;
  margin-bottom: 30px;
  line-height: 1.2;
}

.title-highlight-white {
  font-size: 42px;
  font-weight: bold;
  color: white;
}

/* About Section */
.about-section {
  padding: 80px 20px;
  background-color: #ffffff;
}

.about-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  gap: 50px;
}

.about-image {
  flex: 1;
}

.about-img {
  width: 100%;
  height: auto;
  border-radius: 15px;
}

.about-content {
  flex: 1;
}

.about-title {
  font-size: 30px;
  color: #239640;
  font-weight: bold;
  margin-bottom: 50px;
  line-height: 1.0;
}

.about-highlight {
  font-size: 95px;
  font-weight: bold;
  color: #ff8c42;
}

.about-text {
  color: #1f1f1f;
  line-height: 1.8;
  font-size: 20px;
  text-align: justify;
}

/* Contact Section */
.contact-section {
  background-color: #2d2d2d;
  padding: 80px 0px;
}

.contact-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  gap: 60px;
}

.contact-info {
  flex: 1;
  color: white;
}

.contact-title {
  font-size: 50px;
  font-weight: bold;
  margin-bottom: 15px;
}

.contact-subtitle {
  font-size: 25px;
  margin-bottom: 30px;
  opacity: 0.9;
}

.contact-details {
  font-size: 20px;
  line-height: 2;
}

.contact-form {
  flex: 1;
}

.form-wrapper {
  width: 100%;
}

.form-row {
  display: flex;
  gap: 20px;
}

.form-group {
  margin-bottom: 20px;
  flex: 1;
}

.form-group label {
  display: block;
  color: white;
  margin-bottom: 8px;
  font-size: 15px;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 12px;
  border: 2px solid #555;
  background-color: #3d3d3d;
  color: white;
  border-radius: 5px;
  font-size: 14px;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #7cb342;
}

.submit-btn {
  background-color: #ff8c42;
  color: white;
  padding: 12px 40px;
  border: none;
  font-weight: bold;
  cursor: pointer;
  font-size: 16px;
  transition: background-color 0.3s;
}
/* Responsive Design */
@media (max-width: 768px) {
  .nav-links {
    gap: 15px;
  }
  
  .nav-link {
    font-size: 12px;
  }
  
  .carousel-container {
    height: 400px;
  }
  
  .carousel-arrow {
    font-size: 32px;
    padding: 5px 15px;
  }
  
  .carousel-arrow-left {
    left: 10px;
  }
  
  .carousel-arrow-right {
    right: 10px;
  }
  
  .about-container,
  .contact-container {
    flex-direction: column;
  }
  
  .form-row {
    flex-direction: column;
  }
  
  .carousel-container-small {
    height: 250px;
  }
}
</style>
<style>
body {
  margin: 0;
  padding: 0;
  background: #2d2d2d;
}
</style>