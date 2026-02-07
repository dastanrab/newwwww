@script
<script>
    /*$(document).ready(function (){
        if (!('PushManager' in window)) {
            console.log('Push messaging isn\'t supported.');
        }
        //
        if (Notification.permission === 'denied') {
            console.log('The user has blocked notifications.');
        }
        var firebaseConfig = {
            apiKey: "AIzaSyDAG-1hvKvrssVP1tAdURXgOySFmvNafWw",
            authDomain: "bazist-dashboard-44f81.firebaseapp.com",
            projectId: "bazist-dashboard-44f81",
            storageBucket: "bazist-dashboard-44f81.appspot.com",
            messagingSenderId: "558727115143",
            appId: "1:558727115143:web:a5c50463e7b6a84fb4a322",
            measurementId: "G-88F05CGMYJ"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        if(Notification.permission == 'default'){
            messaging
                .requestPermission()
                .then(function () {
                    return messaging.getToken()
                })
                .then(function(token) {
                    console.log(token);

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '{{ route('d.fcm') }}',
                        type: 'POST',
                        data: {
                            token: token
                        },
                        dataType: 'JSON',
                        success: function (response) {
                            alert('Token saved successfully.');
                        },
                        error: function (err) {
                            console.log('User Chat Token Error'+ err);
                        },
                    });

                }).catch(function (err) {
                console.log('User Chat Token Error'+ err);
            });
        }

        messaging.onMessage(function(payload) {
            alert(payload.data.title);
            const noteTitle = payload.data.title;
            const noteOptions = {
                body: payload.data.content,
                icon: payload.data.picture,
            };
            new Notification(noteTitle, noteOptions);
        });
    });*/


</script>
@endscript
