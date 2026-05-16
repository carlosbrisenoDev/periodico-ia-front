<html>
    <head>
        <style>
            @import url("https://fonts.googleapis.com/css?family=Quicksand");
@import url("https://file.myfontastic.com/FGVacMnLcq5JgZK6TKH3mQ/icons.css");
* {
  font-family: "Quicksand", sans-serif;
  box-sizing: border-box;
}

html, body {
  height: 100%;
}

body {
  margin: 0px;
  overflow: hidden;
}

.clearfix:after, .sidebar ul li:after {
  content: "";
  display: table;
  clear: both;
}

#root {
  height: 100vh;
  display: flex;
  line-height: 1.5;
  margin: 0px;
}

.row {
  flex-direction: row;
  display: flex;
}

.col {
  display: flex;
  flex-direction: column;
}

[class*=col-] {
  display: flex;
  border-right: 1px solid #eaecee;
}

.col-1 {
  flex: 1;
}

.col-2 {
  flex: 1.5;
}

.col-3 {
  flex: 3;
}

[class*=panel] {
  background: #f3f6f9;
  border-left: 1px solid #eaecee;
  min-width: 200px;
  flex-direction: column;
}
[class*=panel]:first-child {
  border-left: 0px;
}

header {
  height: 75px;
  border-bottom: 1px solid #eaecee;
}

[class*=btn-], button {
  border: 0px;
  background: transparent;
  cursor: pointer;
  display: block;
}

.btn-success {
  background: #02c386;
  color: #FFF;
}

.mail-content {
  background: #FFF;
  position: relative;
  flex-direction: column;
}
.mail-content .content {
  flex-direction: column;
}
.mail-content .content .mail-info {
  padding-top: 65px;
  padding-bottom: 40px;
  padding-right: 210px;
  padding-left: 100px;
}
.mail-content .content .mail-info, .mail-content .content .mail-body, .mail-content .content .answer {
  padding-left: 100px;
}
.mail-content .content .sender, .mail-content .content .mail-body {
  color: #6f8393;
  font-size: 14px;
}
.mail-content .content .mail-body {
  padding-right: 100px;
}
.mail-content .content .mail-title {
  font-size: 36px;
  line-height: 48px;
}
.mail-content .content .mail-actions {
  position: absolute;
  font-size: 22px;
  color: #b1bcc6;
  right: 115px;
  padding-top: 100px;
}
.mail-content .content .mail-actions:after {
  content: "";
  background: #eaecee;
  width: 1px;
  height: 100%;
  position: absolute;
  top: 0px;
  left: 48px;
}
.mail-content .content .mail-actions .btn {
  margin: 0 10px;
}
.mail-content header {
  display: flex;
  width: 100%;
}
.mail-content header > div {
  border-right: 1px solid #eaecee;
}
.mail-content header .box-action {
  flex: 0 0 330px;
  width: 330px;
}
.mail-content header .box-search {
  flex: 1;
  position: relative;
}
.mail-content header .box-search i {
  position: absolute;
  z-index: 1;
  top: 22px;
  left: 15px;
  color: #b1bcc6;
  font-weight: light;
}
.mail-content header .box-search input {
  font-size: 18px;
  width: 100%;
  padding: 25px 0px;
  padding-left: 45px;
  border: 0px;
  color: #b1bcc6;
}
.mail-content header .box-profile {
  flex: 0 0 170px;
  width: 170px;
  display: flex;
}
.mail-content header .box-profile .profile-img {
  flex: 0 0 100px;
  background-size: cover;
  background-position: 0% 10%;
  position: relative;
}
.mail-content header .box-profile .profile-img:after {
  content: "";
  position: absolute;
  height: 100%;
  width: 100%;
  background: #b0a4fd;
  opacity: 0.7;
}
.mail-content header .box-profile .mail-counter {
  flex: 1;
  text-align: center;
  font-size: 24px;
  padding-top: 20px;
  color: #FFF;
  background: #b0a4fd;
}
.mail-content header button {
  padding: 18px;
  padding-top: 26px;
  float: left;
}
.mail-content header button i {
  margin: 0px;
  color: #b1bcc6;
  font-size: 26px;
  line-height: 12px;
}

.shadow-box {
  position: relative;
  box-shadow: -3px -3px 30px 15px rgba(202, 207, 212, 0.7);
}

