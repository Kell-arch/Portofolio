<?php
$pageTitle = 'Contact';
$activePage = 'contact';
include __DIR__ . '/header.php';
include __DIR__ . '/navigation/navbar.php';
?>

<main>
    <section class="page-header fade-in">
        <h1>Get In <span>Touch</span></h1>
        <p>Have a project in mind or just want to say hello? Lets connect.</p>
    </section>

    <section class="container">
        <div class="contact-grid">
            <div class="contact-info fade-in-left">
                <p class="section-label">Contact</p>
                <h2 class="section-title" style="font-size: 36px;">Let's Talk</h2>
                <p style="color: var(--text-secondary); margin-bottom: 32px; font-size: 15px;">
                    Feel free to reach out for collaborations, project inquiries,
                    or just a friendly chat. I am always open to new opportunities.
                </p>

                <div class="contact-info-list">
                    <div class="contact-item">
                        <div class="ci-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                            <p class="ci-label">Email</p>
                            <p class="ci-value"><a href="mailto:<?= htmlspecialchars(setting('email')) ?>"><?= htmlspecialchars(setting('email')) ?></a></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="ci-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <p class="ci-label">Location</p>
                            <p class="ci-value"><?= htmlspecialchars(setting('location')) ?></p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <div class="ci-icon"><i class="fas fa-phone"></i></div>
                        <div>
                            <p class="ci-label">Phone</p>
                            <p class="ci-value"><a href="tel:<?= preg_replace('/[^0-9]/', '', setting('phone')) ?>"><?= htmlspecialchars(setting('phone')) ?></a></p>
                        </div>
                    </div>
                </div>

                <div class="contact-social">
                    <a href="<?= htmlspecialchars(setting('github_url')) ?>" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
                        <i class="fab fa-github"></i>
                    </a>
                    <a href="<?= htmlspecialchars(setting('linkedin_url')) ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="mailto:<?= htmlspecialchars(setting('email')) ?>" aria-label="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>

            <div class="contact-form fade-in-right">
                <form id="contactForm" action="#" method="POST">
                    <div class="form-group">
                        <label for="name">Your Name</label>
                        <input type="text" id="name" name="name" placeholder="Enter your name" required>
                        <p class="error-text">Please enter your name.</p>
                    </div>

                    <div class="form-group">
                        <label for="email">Your Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                        <p class="error-text">Please enter a valid email.</p>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" placeholder="What is this about?" required>
                        <p class="error-text">Please enter a subject.</p>
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                        <p class="error-text">Please enter your message.</p>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php
include __DIR__ . '/navigation/footer.php';
?>
