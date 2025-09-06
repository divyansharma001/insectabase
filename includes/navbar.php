<nav class="navbar navbar-expand-lg custom-navbar shadow-sm">
  <div class="container-fluid">
    <!-- Logo + Title -->
    <a class="navbar-brand d-flex align-items-center" href="index.php">
      <img src="assets/img/logo.jpg" alt="Logo" height="45" class="me-3">
      <span class="text-white fw-bold h5 mb-0">InsectaBase</span>
    </a>

    <!-- Toggle for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Menu Items -->
    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
      <ul class="navbar-nav d-flex flex-row flex-wrap text-center">
        <?php
          $currentPage = basename($_SERVER['PHP_SELF']);
          function isActive($page) {
            global $currentPage;
            return strpos($currentPage, $page) !== false ? 'active' : '';
          }
        ?>

        <li class="nav-item">
          <a class="nav-link <?= isActive('index.php') ?>" href="index.php">
            <i class="bi bi-house-door-fill me-2"></i> Home
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('about.php') ?>" href="about.php">
            <i class="bi bi-info-circle-fill me-2"></i> About
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('morphology.php') ?>" href="morphology.php">
            <i class="bi bi-diagram-3 me-2"></i> Morphology
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('factsheet.php') ?>" href="factsheet.php">
            <i class="bi bi-file-earmark-text-fill me-2"></i> Fact Sheet
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('checklist.php') ?>" href="checklist.php">
            <i class="bi bi-list-check me-2"></i> Checklist
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('literature.php') ?>" href="literature.php">
            <i class="bi bi-journal-richtext me-2"></i> Literature
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('credits.php') ?>" href="credits.php">
            <i class="bi bi-stars me-2"></i> Credits
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('contact.php') ?>" href="contact.php">
            <i class="bi bi-envelope-fill me-2"></i> Contact
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('stats.php') ?>" href="stats.php">
            <i class="bi bi-graph-up me-2"></i> Stats
          </a>
        </li>

        <li class="nav-item">
          <a class="nav-link <?= isActive('login.php') ?>" href="admin/login.php">
            <i class="bi bi-person-circle me-2"></i> Admin
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
  .custom-navbar {
    background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
    padding: 1.25rem 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    position: sticky;
    top: 0;
    z-index: 1000;
    backdrop-filter: blur(10px);
    border-bottom: 3px solid rgba(255, 255, 255, 0.1);
  }

  .navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    color: #ffffff !important;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }

  .navbar-brand:hover {
    transform: scale(1.05);
    color: #ffc107 !important;
  }

  .navbar-brand img {
    height: 50px;
    width: 50px;
    border-radius: 12px;
    object-fit: cover;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
  }

  .navbar-brand:hover img {
    border-color: #ffc107;
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
  }

  .navbar-nav {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
  }

  .navbar-nav .nav-link {
    color: #ffffff !important;
    font-weight: 500;
    font-size: 0.95rem;
    padding: 0.875rem 1.25rem !important;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    margin: 0;
    white-space: nowrap;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    border: 2px solid transparent;
  }

  .navbar-nav .nav-link i {
    font-size: 1rem;
    margin-right: 0.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .navbar-nav .nav-link:hover {
    color: #ffc107 !important;
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateY(-3px);
    border-color: rgba(255, 193, 7, 0.3);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
  }

  .navbar-nav .nav-link:hover i {
    transform: scale(1.1);
  }

  .navbar-nav .nav-link.active {
    color: #ffc107 !important;
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%);
    border-color: rgba(255, 193, 7, 0.4);
    box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
    font-weight: 600;
  }

  .navbar-nav .nav-link.active i {
    color: #ffc107;
  }


  /* Mobile Responsiveness */
  @media (max-width: 991.98px) {
    .navbar-nav {
      text-align: center;
      padding: 1rem 0;
      gap: 0.75rem;
      flex-direction: column;
      align-items: stretch;
    }
    
    .navbar-nav .nav-link {
      margin: 0.25rem 0;
      padding: 1rem 1.5rem !important;
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.05);
      border: 2px solid rgba(255, 255, 255, 0.1);
      justify-content: flex-start;
    }
    
    .navbar-nav .nav-link:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 193, 7, 0.3);
      transform: translateX(5px);
    }
    
    .navbar-nav .nav-link.active {
      background: linear-gradient(135deg, rgba(255, 193, 7, 0.2) 0%, rgba(255, 193, 7, 0.1) 100%);
      border-color: rgba(255, 193, 7, 0.4);
    }
  }

  @media (max-width: 768px) {
    .custom-navbar {
      padding: 1rem 0;
    }
    
    .navbar-brand {
      font-size: 1.3rem;
    }
    
    .navbar-brand img {
      height: 40px;
      width: 40px;
    }
    
    .navbar-nav .nav-link {
      font-size: 0.9rem;
      padding: 0.875rem 1.25rem !important;
    }
  }

  @media (max-width: 576px) {
    .custom-navbar {
      padding: 0.75rem 0;
    }
    
    .navbar-brand {
      font-size: 1.2rem;
      gap: 0.5rem;
    }
    
    .navbar-brand img {
      height: 35px;
      width: 35px;
    }
    
    .navbar-nav .nav-link {
      font-size: 0.875rem;
      padding: 0.75rem 1rem !important;
    }
    
    .navbar-nav .nav-link i {
      font-size: 0.9rem;
      margin-right: 0.375rem;
    }
  }

  /* Animation for nav items */
  .navbar-nav .nav-item {
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
  }

  .navbar-nav .nav-item:nth-child(1) { animation-delay: 0.1s; }
  .navbar-nav .nav-item:nth-child(2) { animation-delay: 0.2s; }
  .navbar-nav .nav-item:nth-child(3) { animation-delay: 0.3s; }
  .navbar-nav .nav-item:nth-child(4) { animation-delay: 0.4s; }
  .navbar-nav .nav-item:nth-child(5) { animation-delay: 0.5s; }
  .navbar-nav .nav-item:nth-child(6) { animation-delay: 0.6s; }
  .navbar-nav .nav-item:nth-child(7) { animation-delay: 0.7s; }
  .navbar-nav .nav-item:nth-child(8) { animation-delay: 0.8s; }
  .navbar-nav .nav-item:nth-child(9) { animation-delay: 0.9s; }
  .navbar-nav .nav-item:nth-child(10) { animation-delay: 1.0s; }

  @keyframes fadeInUp {
    from {
      opacity: 0;
      transform: translateY(20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  /* Additional improvements */
  .navbar-toggler {
    border: none;
    padding: 0.75rem;
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  .navbar-toggler:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.05);
  }

  .navbar-toggler:focus {
    box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.3);
    outline: none;
  }

  .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
    width: 1.5em;
    height: 1.5em;
  }
</style>
