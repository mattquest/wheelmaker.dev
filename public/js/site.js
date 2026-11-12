/* wheelmaker.dev — vanilla JS. No framework. */
(function () {
    "use strict";

    var siteKey = document.querySelector('meta[name="recaptcha-site-key"]');
    siteKey = siteKey ? siteKey.getAttribute("content") : null;

    function recaptchaToken(action) {
        if (!siteKey || typeof grecaptcha === "undefined") {
            return Promise.resolve(null);
        }
        return grecaptcha.ready
            ? new Promise(function (resolve) {
                  grecaptcha.ready(function () {
                      grecaptcha.execute(siteKey, { action: action }).then(resolve, function () { resolve(null); });
                  });
              })
            : Promise.resolve(null);
    }

    function csrfToken() {
        var el = document.querySelector('meta[name="csrf-token"]');
        return el ? el.getAttribute("content") : "";
    }

    /* ---- contact form ---- */
    var form = document.getElementById("contactForm");
    if (form) {
        var submit = document.getElementById("contactSubmit");
        var status = document.getElementById("contactStatus");
        var tokenField = document.getElementById("contactToken");

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            submit.disabled = true;
            submit.textContent = "Sending…";
            status.textContent = "";
            status.className = "form-status";

            recaptchaToken("contact").then(function (token) {
                if (token) tokenField.value = token;
                var data = new FormData(form);
                return fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken(),
                        "X-Requested-With": "XMLHttpRequest",
                        Accept: "application/json",
                    },
                    body: data,
                });
            }).then(function (resp) {
                return resp.json().catch(function () { return {}; }).then(function (json) {
                    if (resp.ok && json.status === "ok") {
                        status.textContent = "Thanks — your message is on its way.";
                        status.classList.add("ok");
                        form.reset();
                    } else if (resp.status === 422 && json.errors) {
                        var first = Object.values(json.errors)[0];
                        status.textContent = Array.isArray(first) ? first[0] : first;
                        status.classList.add("err");
                    } else {
                        status.textContent = (json && json.message) || "Something went wrong. Please try again in a moment.";
                        status.classList.add("err");
                    }
                });
            }).catch(function () {
                status.textContent = "Couldn't reach the server. Please try again in a moment.";
                status.classList.add("err");
            }).finally(function () {
                submit.disabled = false;
                submit.textContent = "Send message";
            });
        });
    }

    /* ---- résumé download ---- */
    var resumeBtn = document.getElementById("resumeBtn");
    var resumeForm = document.getElementById("resumeForm");
    if (resumeBtn && resumeForm) {
        var resumeToken = document.getElementById("resumeToken");
        resumeBtn.addEventListener("click", function () {
            resumeBtn.disabled = true;
            recaptchaToken("downloadresume").then(function (token) {
                if (token) resumeToken.value = token;
                resumeForm.submit();
                resumeBtn.disabled = false;
            });
        });
    }
})();
