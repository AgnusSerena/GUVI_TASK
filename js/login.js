$(document).ready(function () {
  const user = localStorage.getItem("isLogin");
  if (user) {
    window.location.href = "/html/profile.html";
  }
});
const form = document.getElementById("form");

function checkInput() {
  const email = document.getElementById("email");
  const password = document.getElementById("password");

  const emailValue = email.value.trim();
  const passwordValue = password.value.trim();

  if (emailValue === "") {
    setError(email, "email is required");
  } else {
    setSuccess(email);
  }

  if (passwordValue === "") {
    setError(password, "password is required");
  } else {
    setSuccess(password);
  }

  if (emailValue === "" || passwordValue === "") {
    return false;
  }
  return true;
}

function setError(element, message) {
  const formcontrol = element.parentElement;
  const small = formcontrol.querySelector("small");
  formcontrol.className = "form-control error";
  small.innerText = message;
}

function setSuccess(element) {
  const formcontrol = element.parentElement;
  formcontrol.className = "form-control success";
}

form.addEventListener("submit", (e) => {
  e.preventDefault();
  // console.log();
  // console.log(checkInput());
  if (checkInput()) {
    var inputData1 = $("#email").val();
    var inputData2 = $("#password").val();
    var data = {
      input1: inputData1,
      input2: inputData2,
    };
    $.ajax({
      url: "/php/login.php",
      type: "POST",
      data: data,
      success: function (response) {
        console.log(response);
        const { status, data, session_id } = response;
        console.log(data);
        localStorage.setItem("isLogin", true);
        localStorage.setItem("session_id", session_id);
        localStorage.setItem("emailid", data.emailid);
        window.location.href = "./profile.html";
      },
      error: function (response) {
        const { status, responseJSON } = response;
        if (responseJSON.message === "invalid password") {
          // $("#output").html("Invalid password.");
          setError(password, "Invalid Password");
        } else if (responseJSON.message === "No user found") {
          setError(email, "email does not exist");
          setError(password, "or Invalid Password");
        }
      },
    });
  }
});
