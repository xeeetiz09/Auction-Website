<footer>
	&copy; Fotheby's Auction House 2023. All rights reserved.
</footer>
<!-- including script for icons in login and register page -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons.js"></script>
<script>
    $(document).ready(function () {
		
        var passwordInput1 = $('#password1');
        var passwordInput2 = $('#password2');
        var passwordInput = $('#password');
        $(".cred_input input").on("input", function () {
            // Check if the input field is empty
            if ($(this).val() === "") {
                // Update the top property of the corresponding label element to 0px
                $(this).siblings("label").css("top", "50%");
            } else {
                // Otherwise, keep the label at -5px
                $(this).siblings("label").css("top", "-5px");
            }
        });

        // when eye button in password field is clicked, the password will be shown
        $('.show-password1').click(function () {
            passwordInput.attr('type', 'text');
            $(this).hide();
            $('.hide-password1').show();
        });
        // when eye close button in password field is clicked, the password will be hidden
        $('.hide-password1').click(function () {
            passwordInput.attr('type', 'password');
            $(this).hide();
            $('.show-password1').show();
        });
        // when eye button in confirm password field is clicked, the password will be shown
		$('.show-password2').click(function () {
            passwordInput1.attr('type', 'text');
            passwordInput2.attr('type', 'text');
            $(this).hide();
            $('.hide-password2').show();
        });
        // when eye closes button in confirm password field is clicked, the password will be hidden
        $('.hide-password2').click(function () {
            passwordInput1.attr('type', 'password');
            passwordInput2.attr('type', 'password');
            $(this).hide();
            $('.show-password2').show();
        });

        // trigger input when page loads
		$('.cred_input input').trigger('input');
    });


</script>
</body>

</html>