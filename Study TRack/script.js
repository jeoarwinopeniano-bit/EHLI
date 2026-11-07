document.addEventListener("DOMContentLoaded", function () {
    let notificationTimer;
    function showNotification(message) {
        let notification = document.querySelector(".form-notification");
        if (!notification) {
            notification = document.createElement("div");
            notification.className = "form-notification";
            notification.setAttribute("role", "alert");
            notification.setAttribute("aria-live", "assertive");
            document.body.appendChild(notification);
        }
        notification.textContent = message;
        notification.classList.remove("notification-hide");
        notification.classList.add("notification-show");
        window.clearTimeout(notificationTimer);
        notificationTimer = window.setTimeout(function () {
            notification.classList.remove("notification-show");
            notification.classList.add("notification-hide");
        }, 2000);
    }

    document.querySelectorAll("#loginForm, #registerForm").forEach(function (form) {
        form.addEventListener("submit", function (event) {
            const fields = Array.from(form.querySelectorAll("input:not([type=hidden])"));
            const emptyRequired = fields.some(function (field) {
                return field.required && field.value.trim() === "";
            });
            if (emptyRequired) {
                event.preventDefault();
                showNotification("Please fill out all required fields.");
                return;
            }
            const invalidField = fields.find(function (field) { return !field.checkValidity(); });
            if (invalidField) {
                event.preventDefault();
                showNotification(invalidField.validationMessage);
            }
        });
    });

    const registerForm = document.querySelector("#registerForm");
    if (registerForm) {
        registerForm.addEventListener("submit", function (event) {
            const passwordField = document.querySelector("#password");
            const confirmField = document.querySelector("#confirm_password");
            if (!passwordField || !confirmField) return;
            const password = passwordField.value;
            const confirmPassword = confirmField.value;
            if (password !== confirmPassword) {
                event.preventDefault();
                showNotification("Passwords do not match.");
            }
        });
    }

    document.querySelectorAll(".transient-message").forEach(function (message) {
        window.setTimeout(function () {
            message.classList.add("message-hide");
            window.setTimeout(function () { message.remove(); }, 350);
        }, 2000);
    });

    const deadline = document.querySelector("#deadline");
    if (deadline) {
        const now = new Date();
        deadline.min = new Date(now.getTime() - now.getTimezoneOffset() * 60000)
            .toISOString().slice(0, 10);
    }

    document.querySelectorAll("form[data-loading]").forEach(function (form) {
        form.addEventListener("submit", function (event) {
            if (event.defaultPrevented) return;
            if (form.hasAttribute("data-confirm") && form.dataset.confirmed !== "true") return;
            const button = form.querySelector('button[type="submit"]');
            if (button) {
                button.disabled = true;
                button.textContent = form.dataset.loading;
                button.setAttribute("aria-busy", "true");
            }
        });
    });

    const dialog = document.createElement("dialog");
    dialog.className = "confirm-dialog";
    dialog.innerHTML = '<div class="confirm-content"><h2 class="confirm-title">Confirm deletion</h2><p class="confirm-message"></p><div class="confirm-actions"><button type="button" class="cancel-delete">Cancel</button><button type="button" class="confirm-delete">Delete</button></div></div>';
    document.body.appendChild(dialog);
    let pendingAction = null;

    document.addEventListener("click", function (event) {
        const link = event.target.closest("a[data-logout-confirm]");
        if (!link) return;
        event.preventDefault();
        pendingAction = function () { window.location.href = link.href; };
        dialog.querySelector(".confirm-title").textContent = "Confirm logout";
        dialog.querySelector(".confirm-message").textContent = "Are you sure you want to log out?";
        dialog.querySelector(".confirm-delete").textContent = "Logout";
        if (typeof dialog.showModal === "function") dialog.showModal();
        else if (window.confirm("Are you sure you want to log out?")) pendingAction();
    });

    document.addEventListener("submit", function (event) {
        const form = event.target.closest("form[data-confirm]");
        if (!form || form.dataset.confirmed === "true") return;
        event.preventDefault();
        pendingAction = function () {
            form.dataset.confirmed = "true";
            form.requestSubmit();
        };
        dialog.querySelector(".confirm-title").textContent = "Confirm deletion";
        dialog.querySelector(".confirm-message").textContent = form.dataset.confirm;
        dialog.querySelector(".confirm-delete").textContent = "Delete";
        if (typeof dialog.showModal === "function") dialog.showModal();
        else if (window.confirm(form.dataset.confirm)) pendingAction();
    });

    dialog.querySelector(".cancel-delete").addEventListener("click", function () {
        dialog.close();
        pendingAction = null;
    });
    dialog.querySelector(".confirm-delete").addEventListener("click", function () {
        if (!pendingAction) return;
        const action = pendingAction;
        pendingAction = null;
        dialog.close();
        action();
    });
});
