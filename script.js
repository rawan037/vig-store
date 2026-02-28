// Splash logic
window.onload = () => {
    setTimeout(() => {
        document.getElementById("authContainer").style.display = "block";
    }, 2500);

    // إذا المستخدم مسجل دخول
    if(localStorage.getItem("user")){
        window.location.href = "home.html";
    }
};

// تبديل تسجيل الدخول / إنشاء حساب
const formTitle = document.getElementById("formTitle");
const toggleText = document.getElementById("toggleText");
const authForm = document.getElementById("authForm");

let isLogin = true;

if(toggleText){
toggleText.addEventListener("click", function(e){
    if(e.target.id === "toggleBtn"){
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
    }
});
}

// تسجيل أو دخول
if(authForm){
authForm.addEventListener("submit", function(e){
    e.preventDefault();

    const phone = document.getElementById("phone").value;
    const password = document.getElementById("password").value;

    if(isLogin){
        const savedUser = JSON.parse(localStorage.getItem("user"));
        if(savedUser && savedUser.phone === phone && savedUser.password === password){
            window.location.href = "home.html";
        } else {
            alert("بيانات غير صحيحة");
        }
    } else {
        localStorage.setItem("user", JSON.stringify({phone, password}));
        alert("تم إنشاء الحساب بنجاح");
        window.location.href = "home.html";
    }
});
}

// تسجيل خروج
function logout(){
    localStorage.removeItem("user");
    window.location.href = "index.html";
}
