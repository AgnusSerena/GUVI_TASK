// const username = document.getElementById("username").value;

$(document).ready(function () {
  const user = localStorage.getItem("isLogin");
  if (!user) {
    window.location.href = "/html/login.html";
  }
  data = {};
  data.redisID = localStorage.getItem("session_id");

  data.action = "getUserDetails";
  // Send AJAX request to get profile
  $.ajax({
    url: "/php/profile.php",
    method: "GET",
    data: data,
    success: function (response) {
      console.log(response);

      const { message } = response;

      if (message === "Invalid Session ID") {
        localStorage.removeItem("isLogin");
        localStorage.removeItem("session_id");
        localStorage.removeItem("emailid");
        window.location.href = "/html/login.html";
      } else {
        const { email, Age, Name, PhoneNumber } = response.userDetails;
        if (email && email.trim() !== "") {
          document.getElementById("useremail").value = email;
        }
        if (Name && Name.trim() !== "") {
          document.getElementById("username").value = Name;
        }

        if (Age && Age.trim() !== "") {
          document.getElementById("userage").value = Age;
        }

        if (PhoneNumber && PhoneNumber.trim() !== "") {
          document.getElementById("userphonenumber").value = PhoneNumber;
        }
      }
    },
    error: function (xhr, status, error) {
      console.error(error);
    },
  });
});
const form = document.getElementById("form");

$("#updateBtn").click(function () {
  const inputs = $('input:not([name="email"])');
  inputs.prop("disabled", false);
});

$("#submit").click(function () {
  data.action = "update";
  data.redisID = localStorage.getItem("session_id");
  const username = document.getElementById("username").value;
  const userage = document.getElementById("userage").value;
  const userphonenumber = document.getElementById("userphonenumber").value;
  const email = document.getElementById("useremail").value;
  data.emailid = email;

  var profiledata = {
    Name: username,
    Age: userage,
    PhoneNumber: userphonenumber,
  };
  data.profiledata = profiledata;
  $.ajax({
    url: "/php/signup.php",
    method: "POST",
    data: data,
    success: (response) => {
      const { status, message } = response;
      if (status) {
        $("#output").html("Updated");

        setTimeout(function () {
          $("#output").empty();
        }, 2000);
        const inputs = $('input:not([name="email"])');
        inputs.prop("disabled", true);
      }
    },
    error: (response) => {
      const { status, message } = response;
      console.log(response);
      console.log(status);
      console.log(message);
    },
  });
});

$("#logout").click(function () {
  session_id = localStorage.getItem("session_id");

  data = {
    redisID: session_id,
    action: "logout",
  };
  $.ajax({
    url: "/php/profile.php",
    method: "GET",
    data: data,
    success: (response) => {
      if (response.status) {
        localStorage.removeItem("isLogin");
        localStorage.removeItem("session_id");
        localStorage.removeItem("emailid");
      }
    },
    error: (jqXHR, textStatus, errorThrown) => {
      console.error("Error:", textStatus, errorThrown);
      console.log("response");
    },
  });
  window.location.href = "./login.html";
});
