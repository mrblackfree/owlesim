/* eslint-disable no-undef */
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: 'AIzaSyD9HIKFrz-VkRl8_96ya7mgZQEA4Vr53VY',
    authDomain: 'esimtel-cf402.firebaseapp.com',
    projectId: 'esimtel-cf402',
    storageBucket: 'esimtel-cf402.firebasestorage.app',
    messagingSenderId: '568357508073',
    appId: '1:568357508073:web:YOUR_ACTUAL_WEB_APP_ID'
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Background message ', payload);

    const notificationTitle = payload.notification?.title || 'Notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: '/firebase-logo.png'
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});
