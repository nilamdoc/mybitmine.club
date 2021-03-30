<?php

print_r($data);
?>
<div class="block sz1 bgcolor trans9 text-align-center padding">
 <img src="/img/myBitMine.png" class="responsive">
</div>
<div class="block sz1 bgcolor text-align-center margin padding">
 <form>
         
         <div class="list no-hairlines-md Viga sz14" id="LoginPage" style="display:block">
         <h3 class=" Rale sz1 c30">Login / Register</h3>
           <ul class=" elevation-8">
             <li class="item-content item-input item-input-outline c70">
               <div class="item-inner ">
                 <div class="item-title c50">Refered by</div>
                 <div class="item-input-wrap c70">
                  <input type="text" name="refer" id="refer" value="<?=$refer?>" readonly="readonly">
                 </div>
                </div>
             </li> 
<li class="item-content item-input">
            <div class="item-media">
              <button class="button button-fill Rale" onclick="sendOTP();"  style="margin-top:-50px;margin-right:10px">Send OTP</button>
            </div>
            <div class="item-inner">
              <div class="item-input-wrap">
                <input type="text" placeholder="Your name" />
                <span class="input-clear-button"></span>
              </div>
            </div>
          </li>

             <li class="item-content item-input item-input-outline c70">
               <div class="item-inner ">
                 <div class="item-title item-floating-label c50">Mobile</div>
                 <div class="item-input-wrap c30">
                  <input type="tel" name="mobile" id="mobile" placeholder="Mobile Number" required validate pattern="\+[0-9]*" class="c30">
                   <span class="input-clear-button"></span>
                 </div>
                 <span class="color-red button-text-color main-color"><small>Example: +919998887776</small></span>
               </div>
            <div class="item-media">
              <a class="button button-fill Rale" onclick="sendOTP();"  style="margin-top:-50px;margin-right:10px">Send OTP</a>
            </div>

             </li> 
             <li class="item-content item-input item-input-outline c70">
               <div class="item-inner ">
                 <div class="item-title item-floating-label c50">OTP</div>
                 <div class="item-input-wrap c30">
                  <input type="number" name="otp" id="otp" placeholder="OTP" required validate pattern="[0-9]*" data-error-message="Only numbers please!" max="999999" min="000001" class="c30">
                   <span class="input-clear-button"></span>
                 </div>
               </div>
               <div class="item-media">
                 <button class="button button-fill Rale" onclick="verifyOTP();" style="margin:5px;margin-right:10px">&nbsp;&nbsp;&nbsp;Verify&nbsp;&nbsp;&nbsp;&nbsp;</button>
               </div>
             </li>
             </ul> 
        </div>
 </form>
</div>