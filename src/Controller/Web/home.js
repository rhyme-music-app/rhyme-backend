const secretInput = document.getElementById("secret-input");

function resetDatabase() {
    fetch('/api/reset/database', {
        method: 'POST',
        body: JSON.stringify({
            'APP_SECRET': secretInput.value ?? ''
        }),
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
    })
    .then(async (response) => {
        data = await response.json();
        alert(data.message);
    })
    .catch((error) => {
        alert('Network Error: ' + error);
    })
    .finally(() => {
        secretInput.value = "";
        secretInput.focus();
    });
}

secretInput.addEventListener("keypress", function (event) {
    if (event.key === "Enter") {
        event.preventDefault();
        document.getElementById("reset-database-button").click();
    }
});
