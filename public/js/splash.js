document.addEventListener("DOMContentLoaded", () => {
  const splash = document.getElementById("splash-screen");
  const main = document.getElementById("main-container");

  // Splash fade-out timing
  setTimeout(() => {
    splash.classList.add("fade-out"); // fade-out animation
    setTimeout(() => {
      splash.style.display = "none"; // hide splash after fade
      main.classList.remove("hidden"); // show login form
    }, 1000); // match fade-out duration
  }, 2000); // delay before fade starts (2s)
  

  // Toggle between Login and Register
  const showRegister = document.getElementById("showRegister");
  const showLogin = document.getElementById("showLogin");
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");

  if (showRegister) {
    showRegister.addEventListener("click", () => {
      loginForm.classList.add("hidden");
      registerForm.classList.remove("hidden");
    });
  }

  if (showLogin) {
    showLogin.addEventListener("click", () => {
      registerForm.classList.add("hidden");
      loginForm.classList.remove("hidden");
    });
  }
});
