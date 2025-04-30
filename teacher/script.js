document.addEventListener("DOMContentLoaded", function() {
    const signUpButton = document.getElementById('signUpButton');
    const signInButton = document.getElementById('signInButton');
    const signInForm = document.getElementById('signIn');
    const signUpForm = document.getElementById('signUp');
    
    if (signUpButton && signInButton && signInForm && signUpForm) {
        signUpButton.addEventListener('click', function() {
            signInForm.style.display = "none";  // Hide Sign In form
            signUpForm.style.display = "block"; // Show Sign Up form
        });

        signInButton.addEventListener('click', function() {
            signUpForm.style.display = "none";  // Hide Sign Up form
            signInForm.style.display = "block"; // Show Sign In form
        });
    } else {
        console.error('One or more elements are missing in the HTML.');
    }
});
