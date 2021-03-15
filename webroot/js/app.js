var $$ = Dom7;
var app = new Framework7();
var server = window.location.protocol+'//'+window.location.hostname +'/';
var storage = "WeCap";

document.addEventListener("backbutton", onBackKeyDown, false);
function onBackKeyDown() {
 mainView.router.back();
}

function dataError(message){
		var opentoastdataError = app.toast.create({
  text: message,
  cssClass: 'toast_red',
  position: 'center',
  closeTimeout: 2000,
  closeButton: false,
 });
	opentoastdataError.open();
}
function dataSucess(message){
		var opentoastdataError = app.toast.create({
  text: message,
  cssClass: 'toast_green',
  position: 'center',
  closeTimeout: 2000,
  closeButton: false,
 });
	opentoastdataError.open();
}
var toastBottom = app.toast.create({
  text: 'Shopping cart updated!!',
  closeTimeout: 2000,
 });
var toastBottomNoInternet = app.toast.create({
  text: 'No Internet connection!',
  position: 'top',
  cssClass: 'toast_red',
  closeTimeout: 2000,
  closeButton: false,
 });


function searchmca() {
 var formData = app.form.convertToData('#join');
 if (formData.mcaNumber == "") {
  app.dialog.alert("Please enter Modicare MCA number.", "Join")
  return false;
 }
 console.log(JSON.stringify(formData));
 if (formData.mcaNumber.length != 8) {
  app.dialog.alert("Please enter correct Modicare MCA number.", "Join")
  return false;
 }
 var submitURL = server + 'wecap/searchmca';
 app.preloader.show();
 app.request.post(submitURL, formData, function (data) {
  var gotData = JSON.parse(data);

  if (gotData['success'] == "Yes") {
   if (gotData['user']['Enable'] == "Yes") {
    htmlnew = '<div class="card" >\
     <div class="card-header">' + gotData['user']['mcaName'] + '</div>\
     <div class="card-content card-content-padding-medium">Claim this MCA Number by verifing the mobile number and date of joining:\
     <input id="mcaNumber" name="mcaNumber" value="' + gotData['user']['mcaNumber'] + '" type="hidden">\
     <ul>\
     <li>\
     <div class="item-content item-input">\
     <div class="item-inner">\
     <div class="item-title item-label">Mobile</div>\
     <div class="item-input-wrap">\
     <input type="number" name="mobile" placeholder="Mobile" required validate pattern="[0-9]*" data-error-message="Only numbers please!" max="9999999999" min="1111111111">\
     </div>\
     </div>\
     </div>\
     </li>\
     <li>\
     <div class="item-content item-input">\
     <div class="item-inner">\
     <div class="item-title item-label">Date of Joining</div>\
     <div class="item-input-wrap">\
     <input type="date" name="dateofjoin" placeholder="Choose..." value="2000-05-31" required validate>\
     </div>\
     </div>\
     </div>\
     </li>\
     </ul><br><br>\
     <div class="block" id="submit">\
     <a href="#" onclick="return sendotp();" class="button button-raised button-fill button-round no-ripple" >Send OTP</a>\
     </div>\
     </div>\
     <div class="card-footer">MCA: ' + gotData['user']['mcaNumber'] + '</div>\
     </div>';
    $$("#MCAFound").html(htmlnew);
    $$("#join").hide();
    app.preloader.hide();
   } else {
    app.dialog.alert("Unable to find MCA", "Join");
    $$("#MCAFound").html("");
    app.preloader.hide();
    success = "No";
   }
  } else {
   app.dialog.alert("Unable to find MCA", "Join");
   $$("#MCAFound").html("");
   app.preloader.hide();
   success = "No";
  }
 }, function () {
  toastBottomNoInternet.open();
  app.preloader.hide();
 });
}

