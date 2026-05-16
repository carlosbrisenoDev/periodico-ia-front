<!DOCTYPE html>
<html>
    <head>
        <title>eCustomerManager</title>
        <script src="https://kit.fontawesome.com/adebd9a11d.js" crossorigin="anonymous"></script>
        <link id="pagestyle" href="{{asset('/assets/css/soft-design-system.css?v=1.0.9')}}" rel="stylesheet" />
        <style>
            @keyframes  glowing {
              0% {
                background-color: #2ba805;
                box-shadow: 0 0 5px #2ba805;
              }
              50% {
                background-color: #49e819;
                box-shadow: 0 0 20px #49e819;
              }
              100% {
                background-color: #2ba805;
                box-shadow: 0 0 5px #2ba805;
              }
            }
            .glowingButton {
              animation: glowing 1300ms infinite;
            }
            html,body{
                height:100%;
                margin:0;
            }
            #container-div{
                display:flex;
                height:100%;
                flex-direction: row-reverse;
            }
            .softphone{
                width:400px;
            }
            .ecm{
                width:100%;
            }
            #gridDiv{
                height:100%;
                display:flex;
                background-color:#FFFFFF;
            }
            #contactActionsDiv{
              display:flex;
              align-items: center;
              flex-wrap: wrap;
              text-align: center;
            }
            .btn{
              width:80%;
              margin-left:10%;
              margin-right:10%;
              margin-top:10px;
              background-color:white;
            }
            .btn-disable{
                display:none;
            }
            #customCCPDiv{
                background-color:#F8F8F8;
            }
            #agentGreetingDiv{
              text-align: center;
              font-weight: bold;;
            }
            #agentStatusDiv{
              text-align:center;
            }
            #goAvailableDiv{
              margin-top:20px;
            }
        </style>
        <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

<body>
    <div id="gridDiv">
        <div id="container-div" style="display: none;">
            <!--Amazon CCP is hiding in here-->
        </div>
        <div id="customCCPDiv" class="softphone" style="width:100px;">
            <!-- custom user experience goes here -->
            <div id="statusDiv" style="margin-top:50px;">
                <div id="agentGreetingDiv">
                    Agente
                </div>
                <div id="agentStatusDiv">
                  Cargando
                  <br>
                  <i class="fa-solid fa-spinner fa-spin" style="animation-duration:2s;"></i>
                </div>
                <div id="goAvailableDiv" class="btn btn-disable" style="background-color: #B2DFDB;" >
                    <span><i class="fa-solid fa-headphones-simple"></i></span><br />
                </div>
                <div id="goOfflineDiv"  class="btn btn-disable" style="background-color: #FFEBEE;">
                    <span><i class="fa-solid fa-power-off"></i></span><br />
                </div>
            </div>
            <div id="contactActionsDiv">
                <div id="answerDiv" class="btn btn-disable">
                    <span><i class="fa-solid fa-phone"></i></span><br />
                </div>
                <div id="hangupDiv" class="btn btn-disable">
                    <span><i class="fa-solid fa-phone-slash"></i></span><br />
                </div>
                <div id="clearDiv" class="btn btn-disable">
                    <span><i class="fa-solid fa-user-large-slash"></i></span><br />
                </div>
            </div>
        </div>
        <div id="logMsgsContainer" style="display:none;">
            <h2>Log Messages</h2>
            <div id="logMsgs" style="height: 400px; overflow: auto; border-style: outset; border-color: gray; border-width: thin;">
                <!-- Used to present all log messages -->
            </div>
        </div>
        <div id="eventMsgsContainer" style="display:none;">
            <h2>Event Messages</h2>
            <div id="eventMsgs" style="height: 400px; overflow: auto; border-style: outset; border-color: gray; border-width: thin;">
                <!-- Used to present all events from Streams API -->
            </div>
        </div>
        <iframe class="ecm" id="ecm" src="{{url('/')}}" frameborder="0"></iframe>
    </div>
    <script src="/lib/connect-streams-min.js"></script>
    <script type="module" src="/scripts/index-invisible.js"></script>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/preventF5.js?r=1"></script>
</body>
</html>