.sidebar .btn-new-message {
  width: 100%;
  height: 100%;
  padding: 28px;
  font-size: 14px;
}
.sidebar .btn-new-message div {
  width: 120px;
  margin: 0 auto;
}
.sidebar .btn-new-message span {
  float: left;
  line-height: 24px;
}
.sidebar ul {
  list-style-type: none;
  margin: 0px;
  padding: 0px;
  margin: 30px;
  margin-top: 15px;
}
.sidebar ul .counter {
  float: right;
  color: #538fff;
}
.sidebar ul li {
  margin-top: 14px;
  font-size: 15px;
  color: #6f8393;
}
.sidebar ul li span {
  line-height: 26px;
}
.sidebar ul li.active, .sidebar ul li:hover {
  color: #262a2d;
  cursor: pointer;
}
.sidebar ul.mail-status label {
  font-size: 12px;
  color: #b1bcc6;
}

.mail-list {
  list-style-type: none;
  margin: 0px;
  padding: 0px;
  position: relative;
}
.mail-list li {
  position: relative;
  padding: 25px 60px;
  border-bottom: 1px solid #eaecee;
}
.mail-list li:hover {
  background: #f6f8fb;
}
.mail-list li.active {
  box-shadow: -3px -3px 30px 15px rgba(202, 207, 212, 0.25);
  background: #FFF;
}
.mail-list .status-icon {
  position: absolute;
  left: 35px;
  top: 20px;
}
.mail-list .name {
  font-size: 18px;
  line-height: 24px;
  color: #262a2d;
}
.mail-list .title {
  font-size: 16px;
  color: #6f8393;
}
.mail-list .preview {
  font-size: 15px;
  margin-top: 8px;
  color: #b1bcc6;
}
.mail-list time {
  position: absolute;
  top: 28px;
  padding-right: 25px;
  right: 0px;
  font-size: 13px;
  color: #b1bcc6;
}
.mail-list time:after {
  height: 100%;
  width: 3px;
  display: block;
  content: "";
  position: absolute;
  top: 0px;
  right: 0px;
}
.mail-list .label-color-orange time:after {
  background: #fd7c4e;
  box-shadow: 0px 0px 10px 4px rgba(253, 124, 78, 0.3);
}
.mail-list .label-color-blue time:after {
  background: #538fff;
  box-shadow: 0px 0px 10px 4px rgba(83, 143, 255, 0.3);
}
.mail-list .label-color-purple time:after {
  background: #c35bee;
  box-shadow: 0px 0px 10px 4px rgba(195, 91, 238, 0.3);
}
.mail-list .label-color-green time:after {
  background: #02c386;
  box-shadow: 0px 0px 10px 4px rgba(2, 195, 134, 0.3);
}

.dropdown-list {
  margin: 30px;
  position: relative;
  cursor: pointer;
  font-size: 14px;
  color: #6f8393;
}
.dropdown-list label {
  cursor: pointer;
}
.dropdown-list input {
  visibility: hidden;
}
.dropdown-list input:checked ~ ul {
  display: block;
}
.dropdown-list .selected-item {
  color: #262a2d;
  text-decoration: underline;
}
.dropdown-list ul {
  z-index: 10;
  display: none;
  position: absolute;
  top: 45px;
  left: -30px;
  list-style-type: none;
  margin: 0px;
  padding: 0px;
  background: #f3f6f9;
  border: 2px solid #eaecee;
  border-radius: 6px;
  min-width: 200px;
}
.dropdown-list ul label {
  padding: 12px;
  line-height: 40px;
}
.dropdown-list ul li {
  cursor: pointer;
  border-bottom: 1px solid #eaecee;
}
.dropdown-list ul li:hover {
  background: #e5e7e9;
}
.dropdown-list ul:after, .dropdown-list ul:before {
  bottom: 100%;
  left: 50%;
  border: solid transparent;
  content: " ";
  height: 0;
  width: 0;
  position: absolute;
  pointer-events: none;
}
.dropdown-list ul:after {
  border-color: rgba(136, 183, 213, 0);
  border-bottom-color: #f3f6f9;
  border-width: 6px;
  margin-left: -6px;
}
.dropdown-list ul:before {
  border-color: rgba(194, 225, 245, 0);
  border-bottom-color: #eaecee;
  border-width: 9px;
  margin-left: -9px;
}

