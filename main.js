$(document).ready(function () {
  $("#sendBtn").click(function () {
    var formData = new FormData();
    formData.append("subject", $("#subject").val());
    formData.append("body", $("#body").val());
    formData.append("attachment", $("#attachment")[0].files[0]);

    $.ajax({
      url: "email_draft.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        alert("Draft saved successfully!");
        console.log(response);
      },
      error: function (xhr, status, error) {
        alert("Failed to saved.");
        console.log(xhr.responseText);
      },
    });
  });
  $(".email_button").click(function () {
    $(this).attr("disabled", "disabled");
    var id = $(this).attr("id");
    var action = $(this).data("action");
    var email_data = [];
    if (action == "single") {
      email_data.push({
        data_id: $(this).data("id"),
        email: $(this).data("email"),
        name: $(this).data("name"),
      });
      senmail(email_data, id);
    }
    if (action == "bulk") {
      $(".single_select").each(function () {
        if ($(this).prop("checked") === true) {
          email_data.push({
            data_id: $(this).data("id"),
            email: $(this).data("email"),
            name: $(this).data("name"),
          });
        }
      });
      senmail(email_data, id);
      senmail1();
    }
    function senmail(emailData, id) {
      $.ajax({
        url: "send_mail.php",
        method: "POST",
        data: { email_data: emailData },
        beforeSend: function () {
          $("#" + id).html("Sending...");
          $("#" + id).addClass("btn-danger");
          emailData.forEach(function (emailDataItem) {
            var dataId = emailDataItem.data_id;
            $("#" + dataId).html("Sending...");
            $("#" + dataId).addClass("btn-danger");
            $("#" + dataId).attr("disabled", "disabled");
          });
        },
        success: function (data) {
          debugger;
          console.log(data);
          $("#" + id).text("Success");
          if (action == "bulk") {
            $("#bulk_email").text("Bulk Mail Sent Successfully!");
          }
          $("#" + id).removeClass("btn-danger");
          $("#" + id).removeClass("btn-primary");
          $("#" + id).addClass("btn-success");
          $("#" + id).attr("disabled", false);
        },
      });
    }

    function senmail1() {
      if ($(".single_select:checked").length === 0) {
        alert("No emails selected for bulk sending.");
        $("#bulk_email").attr("disabled", false);
      }
    }
  });
});
// Function to toggle all checkboxes
function toggleSelectAll(selectAllCheckbox) {
  // Get all checkboxes with the class 'option-checkbox'
  const checkboxes = document.querySelectorAll(".single_select");

  // Set the checked state of each checkbox to match the 'Select All' checkbox
  checkboxes.forEach((checkbox) => {
    checkbox.checked = selectAllCheckbox.checked;
  });
}
