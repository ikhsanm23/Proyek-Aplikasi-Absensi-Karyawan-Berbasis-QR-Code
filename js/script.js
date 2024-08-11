document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const container = document.querySelector('.container');
    const toggleBtn = document.querySelector('.toggle-btn');

    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('closed');
        container.classList.toggle('shifted');
    });

    const qrForm = document.getElementById('qr-form');
    qrForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const qrData = document.getElementById('qr-data').value;
        fetch('generate_qr.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `qr-data=${qrData}`,
        })
        .then(response => response.text())
        .then(data => {
            document.getElementById('qr-result').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
    });

});