[class*=icon] {
  margin: 5px;
  margin-left: 0px;
  margin-right: 10px;
}

.icon-circle, .icon-circle-fill {
  float: left;
  display: block;
  width: 10px;
  height: 10px;
  border: 2px solid #000;
  border-radius: 50%;
}
.icon-circle.color-orange, .color-orange.icon-circle-fill {
  border-color: #fd7c4e;
}
.icon-circle.color-blue, .color-blue.icon-circle-fill {
  border-color: #538fff;
}
.icon-circle.color-green, .color-green.icon-circle-fill {
  border-color: #02c386;
}
.icon-circle.color-purple, .color-purple.icon-circle-fill {
  border-color: #c35bee;
}

.icon-circle-fill {
  background: #538fff;
}

.icon-add {
  float: left;
  display: block;
  width: 14px;
  height: 14px;
  position: relative;
}
.icon-add:after {
  content: "";
  display: block;
  height: 14px;
  width: 2px;
  left: 6px;
  background: #b1bcc6;
  position: absolute;
}
.icon-add:before {
  content: "";
  display: block;
  height: 2px;
  width: 14px;
  left: 0px;
  top: 6px;
  background: #b1bcc6;
  position: absolute;
}

.icon-send-message {
  float: left;
  display: block;
  width: 12px;
  height: 12px;
  position: relative;
  border-radius: 3px;
  border: 2px solid #FFF;
}
.icon-send-message:after {
  content: "";
  display: block;
  width: 2px;
  height: 12px;
  background: #FFF;
  border: 2px solid #02c386;
  transform: rotate(45deg);
  position: absolute;
  bottom: 0px;
  left: 4px;
}

.icon-caret-down {
  top: 12px;
  left: 5px;
  position: relative;
  border: solid transparent;
  height: 10px;
  width: 10px;
  pointer-events: none;
  border-color: transparent;
  border-top-color: #6f8393;
  border-width: 4px;
}

.modal-window {
  position: fixed;
  background-color: rgba(96, 125, 139, 0.8);
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: 999;
  opacity: 0;
  pointer-events: none;
  -webkit-transition: all 0.3s;
  -moz-transition: all 0.3s;
  transition: all 0.3s;
}

.modal-window:target {
  opacity: 1;
  pointer-events: auto;
}

.modal-window > div {
  width: 400px;
  position: relative;
  margin: 10% auto;
  padding: 2rem;
  background: #fff;
  color: #444;
}

.modal-window header {
  font-weight: bold;
}

.modal-close {
  color: #aaa;
  line-height: 50px;
  font-size: 80%;
  position: absolute;
  right: 0;
  text-align: center;
  top: 0;
  width: 70px;
  text-decoration: none;
}

.modal-close:hover {
  color: #000;
}

