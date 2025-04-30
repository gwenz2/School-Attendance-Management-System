function showNotification() {
    if ("Notification" in window) {
        Notification.requestPermission().then((perm) => {
            if (perm === "granted") {
                const notification = new Notification("SAMS", {
                    body: `Teacher sent a message.`,
                    icon: "school-logo.png",
                    tag: `unique-tag-${Date.now()}`
                });

                // Debugging log
                console.log("Notification shown.");

                // Handle notification click
                notification.onclick = () => {
                    console.log("Notification clicked.");
                    window.close();
                    window.open("http://localhost/sams/parent/main.php", "_blank");
                    window.focus();
                    
                };
            } else {
                alert("Notifications are not permitted.");
            }
        });
    } else {
        alert("This browser does not support notifications.");
    }
}

setInterval(() => {
    if (localStorage.getItem('pNotify') === 'true') {
        showNotification();
        localStorage.removeItem('pNotify'); 
    }
}, 1000);