function sendotp() {
 var formData = app.form.convertToData('#claim');
 if (formData.mobile == "") {
  app.dialog.alert("Please enter mobile.", "Claim")
  return false;
 }
 if (formData.mobile.length != 10) {
  app.dialog.alert("Please enter correct mobile.", "Claim");
  return false;
 }
 if (formData.dateofjoin == "") {
  app.dialog.alert("Please enter date of joining.", "Claim")
  return false;
 }
 var submitURL = server + 'wecap/sendotp';
 app.preloader.show();
 app.request.post(submitURL, formData, function (data) {
  var gotData = JSON.parse(data);
  localStorage.setItem(storage + '.otp', gotData['otp']);
  if (gotData['success'] == "Yes") {
   htmlnew = '<div class="card">\
    <div class="card-header">' + gotData['user']['mcaName'] + '</div>\
    <div class="card-content card-content-padding">Claim this MCA Number by verifing the mobile number and date of joining:\
    <input id="mcaNumber" name="mcaNumber" value="' + gotData['user']['mcaNumber'] + '" type="hidden">\
    <ul>\
    <li>\
    <div class="item-content item-input">\
    <div class="item-inner">\
    <div class="item-title item-label">Verify OTP</div>\
    <div class="item-input-wrap">\
    <input type="number" name="otp" placeholder="123456" required validate pattern="[0-9]*" data-error-message="Only numbers please!" max="999999" min="000000">\
    </div>\
    </div>\
    </div>\
    </li>\
    </ul><br><br>\
    <div class="block" id="submit">\
    <a href="#" onclick="return verifyotp();" class="button button-raised button-fill button-round no-ripple" >Verify OTP</a>\
    </div>\
    </div>\
    <div class="card-footer">MCA: ' + gotData['user']['mcaNumber'] + '</div>\
    </div>';
   app.preloader.hide();
   $$("#MCAFound").html(htmlnew);
  } else {
   app.preloader.hide();
   app.dialog.alert("Date of joining is incorrect.", "Claim");
  }
 }, function () {
  toastBottomNoInternet.open();
  app.preloader.hide();
 });
}
function verifyotp() {
 var formData = app.form.convertToData('#claim');
 var otp = localStorage[storage + ".otp"];
 if (formData.otp == otp) {
  localStorage.setItem(storage + '.mcaNumber', formData.mcaNumber);
  checkLogin();
  app.dialog.alert("You are verified!", "Claim");
 }
}

function checkLogin() {
 if (localStorage[storage + ".mcaNumber"]) {
  var mcaNumber = localStorage[storage + ".mcaNumber"];
  var date = new Date();
  var yyyymm = formatYYYYMM(date);
  var submitURL = server + 'wecap/searchmca';
  var formData = new FormData();
  formData.append('mcaNumber', mcaNumber);
  app.preloader.show();
  app.request.post(submitURL, formData, function (data) {
   var gotData = JSON.parse(data);
   console.log(gotData);
			if(typeof gotData['user']['Inner']=="undefined"){
				var mcaBusinessName = gotData['user']['mcaName'];
			}else{
				var mcaBusinessName = gotData['user']['Inner']['mcaBusinessName'];
			}
			
   if (gotData['success'] == "Yes") {
    localStorage.setItem(storage + '.mcaName', gotData['user']['mcaName']);
    localStorage.setItem(storage + '.mcaMobile', gotData['user']['mobile']); 
				localStorage.setItem(storage + '.mcaBusinessName', mcaBusinessName);
    if (gotData['user'][yyyymm]) {
     localStorage.setItem(storage + '.ValidTitle', gotData['user'][yyyymm]['ValidTitle']);
    }
    app.preloader.hide();
    $$("#join").hide();
    console.log('/wecap/dashboard/' + mcaNumber + '/');
    window.location.href ='/wecap/dashboard/' + mcaNumber + '/';
    
    console.log('/dashboard/' + mcaNumber + '/');
    
//				showModalUser();
   } else {
//    $$("#join").show();
    app.preloader.hide();
   }
  }, function () {
   toastBottomNoInternet.open();
   app.preloader.hide();
  });
 } else {
//  $$("#join").show();
 }
}
function formatYYYYMM(date) {
 var d = new Date(date),
 month = '' + (d.getMonth() + 1),
 day = '' + d.getDate(),
 year = d.getFullYear();
 if (month.length < 2)
  month = '0' + month;
 if (day.length < 2)
  day = '0' + day;
 return [year, month].join('-');
}