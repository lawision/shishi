document.addEventListener("DOMContentLoaded", () => {
    const toggle = document.getElementById("themeToggle");
    const body = document.body;

    // Load saved theme from localStorage
    const savedTheme = localStorage.getItem("adminTheme");

    if (savedTheme === "dark-mode") {
        body.classList.add("dark-mode");
        body.classList.remove("light-mode");
        toggle.textContent = "🌙"; // Moon icon for dark mode
    } else if (savedTheme === "light-mode") {
        body.classList.add("light-mode");
        body.classList.remove("dark-mode");
        toggle.textContent = "☀️"; // Sun icon for light mode
    } else {
        // Default theme is dark
        body.classList.add("dark-mode");
        toggle.textContent = "🌙";
        localStorage.setItem("adminTheme", "dark-mode");
    }

    // Toggle theme on button click
    toggle.addEventListener("click", () => {
        if (body.classList.contains("dark-mode")) {
            body.classList.replace("dark-mode", "light-mode");
            toggle.textContent = "☀️"; // Sun icon
            localStorage.setItem("adminTheme", "light-mode");
        } else {
            body.classList.replace("light-mode", "dark-mode");
            toggle.textContent = "🌙"; // Moon icon
            localStorage.setItem("adminTheme", "dark-mode");
        }
    });
});
