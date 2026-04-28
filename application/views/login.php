<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Login POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" href="<?php echo base_url('favicon.png'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/perfect-scrollbar.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/animate.css'); ?>">
    <script src="<?php echo base_url(); ?>assets/js/perfect-scrollbar.min.js"></script>
    <script defer src="<?php echo base_url('assets/js/popper.min.js'); ?>"></script>
    <script defer src="<?php echo base_url('assets/js/tippy-bundle.umd.min.js'); ?>"></script>
    <script defer src="<?php echo base_url('assets/js/sweetalert.min.js'); ?>"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .error-message {
            margin-top: 20px;
            font-size: 12px;
            display: none;
        }
    </style>
</head>

<body
x-data="main"
class="relative overflow-x-hidden font-nunito text-sm font-normal antialiased"
:class="[ $store.app.sidebar ? 'toggle-sidebar' : '', $store.app.theme === 'dark' || $store.app.isDarkMode ?  'dark' : '', $store.app.menu, $store.app.layout,$store.app.rtlClass]"
>
<!-- screen loader -->
<div class="screen_loader animate__animated fixed inset-0 z-[60] grid place-content-center bg-[#fafafa] dark:bg-[#060818]">
    <svg width="64" height="64" viewBox="0 0 135 135" fill="#4361ee">
        <path
        d="M67.447 58c5.523 0 10-4.477 10-10s-4.477-10-10-10-10 4.477-10 10 4.477 10 10 10zm9.448 9.447c0 5.523 4.477 10 10 10 5.522 0 10-4.477 10-10s-4.478-10-10-10c-5.523 0-10 4.477-10 10zm-9.448 9.448c-5.523 0-10 4.477-10 10 0 5.522 4.477 10 10 10s10-4.478 10-10c0-5.523-4.477-10-10-10zM58 67.447c0-5.523-4.477-10-10-10s-10 4.477-10 10 4.477 10 10 10 10-4.477 10-10z"
        >
        <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="-360 67 67" dur="2.5s" repeatCount="indefinite" />
    </path>
    <path
    d="M28.19 40.31c6.627 0 12-5.374 12-12 0-6.628-5.373-12-12-12-6.628 0-12 5.372-12 12 0 6.626 5.372 12 12 12zm30.72-19.825c4.686 4.687 12.284 4.687 16.97 0 4.686-4.686 4.686-12.284 0-16.97-4.686-4.687-12.284-4.687-16.97 0-4.687 4.686-4.687 12.284 0 16.97zm35.74 7.705c0 6.627 5.37 12 12 12 6.626 0 12-5.373 12-12 0-6.628-5.374-12-12-12-6.63 0-12 5.372-12 12zm19.822 30.72c-4.686 4.686-4.686 12.284 0 16.97 4.687 4.686 12.285 4.686 16.97 0 4.687-4.686 4.687-12.284 0-16.97-4.685-4.687-12.283-4.687-16.97 0zm-7.704 35.74c-6.627 0-12 5.37-12 12 0 6.626 5.373 12 12 12s12-5.374 12-12c0-6.63-5.373-12-12-12zm-30.72 19.822c-4.686-4.686-12.284-4.686-16.97 0-4.686 4.687-4.686 12.285 0 16.97 4.686 4.687 12.284 4.687 16.97 0 4.687-4.685 4.687-12.283 0-16.97zm-35.74-7.704c0-6.627-5.372-12-12-12-6.626 0-12 5.373-12 12s5.374 12 12 12c6.628 0 12-5.373 12-12zm-19.823-30.72c4.687-4.686 4.687-12.284 0-16.97-4.686-4.686-12.284-4.686-16.97 0-4.687 4.686-4.687 12.284 0 16.97 4.686 4.687 12.284 4.687 16.97 0z"
    >
    <animateTransform attributeName="transform" type="rotate" from="0 67 67" to="360 67 67" dur="8s" repeatCount="indefinite" />
</path>
</svg>
</div>

<div class="main-container min-h-screen text-black dark:text-white-dark">
    <!-- start main content section -->
    <div class="flex min-h-screen items-center justify-center" style="background-image: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),url('<?= base_url('assets/images/grocery_bg.jpg'); ?>'); background-size: cover; background-position: center;">
        <div class="panel m-6 w-full max-w-lg sm:w-[480px]">
            <h2 class="mb-3 text-2xl font-bold">Login</h2>
            <p class="mb-7">Masukin Username dan Password</p>
            <div>
                <label for="username">Username</label>
                <input id="username" type="text" class="form-input" placeholder="Masukan Username" />
            </div>
            <div style="margin-top: 15px;">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-input" placeholder="Masukan Password" />
            </div>
            <div class="flex items-center rounded bg-danger-light p-3.5 text-danger dark:bg-danger-dark-light  error-message">
                <span class="notif-box">
                    <strong class="title">Warning!</strong>
                    <span class="message-content"></span>
                </span>
            </div>
            <button id="login" class="btn btn-primary w-full" style="margin-top:20px;">Login</button>

        </div>
    </div>
    <!-- end main content section -->
</div>

<script src="<?php echo base_url('assets/js/alpine-collaspe.min.js') ?>"></script>
<script src="<?php echo base_url('assets/js/alpine-persist.min.js') ?>"></script>
<script defer src="<?php echo base_url('assets/js/alpine-ui.min.js') ?>"></script>
<script defer src="<?php echo base_url('assets/js/alpine-focus.min.js') ?>"></script>
<script defer src="<?php echo base_url('assets/js/alpine.min.js') ?>"></script>

<script src="<?php echo base_url('assets/js/custom.js') ?>"></script>

<script>
            // main section
            document.addEventListener('alpine:init', () => {
                Alpine.data('scrollToTop', () => ({
                    showTopButton: false,
                    init() {
                        window.onscroll = () => {
                            this.scrollFunction();
                        };
                    },

                    scrollFunction() {
                        if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                            this.showTopButton = true;
                        } else {
                            this.showTopButton = false;
                        }
                    },

                    goToTop() {
                        document.body.scrollTop = 0;
                        document.documentElement.scrollTop = 0;
                    },
                }));
            });


            $('#login').on('click', function(e) {
                e.preventDefault();

                var username = $("#username").val();
                var password = $("#password").val();

                $.ajax({
                    type: "POST",
                    url: "<?= base_url('Auth/login_process') ?>",
                    dataType: "json",
                    data: {
                        username: username,
                        password: password
                    },
                    success: function (data) {
                        if (data.code == "200") {
                            window.location.href = "<?= base_url('Dashboard') ?>";
                        } else {
                            $('.error-message').show();
                            $('.message-content').text(data.message);
                        }
                    }
                });
            });
        </script>
    </body>
    </html>
