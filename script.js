const formTitle = document.getElementById("formTitle");
const toggleBtn = document.getElementById("toggleBtn");
const toggleText = document.getElementById("toggleText");
const authForm = document.getElementById("authForm");

let isLogin = true;

toggleBtn.onclick = () => {
    isLogin = !isLogin;

    if(isLogin){
        formTitle.innerText = "تسجيل الدخول";
        authForm.querySelector("button").innerText = "دخول";
        toggleText.innerHTML = `ليس لديك حساب؟ <span id="toggleBtn">إنشاء حساب</span>`;
    } else {
        formTitle.innerText = "إنشاء حساب";
        authForm.querySelector("button").innerText = "تسجيل";
        toggleText.innerHTML = `لديك حساب؟ <span id="toggleBtn">تسجيل الدخول</span>`;
    }

    location.reload();
};
