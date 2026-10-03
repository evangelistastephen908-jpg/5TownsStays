<?php
// includes/footer.php - Public Footer
?>
<footer class="site-footer">
    <div class="footer-top">
        <div class="container grid-footer">
            <div class="footer-col brand-col">
                <div class="brand-logo footer-logo">
                    <div class="logo-icon"><i class="fa-solid fa-compass"></i></div>
                    <div class="logo-text">
                        <span class="brand-name">5Towns<span>Stays</span></span>
                        <span class="brand-tagline">Surigao del Sur Information Platform</span>
                    </div>
                </div>
                <p class="footer-desc">
                    Helping tourists discover, search, compare, and inquire about local hotels, resorts, inns, homestays, and guesthouses across the five vibrant municipalities of CarCanMadCarLan.
                </p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <div class="footer-col">
                <h4>CarCanMadCarLan Towns</h4>
                <ul class="footer-links">
                    <li><a href="/berot/accommodations.php?municipality=Cantilan"><i class="fa-solid fa-angle-right"></i> Cantilan Accommodations</a></li>
                    <li><a href="/berot/accommodations.php?municipality=Lanuza"><i class="fa-solid fa-angle-right"></i> Lanuza Surf Lodges</a></li>
                    <li><a href="/berot/accommodations.php?municipality=Madrid"><i class="fa-solid fa-angle-right"></i> Madrid Eco Hotels</a></li>
                    <li><a href="/berot/accommodations.php?municipality=Carmen"><i class="fa-solid fa-angle-right"></i> Carmen Spring Resorts</a></li>
                    <li><a href="/berot/accommodations.php?municipality=Carrascal"><i class="fa-solid fa-angle-right"></i> Carrascal Bay Hotels</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Accommodation Types</h4>
                <ul class="footer-links">
                    <li><a href="/berot/accommodations.php?type=Hotel"><i class="fa-solid fa-angle-right"></i> Hotels & Suites</a></li>
                    <li><a href="/berot/accommodations.php?type=Beach+Resort"><i class="fa-solid fa-angle-right"></i> Beach Resorts</a></li>
                    <li><a href="/berot/accommodations.php?type=Inn"><i class="fa-solid fa-angle-right"></i> Tourist Inns</a></li>
                    <li><a href="/berot/accommodations.php?type=Homestay"><i class="fa-solid fa-angle-right"></i> Local Homestays</a></li>
                    <li><a href="/berot/accommodations.php?type=Guesthouse"><i class="fa-solid fa-angle-right"></i> Guesthouses</a></li>
                </ul>
            </div>

            <div class="footer-col info-col">
                <h4>Research Project Context</h4>
                <p class="research-note">
                    <strong>Project Title:</strong><br>
                    "5TownsStays: A Web-Based Hotel and Resort Information Platform for Tourists in Finding Local Accommodations in CarCanMadCarLan"
                </p>
                <div class="research-tags">
                    <span class="tag">Agile SDLC</span>
                    <span class="tag">PHP / SQLite</span>
                    <span class="tag">Information Platform</span>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>&copy; <?php echo date('Y'); ?> 5TownsStays Platform. Built for Research Demonstration & Tourist Convenience.</p>
            <div class="footer-bottom-links">
                <a href="/berot/admin/login.php">Administrator Login</a>
                <span class="dot-separator">•</span>
                <span class="demo-tag">Sample Data Verification Mode</span>
            </div>
        </div>
    </div>
</footer>

<!-- Universal Toast Container -->
<div id="toastContainer" class="toast-container"></div>

<!-- JS Scripts -->
<script src="/berot/assets/js/main.js"></script>
</body>
</html>
