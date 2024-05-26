$(document).ready(function () {
  const user = localStorage.getItem("isLogin");
  if (user) {
    window.location.href = "/html/profile.html";
  }
});

const form = document.getElementById("form");

function setError(element, message) {
  const formcontrol = element.parentElement;
  const small = formcontrol.querySelector("small");
  formcontrol.className = "form-control error";
  small.innerText = message;
}

function setSuccess(element) {
  const formcontrol = element.parentElement;
  formcontrol.className = "form-control success";
  // small.innerText = "";
}

function checkInput() {
  const email = document.getElementById("email");
  const password = document.getElementById("password");
  const cpassword = document.getElementById("cpassword");
  // console.log("************cpass",cpassword);

  const emailValue = email.value.trim();
  const passwordValue = password.value.trim();
  const cpasswordValue = cpassword.value.trim();

  if (emailValue === "") {
    setError(email, "email is required");
  } else if (!ValidateEmail(emailValue)) {
    setError(email, "Not a Valid email");
  } else {
    setSuccess(email);
  }

  if (passwordValue === "") {
    setError(password, "Password is required");
  } else if (passwordValue !== cpasswordValue) {
    setError(password, "Password does not match");
  } else {
    setSuccess(password);
  }

  if (cpasswordValue === "") {
    setError(cpassword, "Re-enter password");
  } else if (passwordValue !== cpasswordValue) {
    setError(cpassword, "Password does not match");
  } else {
    setSuccess(cpassword);
  }

  return true;
}

function ValidateEmail(emailValue) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(emailValue)) {
    return true;
  }
  return false;
}

form.addEventListener("submit", (e) => {
  e.preventDefault();
  // console.log();
  // console.log(checkInput());
  if (checkInput()) {
    var inputData2 = $("#password").val();
    var inputData3 = $("#email").val();
    var data = {
      input2: inputData2,
      input3: inputData3,
    };
    $.ajax({
      url: "/php/signup.php",
      type: "POST",
      data: data,
      success: function (response) {
        const { status, data, session_id } = response;
        console.log(data);

        // console.log(response); // Log other responses for debugging
        // $("#output").html(response); // Display the response in the output element
        localStorage.setItem("isLogin", true);
        localStorage.setItem("session_id", session_id);
        localStorage.setItem("emailid", data.emailid);
        window.location.href = "./profile.html";
      },
      error: function (response) {
        setError(email, "email aldready exist");
      },
    });
  }
});
