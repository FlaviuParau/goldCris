Newsletter with google recaptcha

25.04.2017

Js - in Design 
Miscellaneous Scripts

<script src='https://www.google.com/recaptcha/api.js'></script>
    <script>
        function onSubmit(token) {
            document.getElementById("footer-newsletter-validate-detail").submit();
        }

    </script>