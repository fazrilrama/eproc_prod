Email Send To
<textarea disabled class="form-control" name="" id="send_list" cols="30" rows="10"></textarea>
<br>
<button class="btn btn-lg btn-info" id="send">Kirim Email</button>
<hr>
Logs <button class="btn btn-sm btn-success" id="download"><i class="fa fa-download"></i></button>
<textarea disabled class="form-control" name="" id="logs" cols="30" rows="10"></textarea>
<script>
    $(document).ready(function() {
        let doSendEmail = function(emailUser, callback = null) {
            $.ajax({
                url: site_url + '/mailer/send_email',
                type: 'post',
                dataType: 'json',
                data: postDataWithCsrf.data({
                    email_target: emailUser
                }),
                success: function(res) {
                    if (callback != null) callback(true, res);
                },
                error: function(err) {
                    if (callback != null) callback(false, err.responseText);
                }
            });
        }

        let sendListContainer = $('#send_list');
        let logsContainer = $('#logs');
        let btnSend = $('#send');
        let logsText = '';
        let emailTargets = [];
        btnSend.attr('disabled', 1);
        let getTarget = function() {
            emailTargets = [];
            // emailTargets.push('riyansaputrai007@gmail.com');
            // emailTargets.push('riyan.cr007@gmail.com');

            // emailTargets.forEach(function(item) {
            //     sendListContainer.append(item + '\n');
            // });
            // btnSend.removeAttr('disabled');

            $.ajax({
                url: site_url + 'mailer/get_all_vendor',
                type: 'get',
                dataType: 'json',
                success: function(res) {
                    res.forEach(function(item) {
                        emailTargets.push(item.email);
                    });

                    emailTargets.forEach(function(item) {
                        sendListContainer.append(item + '\n');
                    });
                    btnSend.removeAttr('disabled');
                },
                error: function(res) {
                    //console.log(res);
                }
            });
        }

        getTarget();
        let destroyClickedElement = function(event) {
            // remove the link from the DOM
            document.body.removeChild(event.target);
        }


        let saveTextAsFile = function() {
            // grab the content of the form field and place it into a variable
            var textToWrite = logsContainer.val();
            //  create a new Blob (html5 magic) that conatins the data from your form feild
            var textFileAsBlob = new Blob([textToWrite], {
                type: 'text/plain'
            });
            // Specify the name of the file to be saved
            var fileNameToSaveAs = "myNewFile.txt";

            // Optionally allow the user to choose a file name by providing 
            // an imput field in the HTML and using the collected data here
            // var fileNameToSaveAs = txtFileName.text;

            // create a link for our script to 'click'
            var downloadLink = document.createElement("a");
            //  supply the name of the file (from the var above).
            // you could create the name here but using a var
            // allows more flexability later.
            downloadLink.download = fileNameToSaveAs;
            // provide text for the link. This will be hidden so you
            // can actually use anything you want.
            downloadLink.innerHTML = "My Hidden Link";

            // allow our code to work in webkit & Gecko based browsers
            // without the need for a if / else block.
            window.URL = window.URL || window.webkitURL;

            // Create the link Object.
            downloadLink.href = window.URL.createObjectURL(textFileAsBlob);
            // when link is clicked call a function to remove it from
            // the DOM in case user wants to save a second file.
            downloadLink.onclick = destroyClickedElement;
            // make sure the link is hidden.
            downloadLink.style.display = "none";
            // add the link to the DOM
            document.body.appendChild(downloadLink);

            // click the new link
            downloadLink.click();
        }

        $("#download").click(function(e) {

            e.preventDefault();
            saveTextAsFile();
        });

        btnSend.click(function() {
            emailTargets.forEach(function(email) {
                doSendEmail(email, function(stat, res) {
                    if (stat) {
                        logsText = `[${res.execution_time}] Send To: ${res.email_target}, Subject: ${res.email_subject},  Success : ${res.success}\n`;
                    } else {
                        logsText = `[${moment().format('Y-m-d HH:MM:SS')}] Error: ${res}\n`;
                    }

                    logsContainer.append(logsText);
                });
            });
        });

    });
</script>