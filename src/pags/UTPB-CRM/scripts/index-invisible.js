import initializeCCP from './initCCP-invisible.js';
import session from './session.js';

// Add the call to init() as an onload so it will only run once the page is loaded
window.onload = (event) => {
    console.log("window.onload")
    try {
        initializeCCP('container-div');
    } catch (error) {
        console.error('CCP initialization error', error);
    }
    window.makeCall = phone => {
        var agent = new lily.Agent();
        agent.connect(connect.Endpoint.byPhoneNumber(phone),{
            success: function() { /* ... */ },
            failure: function(err) {
                
            }
        });
        displayAgentStatus("PendingBusy");
    }

    window.accept = () => {
        acceptContact();
    }

    window.disconnect = () => {
        disconnectContact();
    }
};



// Event listeners for the 5 "buttons" of the Webpage
document.getElementById ('goAvailableDiv').addEventListener("click", goAvailable, false);
document.getElementById ('goOfflineDiv').addEventListener("click", goOffline, false);
document.getElementById ('answerDiv').addEventListener("click", acceptContact, false);
document.getElementById ('hangupDiv').addEventListener("click", disconnectContact, false);
document.getElementById ('clearDiv').addEventListener("click", clearContact, false);


// Set the agent Available
function goAvailable() {
    var routableState = session.agent.getAgentStates().filter(function (state) {
        return state.type === connect.AgentStateType.ROUTABLE;
    })[0];
    session.agent.setState(routableState, {
        success: function () {
            logInfoMsg("Set agent status to Available (routable) via Streams")
        },
        failure: function () {
            logInfoMsg("Failed to set agent status to Available (routable) via Streams")
        }
    });
}

// Set the agent offline
function goOffline() {
    var offlineState = session.agent.getAgentStates().filter(function (state) {
        return state.type === connect.AgentStateType.OFFLINE;
    })[0];
    session.agent.setState(offlineState, {
        success: function () {
            logInfoMsg("Set agent status to Offline via Streams")
        },
        failure: function () {
            logInfoMsg("Failed to set agent status to Offline via Streams")
        }
    });
}

// Accept the contact
function acceptContact() {
    logInfoMsg("Accept contact");
    session.contact.accept({
        success: function () {
            logInfoMsg("Accepted contact via Streams");
        },
        failure: function () {
            logInfoMsg("Failed to accept contact via Streams");
        }
    });

    
}

// Disconnect the current contact
function disconnectContact() {
    //cannot do contact.destroy(), can only destroy (hang-up) agent connection
    session.contact.getAgentConnection().destroy({
        success: function () {
            logInfoMsg("Disconnected contact via Streams");
        },
        failure: function () {
            logInfoMsg("Failed to disconnect contact via Streams");
        }
    });
}

// To destroy the contact when agent is in ACW
function clearContact() {
    session.contact.clear({
        success: function () {
            logInfoMsg("Cleared contact via Streams");
        },
        failure: function () {
            logInfoMsg("Failed to clear contact via Streams");
        }
    });
}


// Global functions used to write Log and Event messages to the screen - Ignore them
export function logMsgToScreen(msg) {
    logMsgs.innerHTML = '<div>' + new Date().toLocaleTimeString() + ' ' + msg + '</div>' + logMsgs.innerHTML;
}

export function logEventToScreen(msg) {
    eventMsgs.innerHTML = '<div>' + new Date().toLocaleTimeString() + ' ' + msg + '</div>' + eventMsgs.innerHTML;
}

export function logInfoMsg(msg) {
    connect.getLog().info(msg);
    logMsgToScreen(msg);
}

export function logInfoEvent(eventMsg) {
    connect.getLog().info(eventMsg);
    logEventToScreen(eventMsg);
}

export function displayAgentStatus(status) {
    let stados = {
        "Available" : {
            name:"Disponible",
            color:"#09B83E",
            do : () => {
                document.querySelector("#goAvailableDiv").classList.add("btn-disable");
                document.querySelector("#goOfflineDiv").classList.remove("btn-disable");
                //document.querySelector("#answerDiv").classList.add("btn-disable");
                document.querySelector("#hangupDiv").classList.add("btn-disable");
                document.querySelector("#clearDiv").classList.add("btn-disable");
            } 
        }, 
        "Offline" : {
            name:"Ocupado",
            color:"#DF2029",
            do : () => {
                document.querySelector("#goOfflineDiv").classList.add("btn-disable");
                document.querySelector("#goAvailableDiv").classList.remove("btn-disable");
                //document.querySelector("#answerDiv").classList.add("btn-disable");
                document.querySelector("#hangupDiv").classList.add("btn-disable");
                document.querySelector("#clearDiv").classList.add("btn-disable");
            } 
        }, 
        "PendingBusy" : {
            name:"Llamada entrante",
            color:"#FFFC00",
            do : () => {
                document.querySelector("#goOfflineDiv").classList.add("btn-disable");
                document.querySelector("#goAvailableDiv").classList.remove("btn-disable");
                //document.querySelector("#answerDiv").classList.add("btn-disable");
                document.querySelector("#hangupDiv").classList.remove("btn-disable");
                document.querySelector("#clearDiv").classList.remove("btn-disable");
            } ,
        "MissedCallAgent" : {
            name:"Llamada perdida",
            color:"#F57D00",
            do : () => {
                document.querySelector("#clearDiv").classList.remove("btn-disable");
            }
        },
        "FailedConnectCustomer" : {
            name:"Fallida",
            color:"#09B83E",
            do : () => {
                document.querySelector("#goAvailableDiv").classList.remove("glowingButton");
            }
        },
        "FailedConnectAgent" : {
            name:"Fallida",
            color:"#09B83E",
            do : () => {
                document.querySelector("#goAvailableDiv").classList.remove("glowingButton");
            }
        },
        }};

    console.debug(status);
    status = stados[status];
    agentStatusDiv.innerHTML = 'Estado: <span style="font-weight: bold">' + status.name + '</span>';
    agentStatusDiv.innerHTML += `<br><i class="fa-solid fa-circle-user" style="color:${status.color}"></i>`;
    status.do();
}
