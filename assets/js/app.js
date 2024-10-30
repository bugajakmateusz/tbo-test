// SCSS
require('../scss/app.scss');


document.getElementById('login_form').addEventListener('submit', function(event) {
    event.preventDefault();

    const email = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const data = {
        "username": email,
        "password": password
    };
    console.log(data);

    fetch('/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => {
            if (response.ok) {
                alert("Git");
                return response.json();
            } else {
                throw new Error('Error: ' + response.statusText);
            }
        })
        .then(data => {
            document.cookie = "session=" + data.session;
            location.reload();
        })
        .catch((error) => {
            alert('Error: ' + error);
        });
});
