// if (typeof toNotifLink1 == 'undefined') {
//     let toNotifLink1 = function (link, linkType, dataID) {
//         $.ajax({
//             url: site_url + 'notification/update',
//             type: 'post',
//             dataType: 'json',
//             data: postDataWithCsrf.data({
//                 id: dataID
//             }),
//             success: function(res){
//                 if (linkType == 'Internal') {

//                     RyLinx.to(link, function() {
//                         $('#loading_content').hide();
//                         Notification.refresh_counter();
//                     });
//                 } else {
//                     window.location.href = link;
//                 }
//             },
//             error: (xhr, res, err) => {
//                 alert(err);
//             }
//         });
//     }
// }
