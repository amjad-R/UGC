<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label>First Name:</label>
            <input type="text" name="first_name" required>
        </div>
        <div>
            <label>Last Name:</label>
            <input type="text" name="last_name" required>
        </div>
        <div>
            <label>Phone:</label>
            <input type="text" name="phone" required>
        </div>
        <div>
            <label>Start Date:</label>
            <input type="date" name="start_date" required>
        </div>
        <div>
            <label>Upload File:</label>
            <input type="file" name="attachment">
        </div>
        <input type="hidden" name="service_id" value="{{ $serviceId }}">


        <h3>Order Details</h3>
        <div>
            <label>Post Type:</label>
            <select name="post_type" id="post_type" onchange="calculatePrice()">
                <option value="video">Video</option>
                <option value="reel">Reel</option>
                <option value="story">Story</option>
            </select>
        </div>
        <div>
            <label>Number of Posts:</label>
            <input type="number" name="post_count" id="post_count" min="1" value="1" onchange="calculatePrice()" required>
        </div>
        <div>
            <label>Duration:</label>
            <input type="number" name="duration" id="duration" min="1" onchange="calculatePrice()" required>
        </div>
        <div>
            <label>Price:</label>
            <input type="text" name="price" id="price" readonly>
        </div>
        <button type="submit">Submit Order</button>
    </form>

    <script>
        function calculatePrice() {
            let postType = document.getElementById("post_type").value;
            let postCount = parseInt(document.getElementById("post_count").value);
            let duration = parseInt(document.getElementById("duration").value);
            let price = 0;

            if (postType === "video") {
                price = postCount * duration * 100;
            } else if (postType === "reel") {
                price = postCount * duration * 80;
            } else if (postType === "story") {
                price = postCount * duration * 50;
            }

            document.getElementById("price").value = price;
        }
    </script>

</body>

</html>
