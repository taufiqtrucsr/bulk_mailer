$(document).ready(function () {
  tinymce.init({
    selector: "textarea.simple",
    plugins:
      "preview powerpaste casechange importcss tinydrive searchreplace autolink autosave directionality advcode visualblocks visualchars fullscreen image link media mediaembed codesample table charmap pagebreak nonbreaking anchor tableofcontents insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker editimage help formatpainter permanentpen pageembed charmap mentions quickbars linkchecker emoticons advtable footnotes mergetags autocorrect typography advtemplate markdown",
    mobile: {
      plugins:
        "preview powerpaste casechange importcss tinydrive searchreplace autolink autosave directionality advcode visualblocks visualchars fullscreen image link media mediaembed codesample table charmap pagebreak nonbreaking anchor tableofcontents insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker help formatpainter pageembed charmap mentions quickbars linkchecker emoticons advtable footnotes mergetags autocorrect typography advtemplate",
    },
    min_height: 380,
    menubar: false,
    toolbar:
      // "undo redo | bold italic underline | align bullist code | forecolor backcolor | fontselect | numlist fullscreen",
      "undo redo | bold italic underline | code fullscreen preview | blocks fontsizeinput | align numlist bullist | link image | table media pageembed | lineheight  outdent indent | strikethrough forecolor backcolor formatpainter removeformat | charmap emoticons checklist | print | pagebreak anchor codesample footnotes mergetags | addtemplate inserttemplate | addcomment showcomments | ltr rtl casechange | spellcheckdialog a11ycheck | restoredraft",
  });
  $(".email_button").click(function () {
    $(this).attr("disabled", "disabled");
    var id = $(this).attr("id");
    var action = $(this).data("action");
    var email_data = [];
    if (action == "single") {
      Swal.fire({
        title: "Confirm: Send Email?",
        html: "Send Mail to <b>" + $(this).data("name") + "</b>",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Send",
        cancelButtonText: "Cancel",
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
      }).then((result) => {
        if (result.isConfirmed) {
          email_data.push({
            data_id: $(this).data("id"),
            email: $(this).data("email"),
            name: $(this).data("name"),
          });
          senmail(email_data);
        } else {
          $(this).attr("disabled", false);
        }
      });
    }
    if (action == "bulk") {
      if ($(".single_select:checked").length === 0) {
        Swal.fire({
          title: "Alert !",
          text: "No Emails selected for Bulk Sending.",
          icon: "error",
        });
        $("#bulk_email").attr("disabled", false);
      } else {
        Swal.fire({
          title: "Confirm: Ready to Send Bulk Emails?",
          html: "Send Bulk Mail",
          icon: "question",
          showCancelButton: true,
          confirmButtonText: "Send",
          cancelButtonText: "Cancel",
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
        }).then((result) => {
          if (result.isConfirmed) {
            $(".single_select").each(function () {
              if ($(this).prop("checked") === true) {
                email_data.push({
                  data_id: $(this).data("id"),
                  email: $(this).data("email"),
                  name: $(this).data("name"),
                });
              }
            });
            senmail(email_data);
            startFetchingContent();
          } else {
            $(this).attr("disabled", false);
          }
        });
      }
    }
    function senmail(emailData) {
      $.ajax({
        url: "send_mail.php",
        method: "POST",
        data: { email_data: emailData },
        beforeSend: function () {
          $("#uploadcsv").attr("disabled", "disabled");
          $("#statusBtn").removeClass("d-none").addClass("d-block");
          $("#bulk_email")
            .html("Sending...")
            .addClass("btn-danger")
            .attr("disabled", "disabled");
          emailData.forEach(function (emailDataItem) {
            var dataId = emailDataItem.data_id;
            $("#" + dataId)
              .html("Sending...")
              .addClass("btn-danger")
              .attr("disabled", "disabled");
          });
        },
        xhr: function () {
          var xhr = new window.XMLHttpRequest();
          xhr.onreadystatechange = function () {
            if (xhr.readyState === 3) {
              $("body").append(xhr.responseText);
            }
          };
          return xhr;
        },
        success: function () {
          $("#bulk_email")
            .text("Sent Successfully!")
            .removeClass("btn-danger btn-primary")
            .addClass("btn-success")
            .attr("disabled", false);
          $(".anlytic .bounce_analytics").removeClass("disabled");
          $("#uploadcsv").attr("disabled", false);
          $("#statusBtn").removeClass("d-block").addClass("d-none");
          emailData.forEach(function (emailDataItem) {
            var dataId = emailDataItem.data_id;
            $("#" + dataId)
              .html("Success")
              .removeClass("btn-danger")
              .addClass("btn-success")
              .attr("disabled", false);
          });
          $.ajax({
            url: "analytics.php",
            method: "POST",
            success: function (response2) {
              Swal.fire({
                title: "Bounce Analytics",
                html: '<div class="loader"></div>',
                text: response2,
                icon: "warning",
                allowOutsideClick: false,
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: "Save data in Excel",
                didOpen: () => {
                  const iframe = document.createElement("iframe");
                  iframe.src = "analytics.php";
                  iframe.style.width = "100%";
                  iframe.style.height = "200px";
                  iframe.style.border = "none";
                  iframe.onload = function () {
                    Swal.update({
                      html: iframe.outerHTML,
                    });
                  };

                  // Append iframe to the modal content
                  Swal.getHtmlContainer().appendChild(iframe);
                },
              }).then(() => {
                $("<form>", {
                  id: "downloadForm",
                  html: '<input type="hidden" name="data" value="value"/>', // Add more hidden inputs as needed
                  action: "download.php",
                  method: "POST",
                })
                  .appendTo(document.body)
                  .submit()
                  .remove();
              });
            },
            error: function () {
              Swal.fire({
                title: "Error!",
                text: "Failed to run the PHP file!",
                icon: "error",
              });
            },
          });
        },
      });
      return false;
    }
  });
  $(".bounce_analytics").click(function () {
    Swal.fire({
      title: "Bounce Analytics",
      html: '<div class="loader"></div>',
      width: 800,
      padding: "1em",
      icon: "warning",
      allowOutsideClick: false,
      showCloseButton: true,
      showCancelButton: true,
      confirmButtonText: "Download data in Excel",
      didOpen: () => {
        const iframe = document.createElement("iframe");
        iframe.src = "analytics.php";
        iframe.style.width = "100%";
        iframe.style.height = "200px";
        iframe.style.border = "none";
        iframe.onload = function () {
          Swal.update({
            html: iframe.outerHTML,
          });
        };

        // Append iframe to the modal content
        Swal.getHtmlContainer().appendChild(iframe);
      },
    }).then((result) => {
      if (result.isConfirmed) {
        $("<form>", {
          id: "downloadForm",
          html: '<input type="hidden" name="data" value="value"/>', // Add more hidden inputs as needed
          action: "download.php",
          method: "POST",
        })
          .appendTo(document.body)
          .submit()
          .remove();
      }
    });
  });
  $("#showLogBtn").click(function () {
    fetch("email_open.php")
      .then((response) => response.text())
      .then((data) => {
        Swal.fire({
          title: "Opened Mail",
          html: `<pre style="text-align:left;overflow-y:scroll;height:200px">${data}</pre>`,
          width: "50%",
          customClass: {
            popup: "swal2-log-popup",
            content: "swal2-log-content",
          },
        });
      })
      .catch((error) => {
        Swal.fire("Error", "Could not load log file.", "error");
      });
  });

  var emailArray = [];
  $(".email_button").filter(function () {
    if ($(this).find(".badge").length > 0) {
      var email = $(this).data("email");

      // Check if email is defined
      if (email !== undefined) {
        if (email !== undefined && !emailArray.includes(email)) {
          emailArray.push(email);
        }
        // Show SweetAlert2 popup
        Swal.fire({
          title: "Duplicate Email Id Found",
          icon: "error",
          text: emailArray.join(",\n"),
        });

        // Log the email to the console
        console.log(email);
      } else {
        console.log("No data-email attribute found.");
      }
    }
  });
  var rowcounting = 0;
  $(".data-item").each(function () {
    rowcounting++;
  });
  $("#totalCount1").text("Total rows: " + rowcounting);
  $("#searchInput").on("keyup", function () {
    var value = $(this).val().toLowerCase();
    var count = 0;
    $(".data-item").each(function () {
      var accordionItem = $(this);
      var text = accordionItem.text().toLowerCase();
      if (text.indexOf(value) > -1) {
        accordionItem.show();
        count++;
      } else {
        accordionItem.hide();
      }
    });
    $("#totalCount1").text("Total matching rows: " + count);
    updateSelectAllAndCount();
  });

  $("#select-all").on("change", function () {
    $(".data-item:visible .single_select").prop(
      "checked",
      $(this).prop("checked")
    );
    countChecked();
  });

  $(".single_select").on("change", function () {
    updateSelectAllAndCount();
  });
  $(".camp").click(function () {
    const campaignName = $(this).data("name");
    Swal.fire({
      title: campaignName,
      html:
        '<div class="loader"></div>' +
        '<div id="contentContainer" style="width: 100%; height: 200px; overflow-y: auto;"></div><br><br>' +
        '<button id="button1" class="swal2-confirm swal2-styled">Bounce Analytics</button>' +
        '<button id="button2" class="swal2-confirm swal2-styled">Email Logs</button>' +
        '<button id="button3" class="swal2-confirm swal2-styled">List Of Data</button>' +
        '<button id="button4" class="swal2-confirm swal2-styled">Sended Mail List</button>',
      width: 800,
      padding: "1em",
      showCloseButton: true,
      showConfirmButton: false,
      didOpen: () => {
        const loader = document.querySelector(".loader");
        const contentContainer = document.getElementById("contentContainer");

        function loadContent(url) {
          loader.style.display = "block";
          $.ajax({
            url: url,
            type: "POST",
            data: { campaignName: campaignName },
            success: function (response) {
              contentContainer.innerHTML = response;
              loader.style.display = "none";
            },
            error: function (xhr, status, error) {
              console.error("Error loading data: " + error);
              contentContainer.innerHTML = "<p>Error loading content.</p>";
              loader.style.display = "none";
            },
          });
        }
        // Load initial content
        loadContent("campaign.php?sheet=0");

        // Button click handlers
        $("#button1").click(function () {
          loadContent("campaign.php?sheet=0");
        });

        $("#button2").click(function () {
          loadContent("email_open.php");
        });

        $("#button3").click(function () {
          loadContent("campaign.php?sheet=1");
        });

        $("#button4").click(function () {
          loadContent("campaign.php?sheet=2");
        });
      },
    });
  });
  function countChecked() {
    var selectedCount = $(".single_select:checked").length;
    $("#totalCount").text("Selected checkboxes: " + selectedCount);
  }
  function updateSelectAllAndCount() {
    var visibleCheckboxes = $(".data-item:visible .single_select");
    var totalVisible = visibleCheckboxes.length;
    var totalChecked = visibleCheckboxes.filter(":checked").length;

    // Update 'Select All' checkbox
    $("#selectAll").prop(
      "checked",
      totalVisible > 0 && totalVisible === totalChecked
    );

    countChecked();
  }
  updateSelectAllAndCount();

  let lastModTime = 0;
  function fetchFileContent() {
    document.querySelector(".loader").style.display = "block";
    $.ajax({
      url: "email_log.php",
      method: "GET",
      data: { lastModTime: lastModTime },
      success: function (response) {
        lastModTime = response.lastModTime;
        $("#swalContent").append(response.content);
        fetchFileContent();
        document.querySelector(".loader").style.display = "none";
      },
      error: function () {
        $("#swalContent").text("Error loading file.");
      },
    });
  }
  function startFetchingContent() {
    Swal.fire({
      title: "Send Mail Log",
      html: '<div class="loader"></div><div class="table-responsive mt-3" style="height:60vh"><table class="table table-bordered table-striped"><thead><tr><th>Sr No</th><th>Name</th><th>Email</th><th>Status</th></tr></thead><tbody id="swalContent"></tbody></table></div>',
      width: "50%",
      allowOutsideClick: false,
      showCloseButton: true,
      didOpen: () => {
        fetchFileContent(); // Start fetching the content after the modal is opened
      },
    });
  }

  $("#statusBtn").on("click", function () {
    startFetchingContent();
  });
});