.modal-window h1 {
  font-size: 150%;
  margin: 0 0 15px;
}
        </style>
    </head>
    <body>
        <div id="root">
            <div class="col-1 panel sidebar">
              <header>
                <a href="#send-mail" class="btn-success btn-new-message shadow-box">
                  <div class="clearfix">
                    <i class="icon-send-message"></i>
                    <span>New message</span>
                  </div>
                </a>
              </header>
                
              <ul class="mail-status">
                 <li class="active">
                   <span class="name">Inbox</span>
                   <span class="counter">34</span>
                </li>
                 <li>
                   <span class="name">Sent</span>
                   <span class="counter"></span>
                </li>
                 <li>
                   <span class="name">Draft</span>
                   <span class="counter"></span>
                </li>
                 <li>
                   <span class="name">Junk</span>
                   <span class="counter"></span>
                </li>
                 <li>
                   <span class="name">Trash</span>
                   <span class="counter"></span>
                </li>
                 <li>
                   <span class="name">Reminder</span>
                   <span class="counter"></span>
                </li>
              </ul>
              
              <ul class="mail-status">
                <label>Labels</label>
                 <li>
                   <i class="icon-circle color-orange"></i>
                   <span class="name">Personal</span>
                   <span class="counter">10</span>
                </li>
                 <li>
                   <i class="icon-circle color-green"></i>
                   <span class="name">Work</span>
                   <span class="counter">10</span>
                </li>
                 <li>
                   <i class="icon-circle color-purple"></i>
                   <span class="name">Friends</span>
                   <span class="counter">10</span>
                </li>
                 <li>
                   <i class="icon-circle color-blue"></i>
                   <span class="name">Personal</span>
                   <span class="counter">10</span>
                </li>  
                 <li>
                   <i class="icon-add"></i>
                   <span class="name">Add</span>
                </li>
              </ul>
            </div>
            <div class="col-2 panel list-panel">
              <header>
                <div class="dropdown-list">
                  <label for="select-sort">Sort by <span class="selected-item">Date</span> <i class="icon-caret-down"></i></label>
                  <input type="checkbox" id="select-sort">
                  <ul class="shadow-box">
                    <li><label for="select-sort">Order by date</label></li>
                    <li><label for="select-sort">Order by priority</label></li>
                    <li><label for="select-sort">Order by frienship</label></li>
                  </ul>
                </div>
          <!--       <div class="search-box">Search box</div> -->
              </header>
              <ul class="mail-list">
                 <li class="label-color-orange">
                   <div class="status-icon"><i class="icon-circle-fill color-blue"></i></div>
                   <div class="name">Lisa Guerrero</div>
                   <div class="title">Company Goals for 2016</div>
                   <div class="preview">Hello everyone, i'm happy to share with you our new company goals...</div>
                   <time>20:24</time>
                </li>
                <li class="label-color-green active">
                   <div class="name">Peter Gregor</div>
                   <div class="title">Design for health project</div>
                   <div class="preview">Hi Jessica, I love your UI design work, and i'd like to talk with you...</div>
                   <time>14:10</time>
                </li>
                <li class="label-color-purple">
                   <div class="name">Sara Richardson</div>
                   <div class="title">Meeting Zurich</div>
                   <div class="preview">Hi Jessica, I will be in Zurich tomorrow, hope we can meet there</div>
                   <time>14:10</time>
                </li>
                <li class="label-color-blue">
                   <div class="name">Jeffrey Reynolds</div>
                   <div class="title">Photos from holliday</div>
                   <div class="preview">Finally, i put together some photos from our awesome holliday... </div>
                   <time>12:04</time>
                </li>
              </ul>    
            </div>
            
            
            
              
            <div class="col-3 mail-content">
              <header>
                <div class="box-action">
                  <button><i class="icon-trash"></i></button>
                  <button><i class="icon-reply"></i></button>
                  <button><i class="icon-reply-all"></i></button>
                  <button><i class="icon-right-arrow"></i></button>
                  <button><i class="icon-opes"></i></button>
                </div>
                <div class="box-search">
                  <div class="block-group">
                    <i class="icon-search"></i>
                    <input type="text" id="mail-search" placeholder="Search">      
                  </div>
                </div>
                <div class="box-profile">
                  <div class="profile-img" style="background-image: url(https://pbs.twimg.com/profile_images/507626256100110338/pK3qZBAq.jpeg);"></div>
                  <div class="mail-counter">
                    1
                  </div>
                </div>
              </header>
              <div class="content">
                <div class="mail-actions">
                    <div class="row">
                      <div class="btn btn-transparent next"><i class="icon-left"></i></div>
                      <div class="btn btn-transparent prev"><i class="icon-right"></i></div>
                    </div>
                  </div>
                <div class="mail-info ">
                  <div class="sender">
                    <span class="mail-from">Peter Gregor</span>
                    to
                    <span class="mail-to">Jessica Larson</span>
                  </div>
                  <span class="mail-title">Design for Health Project</span>
                </div>
                <div class="mail-body">
                  Hi Jessica, <br/><br/>
                  I love your UI design work, and I'd like to talk with you about possibly revamping the UI on our desktop application. Can we jump on Skype to discuss it when you have some time? <br/><br/>
                  I am looking for a UI designer for an upcoming health app. We already have an established web presence and are now moving to the mobile space. 
                  Have a nice day.<br/>
                  <br/>Peter 
                </div>
            </div>
          </div>
            
          <div id="send-mail" class="modal-window">
              <div>
                  <a href="#modal-close" title="Close" class="modal-close">Close</a>
               <h1>New Message</h1>
               <div>Nam tempor turpis sapien, a scelerisque purus pretium vitae. Nunc arcu nulla, pulvinar a ipsum id, sodales consequat enim. Aenean dapibus cursus accumsan.</div>
              </div>
          </div>
        </div>
    </body>
</html>