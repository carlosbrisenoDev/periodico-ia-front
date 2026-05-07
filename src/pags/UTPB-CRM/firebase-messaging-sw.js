 importScripts('https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js');
 importScripts('https://www.gstatic.com/firebasejs/8.2.4/firebase-messaging.js');

 firebase.initializeApp({
   apiKey: "AIzaSyA0q595M_YBc8ouJS7-RxTQCEyTzChYav8",
   authDomain: "unisantorizaba-d6219.firebaseapp.com",
   projectId: "unisantorizaba-d6219",
   storageBucket: "unisantorizaba-d6219.appspot.com",
   messagingSenderId: "137153403272",
   appId: "1:137153403272:web:8cbd3acd09064bcb60023d",
   measurementId: "G-09DNFC1JEE"
 });


 const messaging = firebase.messaging();



messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  // Customize notification here
  const notificationTitle = 'Background Message Title';
  const notificationOptions = {
    body: 'Background Message body.',
    icon: '/firebase-logo.png'
  };

  self.registration.showNotification(notificationTitle,
    notificationOptions);
});

messaging.onMessage((payload) => {
  console.log('Message received. ', payload);
  // ...
});