function moveScroll() {
  var scroll = $(window).scrollTop();
  var anchor_top = $("#maintable").offset().top;
  var anchor_bottom = $("#bottom_anchor").offset().top;
  if (scroll > anchor_top && scroll < anchor_bottom) {
    clone_table = $("#clone");
    if (clone_table.length == 0) {
      clone_table = $("#maintable").clone();
      clone_table.attr("id", "clone");
      clone_table.css({ position: "fixed", "pointer-events": "none", top: 0 });
      clone_table.width($("#maintable").width());
      $("#table-container").append(clone_table);
      $("#clone").css({ visibility: "hidden" });
      $("#clone thead").css({ visibility: "visible" });
    }
  } else {
    $("#clone").remove();
  }
}
$(window).scroll(moveScroll);

(function () {
  "use strict";

  // Fetch all the forms we want to apply custom Bootstrap validation styles to
  var forms = document.querySelectorAll(".needs-validation");

  // Loop over them and prevent submission
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener(
      "submit",
      function (event) {
        event.preventDefault();
        if (!form.checkValidity()) {
          event.stopPropagation();
        } else {
          // var converter = tinymce.activeEditor.getContent();
          var formData = new FormData();
          formData.append("subject", $("#subject").val());
          formData.append("body", $("#body").val());
          formData.append("attachment", $("#attachment")[0].files[0]);
          formData.append("fromname", $("#fromname").val());
          formData.append("fromemail", $("#fromemail").val());
          formData.append("campaingname", $("#campaingname").val());
          debugger;
          $.ajax({
            url: "email_draft.php",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
              console.log(data);
              Swal.fire({
                icon: "success",
                title: "Success",
                text: "Draft saved successfully!",
              });
              $("#composeModal").modal("hide");
            },
            error: function (xhr) {
              Swal.fire({
                icon: "error",
                title: "Something went wrong!",
                text: xhr.responseText,
              });
            },
          });
        }

        form.classList.add("was-validated");
      },
      false
    );
  });
})();
