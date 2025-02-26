document.addEventListener('DOMContentLoaded', () => {
    const openTab = (event, tabId) => {
        document.querySelectorAll('.tabcontent').forEach(tabContent => {
            tabContent.style.display = 'none';
        });

        document.querySelectorAll('.tablinks').forEach(tabLink => {
            tabLink.classList.remove('active');
        });

        const activeTab = document.getElementById(tabId);
        if (activeTab) {
            activeTab.style.display = 'block';
            if (event && event.currentTarget) {
                event.currentTarget.classList.add('active');
            }
        } else {
            console.error(`Tab with ID '${tabId}' not found.`);
        }
    };

    openTab(null, 'login');

    document.querySelectorAll('.tablinks').forEach(tabLink => {
        tabLink.addEventListener('click', event => {
            const tabId = event.currentTarget.getAttribute('data-tab');
            openTab(event, tabId);
        });
    });

    const forgotPasswordLink = document.querySelector('.forgot-password');
    if (forgotPasswordLink) {
        forgotPasswordLink.addEventListener('click', event => {
            event.preventDefault();
            openTab(null, 'reset-password');
        });
    }
});



const baseURL = "http://localhost:8000/api";

function registerUser(formId) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const firstName = formData.get("first_name");
    const lastName = formData.get("last_name");
    const email = formData.get("email");
    const phone = formData.get("phone");
    const password = formData.get("password");
    const passwordConfirmation = formData.get("confirm_password");
    const userType = formData.get("user_type");

    if (!firstName || !lastName || !email || !phone || !password || !passwordConfirmation || !userType) {
        alert("يرجى ملء جميع الحقول.");
        return;
    }

    const requestBody = {
        first_name: firstName,
        last_name: lastName,
        email: email,
        phone: phone,
        password: password,
        password_confirmation: passwordConfirmation,
        user_type: userType
    };

    axios.post(`${baseURL}/register`, requestBody, {
        headers: {
            "Content-Type": "application/json"
        }
    })
        .then(response => {
            if (response.status === 201) {
                alert(response.data.message);
                console.log("User created successfully:", response.data.user);
                window.location.href = "http://127.0.0.1:5500/Login&SigneUp.html";
            }
        })
        .catch(error => {
            if (error.response) {
                console.error("Error response:", error.response.data);
                alert(`خطأ: ${error.response.data.message || "يرجى التحقق من البيانات المدخلة."}`);
            } else {
                console.error("Error:", error);
                alert("حدث خطأ أثناء معالجة الطلب. حاول مرة أخرى لاحقًا.");
            }
        });
}


const registerButton = document.getElementById("register-btn");
if (registerButton) {
    registerButton.addEventListener("click", registerUser);
}



function loginUser(event, formId) {
    event.preventDefault();

    const form = document.getElementById(formId);
    const formData = new FormData(form);
    const email = formData.get("email");
    const password = formData.get("password");
    const userType = formData.get("user_type");

    if (!email || !password || !userType) {
        alert("يرجى ملء جميع الحقول.");
        return;
    }

    const requestBody = {
        email: email,
        password: password,
        user_type: userType
    };

    axios.post(`${baseURL}/login`, requestBody, {
        headers: {
            "Content-Type": "application/json"
        }
    })
        .then(response => {
            if (response.status === 200) {
                alert("تم تسجيل الدخول بنجاح.");
                console.log("Login successful:", response.data);

                const token = response.data;
                if (token) {
                    const tokenValue = token.split('|')[1];
                    if (tokenValue) {
                        console.log("Storing token in localStorage...");
                        localStorage.setItem("authToken", tokenValue);
                        console.log("Token stored:", localStorage.getItem("authToken"));
                    } else {
                        console.error("Token part is missing.");
                    }
                } else {
                    console.error("Token is missing in the response.");
                }



                const firstLogin = localStorage.getItem("firstLogin");

                if (!firstLogin) {
                    localStorage.setItem("firstLogin", "true");
                    window.location.href = "http://127.0.0.1:5500/personal-info.html";
                } else {
                    window.location.href = "http://127.0.0.1:5500/index.html";
                }
            }
        })
        .catch(error => {
            if (error.response) {
                console.error("Error response:", error.response.data);
                alert(`خطأ: ${error.response.data.message || "يرجى التحقق من البيانات المدخلة."}`);
            } else {
                console.error("Error:", error);
                alert("حدث خطأ أثناء معالجة الطلب. حاول مرة أخرى لاحقًا.");
            }
        });
}



const loginForm = document.getElementById("login-form");
if (loginForm) {
    loginForm.addEventListener("submit", loginUser);
}




function forgotPassword(event) {
    event.preventDefault();

    const emailInput = document.querySelector('#reset-password-form input[name="email"]');
    const email = emailInput.value.trim();

    if (!email) {
        alert("يرجى إدخال البريد الإلكتروني.");
        return;
    }

    const requestBody = { email: email };

    axios.post(`${baseURL}/forgot-password`, requestBody, {
        headers: {
            "Content-Type": "application/json"
        }
    })
        .then(response => {
            if (response.status === 200) {
                alert(response.data.message);
                console.log("Forgot password email sent successfully:", response.data);
            }
        })
        .catch(error => {
            if (error.response) {
                console.error("Error response:", error.response.data);
                alert(`خطأ: ${error.response.data.message || "يرجى التحقق من البريد الإلكتروني المدخل."}`);
            } else {
                console.error("Error:", error);
                alert("حدث خطأ أثناء معالجة الطلب. حاول مرة أخرى لاحقًا.");
            }
        });
}

const resetPasswordForm = document.getElementById("reset-password-form");
if (resetPasswordForm) {
    resetPasswordForm.addEventListener("submit", forgotPassword);
}

document.getElementById("login-form").addEventListener("submit", (e) => loginUser(e, "login-form"));
document.getElementById("login-creator-form").addEventListener("submit", (e) => loginUser(e, "login-creator-form"));

document.getElementById("register-btn").addEventListener("click", () => registerUser("signup-form"));
document.getElementById("signup-creator-form").addEventListener("submit", (e) => {
    e.preventDefault();
    registerUser("signup-creator-form");
});