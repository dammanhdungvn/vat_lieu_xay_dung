            </div>
            <!-- End of Main Content -->
            
            <!-- Footer -->
            <footer class="footer bg-white py-4 mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">
                            &copy; <?php echo date('Y'); ?> VLXD Online - Phát triển bởi <a href="#" class="text-primary">Nguyen Viet Hoan</a>
                        </div>
                        <div>
                            <a href="#">Chính sách bảo mật</a>
                            &middot;
                            <a href="#">Điều khoản &amp; Điều kiện</a>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->
        </div>
        <!-- End of Content Wrapper -->
    </div>
    <!-- End of Wrapper -->
    
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="bi bi-arrow-up"></i>
    </a>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom admin script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar trên mobile
            const mobileTogglers = document.querySelectorAll('.navbar-toggler, .overlay');
            const sidebar = document.querySelector('.sidebar');
            const contentWrapper = document.querySelector('.content-wrapper');
            
            if (mobileTogglers.length > 0 && sidebar) {
                mobileTogglers.forEach(toggler => {
                    toggler.addEventListener('click', function() {
                        sidebar.classList.toggle('show');
                        
                        // Thêm overlay khi mở sidebar trên mobile
                        if (sidebar.classList.contains('show') && !document.querySelector('.overlay')) {
                            const overlay = document.createElement('div');
                            overlay.classList.add('overlay');
                            document.body.appendChild(overlay);
                            
                            overlay.addEventListener('click', function() {
                                sidebar.classList.remove('show');
                                overlay.remove();
                            });
                        } else {
                            const overlay = document.querySelector('.overlay');
                            if (overlay) overlay.remove();
                        }
                    });
                });
            }
            
            // Toggle sidebar collapse
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.body.classList.toggle('sidebar-collapsed');
                    const sidebar = document.querySelector('.sidebar');
                    const isCollapsed = sidebar.classList.toggle('collapsed');
                    
                    // Lưu trạng thái vào cookie
                    document.cookie = `sidebar_collapsed=${isCollapsed}; path=/; max-age=31536000`;
                    
                    // Thay đổi icon
                    const icon = sidebarToggle.querySelector('i');
                    if (icon) {
                        if (isCollapsed) {
                            icon.classList.remove('bi-arrow-left-square-fill');
                            icon.classList.add('bi-arrow-right-square-fill');
                        } else {
                            icon.classList.remove('bi-arrow-right-square-fill');
                            icon.classList.add('bi-arrow-left-square-fill');
                        }
                    }
                });
            }
            
            // Toggle dark mode
            const darkModeToggle = document.getElementById('darkModeToggle');
            
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function() {
                    const isDarkMode = document.body.classList.toggle('dark-mode');
                    
                    // Lưu trạng thái vào cookie
                    document.cookie = `dark_mode=${isDarkMode}; path=/; max-age=31536000`;
                    
                    // Thay đổi icon
                    const icon = darkModeToggle.querySelector('i');
                    if (icon) {
                        if (isDarkMode) {
                            icon.classList.remove('bi-moon-fill');
                            icon.classList.add('bi-sun-fill');
                        } else {
                            icon.classList.remove('bi-sun-fill');
                            icon.classList.add('bi-moon-fill');
                        }
                    }
                });
            }
            
            // Tooltips
            const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            if (tooltips.length > 0) {
                tooltips.forEach(tooltip => {
                    new bootstrap.Tooltip(tooltip);
                });
            }
            
            // Dropdowns
            const dropdowns = document.querySelectorAll('.dropdown-toggle');
            if (dropdowns.length > 0) {
                dropdowns.forEach(dropdown => {
                    new bootstrap.Dropdown(dropdown);
                });
            }
        });
    </script>
</body>
</html> 