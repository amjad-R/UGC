const baseURL = "http://localhost:8000/api";

document.getElementById("serviceForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const serviceTitle = document.getElementById("serviceTitle").value;
    const serviceVideo = document.getElementById("serviceVideo").files[0];
    const serviceDescription = document.getElementById("serviceDescription").value;
    const serviceHashtags = document.getElementById("serviceHashtags").value;
    const selectedStatus = document.querySelector(".icon-container.selected")?.getAttribute("data-service-status") || "free";

    const platforms = [];
    document.querySelectorAll('.platform-section').forEach(section => {
        const platform = section.id.replace('-section', '');
        const platformDetails = [];

        section.querySelectorAll('.type-container').forEach(container => {
            const type = container.querySelector('label').textContent.split(' ')[0];
            const timing = container.querySelector('input[placeholder^="Timing"]').value;
            const price = container.querySelector('input[placeholder^="Price"]').value;

            if (timing && price) {
                platformDetails.push({
                    type: type,
                    time: timing,
                    price: price
                });
            }
        });

        if (platformDetails.length > 0) {
            platforms.push({ platform, services: platformDetails });
        }
    });

    if (!serviceTitle || !serviceVideo || !serviceDescription) {
        alert("Please fill out all required fields.");
        return;
    }

    if (platforms.length === 0) {
        alert("Please select at least one platform with its services.");
        return;
    }

    const formData = new FormData();
    formData.append("title", serviceTitle);
    formData.append("video", serviceVideo);
    formData.append("description", serviceDescription);
    formData.append("hashtags", serviceHashtags);
    formData.append("status", selectedStatus);
    formData.append("platforms", JSON.stringify(platforms));
    axios.post(`${baseURL}/services`, formData, {
        headers: {
            "Authorization": `Bearer ${localStorage.getItem("authToken")}`,
            "Content-Type": "multipart/form-data"
        }
    }).then(response => {
        alert("Service submitted successfully!");
        console.log(response.data);
    }).catch(error => {
        console.error("Error submitting service:", error);
        alert("An error occurred while submitting the service.");
    });
});
