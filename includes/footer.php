<footer class="mt-5 py-4" id="main-footer">
  <div class="container">
    <div class="row">
      <div class="col-12 text-center">
        <div class="social-links mb-3">
          <a href="https://instagram.com" target="_blank" class="social-link me-4" aria-label="Instagram">
            <i class="bi bi-instagram"></i>
          </a>
          <a href="https://twitter.com" target="_blank" class="social-link me-4" aria-label="Twitter">
            <i class="bi bi-twitter"></i>
          </a>
          <a href="mailto:harsh.ramrakhiani@gmail.com" class="social-link me-4" aria-label="Email">
            <i class="bi bi-envelope-fill"></i>
          </a>
          <a href="tel:8595680232" class="social-link" aria-label="Phone">
            <i class="bi bi-telephone-fill"></i>
          </a>
        </div>
        
        <div class="footer-content">
          <p class="mb-2">
            <span class="text-gradient fw-bold">© <?= date("Y") ?> InsectaBase</span>
          </p>
          <p class="mb-0 text-muted">
            Built with ❤️ by <a href="mailto:harsh.ramrakhiani@gmail.com" class="text-decoration-none">Harsh Ramrakhiani</a>
          </p>
        </div>
      </div>
    </div>
  </div>
</footer>

<style>
  #main-footer {
    background: linear-gradient(135deg, #343a40 0%, #495057 100%);
    color: #ffffff;
    padding: 3rem 0 2rem;
    margin-top: 4rem;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
  }

  #main-footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, #2e7d32, #ffc107, #2196f3, #2e7d32);
    background-size: 200% 100%;
    animation: gradient-shift 3s ease-in-out infinite;
  }

  @keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
  }

  .social-links {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .social-link {
    color: #ffc107;
    font-size: 1.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: rgba(255, 193, 7, 0.1);
    border: 2px solid transparent;
  }

  .social-link:hover {
    color: #ffffff;
    background: rgba(255, 193, 7, 0.2);
    border-color: #ffc107;
    transform: translateY(-3px) scale(1.1);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
  }

  .footer-content {
    max-width: 600px;
    margin: 0 auto;
  }

  .footer-content p {
    font-size: 1rem;
    line-height: 1.6;
  }

  .text-gradient {
    background: linear-gradient(135deg, #2e7d32 0%, #ffc107 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 700;
  }

  #main-footer a {
    color: #ffc107;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-decoration: none;
  }

  #main-footer a:hover {
    color: #ffffff;
    transform: scale(1.05);
  }

  /* Responsive Design */
  @media (max-width: 768px) {
    #main-footer {
      padding: 2rem 0 1.5rem;
      margin-top: 3rem;
    }
    
    .social-links {
      gap: 0.75rem;
    }
    
    .social-link {
      width: 45px;
      height: 45px;
      font-size: 1.25rem;
    }
    
    .footer-content p {
      font-size: 0.9rem;
    }
  }

  @media (max-width: 576px) {
    #main-footer {
      padding: 1.5rem 0 1rem;
      margin-top: 2rem;
    }
    
    .social-links {
      gap: 0.5rem;
    }
    
    .social-link {
      width: 40px;
      height: 40px;
      font-size: 1.1rem;
    }
    
    .footer-content p {
      font-size: 0.85rem;
    }
  }

  /* Animation for footer elements */
  .social-link {
    animation: fadeInUp 0.8s ease-out;
    animation-fill-mode: both;
  }

  .social-link:nth-child(1) { animation-delay: 0.1s; }
  .social-link:nth-child(2) { animation-delay: 0.2s; }
  .social-link:nth-child(3) { animation-delay: 0.3s; }
  .social-link:nth-child(4) { animation-delay: 0.4s; }

  .footer-content {
    animation: fadeInUp 0.8s ease-out 0.5s both;
  }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>
